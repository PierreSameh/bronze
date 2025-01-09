<?php

use App\Http\Controllers\Admin\PhoneNumberController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Settings
Route::get('/settings', [SettingsController::class, 'index']);
// Social Links
Route::get('/social-links', [SocialLinkController::class, 'index']);
// Phone Numbers
Route::get('/phone-numbers', [PhoneNumberController::class, 'index']);
