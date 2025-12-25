<?php

namespace App\Permissions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EmployeeDepartmentPermissions
{
    /**
     * Register all employee department permissions
     */
    public static function register(): void
    {
        // Basic CRUD permissions
        Gate::define('employee-department.view', function ($user) {
            return $user->hasPermissionTo('employee-department.view');
        });

        Gate::define('employee-department.create', function ($user) {
            return $user->hasPermissionTo('employee-department.create');
        });

        Gate::define('employee-department.edit', function ($user) {
            return $user->hasPermissionTo('employee-department.edit');
        });

        Gate::define('employee-department.delete', function ($user) {
            return $user->hasPermissionTo('employee-department.delete');
        });

        // Manager assignment permissions
        Gate::define('employee-department.assign-manager', function ($user) {
            return $user->hasPermissionTo('employee-department.assign-manager');
        });

        // Department movement permissions
        Gate::define('employee-department.move', function ($user) {
            return $user->hasPermissionTo('employee-department.move');
        });

        // View permissions with different scopes
        Gate::define('employee-department.view-own', function ($user) {
            return $user->hasPermissionTo('employee-department.view-own');
        });

        Gate::define('employee-department.view-team', function ($user) {
            return $user->hasPermissionTo('employee-department.view-team');
        });

        Gate::define('employee-department.view-all', function ($user) {
            return $user->hasPermissionTo('employee-department.view-all');
        });

        // Management permissions
        Gate::define('employee-department.manage-all', function ($user) {
            return $user->hasPermissionTo('employee-department.manage-all');
        });

        // Export/Import permissions
        Gate::define('employee-department.export', function ($user) {
            return $user->hasPermissionTo('employee-department.export');
        });

        Gate::define('employee-department.import', function ($user) {
            return $user->hasPermissionTo('employee-department.import');
        });

        // Analytics and statistics permissions
        Gate::define('employee-department.statistics', function ($user) {
            return $user->hasPermissionTo('employee-department.statistics');
        });

        // Budget management permissions
        Gate::define('employee-department.budget-management', function ($user) {
            return $user->hasPermissionTo('employee-department.budget-management');
        });

        // Hierarchy management permissions
        Gate::define('employee-department.hierarchy-management', function ($user) {
            return $user->hasPermissionTo('employee-department.hierarchy-management');
        });

        // Advanced permissions with business logic
        Gate::define('employee-department.view-sensitive', function ($user) {
            return $user->hasPermissionTo('employee-department.view-sensitive') ||
                   $user->hasRole(['admin', 'hr-manager', 'finance-manager']);
        });

        Gate::define('employee-department.manage-budget', function ($user) {
            return $user->hasPermissionTo('employee-department.manage-budget') ||
                   $user->hasRole(['admin', 'finance-manager']);
        });

        Gate::define('employee-department.manage-hierarchy', function ($user) {
            return $user->hasPermissionTo('employee-department.manage-hierarchy') ||
                   $user->hasRole(['admin', 'hr-manager']);
        });

        Gate::define('employee-department.audit', function ($user) {
            return $user->hasPermissionTo('employee-department.audit') ||
                   $user->hasRole(['admin', 'auditor']);
        });
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'employee-department.view',
            'employee-department.create',
            'employee-department.edit',
            'employee-department.delete',
            'employee-department.assign-manager',
            'employee-department.move',
            'employee-department.view-own',
            'employee-department.view-team',
            'employee-department.view-all',
            'employee-department.manage-all',
            'employee-department.export',
            'employee-department.import',
            'employee-department.statistics',
            'employee-department.budget-management',
            'employee-department.hierarchy-management',
            'employee-department.view-sensitive',
            'employee-department.manage-budget',
            'employee-department.manage-hierarchy',
            'employee-department.audit',
        ];
    }

    /**
     * Get permissions by category
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => [
                'employee-department.view',
                'employee-department.create',
                'employee-department.edit',
                'employee-department.delete',
            ],
            'Manager Management' => [
                'employee-department.assign-manager',
            ],
            'Structure Management' => [
                'employee-department.move',
                'employee-department.hierarchy-management',
            ],
            'Access Control' => [
                'employee-department.view-own',
                'employee-department.view-team',
                'employee-department.view-all',
                'employee-department.view-sensitive',
            ],
            'Administration' => [
                'employee-department.manage-all',
                'employee-department.manage-budget',
                'employee-department.manage-hierarchy',
            ],
            'Data Management' => [
                'employee-department.export',
                'employee-department.import',
            ],
            'Analytics' => [
                'employee-department.statistics',
                'employee-department.audit',
            ],
            'Financial' => [
                'employee-department.budget-management',
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
                'employee-department.view',
                'employee-department.create',
                'employee-department.edit',
                'employee-department.assign-manager',
                'employee-department.move',
                'employee-department.view-all',
                'employee-department.hierarchy-management',
                'employee-department.statistics',
                'employee-department.export',
                'employee-department.import',
            ],
            'department-manager' => [
                'employee-department.view',
                'employee-department.view-own',
                'employee-department.view-team',
                'employee-department.statistics',
            ],
            'finance-manager' => [
                'employee-department.view',
                'employee-department.view-all',
                'employee-department.budget-management',
                'employee-department.statistics',
                'employee-department.export',
            ],
            'employee' => [
                'employee-department.view-own',
            ],
            'auditor' => [
                'employee-department.view',
                'employee-department.view-all',
                'employee-department.audit',
                'employee-department.export',
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
                    return Gate::allows('employee-department.view', $department);

                case 'create':
                    return Gate::allows('employee-department.create');

                case 'edit':
                    return Gate::allows('employee-department.edit', $department);

                case 'delete':
                    return Gate::allows('employee-department.delete', $department);

                case 'assign-manager':
                    return Gate::allows('employee-department.assign-manager', $department);

                case 'move':
                    return Gate::allows('employee-department.move', $department);

                case 'view-budget':
                    return Gate::allows('employee-department.budget-management') ||
                           Gate::allows('employee-department.view-sensitive');

                case 'manage-budget':
                    return Gate::allows('employee-department.manage-budget');

                case 'view-hierarchy':
                    return Gate::allows('employee-department.hierarchy-management') ||
                           Gate::allows('employee-department.view-all');

                case 'manage-hierarchy':
                    return Gate::allows('employee-department.manage-hierarchy');

                case 'export':
                    return Gate::allows('employee-department.export');

                case 'import':
                    return Gate::allows('employee-department.import');

                case 'view-statistics':
                    return Gate::allows('employee-department.statistics');

                case 'audit':
                    return Gate::allows('employee-department.audit');

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
                'employee-department.view',
                'employee-department.create',
                'employee-department.edit',
                'employee-department.delete',
                'employee-department.manage-all',
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
            'employee-department.view' => 'View department information',
            'employee-department.create' => 'Create new departments',
            'employee-department.edit' => 'Edit department details',
            'employee-department.delete' => 'Delete departments',
            'employee-department.assign-manager' => 'Assign managers to departments',
            'employee-department.move' => 'Move departments in hierarchy',
            'employee-department.view-own' => 'View own department information',
            'employee-department.view-team' => 'View team department information',
            'employee-department.view-all' => 'View all department information',
            'employee-department.manage-all' => 'Full department management access',
            'employee-department.export' => 'Export department data',
            'employee-department.import' => 'Import department data',
            'employee-department.statistics' => 'View department statistics and analytics',
            'employee-department.budget-management' => 'Manage department budgets',
            'employee-department.hierarchy-management' => 'Manage department hierarchy',
            'employee-department.view-sensitive' => 'View sensitive department information',
            'employee-department.manage-budget' => 'Full budget management access',
            'employee-department.manage-hierarchy' => 'Full hierarchy management access',
            'employee-department.audit' => 'Audit department activities and changes',
        ];

        return $descriptions[$permission] ?? 'Unknown permission';
    }
}
