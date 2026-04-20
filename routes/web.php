<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes - accessible to all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========== ADMIN ROUTES ==========
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Cooks management
        Route::get('/cooks', [AdminController::class, 'listCooks'])->name('cooks.index');
        Route::get('/cooks/{user}', [AdminController::class, 'showCook'])->name('cooks.show');
        Route::post('/cooks/{user}/approve', [AdminController::class, 'approveCook'])->name('cooks.approve');
        Route::post('/cooks/{user}/reject', [AdminController::class, 'rejectCook'])->name('cooks.reject');
        Route::post('/cooks/{user}/suspend', [AdminController::class, 'suspendCook'])->name('cooks.suspend');
        
        // Orders management
        Route::get('/orders', [AdminController::class, 'listOrders'])->name('orders.index');
        Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
        
        // Dishes management
        Route::get('/dishes', [AdminController::class, 'listDishes'])->name('dishes.index');
        
        // Users management
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users.index');
        
        // Reports management
        Route::get('/reports', [ReportController::class, 'adminIndex'])->name('reports.index');
        Route::get('/reports/{report}', [ReportController::class, 'adminShow'])->name('reports.show');
        Route::patch('/reports/{report}', [ReportController::class, 'adminUpdate'])->name('reports.update');
        Route::patch('/reports/{report}/priority', [ReportController::class, 'updatePriority'])->name('reports.priority');
    });

    // ========== COOK ROUTES ==========
    Route::middleware('cook')->group(function () {
        // Dish routes - only cooks can create/manage dishes
        Route::resource('dishes', DishController::class);
        Route::post('dishes/close-service', [DishController::class, 'closeService'])->name('dishes.close-service');
        
        // Chef orders view
        Route::get('/chef/orders', function () {
            // Eager load relations to avoid N+1 queries
            $orders = auth()->user()->ordersAsCook()
                ->with(['client', 'items.dish'])
                ->orderBy('created_at', 'desc')
                ->get();
            return view('orders.chef-orders', compact('orders'));
        })->name('orders.chef');

        // Chef stats API
        Route::prefix('api/stats')->name('api.stats.')->group(function () {
            Route::get('/chef', [StatsController::class, 'chefStats'])->name('chef');
        });

        // Chef polling API
        Route::prefix('api/orders')->name('api.orders.')->group(function () {
            Route::get('/chef/polling', [OrderController::class, 'apiChefOrders'])->name('chef.polling');
            Route::get('/admin', [OrderController::class, 'apiAdminOrders'])->name('admin');
        });

        // Chef dishes API
        Route::prefix('api/dishes')->name('api.dishes.')->group(function () {
            Route::get('/my-dishes', [DishController::class, 'apiMyDishes'])->name('my-dishes');
        });
    });

    // ========== CLIENT ROUTES ==========
    Route::middleware('client')->group(function () {
        // Menu du jour for clients
        Route::get('/menu-du-jour', function () {
            // Eager load cook relation to avoid N+1 queries
            $dishes = \App\Models\Dish::with('cook')->today()->active()->get();
            // Get cooks who have dishes today with their relations
            $cooks = \App\Models\User::whereHas('dishes', function ($query) {
                $query->today()->active();
            })->with('dishes')->get();
            return view('dishes.menu-du-jour', compact('dishes', 'cooks'));
        })->name('dishes.menu-du-jour');

        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{dish}', [CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/{dish}', [CartController::class, 'remove'])->name('cart.remove');
        Route::patch('/cart/{dish}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        // Client orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // Client stats API
        Route::prefix('api/stats')->name('api.stats.')->group(function () {
            Route::get('/client', [StatsController::class, 'clientStats'])->name('client');
        });

        // Client polling API
        Route::prefix('api/orders')->name('api.orders.')->group(function () {
            Route::get('/client/polling', [OrderController::class, 'apiClientOrders'])->name('client.polling');
        });

        // Client dishes API
        Route::prefix('api/dishes')->name('api.dishes.')->group(function () {
            Route::get('/today', [DishController::class, 'apiTodayDishes'])->name('today');
        });
    });

    // ========== SHARED ROUTES (for all authenticated users) ==========
    // Reports routes - all users can file reports, but view is role-specific
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Admin stats API
    Route::prefix('api/stats')->name('api.stats.')->group(function () {
        Route::get('/admin', [StatsController::class, 'adminStats'])->middleware('admin')->name('admin');
    });
});

require __DIR__.'/auth.php';
