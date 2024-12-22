<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::with(['category', 'options', 'images', 'info'])
        ->orderBy("id","desc")->paginate((int) $request->per_page ?: 10);

        return response()->json([
            "success" => true,
            "message" => "Products retrieved successfully",
            "data"=> $products
        ], 200);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'info_en' => 'nullable|string',
            'info_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'sale_percentage' => 'nullable|numeric|between:0,100',
            'quantity' => 'required|integer|min:0',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'other_info' => 'nullable|array',
            'other_info.*.key_en' => 'required|string',
            'other_info.*.key_ar' => 'required|string',
            'other_info.*.value_en' => 'required|string',
            'other_info.*.value_ar' => 'required|string',
            'options' => 'nullable|array', // Options as key-value pairs
            'options.*.option_type' => 'required|string',
            'options.*.option_value' => 'required|string',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
    
        
        // Create the product
        $product = Product::create($validatedData);
        
        // Save product infos
        if ($request->filled('other_info')) {
            foreach ($request->input('other_info') as $info) {
                $product->info()->create([
                    'key_en' => $info['key_en'],
                    'key_ar' => $info['key_ar'],
                    'value_en' => $info['value_en'],
                    'value_ar' => $info['value_ar'],
                ]);
            }
        }
        // Save options if provided
        if (!empty($validatedData['options'])) {
            foreach ($validatedData['options'] as $option) {
                $product->options()->create([
                    'option_type' => $option['option_type'],
                    'option_value' => $option['option_value'],
                ]);
            }
        }
    
        // Save images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('public/products');
                $product->images()->create([
                    'path' => Storage::url($imagePath),
                ]);
            }
        }
    
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('options', 'images', 'category', 'info'),
        ], 201);
    }
    
    public function show($id)
    {
        $product = Product::with('options', 'images', 'category', 'info')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product,
        ], 200);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'info_en' => 'nullable|string',
            'info_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'sale_percentage' => 'nullable|numeric|between:0,100',
            'rate' => 'nullable|numeric|between:0,5',
            'quantity' => 'required|integer|min:0',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'other_info' => 'nullable|array',
            'other_info.*.key_en' => 'required|string',
            'other_info.*.key_ar' => 'required|string',
            'other_info.*.value_en' => 'required|string',
            'other_info.*.value_ar' => 'required|string',
            'options' => 'nullable|array',
            'options.*.option_type' => 'required|string',
            'options.*.option_value' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
    
        $product->update($validatedData);
    
        // Update product options
        if ($request->filled('options')) {
            $product->options()->delete(); // Clear existing options
            foreach ($request->input('options') as $option) {
                $product->options()->create([
                    'option_type' => $option['option_type'],
                    'option_value' => $option['option_value'],
                ]);
            }
        }
    
        // Update product images
        if ($request->hasFile('images')) {
            $product->images()->delete(); // Clear existing images
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/products');
                $product->images()->create(['path' => Storage::url($path)]);
            }
        }
    
        // Update product infos
        if ($request->filled('other_info')) {
            $product->info()->delete(); // Clear existing product info
            foreach ($request->input('other_info') as $info) {
                $product->productInfos()->create([
                    'key_en' => $info['key_en'],
                    'key_ar' => $info['key_ar'],
                    'value_en' => $info['value_en'],
                    'value_ar' => $info['value_ar'],
                ]);
            }
        }
    
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->load('options', 'images', 'productInfos', 'category', 'info'),
        ], 200);
    }
    

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->images()->each(function ($image) {
            Storage::delete($image->path);
        });

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
