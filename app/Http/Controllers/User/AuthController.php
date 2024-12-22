<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required_unless:joined_with,3','email','unique:users,email'],
            'phone' => ['required_if:joined_with,1',
            'string','numeric','digits:11','unique:users,phone'],
            'password' => ['required_if:joined_with,1',
            'string','min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/u',
            'confirmed'],
        ], [
            "password.regex" => "Password must have Captial and small letters, and a special character",
        ]);

        if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors()->first(),
                ], 422);
        }

        $name = $request->first_name . " " . $request->last_name;
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'phone'=> $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'success'=> true,
            'message' => "You are Logged In",
            'token' => $token,
            'user' => $user
        ], 200);


        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "server error occured",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function askEmailCode(Request $request) {
        $user = $request->user();
        if ($user) {
            $code = rand(1000, 9999);

            $user->last_otp = Hash::make($code);
            $user->last_otp_expire = Carbon::now()->addMinutes(10)->timezone('Africa/Cairo');
            $user->save();

            $msg_title = "Here's your Authentication Code";
            $msg_content = "<h1>";
            $msg_content .= "Your Authentication code is<span style='color: blue'>" . $code . "</span>";
            $msg_content .= "</h1>";

            return response()->json([
                "success"=> true,
                "message" => "Code Sent Successfully",
                "user" => $user
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message"=> "User not found",
        ], 404);
    }

    public function verifyEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            "code" => ["required"],
        ], [
            "code.required" => "Enter Authentication Code",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        $code = $request->code;

        if ($user) {
            if (!Hash::check($code, $user->last_otp ? $user->last_otp : Hash::make(0000))) {
                return response()->json([
                    "success" => false,
                    "message"=> "Invalid Code",
                ], 422);
            } else {
                $timezone = 'Africa/Cairo'; // Replace with your specific timezone if different
                $verificationTime = new Carbon($user->last_otp_expire, $timezone);
                if ($verificationTime->isPast()) {
                    return response()->json([
                        "success" => false,
                        "message" => "Code Expired",
                    ], 422);
                } else {
                    $user->email_verified_at = now();
                    $user->save();
                    return response()->json([
                        "success"=> true,
                        "message" => "Email Verified Successfully",
                    ]);
                }
            }
        }
    }

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


    public function sendForgetPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "identifier" => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success'=> false,
                'message'=> $validator->errors()->first(),
            ], 422);
        }
    
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $request->identifier)->first();
    
        if ($user) {
            $code = rand(1000, 9999);
    
            $user->last_otp = Hash::make($code);
            $user->last_otp_expire = Carbon::now()->addMinutes(10)->timezone('Africa/Cairo');
            $user->save();
    
            $msg_title = "Here's your Authentication Reset Password Code";
            $msg_content = "<h1>";
            $msg_content .= "Your Authentication Reset Password Code is <span style='color: blue'>" . $code . "</span>";
            $msg_content .= "</h1>";
        
            return response()->json([
                "success"=> true,
                "message"=> "Code Sent Successfully",
                ],200);
        } else {
            return response()->json([
                "success"=> false,
                "message"=> "User not found",
            ], 404);
        }
    }
    

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "identifier" => ["required"],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/u',
                'confirmed'
            ],
        ], [
            "identifier.required" => "Please enter your email or phone number",
            "password.required" => "Enter your password",
            "password.min" => "Password must be at least 8 characters long",
            "password.regex" => "Password must contain letters, numbers, and symbols",
            "password.confirmed" => "Password and confirmation do not match",
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message"=> $validator->errors()->first(),
            ], 422);
        }
    
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $request->identifier)->first();
    
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
    
            return response()->json([
                'success'=> true,
                'message'=> "Password Changed Successfully",
            ], 200);
        } else {
            return response()->json([
                "success"=> false,
                "message"=> "User not found",
            ], 404);
        }
    }
    


    public function forgetPasswordCheckCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "identifier" => ["required"],
            "code" => ["required"],
        ], [
            "code.required" => "Enter the verification code",
            "identifier.required" => "Please enter your email or phone number",
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "success"=> false,
                "message"=> $validator->errors()->first(),
            ], 422);
        }
    
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $request->identifier)->first();
        $code = $request->code;
    
        if ($user) {
            if (!Hash::check($code, $user->last_otp ?? Hash::make(0000))) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Invalid Code',
                ], 422);
            } else {
                $timezone = 'Africa/Cairo';
                $verificationTime = new Carbon($user->last_otp_expire, $timezone);
                if ($verificationTime->isPast()) {
                    return response()->json([
                        'success'=> false,
                        'message'=> 'Code Expired',
                    ], 422);
                } else {
                    return response()->json([
                        "success" => true,
                        "message"=> "Code Accepted",
                    ], 200);
                }
            }
        } else {
            return response()->json([
                "success" => false,
                "message" => "User not found",
            ], 404);
        }
    }
    
  
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
