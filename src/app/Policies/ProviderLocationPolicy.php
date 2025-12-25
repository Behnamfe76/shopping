<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProviderLocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any provider locations.
     */
    public function viewAny(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.view-any')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view provider locations.');
    }

    /**
     * Determine whether the user can view the provider location.
     */
    public function view(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.view')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Check if user is associated with the provider
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this provider location.');
    }

    /**
     * Determine whether the user can create provider locations.
     */
    public function create(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.create')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Providers can create locations for themselves
        if ($user->hasRole('provider')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to create provider locations.');
    }

    /**
     * Determine whether the user can update the provider location.
     */
    public function update(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.update')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Providers can update their own locations
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update this provider location.');
    }

    /**
     * Determine whether the user can delete the provider location.
     */
    public function delete(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.delete')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        // Providers can delete their own locations (but not primary ones)
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            if ($providerLocation->is_primary) {
                return Response::deny('Cannot delete primary location.');
            }

            return Response::allow();
        }

        return Response::deny('You do not have permission to delete this provider location.');
    }

    /**
     * Determine whether the user can set the provider location as primary.
     */
    public function setPrimary(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.set-primary')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Providers can set their own locations as primary
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to set this location as primary.');
    }

    /**
     * Determine whether the user can geocode the provider location.
     */
    public function geocode(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.geocode')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager', 'provider'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to geocode locations.');
    }

    /**
     * Determine whether the user can search provider locations.
     */
    public function search(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.search')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager', 'provider'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to search provider locations.');
    }

    /**
     * Determine whether the user can export provider locations.
     */
    public function export(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.export')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to export provider locations.');
    }

    /**
     * Determine whether the user can import provider locations.
     */
    public function import(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.import')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to import provider locations.');
    }

    /**
     * Determine whether the user can view provider location analytics.
     */
    public function viewAnalytics(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.view-analytics')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view provider location analytics.');
    }

    /**
     * Determine whether the user can perform bulk operations on provider locations.
     */
    public function bulkOperations(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.bulk-operations')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform bulk operations on provider locations.');
    }

    /**
     * Determine whether the user can access provider location maps.
     */
    public function viewMap(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.view-map')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager', 'provider'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view provider location maps.');
    }

    /**
     * Determine whether the user can update coordinates for the provider location.
     */
    public function updateCoordinates(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.update-coordinates')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Providers can update coordinates for their own locations
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update coordinates for this provider location.');
    }

    /**
     * Determine whether the user can update operating hours for the provider location.
     */
    public function updateOperatingHours(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.update-operating-hours')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        // Providers can update operating hours for their own locations
        if ($user->hasRole('provider') && $user->provider_id === $providerLocation->provider_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update operating hours for this provider location.');
    }

    /**
     * Determine whether the user can restore the provider location.
     */
    public function restore(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.restore')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to restore this provider location.');
    }

    /**
     * Determine whether the user can permanently delete the provider location.
     */
    public function forceDelete(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.force-delete')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to permanently delete this provider location.');
    }

    /**
     * Determine whether the user can view provider location statistics.
     */
    public function viewStatistics(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.view-statistics')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager', 'provider-manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view provider location statistics.');
    }

    /**
     * Determine whether the user can manage provider location settings.
     */
    public function manageSettings(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.manage-settings')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage provider location settings.');
    }

    /**
     * Determine whether the user can approve provider location changes.
     */
    public function approveChanges(User $user, ProviderLocation $providerLocation): Response
    {
        if ($user->hasPermissionTo('provider-location.approve-changes')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to approve changes for this provider location.');
    }

    /**
     * Determine whether the user can audit provider location changes.
     */
    public function auditChanges(User $user): Response
    {
        if ($user->hasPermissionTo('provider-location.audit-changes')) {
            return Response::allow();
        }

        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to audit provider location changes.');
    }
}
