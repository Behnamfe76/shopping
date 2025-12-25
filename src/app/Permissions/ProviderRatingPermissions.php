<?php

namespace Fereydooni\Shopping\App\Permissions;

use App\Models\ProviderRating;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProviderRatingPermissions
{
    /**
     * Register all provider rating permissions
     */
    public static function register(): void
    {
        // View permissions
        Gate::define('provider-rating.view', function (User $user) {
            return $user->hasPermissionTo('provider-rating.view');
        });

        Gate::define('provider-rating.view-own', function (User $user, ProviderRating $rating) {
            return $user->id === $rating->user_id;
        });

        Gate::define('provider-rating.view-all', function (User $user) {
            return $user->hasPermissionTo('provider-rating.view-all');
        });

        // Create permissions
        Gate::define('provider-rating.create', function (User $user) {
            return $user->hasPermissionTo('provider-rating.create');
        });

        Gate::define('provider-rating.create-own', function (User $user) {
            return $user->hasPermissionTo('provider-rating.create-own');
        });

        // Edit permissions
        Gate::define('provider-rating.edit', function (User $user, ProviderRating $rating) {
            return $user->hasPermissionTo('provider-rating.edit');
        });

        Gate::define('provider-rating.edit-own', function (User $user, ProviderRating $rating) {
            return $user->id === $rating->user_id && $rating->status === 'pending';
        });

        // Delete permissions
        Gate::define('provider-rating.delete', function (User $user, ProviderRating $rating) {
            return $user->hasPermissionTo('provider-rating.delete');
        });

        // Moderation permissions
        Gate::define('provider-rating.moderate', function (User $user) {
            return $user->hasPermissionTo('provider-rating.moderate');
        });

        Gate::define('provider-rating.verify', function (User $user) {
            return $user->hasPermissionTo('provider-rating.verify');
        });

        Gate::define('provider-rating.flag', function (User $user) {
            return $user->hasPermissionTo('provider-rating.flag');
        });

        // Management permissions
        Gate::define('provider-rating.manage-all', function (User $user) {
            return $user->hasPermissionTo('provider-rating.manage-all');
        });

        // Export/Import permissions
        Gate::define('provider-rating.export', function (User $user) {
            return $user->hasPermissionTo('provider-rating.export');
        });

        Gate::define('provider-rating.import', function (User $user) {
            return $user->hasPermissionTo('provider-rating.import');
        });

        // Statistics permissions
        Gate::define('provider-rating.statistics', function (User $user) {
            return $user->hasPermissionTo('provider-rating.statistics');
        });

        // Voting permissions
        Gate::define('provider-rating.vote', function (User $user) {
            return $user->hasPermissionTo('provider-rating.vote');
        });
    }

    /**
     * Get all provider rating permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'provider-rating.view',
            'provider-rating.create',
            'provider-rating.edit',
            'provider-rating.delete',
            'provider-rating.moderate',
            'provider-rating.verify',
            'provider-rating.flag',
            'provider-rating.view-own',
            'provider-rating.create-own',
            'provider-rating.edit-own',
            'provider-rating.view-all',
            'provider-rating.manage-all',
            'provider-rating.export',
            'provider-rating.import',
            'provider-rating.statistics',
            'provider-rating.vote',
        ];
    }

    /**
     * Get basic user permissions
     */
    public static function getBasicUserPermissions(): array
    {
        return [
            'provider-rating.view',
            'provider-rating.create-own',
            'provider-rating.edit-own',
            'provider-rating.vote',
        ];
    }

    /**
     * Get moderator permissions
     */
    public static function getModeratorPermissions(): array
    {
        return [
            'provider-rating.view',
            'provider-rating.view-all',
            'provider-rating.moderate',
            'provider-rating.verify',
            'provider-rating.flag',
            'provider-rating.statistics',
        ];
    }

    /**
     * Get admin permissions
     */
    public static function getAdminPermissions(): array
    {
        return [
            'provider-rating.view',
            'provider-rating.create',
            'provider-rating.edit',
            'provider-rating.delete',
            'provider-rating.moderate',
            'provider-rating.verify',
            'provider-rating.flag',
            'provider-rating.view-own',
            'provider-rating.create-own',
            'provider-rating.edit-own',
            'provider-rating.view-all',
            'provider-rating.manage-all',
            'provider-rating.export',
            'provider-rating.import',
            'provider-rating.statistics',
            'provider-rating.vote',
        ];
    }

    /**
     * Check if user can view a specific rating
     */
    public static function canView(User $user, ProviderRating $rating): bool
    {
        // Users can always view their own ratings
        if ($user->id === $rating->user_id) {
            return true;
        }

        // Users can view approved ratings
        if ($rating->status === 'approved') {
            return true;
        }

        // Moderators and admins can view all ratings
        if ($user->hasPermissionTo('provider-rating.view-all')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can create a rating
     */
    public static function canCreate(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.create') ||
               $user->hasPermissionTo('provider-rating.create-own');
    }

    /**
     * Check if user can edit a specific rating
     */
    public static function canEdit(User $user, ProviderRating $rating): bool
    {
        // Users can edit their own pending ratings
        if ($user->id === $rating->user_id && $rating->status === 'pending') {
            return $user->hasPermissionTo('provider-rating.edit-own');
        }

        // Admins can edit any rating
        if ($user->hasPermissionTo('provider-rating.edit')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can delete a specific rating
     */
    public static function canDelete(User $user, ProviderRating $rating): bool
    {
        // Users can delete their own pending ratings
        if ($user->id === $rating->user_id && $rating->status === 'pending') {
            return $user->hasPermissionTo('provider-rating.delete');
        }

        // Admins can delete any rating
        if ($user->hasPermissionTo('provider-rating.delete')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can moderate ratings
     */
    public static function canModerate(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.moderate');
    }

    /**
     * Check if user can verify ratings
     */
    public static function canVerify(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.verify');
    }

    /**
     * Check if user can flag ratings
     */
    public static function canFlag(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.flag');
    }

    /**
     * Check if user can vote on ratings
     */
    public static function canVote(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.vote');
    }

    /**
     * Check if user can export rating data
     */
    public static function canExport(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.export');
    }

    /**
     * Check if user can import rating data
     */
    public static function canImport(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.import');
    }

    /**
     * Check if user can view rating statistics
     */
    public static function canViewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('provider-rating.statistics');
    }

    /**
     * Get permission descriptions
     */
    public static function getPermissionDescriptions(): array
    {
        return [
            'provider-rating.view' => 'View provider ratings',
            'provider-rating.create' => 'Create provider ratings',
            'provider-rating.edit' => 'Edit provider ratings',
            'provider-rating.delete' => 'Delete provider ratings',
            'provider-rating.moderate' => 'Moderate provider ratings',
            'provider-rating.verify' => 'Verify provider ratings',
            'provider-rating.flag' => 'Flag provider ratings',
            'provider-rating.view-own' => 'View own provider ratings',
            'provider-rating.create-own' => 'Create own provider ratings',
            'provider-rating.edit-own' => 'Edit own provider ratings',
            'provider-rating.view-all' => 'View all provider ratings',
            'provider-rating.manage-all' => 'Manage all provider ratings',
            'provider-rating.export' => 'Export provider rating data',
            'provider-rating.import' => 'Import provider rating data',
            'provider-rating.statistics' => 'View provider rating statistics',
            'provider-rating.vote' => 'Vote on provider ratings',
        ];
    }

    /**
     * Get role-based permission mappings
     */
    public static function getRolePermissions(): array
    {
        return [
            'user' => self::getBasicUserPermissions(),
            'moderator' => self::getModeratorPermissions(),
            'admin' => self::getAdminPermissions(),
        ];
    }
}
