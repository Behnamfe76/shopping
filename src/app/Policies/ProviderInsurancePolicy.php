<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\ProviderInsurance;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderInsurancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any provider insurance records.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.view-any') ||
               $user->hasRole(['admin', 'manager', 'insurance-specialist']);
    }

    /**
     * Determine whether the user can view the provider insurance record.
     */
    public function view(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can view insurance records they created
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Users can view insurance records for providers they manage
        if ($user->hasRole(['admin', 'manager']) && $user->can('view', $providerInsurance->provider)) {
            return true;
        }

        // Insurance specialists can view all insurance records
        if ($user->hasRole('insurance-specialist')) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.view');
    }

    /**
     * Determine whether the user can create provider insurance records.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.create') ||
               $user->hasRole(['admin', 'manager', 'insurance-specialist']);
    }

    /**
     * Determine whether the user can update the provider insurance record.
     */
    public function update(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can update insurance records they created
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Admins and managers can update any insurance record
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        // Insurance specialists can update insurance records
        if ($user->hasRole('insurance-specialist')) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.update');
    }

    /**
     * Determine whether the user can delete the provider insurance record.
     */
    public function delete(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only admins can delete insurance records
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users can delete their own insurance records if they haven't been verified
        if ($user->id === $providerInsurance->created_by &&
            $providerInsurance->verification_status !== 'verified') {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.delete');
    }

    /**
     * Determine whether the user can restore the provider insurance record.
     */
    public function restore(User $user, ProviderInsurance $providerInsurance): bool
    {
        return $user->hasPermissionTo('provider-insurance.restore') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can permanently delete the provider insurance record.
     */
    public function forceDelete(User $user, ProviderInsurance $providerInsurance): bool
    {
        return $user->hasPermissionTo('provider-insurance.force-delete') ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can verify the provider insurance record.
     */
    public function verify(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only insurance specialists and managers can verify insurance
        if ($user->hasRole(['insurance-specialist', 'manager'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.verify');
    }

    /**
     * Determine whether the user can reject the provider insurance record.
     */
    public function reject(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only insurance specialists and managers can reject insurance
        if ($user->hasRole(['insurance-specialist', 'manager'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.reject');
    }

    /**
     * Determine whether the user can renew the provider insurance record.
     */
    public function renew(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can renew their own insurance records
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Admins, managers, and insurance specialists can renew any insurance record
        if ($user->hasRole(['admin', 'manager', 'insurance-specialist'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.renew');
    }

    /**
     * Determine whether the user can upload documents to the provider insurance record.
     */
    public function upload(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can upload documents to their own insurance records
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Admins, managers, and insurance specialists can upload documents
        if ($user->hasRole(['admin', 'manager', 'insurance-specialist'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.upload');
    }

    /**
     * Determine whether the user can search provider insurance records.
     */
    public function search(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.search') ||
               $user->hasRole(['admin', 'manager', 'insurance-specialist']);
    }

    /**
     * Determine whether the user can export provider insurance records.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.export') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can import provider insurance records.
     */
    public function import(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.import') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view analytics for provider insurance.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.view-analytics') ||
               $user->hasRole(['admin', 'manager', 'insurance-specialist']);
    }

    /**
     * Determine whether the user can perform bulk operations on provider insurance records.
     */
    public function bulkOperations(User $user): bool
    {
        return $user->hasPermissionTo('provider-insurance.bulk-operations') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage documents for the provider insurance record.
     */
    public function manageDocuments(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can manage documents for their own insurance records
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Admins, managers, and insurance specialists can manage documents
        if ($user->hasRole(['admin', 'manager', 'insurance-specialist'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.manage-documents');
    }

    /**
     * Determine whether the user can check compliance for the provider insurance record.
     */
    public function checkCompliance(User $user, ProviderInsurance $providerInsurance): bool
    {
        return $user->hasPermissionTo('provider-insurance.check-compliance') ||
               $user->hasRole(['admin', 'manager', 'insurance-specialist']);
    }

    /**
     * Determine whether the user can activate the provider insurance record.
     */
    public function activate(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only insurance specialists and managers can activate insurance
        if ($user->hasRole(['insurance-specialist', 'manager'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.activate');
    }

    /**
     * Determine whether the user can deactivate the provider insurance record.
     */
    public function deactivate(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only insurance specialists and managers can deactivate insurance
        if ($user->hasRole(['insurance-specialist', 'manager'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.deactivate');
    }

    /**
     * Determine whether the user can cancel the provider insurance record.
     */
    public function cancel(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Users can cancel their own insurance records
        if ($user->id === $providerInsurance->created_by) {
            return true;
        }

        // Admins, managers, and insurance specialists can cancel any insurance record
        if ($user->hasRole(['admin', 'manager', 'insurance-specialist'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.cancel');
    }

    /**
     * Determine whether the user can suspend the provider insurance record.
     */
    public function suspend(User $user, ProviderInsurance $providerInsurance): bool
    {
        // Only insurance specialists and managers can suspend insurance
        if ($user->hasRole(['insurance-specialist', 'manager'])) {
            return true;
        }

        return $user->hasPermissionTo('provider-insurance.suspend');
    }
}
