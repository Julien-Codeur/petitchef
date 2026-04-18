<?php

// API Routes exemple pour extension future
// À placer dans routes/api.php

use App\Http\Controllers\Api\DishApiController;
use App\Http\Controllers\Api\OrderApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    
    // Dishes API
    Route::apiResource('dishes', DishApiController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('dishes/{dish}/close-service', [DishApiController::class, 'closeService']);
    Route::get('dishes/today', [DishApiController::class, 'today']);
    
    // Orders API
    Route::apiResource('orders', OrderApiController::class);
    Route::patch('orders/{order}/status', [OrderApiController::class, 'updateStatus']);
    Route::post('orders/{order}/cancel', [OrderApiController::class, 'cancel']);
    
    // User profile
    Route::get('me', [UserApiController::class, 'me']);
    Route::put('profile', [UserApiController::class, 'update']);
});
