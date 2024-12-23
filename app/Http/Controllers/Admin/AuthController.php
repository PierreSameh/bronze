<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
{
    // Validate input
    $request->validate([
        'identifier' => 'required', // Can be email or phone
        'password' => 'required',
    ]);

    $credentials = [
        filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone' => $request->identifier,
        'password' => $request->password,
    ];

    // Attempt login with either email or phone
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        if(!$user->type == 'admin') {
            return response()->json([
                'success'=> false,
                'message'=> "Invalid Credentials",
            ], 401);
        }
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'success'=> true,
            'message'=> "Logged In Successfully",
            'token' => $token,
            'user' => $user
        ], 200);
    }

    return response()->json([
        'success'=> false,
        'message'=> "Invalid Credentials",
    ], 401);
}

public function logout(Request $request) {
    $user = $request->user();

    if ($user) {
        if ($user->tokens())
            $user->tokens()->delete();
    }


    return response()->json([
        "success"=> true,
        "message"=> "Logged Out Successfully",
        ],200);
}
}
