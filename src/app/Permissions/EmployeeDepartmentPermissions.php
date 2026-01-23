<?php

namespace App\Permissions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DepartmentPermissions
{
    /**
     * Register all employee department permissions
     */
    public static function register(): void
    {
        // Basic CRUD permissions
        Gate::define('department.view', function ($user) {
            return $user->hasPermissionTo('department.view');
        });

        Gate::define('department.create', function ($user) {
            return $user->hasPermissionTo('department.create');
        });

        Gate::define('department.edit', function ($user) {
            return $user->hasPermissionTo('department.edit');
        });

        Gate::define('department.delete', function ($user) {
            return $user->hasPermissionTo('department.delete');
        });

        // Manager assignment permissions
        Gate::define('department.assign-manager', function ($user) {
            return $user->hasPermissionTo('department.assign-manager');
        });

        // Department movement permissions
        Gate::define('department.move', function ($user) {
            return $user->hasPermissionTo('department.move');
        });

        // View permissions with different scopes
        Gate::define('department.view-own', function ($user) {
            return $user->hasPermissionTo('department.view-own');
        });

        Gate::define('department.view-team', function ($user) {
            return $user->hasPermissionTo('department.view-team');
        });

        Gate::define('department.view-all', function ($user) {
            return $user->hasPermissionTo('department.view-all');
        });

        // Management permissions
        Gate::define('department.manage-all', function ($user) {
            return $user->hasPermissionTo('department.manage-all');
        });

        // Export/Import permissions
        Gate::define('department.export', function ($user) {
            return $user->hasPermissionTo('department.export');
        });

        Gate::define('department.import', function ($user) {
            return $user->hasPermissionTo('department.import');
        });

        // Analytics and statistics permissions
        Gate::define('department.statistics', function ($user) {
            return $user->hasPermissionTo('department.statistics');
        });

        // Budget management permissions
        Gate::define('department.budget-management', function ($user) {
            return $user->hasPermissionTo('department.budget-management');
        });

        // Hierarchy management permissions
        Gate::define('department.hierarchy-management', function ($user) {
            return $user->hasPermissionTo('department.hierarchy-management');
        });

        // Advanced permissions with business logic
        Gate::define('department.view-sensitive', function ($user) {
            return $user->hasPermissionTo('department.view-sensitive') ||
                   $user->hasRole(['admin', 'hr-manager', 'finance-manager']);
        });

        Gate::define('department.manage-budget', function ($user) {
            return $user->hasPermissionTo('department.manage-budget') ||
                   $user->hasRole(['admin', 'finance-manager']);
        });

        Gate::define('department.manage-hierarchy', function ($user) {
            return $user->hasPermissionTo('department.manage-hierarchy') ||
                   $user->hasRole(['admin', 'hr-manager']);
        });

        Gate::define('department.audit', function ($user) {
            return $user->hasPermissionTo('department.audit') ||
                   $user->hasRole(['admin', 'auditor']);
        });
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'department.view',
            'department.create',
            'department.edit',
            'department.delete',
            'department.assign-manager',
            'department.move',
            'department.view-own',
            'department.view-team',
            'department.view-all',
            'department.manage-all',
            'department.export',
            'department.import',
            'department.statistics',
            'department.budget-management',
            'department.hierarchy-management',
            'department.view-sensitive',
            'department.manage-budget',
            'department.manage-hierarchy',
            'department.audit',
        ];
    }

    /**
     * Get permissions by category
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => [
                'department.view',
                'department.create',
                'department.edit',
                'department.delete',
            ],
            'Manager Management' => [
                'department.assign-manager',
            ],
            'Structure Management' => [
                'department.move',
                'department.hierarchy-management',
            ],
            'Access Control' => [
                'department.view-own',
                'department.view-team',
                'department.view-all',
                'department.view-sensitive',
            ],
            'Administration' => [
                'department.manage-all',
                'department.manage-budget',
                'department.manage-hierarchy',
            ],
            'Data Management' => [
                'department.export',
                'department.import',
            ],
            'Analytics' => [
                'department.statistics',
                'department.audit',
            ],
            'Financial' => [
                'department.budget-management',
            ],
        ];
    }

    /**
     * Get default role permissions mapping
     */
    public static function getDefaultRolePermissions(): array
    {
        return [
            'admin' => self::getAllPermissions(),
            'hr-manager' => [
                'department.view',
                'department.create',
                'department.edit',
                'department.assign-manager',
                'department.move',
                'department.view-all',
                'department.hierarchy-management',
                'department.statistics',
                'department.export',
                'department.import',
            ],
            'department-manager' => [
                'department.view',
                'department.view-own',
                'department.view-team',
                'department.statistics',
            ],
            'finance-manager' => [
                'department.view',
                'department.view-all',
                'department.budget-management',
                'department.statistics',
                'department.export',
            ],
            'employee' => [
                'department.view-own',
            ],
            'auditor' => [
                'department.view',
                'department.view-all',
                'department.audit',
                'department.export',
            ],
        ];
    }

    /**
     * Check if user can perform action on department
     */
    public static function canPerformAction(string $action, $user, $department = null): bool
    {
        try {
            switch ($action) {
                case 'view':
                    return Gate::allows('department.view', $department);

                case 'create':
                    return Gate::allows('department.create');

                case 'edit':
                    return Gate::allows('department.edit', $department);

                case 'delete':
                    return Gate::allows('department.delete', $department);

                case 'assign-manager':
                    return Gate::allows('department.assign-manager', $department);

                case 'move':
                    return Gate::allows('department.move', $department);

                case 'view-budget':
                    return Gate::allows('department.budget-management') ||
                           Gate::allows('department.view-sensitive');

                case 'manage-budget':
                    return Gate::allows('department.manage-budget');

                case 'view-hierarchy':
                    return Gate::allows('department.hierarchy-management') ||
                           Gate::allows('department.view-all');

                case 'manage-hierarchy':
                    return Gate::allows('department.manage-hierarchy');

                case 'export':
                    return Gate::allows('department.export');

                case 'import':
                    return Gate::allows('department.import');

                case 'view-statistics':
                    return Gate::allows('department.statistics');

                case 'audit':
                    return Gate::allows('department.audit');

                default:
                    Log::warning('Unknown permission action', [
                        'action' => $action,
                        'user_id' => $user->id ?? 'unknown',
                    ]);

                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Error checking permission', [
                'action' => $action,
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get user's effective permissions for department
     */
    public static function getUserPermissions($user, $department = null): array
    {
        try {
            $permissions = [];

            foreach (self::getAllPermissions() as $permission) {
                if (Gate::allows($permission, $department)) {
                    $permissions[] = $permission;
                }
            }

            return $permissions;
        } catch (\Exception $e) {
            Log::error('Error getting user permissions', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Check if user has any department management permissions
     */
    public static function hasAnyDepartmentPermission($user): bool
    {
        try {
            $permissions = [
                'department.view',
                'department.create',
                'department.edit',
                'department.delete',
                'department.manage-all',
            ];

            foreach ($permissions as $permission) {
                if ($user->hasPermissionTo($permission)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error checking department permissions', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get permission description
     */
    public static function getPermissionDescription(string $permission): string
    {
        $descriptions = [
            'department.view' => 'View department information',
            'department.create' => 'Create new departments',
            'department.edit' => 'Edit department details',
            'department.delete' => 'Delete departments',
            'department.assign-manager' => 'Assign managers to departments',
            'department.move' => 'Move departments in hierarchy',
            'department.view-own' => 'View own department information',
            'department.view-team' => 'View team department information',
            'department.view-all' => 'View all department information',
            'department.manage-all' => 'Full department management access',
            'department.export' => 'Export department data',
            'department.import' => 'Import department data',
            'department.statistics' => 'View department statistics and analytics',
            'department.budget-management' => 'Manage department budgets',
            'department.hierarchy-management' => 'Manage department hierarchy',
            'department.view-sensitive' => 'View sensitive department information',
            'department.manage-budget' => 'Full budget management access',
            'department.manage-hierarchy' => 'Full hierarchy management access',
            'department.audit' => 'Audit department activities and changes',
        ];

        return $descriptions[$permission] ?? 'Unknown permission';
    }
}
