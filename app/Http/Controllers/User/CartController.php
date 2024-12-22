<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartOption;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the cart items for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $cartItems = Cart::with(['product', 'cartOptions.productOption'])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => $cartItems,
        ]);
    }

    /**
     * Store a newly created cart item along with its options.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'array',
            'options.*' => 'exists:product_options,id',
        ]);
    
        $product = Product::find($request->product_id);
    
        // Check if requested quantity is available
        if ($request->quantity > $product->quantity) {
            return response()->json(['message' => 'Insufficient product quantity available.'], 400);
        }
    
        // Check if the product already exists in the cart with the same options
        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('product_id', $request->product_id)
                        ->whereHas('product.options', function($query) use ($request) {
                            $query->whereIn('id', $request->options);
                        })
                        ->first();
    
        if ($cartItem) {
            // Update the quantity in the existing cart item
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $product->quantity) {
                return response()->json(['message' => 'Insufficient product quantity available for the updated cart item.'], 400);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create a new cart item
            $cartItem = Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
    
            // Add options if provided
            if ($request->has('options')) {
                foreach ($request->options as $optionId) {
                    CartOption::create([
                        'cart_id' => $cartItem->id,
                        'product_option_id' => $optionId,
                    ]);
                }
            }
        }
    
        return response()->json(['message' => 'Product added to cart successfully.'], 201);
    }
    

    /**
     * Update the quantity of the cart item.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    
        $cartItem = Cart::findOrFail($id);
        $user = $request->user();
        // Check if the cart item belongs to the authenticated user
        if ($cartItem->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
    
        $product = $cartItem->product;
    
        // Check if the requested quantity is available
        if ($request->quantity > $product->quantity) {
            return response()->json(['message' => 'Insufficient product quantity available.'], 400);
        }
    
        // Update the quantity of the existing cart item
        $cartItem->update(['quantity' => $request->quantity]);
    
        return response()->json(['message' => 'Cart item updated successfully.'], 200);
    }
    

    /**
     * Remove the specified cart item.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;

        $cart = Cart::where('id', $id)->where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart item not found',
            ], 404);
        }

        // Delete cart item and its options
        $cart->delete();

        return response()->json([
            'message' => 'Cart item removed successfully',
        ]);
    }

    /**
     * Clear all cart items for the authenticated user.
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ]);
    }
}


