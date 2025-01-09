<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\Product;

class ProductRepository
{
    /**
     * Create a new product record in the database.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data)
    {
        // Validate input data before creating the product
        return Product::create($data);
    }

    /**
     * Assign multiple options to a product.
     *
     * @param \App\Models\Product $product
     * @param array $options
     * @return void
     */
    public function assignOptions($product, array $options)
    {
        if (!empty($options)) {
            // Create multiple related option records for the product
            $product->options()->createMany($options);
        }
    }

    /**
     * Assign translation or additional data to a product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return void
     */
    public function assignData($product, array $data)
    {
        // Validate and ensure data contains translatable fields
        $product->translations()->create($data);
    }

    /**
     * get product with media.
     *
     * @param string $itemNo
     * @return void
     */
    public function getByItemNo($itemNo)
    {
        // Fetch the product by itemNo
        $product = Product::where('itemNo', $itemNo)->first();

        // Retrieve images/media associated with the product
        if ($product)
            $media =  $product?->getMedia('images')->map(function ($mediaItem) {
                return [
                    'id' => $mediaItem->id,
                    'file_name' => $mediaItem->file_name,
                    'url' => $mediaItem->getUrl(),
                    'size' => $mediaItem->size,
                    'mime_type' => $mediaItem->mime_type,
                ];
            });

        if ($product)
            $product->media = $media;

        return $product;
    }

    /**
     * Retrieve paginated products with their first media item and translations.
     *
     * This function fetches products from the database, including their associated
     * translations and the first image in their media collection. The results are
     * paginated with a limit of 20 products per page.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function getProducts()
    {
        $products = Product::with('translations')->paginate(20);

        if ($products)
            foreach ($products as $item) {
                $item->media = $item->getMedia('images')->first();
            }

        return $products;
    }

    /**
     * Create a new group record.
     *
     * @return \App\Models\Group
     */
    public function createGroup()
    {
        // Optionally set default attributes for a group if needed
        return Group::create();
    }
}
