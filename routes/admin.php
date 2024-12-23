<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        //Banners
        Route::apiResource('banners', BannerController::class);
        //Categories
        Route::apiResource('categories', CategoryController::class);
        //Products
        Route::apiResource('products', ProductController::class);
        //Cities
        Route::get('/cities', [ShippingController::class, 'index']);
        Route::post('/cities', [ShippingController::class, 'createCity']);
        Route::delete('/cities/{id}', [ShippingController::class, 'deleteCity']);
        //Shipping Methods
        Route::get('/shipping-methods', [ShippingController::class, 'shippingMethods']);
        Route::post('/shipping-methods', [ShippingController::class, 'createShippingMethod']);
        Route::delete('/shipping-methods/{id}', [ShippingController::class, 'deleteShippingMethod']);
        //Shipping Details
        Route::post('/cities/attach-shipping-method', [ShippingController::class, 'attachShippingMethod']);
        Route::put('/cities/{cityId}/update-shipping-method/{shippingMethodId}', [ShippingController::class, 'updateShippingDetails']);
        Route::delete('/cities/{cityId}/detach-shipping-method/{shippingMethodId}', [ShippingController::class, 'detachShippingMethod']);
    });
});