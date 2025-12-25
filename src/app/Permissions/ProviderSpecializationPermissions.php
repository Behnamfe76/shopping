<?php

namespace Fereydooni\Shopping\App\Permissions;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProviderSpecializationPermissions
{
    /**
     * Register all provider specialization permissions.
     */
    public static function register(): void
    {
        // View permissions
        Gate::define('provider-specialization.view', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.view');
        });

        Gate::define('provider-specialization.view-own', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.view-own');
        });

        Gate::define('provider-specialization.view-all', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.view-all');
        });

        // Create permissions
        Gate::define('provider-specialization.create', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.create');
        });

        Gate::define('provider-specialization.create-own', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.create-own');
        });

        // Edit permissions
        Gate::define('provider-specialization.edit', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.edit');
        });

        Gate::define('provider-specialization.edit-own', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.edit-own');
        });

        // Delete permissions
        Gate::define('provider-specialization.delete', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.delete');
        });

        // Verification permissions
        Gate::define('provider-specialization.verify', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.verify');
        });

        Gate::define('provider-specialization.reject', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.reject');
        });

        // Primary specialization permissions
        Gate::define('provider-specialization.set-primary', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.set-primary');
        });

        Gate::define('provider-specialization.remove-primary', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.remove-primary');
        });

        // Status management permissions
        Gate::define('provider-specialization.activate', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.activate');
        });

        Gate::define('provider-specialization.deactivate', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.deactivate');
        });

        // Management permissions
        Gate::define('provider-specialization.manage-all', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.manage-all');
        });

        // Export/Import permissions
        Gate::define('provider-specialization.export', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.export');
        });

        Gate::define('provider-specialization.import', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.import');
        });

        // Statistics permissions
        Gate::define('provider-specialization.statistics', function (User $user) {
            return $user->hasPermissionTo('provider-specialization.statistics');
        });

        // Model-specific permissions
        Gate::define('view', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canViewSpecialization($user, $specialization);
        });

        Gate::define('update', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canUpdateSpecialization($user, $specialization);
        });

        Gate::define('delete', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canDeleteSpecialization($user, $specialization);
        });

        Gate::define('verify', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canVerifySpecialization($user, $specialization);
        });

        Gate::define('reject', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canRejectSpecialization($user, $specialization);
        });

        Gate::define('set-primary', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canSetPrimarySpecialization($user, $specialization);
        });

        Gate::define('activate', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canActivateSpecialization($user, $specialization);
        });

        Gate::define('deactivate', ProviderSpecialization::class, function (User $user, ProviderSpecialization $specialization) {
            return static::canDeactivateSpecialization($user, $specialization);
        });
    }

    /**
     * Check if user can view a specialization.
     */
    public static function canViewSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with view-all permission can view all
        if ($user->can('provider-specialization.view-all')) {
            return true;
        }

        // Users with view-own permission can view their own specializations
        if ($user->can('provider-specialization.view-own')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        // Users with basic view permission can view verified specializations
        if ($user->can('provider-specialization.view')) {
            return $specialization->verification_status === 'verified';
        }

        return false;
    }

    /**
     * Check if user can update a specialization.
     */
    public static function canUpdateSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can update all
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with edit-own permission can edit their own specializations
        if ($user->can('provider-specialization.edit-own')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        // Users with basic edit permission can edit verified specializations
        if ($user->can('provider-specialization.edit')) {
            return $specialization->verification_status === 'verified';
        }

        return false;
    }

    /**
     * Check if user can delete a specialization.
     */
    public static function canDeleteSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can delete all
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with delete permission can delete their own specializations
        if ($user->can('provider-specialization.delete')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        return false;
    }

    /**
     * Check if user can verify a specialization.
     */
    public static function canVerifySpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can verify all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with verification permission can verify specializations
        if ($user->can('provider-specialization.verify')) {
            return in_array($specialization->verification_status, ['unverified', 'pending']);
        }

        return false;
    }

    /**
     * Check if user can reject a specialization.
     */
    public static function canRejectSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can reject all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with rejection permission can reject specializations
        if ($user->can('provider-specialization.reject')) {
            return in_array($specialization->verification_status, ['unverified', 'pending']);
        }

        return false;
    }

    /**
     * Check if user can set a specialization as primary.
     */
    public static function canSetPrimarySpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can set any specialization as primary
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can set any specialization as primary
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with set-primary permission can set their own specializations as primary
        if ($user->can('provider-specialization.set-primary')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        return false;
    }

    /**
     * Check if user can activate a specialization.
     */
    public static function canActivateSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can activate all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can activate all
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with activate permission can activate their own specializations
        if ($user->can('provider-specialization.activate')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        return false;
    }

    /**
     * Check if user can deactivate a specialization.
     */
    public static function canDeactivateSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Super admins can deactivate all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can deactivate all
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with deactivate permission can deactivate their own specializations
        if ($user->can('provider-specialization.deactivate')) {
            return static::isOwnSpecialization($user, $specialization);
        }

        return false;
    }

    /**
     * Check if user can create specializations for a provider.
     */
    public static function canCreateSpecializationForProvider(User $user, Provider $provider): bool
    {
        // Super admins can create specializations for any provider
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can create specializations for any provider
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with create-own permission can create specializations for their own providers
        if ($user->can('provider-specialization.create-own')) {
            return static::isOwnProvider($user, $provider);
        }

        // Users with basic create permission can create specializations
        return $user->can('provider-specialization.create');
    }

    /**
     * Check if user can view specializations for a provider.
     */
    public static function canViewProviderSpecializations(User $user, Provider $provider): bool
    {
        // Super admins can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with view-all permission can view all
        if ($user->can('provider-specialization.view-all')) {
            return true;
        }

        // Users with view-own permission can view their own provider specializations
        if ($user->can('provider-specialization.view-own')) {
            return static::isOwnProvider($user, $provider);
        }

        // Users with basic view permission can view verified specializations
        return $user->can('provider-specialization.view');
    }

    /**
     * Check if user can manage specializations for a provider.
     */
    public static function canManageProviderSpecializations(User $user, Provider $provider): bool
    {
        // Super admins can manage all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users with manage-all permission can manage all
        if ($user->can('provider-specialization.manage-all')) {
            return true;
        }

        // Users with manage permission can manage their own provider specializations
        if ($user->can('provider-specialization.manage')) {
            return static::isOwnProvider($user, $provider);
        }

        return false;
    }

    /**
     * Check if specialization belongs to user's provider.
     */
    protected static function isOwnSpecialization(User $user, ProviderSpecialization $specialization): bool
    {
        // Check if user is the provider owner
        if ($specialization->provider && $specialization->provider->user_id === $user->id) {
            return true;
        }

        // Check if user is the provider
        if ($specialization->provider_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if provider belongs to user.
     */
    protected static function isOwnProvider(User $user, Provider $provider): bool
    {
        // Check if user is the provider owner
        if ($provider->user_id === $user->id) {
            return true;
        }

        // Check if user is the provider
        if ($provider->id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Get all available permissions.
     */
    public static function getAllPermissions(): array
    {
        return [
            'provider-specialization.view',
            'provider-specialization.create',
            'provider-specialization.edit',
            'provider-specialization.delete',
            'provider-specialization.verify',
            'provider-specialization.set-primary',
            'provider-specialization.activate',
            'provider-specialization.deactivate',
            'provider-specialization.view-own',
            'provider-specialization.create-own',
            'provider-specialization.edit-own',
            'provider-specialization.view-all',
            'provider-specialization.manage-all',
            'provider-specialization.export',
            'provider-specialization.import',
            'provider-specialization.statistics',
        ];
    }

    /**
     * Get permissions for a specific role.
     */
    public static function getPermissionsForRole(string $role): array
    {
        $permissions = [];

        switch ($role) {
            case 'super-admin':
                $permissions = static::getAllPermissions();
                break;

            case 'admin':
                $permissions = [
                    'provider-specialization.view',
                    'provider-specialization.create',
                    'provider-specialization.edit',
                    'provider-specialization.delete',
                    'provider-specialization.verify',
                    'provider-specialization.set-primary',
                    'provider-specialization.activate',
                    'provider-specialization.deactivate',
                    'provider-specialization.view-all',
                    'provider-specialization.manage-all',
                    'provider-specialization.export',
                    'provider-specialization.import',
                    'provider-specialization.statistics',
                ];
                break;

            case 'provider':
                $permissions = [
                    'provider-specialization.view-own',
                    'provider-specialization.create-own',
                    'provider-specialization.edit-own',
                    'provider-specialization.delete',
                    'provider-specialization.set-primary',
                    'provider-specialization.activate',
                    'provider-specialization.deactivate',
                ];
                break;

            case 'verifier':
                $permissions = [
                    'provider-specialization.view',
                    'provider-specialization.verify',
                    'provider-specialization.reject',
                    'provider-specialization.statistics',
                ];
                break;

            case 'user':
                $permissions = [
                    'provider-specialization.view',
                ];
                break;
        }

        return $permissions;
    }

    /**
     * Check if user has any specialization-related permissions.
     */
    public static function hasAnySpecializationPermission(User $user): bool
    {
        return $user->hasAnyPermission(static::getAllPermissions());
    }

    /**
     * Check if user can perform any specialization management actions.
     */
    public static function canManageSpecializations(User $user): bool
    {
        return $user->hasAnyPermission([
            'provider-specialization.manage-all',
            'provider-specialization.create',
            'provider-specialization.edit',
            'provider-specialization.delete',
            'provider-specialization.verify',
            'provider-specialization.set-primary',
            'provider-specialization.activate',
            'provider-specialization.deactivate',
        ]);
    }

    /**
     * Check if user can view specialization statistics.
     */
    public static function canViewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('provider-specialization.statistics');
    }

    /**
     * Check if user can export specialization data.
     */
    public static function canExport(User $user): bool
    {
        return $user->hasPermissionTo('provider-specialization.export');
    }

    /**
     * Check if user can import specialization data.
     */
    public static function canImport(User $user): bool
    {
        return $user->hasPermissionTo('provider-specialization.import');
    }
}
