<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class CustomerPreferencePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer preferences.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer-preferences.viewAny');
    }

    /**
     * Determine whether the user can view the customer preference.
     */
    public function view(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.view') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can create customer preferences.
     */
    public function create(User $user): bool
    {
        return $user->can('customer-preferences.create');
    }

    /**
     * Determine whether the user can update the customer preference.
     */
    public function update(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.update') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can delete the customer preference.
     */
    public function delete(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.delete') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can restore the customer preference.
     */
    public function restore(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer preference.
     */
    public function forceDelete(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.forceDelete');
    }

    /**
     * Determine whether the user can activate the customer preference.
     */
    public function activate(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.activate') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can deactivate the customer preference.
     */
    public function deactivate(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.deactivate') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can set preferences for a customer.
     */
    public function setPreference(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.setPreference') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can get preferences for a customer.
     */
    public function getPreference(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.getPreference') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can remove preferences for a customer.
     */
    public function removePreference(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.removePreference') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can reset preferences for a customer.
     */
    public function resetPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.resetPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can import preferences for a customer.
     */
    public function importPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.importPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can export preferences for a customer.
     */
    public function exportPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.exportPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can sync preferences for a customer.
     */
    public function syncPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.syncPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view analytics for customer preferences.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('customer-preferences.viewAnalytics');
    }

    /**
     * Determine whether the user can view customer preference analytics.
     */
    public function viewCustomerAnalytics(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.viewAnalytics') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can manage preference templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->can('customer-preferences.manageTemplates');
    }

    /**
     * Determine whether the user can apply preference templates.
     */
    public function applyTemplate(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.applyTemplate') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can clone preferences between customers.
     */
    public function clonePreferences(User $user, Customer $sourceCustomer, Customer $targetCustomer): bool
    {
        return $user->can('customer-preferences.clonePreferences') ||
               $user->id === $sourceCustomer->user_id ||
               $user->id === $targetCustomer->user_id;
    }

    /**
     * Determine whether the user can compare preferences between customers.
     */
    public function comparePreferences(User $user, Customer $customer1, Customer $customer2): bool
    {
        return $user->can('customer-preferences.comparePreferences') ||
               $user->id === $customer1->user_id ||
               $user->id === $customer2->user_id;
    }

    /**
     * Determine whether the user can backup customer preferences.
     */
    public function backupPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.backupPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can restore customer preferences.
     */
    public function restorePreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.restorePreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can migrate customer preferences.
     */
    public function migratePreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.migratePreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view preference audit trail.
     */
    public function viewAuditTrail(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.viewAuditTrail') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can view preference version history.
     */
    public function viewVersionHistory(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.viewVersionHistory') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can manage preference categories.
     */
    public function manageCategories(User $user): bool
    {
        return $user->can('customer-preferences.manageCategories');
    }

    /**
     * Determine whether the user can manage preference types.
     */
    public function manageTypes(User $user): bool
    {
        return $user->can('customer-preferences.manageTypes');
    }

    /**
     * Determine whether the user can view preference statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('customer-preferences.viewStatistics');
    }

    /**
     * Determine whether the user can view customer preference statistics.
     */
    public function viewCustomerStatistics(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.viewStatistics') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can search customer preferences.
     */
    public function searchPreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.searchPreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can bulk update customer preferences.
     */
    public function bulkUpdate(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.bulkUpdate') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can validate customer preferences.
     */
    public function validatePreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.validatePreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can manage default preferences.
     */
    public function manageDefaults(User $user): bool
    {
        return $user->can('customer-preferences.manageDefaults');
    }

    /**
     * Determine whether the user can initialize customer preferences.
     */
    public function initializePreferences(User $user, Customer $customer): bool
    {
        return $user->can('customer-preferences.initializePreferences') ||
               $user->id === $customer->user_id;
    }

    /**
     * Determine whether the user can manage preference metadata.
     */
    public function manageMetadata(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.manageMetadata') ||
               $user->id === $preference->customer->user_id;
    }

    /**
     * Determine whether the user can view preference metadata.
     */
    public function viewMetadata(User $user, CustomerPreference $preference): bool
    {
        return $user->can('customer-preferences.viewMetadata') ||
               $user->id === $preference->customer->user_id;
    }
}
