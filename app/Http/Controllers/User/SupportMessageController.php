<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportMessageController extends Controller
{
    public function store(Request $request){
        try{
            $validated = $request->validate([
                "message" => "required|string|max:1000"
            ]);

            $user = $request->user();

            $message = SupportMessage::create([
                "user_id" => $user->id,
                "message" => $request->message
            ]);

            return response()->json([
                "success" => true,
                "message" => "Message sent successfully",
                "data" => $message
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => "server error occured",
                "error" => $e->getMessage()
            ],500);
        }
    }
}
