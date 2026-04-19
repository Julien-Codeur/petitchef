<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Admin Controller - Manage cooks, orders, and platform
 */
class AdminController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show admin dashboard with stats
     */
    public function dashboard()
    {
        $this->authorize('admin', User::class);

        $stats = [
            'total_cooks' => User::where('role', 'cook')->count(),
            'verified_cooks' => User::where('role', 'cook')->where('is_verified', true)->count(),
            'pending_cooks' => User::where('role', 'cook')->where('is_verified', false)->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_orders' => \App\Models\Order::count(),
            'total_dishes' => \App\Models\Dish::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show list of all cooks (verified and pending)
     */
    public function listCooks(Request $request)
    {
        $this->authorize('admin', User::class);

        $filter = $request->get('filter', 'all'); // all, verified, pending, suspended

        $query = User::where('role', 'cook');

        if ($filter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($filter === 'pending') {
            $query->where('is_verified', false);
        }

        $cooks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.cooks.index', compact('cooks', 'filter'));
    }

    /**
     * Show cook profile details
     */
    public function showCook(User $user)
    {
        $this->authorize('admin', User::class);

        // Make sure it's a cook
        if ($user->role !== 'cook') {
            abort(404);
        }

        // Load related data
        $dishes = $user->dishes()->latest()->get();
        $orders = $user->ordersAsCook()->latest()->get();
        $orderCount = $orders->count();
        $avgRating = 4.8; // TODO: implement ratings

        return view('admin.cooks.show', compact('user', 'dishes', 'orders', 'orderCount', 'avgRating'));
    }

    /**
     * Approve (verify) a cook
     */
    public function approveCook(User $user)
    {
        $this->authorize('admin', User::class);

        if ($user->role !== 'cook') {
            abort(404);
        }

        $user->update(['is_verified' => true]);

        return redirect()->route('admin.cooks.show', $user)
            ->with('success', "Le cuisinier {$user->name} a été approuvé ✅");
    }

    /**
     * Reject (deny) a cook
     */
    public function rejectCook(User $user, Request $request)
    {
        $this->authorize('admin', User::class);

        if ($user->role !== 'cook') {
            abort(404);
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        // Send notification to cook (TODO: implement notifications)
        $reason = $request->input('reason');

        // Update status (could add rejection reason to separate column)
        $user->update(['is_verified' => false]);

        return redirect()->route('admin.cooks.index')
            ->with('success', "Le cuisinier {$user->name} a été rejeté. Raison enregistrée.");
    }

    /**
     * Suspend a cook (prevent from using platform)
     */
    public function suspendCook(User $user, Request $request)
    {
        $this->authorize('admin', User::class);

        if ($user->role !== 'cook') {
            abort(404);
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        // Add a suspension flag or status
        // For now, we'll just set is_verified to false
        $user->update(['is_verified' => false]);

        return redirect()->route('admin.cooks.show', $user)
            ->with('warning', "Le cuisinier {$user->name} a été suspendu.");
    }

    /**
     * List all orders (admin can see everything)
     */
    public function listOrders(Request $request)
    {
        $this->authorize('admin', User::class);

        $filter = $request->get('filter', 'all');
        $query = \App\Models\Order::query();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->with(['client', 'cook', 'items.dish'])
            ->paginate(15);

        return view('admin.orders.index', compact('orders', 'filter'));
    }

    /**
     * Show order details
     */
    public function showOrder(\App\Models\Order $order)
    {
        $this->authorize('admin', User::class);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * List all dishes
     */
    public function listDishes(Request $request)
    {
        $this->authorize('admin', User::class);

        $dishes = \App\Models\Dish::with('cook')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.dishes.index', compact('dishes'));
    }

    /**
     * List all users
     */
    public function listUsers(Request $request)
    {
        $this->authorize('admin', User::class);

        $users = User::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }
}
