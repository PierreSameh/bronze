<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
     // Get all addresses for the authenticated user
     public function index()
     {
         $addresses = auth()->user()->addresses; // Get addresses for the authenticated user
         return response()->json([
            "success" => true,
            "message" => "Addresses retrieved successfully",
            "data"=> $addresses
        ], 200);
     }
 
     // Store a new address for the authenticated user
     public function store(Request $request)
     {
        try{
         $validator = Validator::make($request->all(), [
             'receipent_name' => 'required|string|max:255',
             'City' => 'required|string|max:255',
             'address_one' => 'required|string|max:255',
             'zipcode' => 'required|integer',
             'phone' => 'required|string|max:15',
             'district' => 'nullable|string|max:255',
             'company' => 'nullable|string|max:255',
             'id_number' => 'nullable|string|max:255',
             'address_two' => 'nullable|string|max:255',
             'country' => 'nullable|string|max:255',
             'email' => 'nullable|email|max:255',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['message' => $validator->errors()->first()], 422);
         }
 
         // Create and save the new address
         $address = new Address($request->all());
         $address->user_id = auth()->user()->id; // Set the authenticated user ID
         $address->save();
 
         return response()->json(['message' => 'Address added successfully', 'address' => $address], 201);
        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => 'server error occured',
                "error" => $e->getMessage()
            ], 500);
        }
     }
 
     // Show the details of a specific address
     public function show($id)
     {
         $address = Address::find($id);
 
         if (!$address) {
             return response()->json(['message' => 'Address not found'], 404);
         }
 
         return response()->json($address);
     }
 
     // Update an existing address
     public function update(Request $request, $id)
     {
         $validator = Validator::make($request->all(), [
             'receipent_name' => 'sometimes|string|max:255',
             'City' => 'sometimes|string|max:255',
             'address_one' => 'sometimes|string|max:255',
             'zipcode' => 'sometimes|integer',
             'phone' => 'sometimes|string|max:15',
             'district' => 'nullable|string|max:255',
             'company' => 'nullable|string|max:255',
             'id_number' => 'nullable|string|max:255',
             'address_two' => 'nullable|string|max:255',
             'country' => 'nullable|string|max:255',
             'email' => 'nullable|email|max:255',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['message' => $validator->errors()->first()], 422);
         }
 
         $address = Address::find($id);
 
         if (!$address) {
             return response()->json(['message' => 'Address not found'], 404);
         }
 
         // Check if the address belongs to the authenticated user
         if ($address->user_id !== auth()->user()->id) {
             return response()->json(['message' => 'Unauthorized to update this address'], 403);
         }
 
         // Update the address with the new data
         $address->update($request->all());
 
         return response()->json(['message' => 'Address updated successfully', 'address' => $address]);
     }
 
     // Delete a specific address
     public function destroy($id)
     {
         $address = Address::find($id);
 
         if (!$address) {
             return response()->json(['message' => 'Address not found'], 404);
         }
 
         // Check if the address belongs to the authenticated user
         if ($address->user_id !== auth()->user()->id) {
             return response()->json(['message' => 'Unauthorized to delete this address'], 403);
         }
 
         $address->delete();
 
         return response()->json(['message' => 'Address deleted successfully']);
     }
}
