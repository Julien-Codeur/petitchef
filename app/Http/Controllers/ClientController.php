<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliveryAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Get client dashboard with available menus
     */
    public function dashboard(Request $request): JsonResponse
    {
        $client = $request->user();
        
        if (!$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');

        return response()->json([
            'client' => $client,
            'active_dishes' => Dish::ofTheDay()
                ->where('is_active', true)
                ->where('available_qty', '>', 0)
                ->get(),
            'my_orders' => $client->clientOrders()
                ->whereDate('served_date', $today)
                ->with('orderItems.dish', 'cook')
                ->get(),
            'my_deliveries' => $client->clientOrders()
                ->whereDate('served_date', $today)
                ->get(),
        ]);
    }

    /**
     * Get available menus from all cooks
     */
    public function availableMenus(Request $request): JsonResponse
    {
        $today = now()->format('Y-m-d');

        $dishes = Dish::ofTheDay()
            ->where('is_active', true)
            ->where('available_qty', '>', 0)
            ->with('cook:id,name,phone')
            ->get();

        return response()->json([
            'dishes' => $dishes,
        ]);
    }

    /**
     * Create a new order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $client = $request->user();
        
        if (!$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'cook_id' => 'required|integer|exists:users,id',
            'delivery_address_id' => 'required|integer|exists:delivery_addresses,id',
            'pickup_time' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.dish_id' => 'required|integer|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.customization_notes' => 'nullable|string',
            'notes_client' => 'nullable|string',
            'payment_method' => 'required|in:cash,partner_tickets,card',
        ]);

        try {
            DB::beginTransaction();

            $dish = Dish::find($validated['items'][0]['dish_id']);
            $today = $dish->served_date;

            $order = $client->clientOrders()->create([
                'cook_id' => $validated['cook_id'],
                'delivery_address_id' => $validated['delivery_address_id'],
                'served_date' => $today,
                'pickup_time' => $validated['pickup_time'],
                'location_name' => DeliveryAddress::find($validated['delivery_address_id'])->name,
                'status' => 'pending',
                'notes_client' => $validated['notes_client'] ?? null,
                'payment_method' => $validated['payment_method'],
                'total_price' => 0, // Will be calculated
            ]);

            $totalPrice = 0;
            foreach ($validated['items'] as $item) {
                $dish = Dish::find($item['dish_id']);
                
                // Check if enough stock
                if ($dish->available_qty < $item['quantity']) {
                    throw new \Exception('Insufficient stock for ' . $dish->name);
                }

                // Decrement stock
                $dish->decrementStock($item['quantity']);

                // Create order item with snapshot price
                $order->orderItems()->create([
                    'dish_id' => $dish->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $dish->price,
                    'customization_notes' => $item['customization_notes'] ?? null,
                ]);

                $totalPrice += $dish->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('orderItems.dish'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get my orders
     */
    public function myOrders(Request $request): JsonResponse
    {
        $client = $request->user();
        
        if (!$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = $client->clientOrders()
            ->with('orderItems.dish', 'cook', 'deliveryAddress')
            ->orderBy('served_date', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Get order details
     */
    public function getOrder(Request $request, Order $order): JsonResponse
    {
        $client = $request->user();
        
        if ($order->client_id !== $client->id || !$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load('orderItems.dish', 'cook', 'deliveryAddress'),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Request $request, Order $order): JsonResponse
    {
        $client = $request->user();
        
        if ($order->client_id !== $client->id || !$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow cancellation if not yet ready
        if ($order->status === 'ready' || $order->status === 'delivered') {
            return response()->json([
                'message' => 'Cannot cancel order in this status',
            ], 400);
        }

        // Restore stock
        foreach ($order->orderItems as $item) {
            $item->dish->increment('available_qty', $item->quantity);
        }

        $order->markCancelled();

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }

    /**
     * Track order delivery
     */
    public function trackOrder(Request $request, Order $order): JsonResponse
    {
        $client = $request->user();
        
        if ($order->client_id !== $client->id || !$client->isClient()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $delivery = $order->cook->deliveries()
            ->where('served_date', $order->served_date)
            ->with('deliveryStops')
            ->first();

        return response()->json([
            'order' => $order,
            'delivery' => $delivery,
        ]);
    }
}
