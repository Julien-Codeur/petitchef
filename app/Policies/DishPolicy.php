<?php

namespace App\Policies;

use App\Models\Dish;
use App\Models\User;

class DishPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Clients can view dishes, cooks can view their own
        return $user->isClient() || $user->isCook();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dish $dish): bool
    {
        // Clients can view active dishes, cooks can view their own
        if ($user->isClient()) {
            return $dish->is_active;
        }
        
        if ($user->isCook()) {
            return $user->id === $dish->cook_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only verified cooks can create dishes
        return $user->isCook() && $user->is_verified;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dish $dish): bool
    {
        // Only the cook who created the dish can update it
        return $user->isCook() && $user->id === $dish->cook_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dish $dish): bool
    {
        // Only the cook who created the dish can delete it
        return $user->isCook() && $user->id === $dish->cook_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dish $dish): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dish $dish): bool
    {
        return false;
    }

    /**
     * Check if cook can close service
     */
    public function closeService(User $user): bool
    {
        return $user->isCook();
    }
}
