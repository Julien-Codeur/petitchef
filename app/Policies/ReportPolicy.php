<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Determine if the user can create a report
     */
    public function create(User $user): bool
    {
        return !is_null($user->id); // Any authenticated user can create reports
    }

    /**
     * Determine if the user can view the report
     */
    public function view(User $user, Report $report): bool
    {
        // Reporter can view their own reports
        if ($report->reporter_id === $user->id) {
            return true;
        }

        // Admin can view all reports
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can view any report
     */
    public function viewAny(User $user): bool
    {
        // Only admins can see all reports
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the report
     */
    public function update(User $user, Report $report): bool
    {
        // Only admin can update reports
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the report
     */
    public function delete(User $user, Report $report): bool
    {
        // Only the reporter can delete their own report (if pending)
        if ($report->reporter_id === $user->id && $report->status === 'pending') {
            return true;
        }

        // Admin can delete any report
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
