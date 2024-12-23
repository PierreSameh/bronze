<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewInteract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    // Get all reviews for a specific product
    public function index($productId)
    {
        $reviews = Review::withCount([
            'interacts as likes_count' => function($query) {
                $query->where('interact', 'like');
            },
            'interacts as dislikes_count' => function($query) {
                $query->where('interact', 'dislike');
            }
        ])
        ->where('product_id', $productId)
        ->with(['user', 'interacts'])
        ->get();
        return response()->json([
            "success" => true,
            "message" => "Reviews fetched successfully",
            "data" => $reviews
        ], 200);
    }

    // Store a new review for a product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rate' => 'required|numeric|min:1|max:5', // Assuming the rate is between 1 and 5
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Create and save the new review
        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'rate' => $request->rate,
            'comment' => $request->comment ?? null,
        ]);

        // Update the product's rating
        $this->updateProductRating($request->product_id);

        return response()->json(['message' => 'Review added successfully', 'review' => $review], 201);
    }

    // Show a specific review
    public function show($id)
    {
        $review = Review::with(['user', 'product'])->withCount([
            'interacts as likes_count' => function($query) {
                $query->where('interact', 'like');
            },
            'interacts as dislikes_count' => function($query) {
                $query->where('interact', 'dislike');
            }
        ])->find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        return response()->json([
            'success'=> true,
            "message" => "Review fetched successfully",
            "data" => $review
        ] ,200);
    }

    // Update an existing review
    public function update(Request $request, $id)
    {
        $review = Review::find($id);
    
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }


        // Check if the review belongs to the authenticated user
        if ($review->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized to update this review'], 403);
        }

        // Update the review data
        $review->update([
            'rate' => $request->rate,
            'comment' => $request->comment,
        ]);

        // Update the product's rating
        $this->updateProductRating($review->product_id);

        return response()->json(['message' => 'Review updated successfully', 'review' => $review]);
    }

    // Delete a specific review
    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        // Check if the review belongs to the authenticated user
        if ($review->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized to delete this review'], 403);
        }

        $review->delete();

        // Update the product's rating
        $this->updateProductRating($review->product_id);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    public function interact(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'interact' => 'required|in:like,dislike',  // Only like or dislike are valid
        ]);

        $user = $request->user();

        // Find the review
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }

        // Check if the user has already interacted with this review
        $existingInteraction = ReviewInteract::where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->first();

        if ($existingInteraction) {
            // If the user already interacted, update their interaction
            $existingInteraction->update([
                'interact' => $request->interact,
            ]);
        } else {
            // If no existing interaction, create a new one
            ReviewInteract::create([
                'user_id' => $user->id,
                'review_id' => $review->id,
                'interact' => $request->interact,
            ]);
        }

        // Recalculate the likes and dislikes count after the interaction
        $review->loadCount([
            'interacts as likes_count' => function ($query) {
                $query->where('interact', 'like');
            },
            'interacts as dislikes_count' => function ($query) {
                $query->where('interact', 'dislike');
            },
        ]);

        return response()->json([
            'message' => 'Interaction recorded successfully.',
            'likes_count' => $review->likes_count,
            'dislikes_count' => $review->dislikes_count,
        ]);
    }

    // Update the product's rating based on all the reviews
    private function updateProductRating($productId)
    {
        // Get the average rating of the product
        $averageRating = Review::where('product_id', $productId)->avg('rate');
        
        // Update the product's rating in the product table
        $product = Product::find($productId);
        if ($product) {
            $product->update(['rate' => $averageRating]);
        }
    }

}
