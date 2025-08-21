<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\Customer;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.viewAny');
    }

    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->can('customers.view') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->can('customers.update') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('customers.delete');
    }

    /**
     * Determine whether the user can restore the customer.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $user->can('customers.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->can('customers.forceDelete');
    }

    /**
     * Determine whether the user can activate the customer.
     */
    public function activate(User $user, Customer $customer): bool
    {
        return $user->can('customers.activate');
    }

    /**
     * Determine whether the user can deactivate the customer.
     */
    public function deactivate(User $user, Customer $customer): bool
    {
        return $user->can('customers.deactivate');
    }

    /**
     * Determine whether the user can suspend the customer.
     */
    public function suspend(User $user, Customer $customer): bool
    {
        return $user->can('customers.suspend');
    }

    /**
     * Determine whether the user can manage loyalty points.
     */
    public function manageLoyaltyPoints(User $user, Customer $customer): bool
    {
        return $user->can('customers.manageLoyaltyPoints');
    }

    /**
     * Determine whether the user can view customer analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('customers.viewAnalytics');
    }

    /**
     * Determine whether the user can export customer data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('customers.exportData');
    }

    /**
     * Determine whether the user can import customer data.
     */
    public function importData(User $user): bool
    {
        return $user->can('customers.importData');
    }

    /**
     * Determine whether the user can view customer notes.
     */
    public function viewNotes(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewNotes') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can add customer notes.
     */
    public function addNotes(User $user, Customer $customer): bool
    {
        return $user->can('customers.addNotes');
    }

    /**
     * Determine whether the user can view customer preferences.
     */
    public function viewPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewPreferences') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can update customer preferences.
     */
    public function updatePreferences(User $user, Customer $customer): bool
    {
        return $user->can('customers.updatePreferences') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view customer order history.
     */
    public function viewOrderHistory(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewOrderHistory') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view customer addresses.
     */
    public function viewAddresses(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewAddresses') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view customer reviews.
     */
    public function viewReviews(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewReviews') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view customer wishlist.
     */
    public function viewWishlist(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewWishlist') || $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can search customers.
     */
    public function search(User $user): bool
    {
        return $user->can('customers.search');
    }

    /**
     * Determine whether the user can view customer statistics.
     */
    public function viewStats(User $user): bool
    {
        return $user->can('customers.viewStats');
    }

    /**
     * Determine whether the user can manage customer marketing preferences.
     */
    public function manageMarketing(User $user, Customer $customer): bool
    {
        return $user->can('customers.manageMarketing');
    }

    /**
     * Determine whether the user can view customer lifetime value.
     */
    public function viewLifetimeValue(User $user, Customer $customer): bool
    {
        return $user->can('customers.viewLifetimeValue');
    }
}
