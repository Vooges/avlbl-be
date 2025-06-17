<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemSizeController;
use App\Http\Controllers\UserItemSizeController;

Route::get('auth', [AuthController::class, 'redirectToAuth']);
Route::get('auth/callback', [AuthController::class, 'handleAuthCallback']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    
    Route::apiResource('items', ItemController::class);
    Route::apiResource('items/{item}/itemSizes', ItemSizeController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('items/{item}/userItemSizes', UserItemSizeController::class)->only(['store', 'destroy']);
});