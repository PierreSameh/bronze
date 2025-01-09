<?php

use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\SupportMessageController;
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
    //GetCities
    Route::get('/get-cities', [AddressController::class,'getCities']);
    //Categories
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/category/{id}/products', [ProductController::class, 'byCategory']);
    Route::get('/category/{id}/products/paginate', [ProductController::class, 'byCategoryPaginate']);
    //Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/paginate', [ProductController::class, 'paginate']);
    Route::get('/products/{id}', [ProductController::class,'show']);
    Route::get('/reviews/{id}', [ReviewController::class, 'show']);
    
    Route::middleware('auth:sanctum')->group(function () {
        //AuthController
        Route::get('/ask-email-verfication-code', [AuthController::class, "askEmailCode"]);
        Route::post('/verify-email', [AuthController::class, "verifyEmail"]);
        Route::post('/logout', [AuthController::class, "logout"]);
        //Profile
        Route::post('/change-password', [ProfileController::class, "changePassword"]);
        Route::get('/profile/get', [ProfileController::class, "get"]);
        Route::put('/profile/update', [ProfileController::class, 'update']);
        Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount']);
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
        //Addresses
        Route::apiResource('addresses', AddressController::class);
        //Reviews
        Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::put('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
        Route::post('/reviews/{id}/interact', [ReviewController::class, 'interact']);
        Route::delete('/reviews/{id}/interact', [ReviewController::class, 'removeInteract']);

        //Support Messages
        Route::post('/support-messages', [SupportMessageController::class, 'store']);

        //Order
        Route::post('/orders/place-order', [OrderController::class, 'placeOrder']);
        Route::post('/orders/all', [OrderController::class, 'index']);
        Route::post('/orders/get/{id}', [OrderController::class, 'show']);
    });
});