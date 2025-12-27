<?php

namespace Fereydooni\Shopping\app\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.viewAny');
    }

    /**
     * Determine whether the user can view the User.
     */
    public function view(User $user): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the User.
     */
    public function update(User $user): bool
    {
        return $user->can('users.update');
    }

    /**
     * Determine whether the user can delete the User.
     */
    public function delete(User $user): bool
    {
        return $user->can('users.delete');
    }

    /**
     * Determine whether the user can restore the User.
     */
    public function restore(User $user): bool
    {
        return $user->can('users.restore');
    }

    /**
     * Determine whether the user can permanently delete the User.
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('users.forceDelete');
    }

    /**
     * Determine whether the user can activate the User.
     */
    public function activate(User $user): bool
    {
        return $user->can('users.activate');
    }

    /**
     * Determine whether the user can deactivate the User.
     */
    public function deactivate(User $user): bool
    {
        return $user->can('users.deactivate');
    }

    /**
     * Determine whether the user can suspend the User.
     */
    public function suspend(User $user): bool
    {
        return $user->can('users.suspend');
    }

    /**
     * Determine whether the user can manage loyalty points.
     */
    public function manageLoyaltyPoints(User $user): bool
    {
        return $user->can('users.manageLoyaltyPoints');
    }

    /**
     * Determine whether the user can view User analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('users.viewAnalytics');
    }

    /**
     * Determine whether the user can export User data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('users.exportData');
    }

    /**
     * Determine whether the user can import User data.
     */
    public function importData(User $user): bool
    {
        return $user->can('users.importData');
    }

    /**
     * Determine whether the user can view User notes.
     */
    public function viewNotes(User $user): bool
    {
        return $user->can('users.viewNotes');
    }

    /**
     * Determine whether the user can add User notes.
     */
    public function addNotes(User $user): bool
    {
        return $user->can('users.addNotes');
    }

    /**
     * Determine whether the user can view User preferences.
     */
    public function viewPreferences(User $user): bool
    {
        return $user->can('users.viewPreferences');
    }

    /**
     * Determine whether the user can update User preferences.
     */
    public function updatePreferences(User $user): bool
    {
        return $user->can('users.updatePreferences');
    }

    /**
     * Determine whether the user can view User order history.
     */
    public function viewOrderHistory(User $user): bool
    {
        return $user->can('users.viewOrderHistory');
    }

    /**
     * Determine whether the user can view User addresses.
     */
    public function viewAddresses(User $user): bool
    {
        return $user->can('users.viewAddresses');
    }

    /**
     * Determine whether the user can view User reviews.
     */
    public function viewReviews(User $user): bool
    {
        return $user->can('users.viewReviews');
    }

    /**
     * Determine whether the user can view User wishlist.
     */
    public function viewWishlist(User $user): bool
    {
        return $user->can('users.viewWishlist');
    }

    /**
     * Determine whether the user can search users.
     */
    public function search(User $user): bool
    {
        return $user->can('users.search');
    }

    /**
     * Determine whether the user can view User statistics.
     */
    public function viewStats(User $user): bool
    {
        return $user->can('users.viewStats');
    }

    /**
     * Determine whether the user can manage User marketing preferences.
     */
    public function manageMarketing(User $user): bool
    {
        return $user->can('users.manageMarketing');
    }

    /**
     * Determine whether the user can view User lifetime value.
     */
    public function viewLifetimeValue(User $user): bool
    {
        return $user->can('users.viewLifetimeValue');
    }
}
