<?php

namespace Fereydooni\Shopping\app\Permissions;

use Illuminate\Support\Facades\Gate;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use App\Models\User;

class EmployeePositionPermissions
{
    /**
     * Register all employee position permissions.
     */
    public static function register(): void
    {
        // View permissions
        Gate::define('employee-position.view', function (User $user) {
            return $user->hasPermissionTo('employee-position.view');
        });

        Gate::define('employee-position.view-own', function (User $user) {
            return $user->hasPermissionTo('employee-position.view-own');
        });

        Gate::define('employee-position.view-team', function (User $user) {
            return $user->hasPermissionTo('employee-position.view-team');
        });

        Gate::define('employee-position.view-department', function (User $user) {
            return $user->hasPermissionTo('employee-position.view-department');
        });

        Gate::define('employee-position.view-all', function (User $user) {
            return $user->hasPermissionTo('employee-position.view-all');
        });

        // Create permissions
        Gate::define('employee-position.create', function (User $user) {
            return $user->hasPermissionTo('employee-position.create');
        });

        // Edit permissions
        Gate::define('employee-position.edit', function (User $user, EmployeePosition $position = null) {
            if (!$user->hasPermissionTo('employee-position.edit')) {
                return false;
            }

            // If no specific position, check general edit permission
            if (!$position) {
                return true;
            }

            // Check if user can edit this specific position
            return $user->hasPermissionTo('employee-position.edit');
        });

        // Delete permissions
        Gate::define('employee-position.delete', function (User $user, EmployeePosition $position = null) {
            if (!$user->hasPermissionTo('employee-position.delete')) {
                return false;
            }

            // If no specific position, check general delete permission
            if (!$position) {
                return true;
            }

            // Check if user can delete this specific position
            return $user->hasPermissionTo('employee-position.delete');
        });

        // Special permissions
        Gate::define('employee-position.update-salary', function (User $user, EmployeePosition $position = null) {
            if (!$user->hasPermissionTo('employee-position.update-salary')) {
                return false;
            }

            // If no specific position, check general salary update permission
            if (!$position) {
                return true;
            }

            // Check if user can update salary for this specific position
            return $user->hasPermissionTo('employee-position.update-salary');
        });

        Gate::define('employee-position.set-hiring', function (User $user, EmployeePosition $position = null) {
            if (!$user->hasPermissionTo('employee-position.set-hiring')) {
                return false;
            }

            // If no specific position, check general hiring permission
            if (!$position) {
                return true;
            }

            // Check if user can set hiring for this specific position
            return $user->hasPermissionTo('employee-position.set-hiring');
        });

        Gate::define('employee-position.manage-all', function (User $user) {
            return $user->hasPermissionTo('employee-position.manage-all');
        });

        Gate::define('employee-position.export', function (User $user) {
            return $user->hasPermissionTo('employee-position.export');
        });

        Gate::define('employee-position.import', function (User $user) {
            return $user->hasPermissionTo('employee-position.import');
        });

        Gate::define('employee-position.statistics', function (User $user) {
            return $user->hasPermissionTo('employee-position.statistics');
        });

        Gate::define('employee-position.salary-management', function (User $user) {
            return $user->hasPermissionTo('employee-position.salary-management');
        });

        Gate::define('employee-position.hiring-management', function (User $user) {
            return $user->hasPermissionTo('employee-position.hiring-management');
        });
    }

    /**
     * Get all available permissions.
     */
    public static function getAllPermissions(): array
    {
        return [
            'employee-position.view',
            'employee-position.create',
            'employee-position.edit',
            'employee-position.delete',
            'employee-position.update-salary',
            'employee-position.set-hiring',
            'employee-position.view-own',
            'employee-position.view-team',
            'employee-position.view-department',
            'employee-position.view-all',
            'employee-position.manage-all',
            'employee-position.export',
            'employee-position.import',
            'employee-position.statistics',
            'employee-position.salary-management',
            'employee-position.hiring-management',
        ];
    }

    /**
     * Get basic permissions for regular users.
     */
    public static function getBasicPermissions(): array
    {
        return [
            'employee-position.view-own',
            'employee-position.view-team',
        ];
    }

    /**
     * Get manager permissions.
     */
    public static function getManagerPermissions(): array
    {
        return [
            'employee-position.view',
            'employee-position.view-team',
            'employee-position.view-department',
            'employee-position.create',
            'employee-position.edit',
            'employee-position.set-hiring',
            'employee-position.statistics',
            'employee-position.hiring-management',
        ];
    }

    /**
     * Get HR permissions.
     */
    public static function getHRPermissions(): array
    {
        return [
            'employee-position.view',
            'employee-position.view-all',
            'employee-position.create',
            'employee-position.edit',
            'employee-position.delete',
            'employee-position.update-salary',
            'employee-position.set-hiring',
            'employee-position.export',
            'employee-position.import',
            'employee-position.statistics',
            'employee-position.salary-management',
            'employee-position.hiring-management',
        ];
    }

    /**
     * Get admin permissions.
     */
    public static function getAdminPermissions(): array
    {
        return [
            'employee-position.view',
            'employee-position.view-all',
            'employee-position.create',
            'employee-position.edit',
            'employee-position.delete',
            'employee-position.update-salary',
            'employee-position.set-hiring',
            'employee-position.manage-all',
            'employee-position.export',
            'employee-position.import',
            'employee-position.statistics',
            'employee-position.salary-management',
            'employee-position.hiring-management',
        ];
    }

    /**
     * Get permissions by role.
     */
    public static function getPermissionsByRole(string $role): array
    {
        return match($role) {
            'basic' => self::getBasicPermissions(),
            'manager' => self::getManagerPermissions(),
            'hr' => self::getHRPermissions(),
            'admin' => self::getAdminPermissions(),
            default => self::getBasicPermissions(),
        };
    }

    /**
     * Check if user has any position permission.
     */
    public static function hasAnyPositionPermission(User $user): bool
    {
        return $user->hasAnyPermission(self::getAllPermissions());
    }

    /**
     * Check if user has position management permission.
     */
    public static function hasPositionManagementPermission(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.manage-all');
    }

    /**
     * Check if user can view positions.
     */
    public static function canViewPositions(User $user): bool
    {
        return $user->hasAnyPermission([
            'employee-position.view',
            'employee-position.view-own',
            'employee-position.view-team',
            'employee-position.view-department',
            'employee-position.view-all',
        ]);
    }

    /**
     * Check if user can create positions.
     */
    public static function canCreatePositions(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.create');
    }

    /**
     * Check if user can edit positions.
     */
    public static function canEditPositions(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.edit');
    }

    /**
     * Check if user can delete positions.
     */
    public static function canDeletePositions(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.delete');
    }

    /**
     * Check if user can update position salaries.
     */
    public static function canUpdatePositionSalaries(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.update-salary');
    }

    /**
     * Check if user can set positions to hiring.
     */
    public static function canSetPositionsToHiring(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.set-hiring');
    }

    /**
     * Check if user can export position data.
     */
    public static function canExportPositionData(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.export');
    }

    /**
     * Check if user can import position data.
     */
    public static function canImportPositionData(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.import');
    }

    /**
     * Check if user can view position statistics.
     */
    public static function canViewPositionStatistics(User $user): bool
    {
        return $user->hasPermissionTo('employee-position.statistics');
    }

    /**
     * Get permission descriptions.
     */
    public static function getPermissionDescriptions(): array
    {
        return [
            'employee-position.view' => 'View employee positions',
            'employee-position.create' => 'Create new employee positions',
            'employee-position.edit' => 'Edit existing employee positions',
            'employee-position.delete' => 'Delete employee positions',
            'employee-position.update-salary' => 'Update position salary ranges',
            'employee-position.set-hiring' => 'Set positions to hiring status',
            'employee-position.view-own' => 'View own position information',
            'employee-position.view-team' => 'View team member positions',
            'employee-position.view-department' => 'View department positions',
            'employee-position.view-all' => 'View all positions across organization',
            'employee-position.manage-all' => 'Full management of all positions',
            'employee-position.export' => 'Export position data',
            'employee-position.import' => 'Import position data',
            'employee-position.statistics' => 'View position statistics and analytics',
            'employee-position.salary-management' => 'Manage position salary structures',
            'employee-position.hiring-management' => 'Manage hiring processes and job postings',
        ];
    }

    /**
     * Get permission categories.
     */
    public static function getPermissionCategories(): array
    {
        return [
            'view' => [
                'employee-position.view',
                'employee-position.view-own',
                'employee-position.view-team',
                'employee-position.view-department',
                'employee-position.view-all',
            ],
            'manage' => [
                'employee-position.create',
                'employee-position.edit',
                'employee-position.delete',
                'employee-position.manage-all',
            ],
            'special' => [
                'employee-position.update-salary',
                'employee-position.set-hiring',
                'employee-position.salary-management',
                'employee-position.hiring-management',
            ],
            'data' => [
                'employee-position.export',
                'employee-position.import',
                'employee-position.statistics',
            ],
        ];
    }
}
