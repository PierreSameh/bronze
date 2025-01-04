<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\FetchProductTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getProducts();

        return response()->json([
            "success" => true,
            "message" => "Products retrieved successfully",
            "data" => $products
        ], 200);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            // Step 1: Validate and retrieve the data from the FormRequest
            $validated = $request->validated();

            // Step 2: Use the ProductService to create the products
            $productModels = $this->productService->createProduct($validated);

            // Log the created product models for debugging purposes
            Log::info('Products created successfully', ['products' => $productModels]);

            // Step 3: Return a success response with the created products
            return response()->json([
                "success" => true,
                "message" => "Products created successfully",
                "data" => $productModels
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 422);
        } catch (\Throwable $th) {
            // Step 5: Handle any other exceptions and return an error message
            Log::error('Error creating products', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                "success" => false,
                "message" => "An error occurred while creating products. Please try again later."
            ], 500);
        }
    }

    public function show($itemNo)
    {
        try {
            $product = $this->productService->getProductByItmeNo($itemNo);

            return response()->json([
                "success" => true,
                "message" => "Products created successfully",
                "data" => $product
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "Not Found Product"
            ], 404);
        }
    }

    // update

    // destroy
}
