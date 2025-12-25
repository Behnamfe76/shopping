<?php

namespace App\Permissions;

use App\Models\Employee;
use App\Models\EmployeeSalaryHistory;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class EmployeeSalaryHistoryPermissions
{
    public static function register(): void
    {
        // View permissions
        Gate::define('employee-salary-history.view', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.view');
        });

        Gate::define('employee-salary-history.view-own', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.view-own');
        });

        Gate::define('employee-salary-history.view-team', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.view-team');
        });

        Gate::define('employee-salary-history.view-department', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.view-department');
        });

        Gate::define('employee-salary-history.view-all', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.view-all');
        });

        // Create permissions
        Gate::define('employee-salary-history.create', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.create');
        });

        // Edit permissions
        Gate::define('employee-salary-history.edit', function (User $user, ?EmployeeSalaryHistory $salaryHistory = null) {
            if (! $user->hasPermissionTo('employee-salary-history.edit')) {
                return false;
            }

            // If no specific record, just check general permission
            if (! $salaryHistory) {
                return true;
            }

            // Check if user can edit this specific record
            return $user->hasPermissionTo('employee-salary-history.edit');
        });

        // Delete permissions
        Gate::define('employee-salary-history.delete', function (User $user, ?EmployeeSalaryHistory $salaryHistory = null) {
            if (! $user->hasPermissionTo('employee-salary-history.delete')) {
                return false;
            }

            // If no specific record, just check general permission
            if (! $salaryHistory) {
                return true;
            }

            // Check if user can delete this specific record
            return $user->hasPermissionTo('employee-salary-history.delete');
        });

        // Approval permissions
        Gate::define('employee-salary-history.approve', function (User $user, ?EmployeeSalaryHistory $salaryHistory = null) {
            if (! $user->hasPermissionTo('employee-salary-history.approve')) {
                return false;
            }

            // If no specific record, just check general permission
            if (! $salaryHistory) {
                return true;
            }

            // Check if user can approve this specific record
            return $user->hasPermissionTo('employee-salary-history.approve');
        });

        Gate::define('employee-salary-history.reject', function (User $user, ?EmployeeSalaryHistory $salaryHistory = null) {
            if (! $user->hasPermissionTo('employee-salary-history.reject')) {
                return false;
            }

            // If no specific record, just check general permission
            if (! $salaryHistory) {
                return true;
            }

            // Check if user can reject this specific record
            return $user->hasPermissionTo('employee-salary-history.reject');
        });

        // Management permissions
        Gate::define('employee-salary-history.manage-all', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.manage-all');
        });

        // Export/Import permissions
        Gate::define('employee-salary-history.export', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.export');
        });

        Gate::define('employee-salary-history.import', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.import');
        });

        // Statistics and analytics permissions
        Gate::define('employee-salary-history.statistics', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.statistics');
        });

        Gate::define('employee-salary-history.salary-analysis', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.salary-analysis');
        });

        // Retroactive adjustments permissions
        Gate::define('employee-salary-history.retroactive-adjustments', function (User $user) {
            return $user->hasPermissionTo('employee-salary-history.retroactive-adjustments');
        });

        // Policy-based permissions
        Gate::define('employee-salary-history.view-employee', function (User $user, Employee $employee) {
            // Check if user has general view permission
            if (! $user->hasPermissionTo('employee-salary-history.view')) {
                return false;
            }

            // Check specific permissions
            if ($user->hasPermissionTo('employee-salary-history.view-all')) {
                return true;
            }

            if ($user->hasPermissionTo('employee-salary-history.view-own') && $user->id === $employee->user_id) {
                return true;
            }

            if ($user->hasPermissionTo('employee-salary-history.view-team')) {
                // Check if user is in the same team as the employee
                return $user->team_id === $employee->team_id;
            }

            if ($user->hasPermissionTo('employee-salary-history.view-department')) {
                // Check if user is in the same department as the employee
                return $user->department_id === $employee->department_id;
            }

            return false;
        });

        Gate::define('employee-salary-history.manage-employee', function (User $user, Employee $employee) {
            // Check if user has general management permission
            if (! $user->hasPermissionTo('employee-salary-history.manage-all')) {
                return false;
            }

            // Check if user can manage this specific employee
            return $user->hasPermissionTo('employee-salary-history.manage-all');
        });

        Gate::define('employee-salary-history.approve-employee', function (User $user, Employee $employee) {
            // Check if user has general approval permission
            if (! $user->hasPermissionTo('employee-salary-history.approve')) {
                return false;
            }

            // Check if user can approve for this specific employee
            return $user->hasPermissionTo('employee-salary-history.approve');
        });

        Gate::define('employee-salary-history.reject-employee', function (User $user, Employee $employee) {
            // Check if user has general rejection permission
            if (! $user->hasPermissionTo('employee-salary-history.reject')) {
                return false;
            }

            // Check if user can reject for this specific employee
            return $user->hasPermissionTo('employee-salary-history.reject');
        });
    }

    public static function getPermissions(): array
    {
        return [
            // View permissions
            'employee-salary-history.view' => 'View salary history records',
            'employee-salary-history.view-own' => 'View own salary history',
            'employee-salary-history.view-team' => 'View team salary history',
            'employee-salary-history.view-department' => 'View department salary history',
            'employee-salary-history.view-all' => 'View all salary history records',

            // Create permissions
            'employee-salary-history.create' => 'Create salary history records',

            // Edit permissions
            'employee-salary-history.edit' => 'Edit salary history records',

            // Delete permissions
            'employee-salary-history.delete' => 'Delete salary history records',

            // Approval permissions
            'employee-salary-history.approve' => 'Approve salary changes',
            'employee-salary-history.reject' => 'Reject salary changes',

            // Management permissions
            'employee-salary-history.manage-all' => 'Manage all salary history records',

            // Export/Import permissions
            'employee-salary-history.export' => 'Export salary history data',
            'employee-salary-history.import' => 'Import salary history data',

            // Statistics and analytics permissions
            'employee-salary-history.statistics' => 'View salary statistics',
            'employee-salary-history.salary-analysis' => 'Perform salary analysis',

            // Retroactive adjustments permissions
            'employee-salary-history.retroactive-adjustments' => 'Manage retroactive salary adjustments',
        ];
    }

    public static function getRolePermissions(): array
    {
        return [
            'HR Manager' => [
                'employee-salary-history.view-all',
                'employee-salary-history.create',
                'employee-salary-history.edit',
                'employee-salary-history.delete',
                'employee-salary-history.approve',
                'employee-salary-history.reject',
                'employee-salary-history.manage-all',
                'employee-salary-history.export',
                'employee-salary-history.import',
                'employee-salary-history.statistics',
                'employee-salary-history.salary-analysis',
                'employee-salary-history.retroactive-adjustments',
            ],
            'HR Specialist' => [
                'employee-salary-history.view-all',
                'employee-salary-history.create',
                'employee-salary-history.edit',
                'employee-salary-history.approve',
                'employee-salary-history.reject',
                'employee-salary-history.export',
                'employee-salary-history.statistics',
                'employee-salary-history.salary-analysis',
            ],
            'Department Manager' => [
                'employee-salary-history.view-department',
                'employee-salary-history.create',
                'employee-salary-history.edit',
                'employee-salary-history.approve',
                'employee-salary-history.reject',
                'employee-salary-history.statistics',
            ],
            'Team Lead' => [
                'employee-salary-history.view-team',
                'employee-salary-history.create',
                'employee-salary-history.edit',
                'employee-salary-history.statistics',
            ],
            'Employee' => [
                'employee-salary-history.view-own',
            ],
            'Finance Manager' => [
                'employee-salary-history.view-all',
                'employee-salary-history.statistics',
                'employee-salary-history.salary-analysis',
                'employee-salary-history.export',
                'employee-salary-history.retroactive-adjustments',
            ],
            'Finance Specialist' => [
                'employee-salary-history.view-all',
                'employee-salary-history.statistics',
                'employee-salary-history.export',
            ],
        ];
    }

    public static function getDefaultPermissions(): array
    {
        return [
            'employee-salary-history.view-own',
        ];
    }

    public static function getAdminPermissions(): array
    {
        return [
            'employee-salary-history.view-all',
            'employee-salary-history.create',
            'employee-salary-history.edit',
            'employee-salary-history.delete',
            'employee-salary-history.approve',
            'employee-salary-history.reject',
            'employee-salary-history.manage-all',
            'employee-salary-history.export',
            'employee-salary-history.import',
            'employee-salary-history.statistics',
            'employee-salary-history.salary-analysis',
            'employee-salary-history.retroactive-adjustments',
        ];
    }
}
