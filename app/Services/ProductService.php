<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Traits\FetchProductTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ProductService
{
    use FetchProductTrait;

    protected $productRepository;

    /**
     * Constructor to initialize the ProductRepository dependency.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Creates products by fetching models from a remote source and saving them to the database.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function createProduct($data)
    {
        // Fetch product models from the provided URL using the FetchProductTrait
        $models = $this->fetchProductsModels($data['url']);

        foreach ($models['data'] as $model) {
            $product = $this->productRepository->getByItemNo($model['itemNo']);

            if ($product) {
                throw ValidationException::withMessages([
                    'itemNo' => ['Product already exists.']
                ]);
            }
        }

        // Handle errors during model fetching
        if ($models['status'] === false) {
            throw new Exception($models['message']);
        }

        try {
            // Begin a database transaction to ensure data consistency
            DB::beginTransaction();

            $product_models = [];

            // Create a group for the fetched products
            $group = $this->productRepository->createGroup();

            // Iterate over each fetched model and create products
            foreach ($models['data'] as $model) {
                $model['group_id'] = $group->id;
                $model['category_id'] = $data['category_id'];

                // Save the product to the database
                $product = $this->productRepository->create($model);

                // Assign options to the product
                $this->productRepository->assignOptions(
                    $product,
                    !empty($model['optionList']) ? $model['optionList']['items'] : []
                );

                // Assign additional data to the product
                $this->productRepository->assignData($product, $model);

                // Download and assign product images
                $this->downloadProductImages($product);

                $product_models[] = $product;
            }

            // Commit the transaction if all operations succeed
            DB::commit();

            return $product_models;
        } catch (Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Retrieve a product by its item number.
     *
     * @param string $itemNo
     * @return \App\Models\Product
     * @throws \Exception
     */
    public function getProductByItmeNo($itemNo)
    {
        $product = $this->productRepository->getByItemNo($itemNo);

        if (!$product) {
            throw new Exception('Product not found.');
        }

        return $product;
    }

    public function getProducts()
    {
        return $this->productRepository->getProducts();
    }

    /**
     * Downloads product images from a remote API and assigns them to the product.
     *
     * @param object $product
     * @return void
     * @throws Exception
     */
    private function downloadProductImages($product)
    {
        $apiUrl = "https://open.sunsky-online.com/openapi/product!getImages.do";
        $parameters = [
            'key'       => $this->key,
            'itemNo'    => $product->itemNo,
            'size'      => '700',
            'watermark' => 'mysite.com',
        ];
        $zipPath = storage_path("app/public/{$product->itemNo}.zip");
        $extractPath = storage_path("app/public/{$product->itemNo}");

        // Download the ZIP file containing images
        OpenApiService::download($apiUrl, $this->secret, $parameters, $zipPath);

        $zip = new ZipArchive();

        // Extract the ZIP file
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new Exception('Unable to unzip the file.');
        }

        // Ensure the product exists before assigning images
        if (!$product) {
            throw new Exception('Product not found.');
        }

        // Assign images to the product's media collection
        $files = File::files($extractPath);
        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                $product->addMedia($file->getPathname())->toMediaCollection('images');
            }
        }

        // Cleanup: Remove the ZIP file and extracted folder
        File::delete($zipPath);
        File::deleteDirectory($extractPath);
    }
}
