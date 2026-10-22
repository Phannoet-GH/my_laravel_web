<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ReviewApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SE Shop REST API
|--------------------------------------------------------------------------
*/

// Public Auth Routes (Rate Limited to prevent credential stuffing)
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:auth');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');

// Public Product & Category Routes
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{idOrSlug}', [ProductApiController::class, 'show']);
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{idOrSlug}', [CategoryApiController::class, 'show']);

// Public Reviews (Rate Limited against review spam)
Route::get('/products/{productId}/reviews', [ReviewApiController::class, 'index']);
Route::post('/products/{productId}/reviews', [ReviewApiController::class, 'store'])->middleware('throttle:reviews');

// Public Order Lookup & Guest Checkout (Rate Limited against enumeration and carding/stock locking)
Route::post('/orders/lookup', [OrderApiController::class, 'lookup'])->middleware('throttle:lookup');
Route::post('/orders/guest-checkout', [OrderApiController::class, 'store'])->middleware('throttle:orders');

// Protected Routes (Sanctum Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Orders Management
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store'])->middleware('throttle:orders');
    Route::get('/orders/{orderNumber}', [OrderApiController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [OrderApiController::class, 'cancel']);


    // Admin API Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::post('/products', [ProductApiController::class, 'store']);
        Route::put('/products/{id}', [ProductApiController::class, 'update']);
        Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);

        Route::post('/categories', [CategoryApiController::class, 'store']);
        Route::put('/categories/{id}', [CategoryApiController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryApiController::class, 'destroy']);

        Route::patch('/orders/{id}/status', [OrderApiController::class, 'updateStatus']);

        // Admin User Management API Routes
        Route::get('/users', [\App\Http\Controllers\Api\UserApiController::class, 'index']);
        Route::post('/users', [\App\Http\Controllers\Api\UserApiController::class, 'store']);
        Route::get('/users/{id}', [\App\Http\Controllers\Api\UserApiController::class, 'show']);
        Route::put('/users/{id}', [\App\Http\Controllers\Api\UserApiController::class, 'update']);
        Route::patch('/users/{id}/password', [\App\Http\Controllers\Api\UserApiController::class, 'updatePassword']);
        Route::delete('/users/{id}', [\App\Http\Controllers\Api\UserApiController::class, 'destroy']);
    });
});
