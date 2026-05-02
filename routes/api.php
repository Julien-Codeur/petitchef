<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CookController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // General auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);

    // Cook routes
    Route::prefix('cook')->middleware('role:cook')->group(function () {
        Route::get('/dashboard', [CookController::class, 'dashboard']);
        Route::post('/dishes', [CookController::class, 'storeDish']);
        Route::put('/dishes/{dish}', [CookController::class, 'updateDish']);
        Route::post('/mark-orders-ready', [CookController::class, 'markOrdersReady']);
        Route::get('/delivery-routes', [CookController::class, 'deliveryRoutes']);
        Route::put('/deliveries/{delivery}/status', [CookController::class, 'updateDeliveryStatus']);
        Route::get('/critical-alerts', [CookController::class, 'criticalAlerts']);
    });

    // Client routes
    Route::prefix('client')->middleware('role:client')->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard']);
        Route::get('/available-menus', [ClientController::class, 'availableMenus']);
        Route::post('/orders', [ClientController::class, 'createOrder']);
        Route::get('/my-orders', [ClientController::class, 'myOrders']);
        Route::get('/orders/{order}', [ClientController::class, 'getOrder']);
        Route::delete('/orders/{order}', [ClientController::class, 'cancelOrder']);
        Route::get('/orders/{order}/track', [ClientController::class, 'trackOrder']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyCook']);
        Route::get('/orders', [AdminController::class, 'getOrders']);
        Route::get('/orders/{order}', [AdminController::class, 'getOrder']);
        Route::post('/flag-content', [AdminController::class, 'flagContent']);
        Route::get('/statistics', [AdminController::class, 'statistics']);
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser']);
        Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser']);
    });
});
