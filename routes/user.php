<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('user')->group(function () {
    //AuthController
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, "sendForgetPasswordEmail"]);
    Route::post('/forgot-password-check-code', [AuthController::class, "forgetPasswordCheckCode"]);
    Route::post('/forgot-password-set', [AuthController::class,'forgetPassword']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        //AuthController
        Route::get('/ask-email-verfication-code', [AuthController::class, "askEmailCode"]);
        Route::post('/verify-email', [AuthController::class, "verifyEmail"]);
        Route::post('/change-password', [AuthController::class, "changePassword"]);
        Route::post('/logout', [AuthController::class, "logout"]);
        //Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
        Route::delete('/wishlist', [WishlistController::class, 'clear']);
        //Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);
    });
});