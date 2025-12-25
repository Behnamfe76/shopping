<?php

namespace Fereydooni\Shopping\app\Permissions;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Support\Facades\Gate;

class EmployeeBenefitsPermissions
{
    /**
     * Register all employee benefits permissions
     */
    public static function register(): void
    {
        // View permissions
        Gate::define('employee-benefits.view', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.view');
        });

        Gate::define('employee-benefits.view-own', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.view-own');
        });

        Gate::define('employee-benefits.view-team', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.view-team');
        });

        Gate::define('employee-benefits.view-department', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.view-department');
        });

        Gate::define('employee-benefits.view-all', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.view-all');
        });

        // Create permissions
        Gate::define('employee-benefits.create', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.create');
        });

        Gate::define('employee-benefits.create-own', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.create-own');
        });

        // Edit permissions
        Gate::define('employee-benefits.edit', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.edit');
        });

        Gate::define('employee-benefits.edit-own', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.edit-own');
        });

        // Delete permissions
        Gate::define('employee-benefits.delete', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.delete');
        });

        // Enrollment permissions
        Gate::define('employee-benefits.enroll', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.enroll');
        });

        // Termination permissions
        Gate::define('employee-benefits.terminate', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.terminate');
        });

        // Cancellation permissions
        Gate::define('employee-benefits.cancel', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.cancel');
        });

        // Management permissions
        Gate::define('employee-benefits.manage-all', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.manage-all');
        });

        // Export/Import permissions
        Gate::define('employee-benefits.export', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.export');
        });

        Gate::define('employee-benefits.import', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.import');
        });

        // Statistics permissions
        Gate::define('employee-benefits.statistics', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.statistics');
        });

        // Cost analysis permissions
        Gate::define('employee-benefits.cost-analysis', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.cost-analysis');
        });

        // Renewal management permissions
        Gate::define('employee-benefits.renewal-management', function (User $user) {
            return $user->hasPermissionTo('employee-benefits.renewal-management');
        });

        // Model-specific permissions
        Gate::define('view', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.view-all') ||
                   $user->can('employee-benefits.view-department') ||
                   $user->can('employee-benefits.view-team') ||
                   $user->can('employee-benefits.view-own') ||
                   $user->id === $benefit->employee->user_id;
        });

        Gate::define('update', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.edit') ||
                   $user->can('employee-benefits.edit-own') ||
                   $user->id === $benefit->employee->user_id;
        });

        Gate::define('delete', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.delete') ||
                   $user->can('employee-benefits.manage-all');
        });

        Gate::define('enroll', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.enroll') ||
                   $user->can('employee-benefits.manage-all');
        });

        Gate::define('terminate', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.terminate') ||
                   $user->can('employee-benefits.manage-all');
        });

        Gate::define('cancel', function (User $user, EmployeeBenefits $benefit) {
            return $user->can('employee-benefits.cancel') ||
                   $user->can('employee-benefits.manage-all');
        });
    }

    /**
     * Get all employee benefits permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'employee-benefits.view',
            'employee-benefits.create',
            'employee-benefits.edit',
            'employee-benefits.delete',
            'employee-benefits.enroll',
            'employee-benefits.terminate',
            'employee-benefits.cancel',
            'employee-benefits.view-own',
            'employee-benefits.create-own',
            'employee-benefits.edit-own',
            'employee-benefits.view-team',
            'employee-benefits.view-department',
            'employee-benefits.view-all',
            'employee-benefits.manage-all',
            'employee-benefits.export',
            'employee-benefits.import',
            'employee-benefits.statistics',
            'employee-benefits.cost-analysis',
            'employee-benefits.renewal-management',
        ];
    }

    /**
     * Get permissions by category
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => [
                'employee-benefits.view',
                'employee-benefits.create',
                'employee-benefits.edit',
                'employee-benefits.delete',
            ],
            'Enrollment Management' => [
                'employee-benefits.enroll',
                'employee-benefits.terminate',
                'employee-benefits.cancel',
            ],
            'Access Control' => [
                'employee-benefits.view-own',
                'employee-benefits.create-own',
                'employee-benefits.edit-own',
                'employee-benefits.view-team',
                'employee-benefits.view-department',
                'employee-benefits.view-all',
                'employee-benefits.manage-all',
            ],
            'Data Management' => [
                'employee-benefits.export',
                'employee-benefits.import',
            ],
            'Analytics' => [
                'employee-benefits.statistics',
                'employee-benefits.cost-analysis',
                'employee-benefits.renewal-management',
            ],
        ];
    }
}
