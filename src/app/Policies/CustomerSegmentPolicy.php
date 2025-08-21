<?php

namespace App\Policies;

use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerSegmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer segments.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer-segments.viewAny');
    }

    /**
     * Determine whether the user can view the customer segment.
     */
    public function view(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.view');
    }

    /**
     * Determine whether the user can create customer segments.
     */
    public function create(User $user): bool
    {
        return $user->can('customer-segments.create');
    }

    /**
     * Determine whether the user can update the customer segment.
     */
    public function update(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.update');
    }

    /**
     * Determine whether the user can delete the customer segment.
     */
    public function delete(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.delete');
    }

    /**
     * Determine whether the user can restore the customer segment.
     */
    public function restore(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer segment.
     */
    public function forceDelete(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.forceDelete');
    }

    /**
     * Determine whether the user can activate the customer segment.
     */
    public function activate(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.activate');
    }

    /**
     * Determine whether the user can deactivate the customer segment.
     */
    public function deactivate(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.deactivate');
    }

    /**
     * Determine whether the user can make the customer segment automatic.
     */
    public function makeAutomatic(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.makeAutomatic');
    }

    /**
     * Determine whether the user can make the customer segment manual.
     */
    public function makeManual(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.makeManual');
    }

    /**
     * Determine whether the user can make the customer segment dynamic.
     */
    public function makeDynamic(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.makeDynamic');
    }

    /**
     * Determine whether the user can make the customer segment static.
     */
    public function makeStatic(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.makeStatic');
    }

    /**
     * Determine whether the user can set the priority of the customer segment.
     */
    public function setPriority(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.setPriority');
    }

    /**
     * Determine whether the user can calculate customers for the customer segment.
     */
    public function calculateCustomers(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.calculateCustomers');
    }

    /**
     * Determine whether the user can recalculate all customer segments.
     */
    public function recalculateAll(User $user): bool
    {
        return $user->can('customer-segments.recalculateAll');
    }

    /**
     * Determine whether the user can add customers to the customer segment.
     */
    public function addCustomer(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.addCustomer');
    }

    /**
     * Determine whether the user can remove customers from the customer segment.
     */
    public function removeCustomer(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.removeCustomer');
    }

    /**
     * Determine whether the user can update the criteria of the customer segment.
     */
    public function updateCriteria(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.updateCriteria');
    }

    /**
     * Determine whether the user can update the conditions of the customer segment.
     */
    public function updateConditions(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.updateConditions');
    }

    /**
     * Determine whether the user can validate criteria.
     */
    public function validateCriteria(User $user): bool
    {
        return $user->can('customer-segments.validateCriteria');
    }

    /**
     * Determine whether the user can validate conditions.
     */
    public function validateConditions(User $user): bool
    {
        return $user->can('customer-segments.validateConditions');
    }

    /**
     * Determine whether the user can view analytics for customer segments.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('customer-segments.viewAnalytics');
    }

    /**
     * Determine whether the user can export customer segment data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('customer-segments.exportData');
    }

    /**
     * Determine whether the user can import customer segment data.
     */
    public function importData(User $user): bool
    {
        return $user->can('customer-segments.importData');
    }

    /**
     * Determine whether the user can duplicate customer segments.
     */
    public function duplicate(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.duplicate');
    }

    /**
     * Determine whether the user can merge customer segments.
     */
    public function merge(User $user): bool
    {
        return $user->can('customer-segments.merge');
    }

    /**
     * Determine whether the user can split customer segments.
     */
    public function split(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->can('customer-segments.split');
    }
}
