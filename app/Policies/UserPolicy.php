<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only admins can access admin features
     */
    public function admin(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Users can view their own profile
     */
    public function viewProfile(User $user, User $profile): bool
    {
        return $user->id === $profile->id || $user->isAdmin();
    }
}
