<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only cooks and admins can view multiple orders
        return $user->isCook() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Client can view their own order
        if ($user->isClient()) {
            return $order->client_id === $user->id;
        }

        // Cook can view orders they received
        if ($user->isCook()) {
            return $order->cook_id === $user->id;
        }

        // Admin can view any order
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only clients can create orders
        return $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Cook can update status of their orders
        if ($user->isCook()) {
            return $order->cook_id === $user->id;
        }

        // Client can only update note (if order is not yet being prepared)
        if ($user->isClient()) {
            return $order->client_id === $user->id && $order->status === 'received';
        }

        // Admin can update any order
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only clients can delete their own orders and only if received
        if ($user->isClient()) {
            return $order->client_id === $user->id && $order->status === 'received';
        }

        // Admin can delete any order
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Check if cook can change order status
     */
    public function changeStatus(User $user, Order $order): bool
    {
        return $user->isCook() && $order->cook_id === $user->id;
    }

    /**
     * Check if cook can close service for a day
     */
    public function closeService(User $user): bool
    {
        return $user->isCook();
    }
}
