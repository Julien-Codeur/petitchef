<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Auth;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Stats API endpoints - only authenticated users
    Route::get('/stats/admin', [StatsController::class, 'adminStats']);
    Route::get('/stats/chef', [StatsController::class, 'chefStats']);
    Route::get('/stats/client', [StatsController::class, 'clientStats']);
});
