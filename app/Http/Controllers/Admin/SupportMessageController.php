<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportMessageController extends Controller
{
    public function index(){
        $messages = SupportMessage::all();
        return response()->json([
            "success" => true,
            "data"=> $messages
        ], 200);
    }
    public function paginate(Request $request){
        $messages = SupportMessage::paginate((int) $request->per_page ?: 10);
        return response()->json([
            "success" => true,
            "data"=> $messages
        ], 200);
    }

    public function show($id){
        $message = SupportMessage::find($id);
        if (!$message) {
            return response()->json([
                "success" => false,
                "message" => "Message not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "data"=> $message
        ], 200);
    }

    public function destroy($id){
        $message = SupportMessage::find($id);
        if (!$message) {
            return response()->json([
                "success" => false,
                "message" => "Message not found"
            ], 404);
        }
        $message->delete();
        return response()->json([
            "success" => true,
            "message" => "Message deleted successfully"
        ], 200);
    }
}
