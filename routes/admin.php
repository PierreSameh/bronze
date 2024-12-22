<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login']);

    // Route::middleware('auth:sanctum')->group(function () {
        //Banners
        Route::apiResource('banners', BannerController::class);
        //Categories
        Route::apiResource('categories', CategoryController::class);
        //Products
        Route::apiResource('products', ProductController::class);

    // });
});