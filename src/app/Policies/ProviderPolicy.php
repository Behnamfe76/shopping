<?php

namespace Fereydooni\Shopping\App\Policies;

use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class ProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any providers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('providers.viewAny');
    }

    /**
     * Determine whether the user can view the provider.
     */
    public function view(User $user, Provider $provider): bool
    {
        return $user->can('providers.view') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can create providers.
     */
    public function create(User $user): bool
    {
        return $user->can('providers.create');
    }

    /**
     * Determine whether the user can update the provider.
     */
    public function update(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can delete the provider.
     */
    public function delete(User $user, Provider $provider): bool
    {
        return $user->can('providers.delete');
    }

    /**
     * Determine whether the user can restore the provider.
     */
    public function restore(User $user, Provider $provider): bool
    {
        return $user->can('providers.restore');
    }

    /**
     * Determine whether the user can permanently delete the provider.
     */
    public function forceDelete(User $user, Provider $provider): bool
    {
        return $user->can('providers.forceDelete');
    }

    /**
     * Determine whether the user can activate the provider.
     */
    public function activate(User $user, Provider $provider): bool
    {
        return $user->can('providers.activate') &&
               $provider->status !== ProviderStatus::ACTIVE;
    }

    /**
     * Determine whether the user can deactivate the provider.
     */
    public function deactivate(User $user, Provider $provider): bool
    {
        return $user->can('providers.deactivate') &&
               $provider->status === ProviderStatus::ACTIVE;
    }

    /**
     * Determine whether the user can suspend the provider.
     */
    public function suspend(User $user, Provider $provider): bool
    {
        return $user->can('providers.suspend') &&
               $provider->status !== ProviderStatus::SUSPENDED;
    }

    /**
     * Determine whether the user can manage provider ratings.
     */
    public function manageRating(User $user, Provider $provider): bool
    {
        return $user->can('providers.manageRating');
    }

    /**
     * Determine whether the user can manage provider quality.
     */
    public function manageQuality(User $user, Provider $provider): bool
    {
        return $user->can('providers.manageQuality');
    }

    /**
     * Determine whether the user can manage provider financials.
     */
    public function manageFinancial(User $user, Provider $provider): bool
    {
        return $user->can('providers.manageFinancial');
    }

    /**
     * Determine whether the user can manage provider contracts.
     */
    public function manageContract(User $user, Provider $provider): bool
    {
        return $user->can('providers.manageContract');
    }

    /**
     * Determine whether the user can view provider analytics.
     */
    public function viewAnalytics(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewAnalytics') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can export provider data.
     */
    public function exportData(User $user, Provider $provider): bool
    {
        return $user->can('providers.exportData') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can import provider data.
     */
    public function importData(User $user): bool
    {
        return $user->can('providers.importData');
    }

    /**
     * Determine whether the user can view sensitive provider data.
     */
    public function viewSensitiveData(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewSensitiveData');
    }

    /**
     * Determine whether the user can update provider specializations.
     */
    public function updateSpecializations(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can update provider certifications.
     */
    public function updateCertifications(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can update provider insurance.
     */
    public function updateInsurance(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can add notes to the provider.
     */
    public function addNote(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can view provider notes.
     */
    public function viewNotes(User $user, Provider $provider): bool
    {
        return $user->can('providers.view') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can update provider location.
     */
    public function updateLocation(User $user, Provider $provider): bool
    {
        return $user->can('providers.update') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can view provider orders.
     */
    public function viewOrders(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewOrders') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can view provider products.
     */
    public function viewProducts(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewProducts') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can view provider invoices.
     */
    public function viewInvoices(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewInvoices') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can view provider payments.
     */
    public function viewPayments(User $user, Provider $provider): bool
    {
        return $user->can('providers.viewPayments') ||
               $user->id === $provider->user_id;
    }

    /**
     * Determine whether the user can evaluate provider performance.
     */
    public function evaluatePerformance(User $user, Provider $provider): bool
    {
        return $user->can('providers.evaluatePerformance');
    }

    /**
     * Determine whether the user can manage provider performance metrics.
     */
    public function managePerformanceMetrics(User $user, Provider $provider): bool
    {
        return $user->can('providers.managePerformanceMetrics');
    }

    /**
     * Determine whether the user can search providers.
     */
    public function search(User $user): bool
    {
        return $user->can('providers.search');
    }

    /**
     * Determine whether the user can view provider statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('providers.viewStatistics');
    }

    /**
     * Determine whether the user can view provider reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('providers.viewReports');
    }

    /**
     * Determine whether the user can manage provider relationships.
     */
    public function manageRelationships(User $user, Provider $provider): bool
    {
        return $user->can('providers.manageRelationships');
    }

    /**
     * Determine whether the user can approve provider applications.
     */
    public function approveApplication(User $user, Provider $provider): bool
    {
        return $user->can('providers.approveApplication') &&
               $provider->status === ProviderStatus::PENDING;
    }

    /**
     * Determine whether the user can reject provider applications.
     */
    public function rejectApplication(User $user, Provider $provider): bool
    {
        return $user->can('providers.rejectApplication') &&
               $provider->status === ProviderStatus::PENDING;
    }

    /**
     * Determine whether the user can blacklist the provider.
     */
    public function blacklist(User $user, Provider $provider): bool
    {
        return $user->can('providers.blacklist') &&
               $provider->status !== ProviderStatus::BLACKLISTED;
    }

    /**
     * Determine whether the user can remove provider from blacklist.
     */
    public function removeFromBlacklist(User $user, Provider $provider): bool
    {
        return $user->can('providers.removeFromBlacklist') &&
               $provider->status === ProviderStatus::BLACKLISTED;
    }
}
