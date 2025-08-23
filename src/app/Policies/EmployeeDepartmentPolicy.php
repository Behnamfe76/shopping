<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeDepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('employee-department.view') ||
               $user->can('employee-department.view-all') ||
               $user->can('employee-department.view-own') ||
               $user->can('employee-department.view-team');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        // Check if user has general view permission
        if ($user->can('employee-department.view-all')) {
            return true;
        }

        // Check if user can view their own department
        if ($user->can('employee-department.view-own')) {
            // This would typically check if user belongs to this department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can view team departments
        if ($user->can('employee-department.view-team')) {
            // This would typically check if user manages this department or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check basic view permission
        return $user->can('employee-department.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('employee-department.create') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        // Check if user has general edit permission
        if ($user->can('employee-department.manage-all')) {
            return true;
        }

        // Check basic edit permission
        if (!$user->can('employee-department.edit')) {
            return false;
        }

        // Check if user can edit their own department
        if ($user->can('employee-department.view-own')) {
            // This would typically check if user belongs to this department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can edit team departments
        if ($user->can('employee-department.view-team')) {
            // This would typically check if user manages this department or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        // Check if user has general delete permission
        if ($user->can('employee-department.manage-all')) {
            return true;
        }

        // Check basic delete permission
        if (!$user->can('employee-department.delete')) {
            return false;
        }

        // Additional checks for department deletion
        // Check if department has employees
        if ($employeeDepartment->employees()->count() > 0) {
            return false;
        }

        // Check if department has child departments
        if ($employeeDepartment->children()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.edit') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.delete') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can assign a manager to the department.
     */
    public function assignManager(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.assign-manager') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can move the department in hierarchy.
     */
    public function move(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.move') ||
               $user->can('employee-department.hierarchy-management') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can view budget information.
     */
    public function viewBudget(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.budget-management') ||
               $user->can('employee-department.view-sensitive') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can manage budget.
     */
    public function manageBudget(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.manage-budget') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can view hierarchy information.
     */
    public function viewHierarchy(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.hierarchy-management') ||
               $user->can('employee-department.view-all') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can manage hierarchy.
     */
    public function manageHierarchy(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.manage-hierarchy') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can export department data.
     */
    public function export(User $user, EmployeeDepartment $employeeDepartment = null): bool
    {
        return $user->can('employee-department.export') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can import department data.
     */
    public function import(User $user, EmployeeDepartment $employeeDepartment = null): bool
    {
        return $user->can('employee-department.import') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can view department statistics.
     */
    public function viewStatistics(User $user, EmployeeDepartment $employeeDepartment = null): bool
    {
        return $user->can('employee-department.statistics') ||
               $user->can('employee-department.view-all') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can audit department activities.
     */
    public function audit(User $user, EmployeeDepartment $employeeDepartment = null): bool
    {
        return $user->can('employee-department.audit') ||
               $user->can('employee-department.manage-all');
    }

    /**
     * Determine whether the user can view sensitive department information.
     */
    public function viewSensitive(User $user, EmployeeDepartment $employeeDepartment): bool
    {
        return $user->can('employee-department.view-sensitive') ||
               $user->can('employee-department.manage-all');
    }
}
