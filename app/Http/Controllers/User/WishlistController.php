<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the wishlist items for a specific user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $wishlist = Wishlist::with(['product.category', 'product.images', 'product.options', 'product.info'])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Wishlist retrieved successfully',
            'data' => $wishlist,
        ]);
    }

    /**
     * Store a newly created wishlist item.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id;

        // Check if the product already exists in the user's wishlist
        if (Wishlist::where('user_id', $userId)->where('product_id', $validatedData['product_id'])->first()) {
            return response()->json([
                'message' => 'Product already exists in the wishlist',
            ], 409);
        }

        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $validatedData['product_id'],
        ]);

        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'data' => $wishlist,
        ], 201);
    }

    /**
     * Remove the specified wishlist item.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;

        $wishlist = Wishlist::where('id', $id)->where('user_id', $userId)->first();

        if (!$wishlist) {
            return response()->json([
                'message' => 'Wishlist item not found',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'message' => 'Wishlist item removed successfully',
        ], 200);
    }

    /**
     * Clear the entire wishlist for the user.
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        Wishlist::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Wishlist cleared successfully',
        ]);
    }
}

