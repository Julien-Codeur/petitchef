<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookController extends Controller
{
    /**
     * Get cook dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $cook = $request->user();
        
        if (!$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');

        return response()->json([
            'cook' => $cook,
            'dishes_today' => $cook->dishes()
                ->where('served_date', $today)
                ->get(),
            'orders_today' => $cook->orders()
                ->whereDate('served_date', $today)
                ->with('orderItems.dish', 'client')
                ->get(),
            'deliveries_today' => $cook->deliveries()
                ->where('served_date', $today)
                ->with('deliveryStops')
                ->get(),
            'critical_dishes' => $cook->dishes()
                ->where('served_date', $today)
                ->critical()
                ->get(),
        ]);
    }

    /**
     * Create a new dish
     */
    public function storeDish(Request $request): JsonResponse
    {
        $cook = $request->user();
        
        if (!$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'initial_qty' => 'required|integer|min:1',
            'served_date' => 'required|date',
            'critical_threshold' => 'required|integer|min:0',
        ]);

        $dish = $cook->dishes()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'initial_qty' => $validated['initial_qty'],
            'available_qty' => $validated['initial_qty'],
            'served_date' => $validated['served_date'],
            'critical_threshold' => $validated['critical_threshold'],
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Dish created successfully',
            'dish' => $dish,
        ], 201);
    }

    /**
     * Update dish availability
     */
    public function updateDish(Request $request, Dish $dish): JsonResponse
    {
        $cook = $request->user();
        
        if ($dish->cook_id !== $cook->id || !$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'available_qty' => 'integer|min:0',
            'is_active' => 'boolean',
            'critical_threshold' => 'integer|min:0',
        ]);

        $dish->update($validated);

        return response()->json([
            'message' => 'Dish updated successfully',
            'dish' => $dish,
        ]);
    }

    /**
     * Mark orders as ready for delivery
     */
    public function markOrdersReady(Request $request): JsonResponse
    {
        $cook = $request->user();
        
        if (!$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer',
        ]);

        $orders = Order::whereIn('id', $validated['order_ids'])
            ->where('cook_id', $cook->id)
            ->get();

        foreach ($orders as $order) {
            $order->markReady();
        }

        return response()->json([
            'message' => 'Orders marked as ready',
            'orders' => $orders,
        ]);
    }

    /**
     * Get delivery routes for today
     */
    public function deliveryRoutes(Request $request): JsonResponse
    {
        $cook = $request->user();
        
        if (!$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');

        $deliveries = $cook->deliveries()
            ->where('served_date', $today)
            ->with('deliveryStops.deliveryAddress')
            ->get();

        return response()->json([
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus(Request $request, Delivery $delivery): JsonResponse
    {
        $cook = $request->user();
        
        if ($delivery->cook_id !== $cook->id || !$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:en_préparation,prête_à_partir,en_route,complète',
        ]);

        $delivery->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Delivery status updated',
            'delivery' => $delivery,
        ]);
    }

    /**
     * Get critical stock alerts
     */
    public function criticalAlerts(Request $request): JsonResponse
    {
        $cook = $request->user();
        
        if (!$cook->isCook()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');

        $criticalDishes = $cook->dishes()
            ->where('served_date', $today)
            ->critical()
            ->get();

        return response()->json([
            'critical_dishes' => $criticalDishes,
            'count' => $criticalDishes->count(),
        ]);
    }
}
