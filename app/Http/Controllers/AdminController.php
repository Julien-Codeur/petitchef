<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get admin dashboard with platform statistics
     */
    public function dashboard(Request $request): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = now()->format('Y-m-d');

        return response()->json([
            'admin' => $admin,
            'statistics' => [
                'total_users' => User::count(),
                'total_cooks' => User::where('role', 'cook')->count(),
                'total_clients' => User::where('role', 'client')->count(),
                'total_deliveries' => User::where('role', 'delivery_person')->count(),
                'orders_today' => Order::whereDate('served_date', $today)->count(),
                'revenue_today' => Order::whereDate('served_date', $today)->sum('total_price'),
            ],
            'pending_verifications' => User::where('role', 'cook')
                ->where('verified', false)
                ->count(),
        ]);
    }

    /**
     * Get all users with filtering
     */
    public function getUsers(Request $request): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(15);

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Verify a cook profile
     */
    public function verifyCook(Request $request, User $user): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin() || $user->role !== 'cook') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->update(['verified' => true]);

        return response()->json([
            'message' => 'Cook verified successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get all orders
     */
    public function getOrders(Request $request): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Order::with('client', 'cook', 'deliveryAddress', 'orderItems');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('served_date', $request->date);
        }

        $orders = $query->orderBy('served_date', 'desc')->paginate(20);

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Get order details
     */
    public function getOrder(Request $request, Order $order): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load('client', 'cook', 'deliveryAddress', 'orderItems.dish'),
        ]);
    }

    /**
     * Moderate/flag an order or dish
     */
    public function flagContent(Request $request): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:order,dish,user',
            'id' => 'required|integer',
            'reason' => 'required|string|max:500',
        ]);

        // Log moderation action
        \Log::info('Admin moderation', [
            'admin_id' => $admin->id,
            'type' => $validated['type'],
            'id' => $validated['id'],
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Content flagged for review',
        ]);
    }

    /**
     * Get platform statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $startDate = $request->query('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('served_date', [$startDate, $endDate])
            ->with('cook')
            ->get();

        return response()->json([
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'statistics' => [
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->sum('total_price'),
                'average_order_value' => $orders->avg('total_price'),
                'orders_by_status' => $orders->groupBy('status')->map->count(),
                'orders_by_cook' => $orders->groupBy('cook_id')->map->count(),
            ],
        ]);
    }

    /**
     * Suspend a user account
     */
    public function suspendUser(Request $request, User $user): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user->update(['suspended_at' => now()]);

        \Log::warning('User suspended', [
            'admin_id' => $admin->id,
            'user_id' => $user->id,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'User suspended successfully',
        ]);
    }

    /**
     * Unsuspend a user account
     */
    public function unsuspendUser(Request $request, User $user): JsonResponse
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->update(['suspended_at' => null]);

        return response()->json([
            'message' => 'User unsuspended successfully',
        ]);
    }
}
