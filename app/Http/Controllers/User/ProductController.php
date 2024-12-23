<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Fetch products along with relationships and the count of likes and dislikes for each review
        $products = Product::with([
            'category',
            'options',
            'images',
            'info',
            'reviews' => function ($query) {
                $query->withCount([
                    'interacts as likes_count' => function ($query) {
                        $query->where('interact', 'like');
                    },
                    'interacts as dislikes_count' => function ($query) {
                        $query->where('interact', 'dislike');
                    }
                ]);
            },
        ])
        ->get();

        // if($request->user()){
        // // Add user interaction data to each review
        // $products = $products->map(function ($product)  {
        //     $product->reviews = $product->reviews->map(function ($review)  {
        //         // Check if the user has interacted with the review
        //         $userInteracted = $review->interacts->where('user_id', $request->user()->id)->first();
        //         $review->user_interaction = $userInteracted ? $userInteracted->interact : null; // 'like' or 'dislike' or null
        //         return $review;
        //     });
        //     return $product;
        // });
        // }

        return response()->json([
            "success" => true,
            "message" => "Products fetched successfully",
            "data" => $products
        ], 200);
    }
    public function paginate(Request $request)
    {
        // Fetch products along with relationships and the count of likes and dislikes for each review
        $products = Product::with([
            'category',
            'options',
            'images',
            'info',
            'reviews' => function ($query) {
                $query->withCount([
                    'interacts as likes_count' => function ($query) {
                        $query->where('interact', 'like');
                    },
                    'interacts as dislikes_count' => function ($query) {
                        $query->where('interact', 'dislike');
                    }
                ]);
            },
        ])
        ->paginate((int) $request->per_page ?: 10);

        // if($request->user()){
        // // Add user interaction data to each review
        // $products = $products->map(function ($product)  {
        //     $product->reviews = $product->reviews->map(function ($review)  {
        //         // Check if the user has interacted with the review
        //         $userInteracted = $review->interacts->where('user_id', $request->user()->id)->first();
        //         $review->user_interaction = $userInteracted ? $userInteracted->interact : null; // 'like' or 'dislike' or null
        //         return $review;
        //     });
        //     return $product;
        // });
        // }

        return response()->json([
            "success" => true,
            "message" => "Products fetched successfully",
            "data" => $products
        ], 200);
    }
    public function categories(){
        $category = Category::with(['products.options', 'products.images'])->get();
        if(!$category){
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ], 404);
        }
        return response()->json([
            "success" => true,
            "message" => "Category fetched successfully",
            "data" => $category
        ], 200);
    }
    public function byCategory(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ], 404);
        }
        // Fetch products along with relationships and the count of likes and dislikes for each review
        $products = Product::where('category_id', $category->id)->with([
            'category',
            'options',
            'images',
            'info',
            'reviews' => function ($query) {
                $query->withCount([
                    'interacts as likes_count' => function ($query) {
                        $query->where('interact', 'like');
                    },
                    'interacts as dislikes_count' => function ($query) {
                        $query->where('interact', 'dislike');
                    }
                ]);
            },
        ])
        ->get();

        // if($request->user()){
        // // Add user interaction data to each review
        // $products = $products->map(function ($product)  {
        //     $product->reviews = $product->reviews->map(function ($review)  {
        //         // Check if the user has interacted with the review
        //         $userInteracted = $review->interacts->where('user_id', $request->user()->id)->first();
        //         $review->user_interaction = $userInteracted ? $userInteracted->interact : null; // 'like' or 'dislike' or null
        //         return $review;
        //     });
        //     return $product;
        // });
        // }

        return response()->json([
            "success" => true,
            "message" => "Products fetched successfully",
            "data" => $products
        ], 200);
    }
    public function byCategoryPaginate(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found",
            ], 404);
        }
        // Fetch products along with relationships and the count of likes and dislikes for each review
        $products = Product::where('category_id', $category->id)->with([
            'category',
            'options',
            'images',
            'info',
            'reviews' => function ($query) {
                $query->withCount([
                    'interacts as likes_count' => function ($query) {
                        $query->where('interact', 'like');
                    },
                    'interacts as dislikes_count' => function ($query) {
                        $query->where('interact', 'dislike');
                    }
                ]);
            },
        ])
        ->paginate((int) $request->per_page ?: 10);

        // if($request->user()){
        // // Add user interaction data to each review
        // $products = $products->map(function ($product)  {
        //     $product->reviews = $product->reviews->map(function ($review)  {
        //         // Check if the user has interacted with the review
        //         $userInteracted = $review->interacts->where('user_id', $request->user()->id)->first();
        //         $review->user_interaction = $userInteracted ? $userInteracted->interact : null; // 'like' or 'dislike' or null
        //         return $review;
        //     });
        //     return $product;
        // });
        // }

        return response()->json([
            "success" => true,
            "message" => "Products fetched successfully",
            "data" => $products
        ], 200);
    }

    public function show($id){
        $product = Product::with([
            'category',
            'options',
            'images',
            'info',
            'reviews' => function ($query) {
                $query->withCount([
                    'interacts as likes_count' => function ($query) {
                        $query->where('interact', 'like');
                    },
                    'interacts as dislikes_count' => function ($query) {
                        $query->where('interact', 'dislike');
                    }
                ]);
            },
        ])->find($id);
        if(!$product){
            return response()->json([
                "success" => false,
                "message" => "Product not found",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Product fetched successfully",
            "data" => $product
        ], 200);
    }
}
