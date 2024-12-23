<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    // Display a listing of the phone numbers
    public function index()
    {
        $phoneNumbers = PhoneNumber::all();
        return response()->json([
            'success' => true,
            'message' => 'Phone numbers retrieved successfully.',
            'data' => $phoneNumbers
        ], 200);
    }

    // Store a newly created phone number in storage
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|unique:phone_numbers,phone_number', // Example validation
        ]);

        $phoneNumber = PhoneNumber::create([
            "phone_number" => $request->input("phone_number"),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone number created successfully.',
            'data' => $phoneNumber
        ], 201); // 201 HTTP status code for created resource
    }

    public function destroy($id){
        $phoneNumber = PhoneNumber::find($id);
        if(!$phoneNumber){
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found.',
            ], 404);
        }
        $phoneNumber->delete();

        return response()->json([
            'success'=> true,
            'message'=> "Phone number deleted successfully",
        ], 200);
    }


}
