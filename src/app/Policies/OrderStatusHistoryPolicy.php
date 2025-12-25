<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class OrderStatusHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any status history records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('order-status-history.view.any');
    }

    /**
     * Determine whether the user can view the status history record.
     */
    public function view(User $user, OrderStatusHistory $history): bool
    {
        // Check if user can view any status history
        if ($user->can('order-status-history.view.any')) {
            return true;
        }

        // Check if user can view their own status history
        if ($user->can('order-status-history.view.own')) {
            return $history->changed_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create status history records.
     */
    public function create(User $user): bool
    {
        return $user->can('order-status-history.create');
    }

    /**
     * Determine whether the user can update the status history record.
     */
    public function update(User $user, OrderStatusHistory $history): bool
    {
        // Check if user can update any status history
        if ($user->can('order-status-history.update.any')) {
            return true;
        }

        // Check if user can update their own status history
        if ($user->can('order-status-history.update.own')) {
            return $history->changed_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the status history record.
     */
    public function delete(User $user, OrderStatusHistory $history): bool
    {
        // Check if user can delete any status history
        if ($user->can('order-status-history.delete.any')) {
            return true;
        }

        // Check if user can delete their own status history
        if ($user->can('order-status-history.delete.own')) {
            return $history->changed_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can search status history records.
     */
    public function search(User $user): bool
    {
        return $user->can('order-status-history.search');
    }

    /**
     * Determine whether the user can export status history records.
     */
    public function export(User $user): bool
    {
        return $user->can('order-status-history.export');
    }

    /**
     * Determine whether the user can import status history records.
     */
    public function import(User $user): bool
    {
        return $user->can('order-status-history.import');
    }

    /**
     * Determine whether the user can view order timeline.
     */
    public function viewTimeline(User $user, $orderId = null): bool
    {
        // Check if user can view any timeline
        if ($user->can('order-status-history.timeline.view.any')) {
            return true;
        }

        // Check if user can view their own timeline
        if ($user->can('order-status-history.timeline.view.own')) {
            // This would need additional logic to check if the order belongs to the user
            // For now, we'll return true if they have the permission
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        // Check if user can view any analytics
        if ($user->can('order-status-history.analytics.view.any')) {
            return true;
        }

        // Check if user can view their own analytics
        if ($user->can('order-status-history.analytics.view.own')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('order-status-history.reports.view');
    }

    /**
     * Determine whether the user can generate reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('order-status-history.reports.generate');
    }

    /**
     * Determine whether the user can validate status history entries.
     */
    public function validate(User $user): bool
    {
        return $user->can('order-status-history.validate');
    }
}
