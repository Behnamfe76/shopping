<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\EmployeePosition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('employee-position.view') ||
               $user->can('employee-position.view-all') ||
               $user->can('employee-position.view-own') ||
               $user->can('employee-position.view-team') ||
               $user->can('employee-position.view-department');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeePosition $employeePosition): bool
    {
        // Check if user has general view permission
        if ($user->can('employee-position.view-all')) {
            return true;
        }

        // Check if user can view their own position
        if ($user->can('employee-position.view-own')) {
            // This would typically check if user holds this position
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can view team positions
        if ($user->can('employee-position.view-team')) {
            // This would typically check if user manages this position or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can view department positions
        if ($user->can('employee-position.view-department')) {
            // This would typically check if user belongs to the same department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check basic view permission
        return $user->can('employee-position.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('employee-position.create') ||
               $user->can('employee-position.manage-all');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeePosition $employeePosition): bool
    {
        // Check if user has general edit permission
        if ($user->can('employee-position.manage-all')) {
            return true;
        }

        // Check basic edit permission
        if (!$user->can('employee-position.edit')) {
            return false;
        }

        // Check if user can edit their own position
        if ($user->can('employee-position.view-own')) {
            // This would typically check if user holds this position
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can edit team positions
        if ($user->can('employee-position.view-team')) {
            // This would typically check if user manages this position or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can edit department positions
        if ($user->can('employee-position.view-department')) {
            // This would typically check if user belongs to the same department
            // For now, we'll allow if they have the permission
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeePosition $employeePosition): bool
    {
        // Check if user has general delete permission
        if ($user->can('employee-position.manage-all')) {
            return true;
        }

        // Check basic delete permission
        if (!$user->can('employee-position.delete')) {
            return false;
        }

        // Check if user can delete their own position
        if ($user->can('employee-position.view-own')) {
            // This would typically check if user holds this position
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can delete team positions
        if ($user->can('employee-position.view-team')) {
            // This would typically check if user manages this position or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can delete department positions
        if ($user->can('employee-position.view-department')) {
            // This would typically check if user belongs to the same department
            // For now, we'll allow if they have the permission
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->can('employee-position.edit') ||
               $user->can('employee-position.manage-all');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->can('employee-position.delete') ||
               $user->can('employee-position.manage-all');
    }

    /**
     * Determine whether the user can update salary information.
     */
    public function updateSalary(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->can('employee-position.update-salary') ||
               $user->can('employee-position.manage-all') ||
               $user->can('employee-position.salary-management');
    }

    /**
     * Determine whether the user can set hiring status.
     */
    public function setHiring(User $user, EmployeePosition $employeePosition): bool
    {
        return $user->can('employee-position.set-hiring') ||
               $user->can('employee-position.manage-all') ||
               $user->can('employee-position.hiring-management');
    }

    /**
     * Determine whether the user can export position data.
     */
    public function export(User $user): bool
    {
        return $user->can('employee-position.export') ||
               $user->can('employee-position.manage-all');
    }

    /**
     * Determine whether the user can import position data.
     */
    public function import(User $user): bool
    {
        return $user->can('employee-position.import') ||
               $user->can('employee-position.manage-all');
    }

    /**
     * Determine whether the user can view position statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('employee-position.statistics') ||
               $user->can('employee-position.manage-all');
    }
}
