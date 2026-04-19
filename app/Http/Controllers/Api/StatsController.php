<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Dish;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    /**
     * Get admin stats for charts
     */
    public function adminStats()
    {
        // Revenue trend (last 7 days)
        $revenueTrend = \DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Orders by status
        $ordersByStatus = \DB::table('orders')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top 5 dishes
        $topDishes = \DB::table('dishes')
            ->selectRaw('dishes.name, COUNT(order_dishes.id) as times_ordered, SUM(order_dishes.quantity) as total_quantity')
            ->leftJoin('order_dishes', 'dishes.id', '=', 'order_dishes.dish_id')
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->toArray();

        // Cook performance (orders completed)
        $cookPerformance = \DB::table('users')
            ->selectRaw('users.name, COUNT(orders.id) as completed_orders, SUM(orders.total_price) as total_revenue')
            ->leftJoin('orders', 'users.id', '=', 'orders.cook_id')
            ->where('users.role', 'cook')
            ->where('orders.status', 'delivered')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('completed_orders')
            ->limit(5)
            ->get()
            ->toArray();

        // Reports by priority
        $reportsByPriority = \DB::table('reports')
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return response()->json([
            'revenueTrend' => $revenueTrend,
            'ordersByStatus' => $ordersByStatus,
            'topDishes' => $topDishes,
            'cookPerformance' => $cookPerformance,
            'reportsByPriority' => $reportsByPriority,
        ]);
    }

    /**
     * Get chef stats for charts
     */
    public function chefStats()
    {
        $cook = Auth::user();

        // Orders by hour (today)
        $ordersByHour = \DB::table('orders')
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('cook_id', $cook->id)
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();

        // Revenue trend (last 7 days)
        $revenueTrend = \DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->where('cook_id', $cook->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // Orders by status (today)
        $ordersByStatus = \DB::table('orders')
            ->selectRaw('status, COUNT(*) as count')
            ->where('cook_id', $cook->id)
            ->whereDate('created_at', today())
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top dishes (by sales)
        $topDishes = \DB::table('dishes')
            ->selectRaw('dishes.name, SUM(order_dishes.quantity) as quantity_sold, SUM(order_dishes.quantity * order_dishes.unit_price) as revenue')
            ->leftJoin('order_dishes', 'dishes.id', '=', 'order_dishes.dish_id')
            ->leftJoin('orders', 'order_dishes.order_id', '=', 'orders.id')
            ->where('dishes.cook_id', $cook->id)
            ->whereDate('orders.created_at', today())
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get()
            ->toArray();

        return response()->json([
            'ordersByHour' => $ordersByHour,
            'revenueTrend' => $revenueTrend,
            'ordersByStatus' => $ordersByStatus,
            'topDishes' => $topDishes,
        ]);
    }

    /**
     * Get client stats
     */
    public function clientStats()
    {
        $client = Auth::user();

        // Orders by status
        $ordersByStatus = \DB::table('orders')
            ->selectRaw('status, COUNT(*) as count')
            ->where('client_id', $client->id)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Spending trend (last 12 months)
        $spendingTrend = \DB::table('orders')
            ->selectRaw('DATE_TRUNC(\'month\', created_at) as month, SUM(total_price) as total')
            ->where('client_id', $client->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->subMonths(12), now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        // Total spent by cook
        $spentByCook = \DB::table('users')
            ->selectRaw('users.name, SUM(orders.total_price) as total')
            ->leftJoin('orders', 'orders.cook_id', '=', 'users.id')
            ->where('orders.client_id', $client->id)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();

        return response()->json([
            'ordersByStatus' => $ordersByStatus,
            'spendingTrend' => $spendingTrend,
            'spentByCook' => $spentByCook,
        ]);
    }
}
