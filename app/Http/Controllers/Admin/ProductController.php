<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::with(['category', 'products.options', 'products.images'])
        ->orderBy("id","desc")->paginate((int) $request->per_page ?: 10);

        return response()->json([
            "success" => true,
            "message" => "Products retrieved successfully",
            "data"=> $products
        ], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "category_id" => "required|exists:categories,id",
            "name_en" => "required|string|max:255",
            "name_ar" => "required|string|max:255",
            "info_en" => "nullable|string",
            "info_ar" => "nullable|string",
            "price"=> "required|numeric",
            "wholesale_price"=> "nullable|numeric",
            "sale_percentage"=> "nullable|numeric|min:0,1|max:100",
            "quantity" => "required|numeric",
            "description_en" => "nullable|string",
            "description_ar" => "nullable|string",
            "other_info_en" => "nullable|array",
            "other_info_ar" => "nullable|array",
        ]);
    }
}
