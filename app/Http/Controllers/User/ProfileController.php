<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "old_password" => 'required',
            'password' => 'required|string|min:8|
            regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/u
            |confirmed',
            ], [
            "password.regex" => "Password must have Captial and small letters, and a special character",
            ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        $old_password = $request->old_password;

        if ($user) {
            if (!Hash::check($old_password, $user->password)) {
                return response()->json([
                    "success"=> false,
                    "message"=> "Incorrect Password",
                ], 422);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                "success"=> true,
                "message" => "Password Changed Successfully",
            ], 200);
        }
    }

    public function get(Request $reqeust){
        $user = $reqeust->user()->load('city');
        return response()->json([
            "success" => true,
            "data" => $user
        ], 200);
    }

    // Update User Profile (with photo upload)
    public function update(Request $request)
    {
        $user = $request->user(); // Get the currently authenticated user
    
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|numeric|digits_between:10,15',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'city_id' => 'nullable|exists:cities,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Image validation
        ]);
    
        // Concatenate first_name and last_name and update the name column
        $user->name = $request->first_name . ' ' . $request->last_name;
    
        // Handle the photo upload if present
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::exists($user->photo)) {
                Storage::delete($user->photo);
            }
    
            // Store the new photo
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $photoPath;
        }
    
        // Update the rest of the profile information
        $user->update($request->only(['email', 'phone', 'birthday', 'gender', 'city_id']));
    
        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }
    

    // Delete Account
    public function deleteAccount(Request $request)
    {
        $user = $request->user(); // Get the currently authenticated user

        // Optionally, delete the photo before removing the user (if it exists)
        if ($user->photo && Storage::exists($user->photo)) {
            Storage::delete($user->photo);
        }

        // Delete the user from the database
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.'
        ]);
    }
}
