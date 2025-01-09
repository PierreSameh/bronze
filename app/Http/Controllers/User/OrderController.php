<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function placeOrder(Request $request){
        $validator = Validator::make($request->all(), [
            "address_id" => "required|exists:addresses,id",
            "promocode" => "nullable|exists:promocodes,promocode",
            "shipping_method" => "required|string",
            "payment_method" => "required|string"
        ]);
        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->get();

        if(count($cart) == 0){
            return response()->json([
                'message' => 'Cart is empty',
            ],  400);
        }
        $address = Address::find($request->address_id);
        $order = Order::create([
            'user_id'=> $user->id,
            'name' => $address->receipent_name,
            'phone' => $address->phone,
            'email' => $address->email,
            'address' => $address->address_one,
            'city' => $address->city,
            'country' => $address->country,
            'zipcode' => $address->zipcode,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'promocode_id' => $request->promocode ?? null,
        ]);

        foreach($cart as $item){
            $orderItem = $order->orderItems()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
            // Check if the item has cartOptions (assuming there's a relation or a method to retrieve them)
        if ($item->cartOptions()->exists()) {
            // Loop through the cart options and create corresponding order options
            foreach ($item->cartOptions as $cartOption) {
                $orderItem->orderOptions()->create([
                    'product_option_id' => $cartOption->product_option_id, // Assuming this is the relevant column
                ]);
            }
        }
        }
        // Delete all cart items for the user
        Cart::where('user_id', $user->id)->delete();

        // Return response
        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ], 200);
    }

    public function index()
{   
    $user = auth()->user();
    $orders = Order::where('user_id', $user->id)
    ->with(['orderItems.product'])->get();

    return response()->json([
        'success' => true,
        'data' => $orders,
    ]);
}

public function show($id)
{
    $order = Order::with(['orderItems.product'])->find($id);

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $order,
    ]);
}


}
