<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Display a listing of cities and their shipping methods.
     */
    public function index()
    {
        $cities = City::with('shippingMethods')->get();
        return response()->json($cities);
    }
    public function shippingMethods()
    {
        $shippingMethods = ShippingMethod::with('cities')->get();
        return response()->json($shippingMethods);
    }

    /**
     * Create a new city.
     */
    public function createCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255|unique:cities,city',
        ]);

        $city = City::create($request->only('city'));

        return response()->json(['message' => 'City created successfully', 'city' => $city], 201);
    }

    /**
     * Create a new shipping method.
     */
    public function createShippingMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shipping_methods,name',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $iconPath = null;
    
        // Save the icon if provided
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('shipping-icons', 'public');
        }
    
        $shippingMethod = ShippingMethod::create([
            'name' => $request->name,
            'icon' => $iconPath, // Save the icon path to the database
        ]);
    
        return response()->json([
            'message' => 'Shipping method created successfully',
            'shipping_method' => $shippingMethod,
        ], 201);
    }
    
    /**
     * Attach a shipping method to a city with shipping cost and time range.
     */
    public function attachShippingMethod(Request $request)
    {
        $request->validate([
            'city_id' => "required|exists:cities,id",
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'shipping_cost' => 'required|numeric|min:0',
            'time_range' => 'required|string|max:255',
        ]);

        $city = City::findOrFail($request->city_id);
        $city->shippingMethods()->attach($request->shipping_method_id, $request->only('shipping_cost', 'time_range'));

        return response()->json(['message' => 'Shipping method attached to city successfully']);
    }

    /**
     * Update the shipping details for a city and shipping method.
     */
    public function updateShippingDetails(Request $request, $cityId, $shippingMethodId)
    {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'time_range' => 'required|string|max:255',
        ]);

        $city = City::findOrFail($cityId);

        if (!$city->shippingMethods()->where('shipping_method_id', $shippingMethodId)->exists()) {
            return response()->json(['message' => 'Shipping method not attached to city'], 404);
        }

        $city->shippingMethods()->updateExistingPivot($shippingMethodId, $request->only('shipping_cost', 'time_range'));

        return response()->json(['message' => 'Shipping details updated successfully']);
    }

    /**
     * Remove a shipping method from a city.
     */
    public function detachShippingMethod($cityId, $shippingMethodId)
    {
        $city = City::findOrFail($cityId);

        if (!$city->shippingMethods()->where('shipping_method_id', $shippingMethodId)->exists()) {
            return response()->json(['message' => 'Shipping method not attached to city'], 404);
        }

        $city->shippingMethods()->detach($shippingMethodId);

        return response()->json(['message' => 'Shipping method detached from city successfully']);
    }

    /**
     * Delete a city.
     */
    public function deleteCity($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['message' => 'City deleted successfully']);
    }

    /**
     * Delete a shipping method.
     */
    public function deleteShippingMethod($id)
    {
        $shippingMethod = ShippingMethod::findOrFail($id);
        $shippingMethod->delete();

        return response()->json(['message' => 'Shipping method deleted successfully']);
    }
}
