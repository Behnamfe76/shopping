<?php

namespace Fereydooni\Shopping\app\Policies;

use App\Models\User;
use Fereydooni\Shopping\app\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('department.view') ||
               $user->can('department.view-all') ||
               $user->can('department.view-own') ||
               $user->can('department.view-team');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $Department): bool
    {
        // Check if user has general view permission
        if ($user->can('department.view-all')) {
            return true;
        }

        // Check if user can view their own department
        if ($user->can('department.view-own')) {
            // This would typically check if user belongs to this department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can view team departments
        if ($user->can('department.view-team')) {
            // This would typically check if user manages this department or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check basic view permission
        return $user->can('department.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('department.create') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $Department): bool
    {
        // Check if user has general edit permission
        if ($user->can('department.manage-all')) {
            return true;
        }

        // Check basic edit permission
        if (! $user->can('department.edit')) {
            return false;
        }

        // Check if user can edit their own department
        if ($user->can('department.view-own')) {
            // This would typically check if user belongs to this department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can edit team departments
        if ($user->can('department.view-team')) {
            // This would typically check if user manages this department or is in the same hierarchy
            // For now, we'll allow if they have the permission
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $Department): bool
    {
        // Check if user has general delete permission
        if ($user->can('department.manage-all')) {
            return true;
        }

        // Check basic delete permission
        if (! $user->can('department.delete')) {
            return false;
        }

        // Additional checks for department deletion
        // Check if department has employees
        if ($Department->employees()->count() > 0) {
            return false;
        }

        // Check if department has child departments
        if ($Department->children()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $Department): bool
    {
        return $user->can('department.edit') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $Department): bool
    {
        return $user->can('department.delete') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can assign a manager to the department.
     */
    public function assignManager(User $user, Department $Department): bool
    {
        return $user->can('department.assign-manager') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can move the department in hierarchy.
     */
    public function move(User $user, Department $Department): bool
    {
        return $user->can('department.move') ||
               $user->can('department.hierarchy-management') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can view budget information.
     */
    public function viewBudget(User $user, Department $Department): bool
    {
        return $user->can('department.budget-management') ||
               $user->can('department.view-sensitive') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can manage budget.
     */
    public function manageBudget(User $user, Department $Department): bool
    {
        return $user->can('department.manage-budget') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can view hierarchy information.
     */
    public function viewHierarchy(User $user, Department $Department): bool
    {
        return $user->can('department.hierarchy-management') ||
               $user->can('department.view-all') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can manage hierarchy.
     */
    public function manageHierarchy(User $user, Department $Department): bool
    {
        return $user->can('department.manage-hierarchy') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can export department data.
     */
    public function export(User $user, ?Department $Department = null): bool
    {
        return $user->can('department.export') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can import department data.
     */
    public function import(User $user, ?Department $Department = null): bool
    {
        return $user->can('department.import') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can view department statistics.
     */
    public function viewStatistics(User $user, ?Department $Department = null): bool
    {
        return $user->can('department.statistics') ||
               $user->can('department.view-all') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can audit department activities.
     */
    public function audit(User $user, ?Department $Department = null): bool
    {
        return $user->can('department.audit') ||
               $user->can('department.manage-all');
    }

    /**
     * Determine whether the user can view sensitive department information.
     */
    public function viewSensitive(User $user, Department $Department): bool
    {
        return $user->can('department.view-sensitive') ||
               $user->can('department.manage-all');
    }
}
