<?php

namespace App\Permissions;

use App\Models\EmployeeSkill;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeSkillPermissions
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employee skills.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.view-all') ||
               $user->hasPermissionTo('employee-skill.view-department') ||
               $user->hasPermissionTo('employee-skill.view-team') ||
               $user->hasPermissionTo('employee-skill.view-own');
    }

    /**
     * Determine whether the user can view the employee skill.
     */
    public function view(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user can view all skills
        if ($user->hasPermissionTo('employee-skill.view-all')) {
            return true;
        }

        // Check if user can view department skills
        if ($user->hasPermissionTo('employee-skill.view-department')) {
            return $this->isInSameDepartment($user, $employeeSkill);
        }

        // Check if user can view team skills
        if ($user->hasPermissionTo('employee-skill.view-team')) {
            return $this->isInSameTeam($user, $employeeSkill);
        }

        // Check if user can view own skills
        if ($user->hasPermissionTo('employee-skill.view-own')) {
            return $this->isOwnSkill($user, $employeeSkill);
        }

        return false;
    }

    /**
     * Determine whether the user can create employee skills.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.create-all') ||
               $user->hasPermissionTo('employee-skill.create-own');
    }

    /**
     * Determine whether the user can create skills for a specific employee.
     */
    public function createForEmployee(User $user, int $employeeId): bool
    {
        // Check if user can create for any employee
        if ($user->hasPermissionTo('employee-skill.create-all')) {
            return true;
        }

        // Check if user can create for own employee record
        if ($user->hasPermissionTo('employee-skill.create-own')) {
            return $this->isOwnEmployee($user, $employeeId);
        }

        return false;
    }

    /**
     * Determine whether the user can update the employee skill.
     */
    public function update(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user can update all skills
        if ($user->hasPermissionTo('employee-skill.edit-all')) {
            return true;
        }

        // Check if user can update own skills
        if ($user->hasPermissionTo('employee-skill.edit-own')) {
            return $this->isOwnSkill($user, $employeeSkill);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the employee skill.
     */
    public function delete(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user can delete all skills
        if ($user->hasPermissionTo('employee-skill.delete-all')) {
            return true;
        }

        // Check if user can delete own skills
        if ($user->hasPermissionTo('employee-skill.delete-own')) {
            return $this->isOwnSkill($user, $employeeSkill);
        }

        return false;
    }

    /**
     * Determine whether the user can verify the employee skill.
     */
    public function verify(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->hasPermissionTo('employee-skill.verify');
    }

    /**
     * Determine whether the user can certify the employee skill.
     */
    public function certify(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->hasPermissionTo('employee-skill.certify');
    }

    /**
     * Determine whether the user can set the skill as primary.
     */
    public function setPrimary(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->hasPermissionTo('employee-skill.set-primary');
    }

    /**
     * Determine whether the user can export skills data.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.export');
    }

    /**
     * Determine whether the user can import skills data.
     */
    public function import(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.import');
    }

    /**
     * Determine whether the user can view skills statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.statistics');
    }

    /**
     * Determine whether the user can view skills gap analysis.
     */
    public function viewGapAnalysis(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.gap-analysis');
    }

    /**
     * Determine whether the user can manage certifications.
     */
    public function manageCertifications(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.certification-management');
    }

    /**
     * Determine whether the user can manage all skills.
     */
    public function manageAll(User $user): bool
    {
        return $user->hasPermissionTo('employee-skill.manage-all');
    }

    /**
     * Check if the skill belongs to the user's own employee record.
     */
    private function isOwnSkill(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user has an employee record
        if (!$user->employee) {
            return false;
        }

        return $user->employee->id === $employeeSkill->employee_id;
    }

    /**
     * Check if the employee ID belongs to the user's own employee record.
     */
    private function isOwnEmployee(User $user, int $employeeId): bool
    {
        // Check if user has an employee record
        if (!$user->employee) {
            return false;
        }

        return $user->employee->id === $employeeId;
    }

    /**
     * Check if the skill belongs to an employee in the same department as the user.
     */
    private function isInSameDepartment(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user has an employee record
        if (!$user->employee || !$user->employee->department_id) {
            return false;
        }

        // Check if the skill's employee is in the same department
        return $employeeSkill->employee && 
               $employeeSkill->employee->department_id === $user->employee->department_id;
    }

    /**
     * Check if the skill belongs to an employee in the same team as the user.
     */
    private function isInSameTeam(User $user, EmployeeSkill $employeeSkill): bool
    {
        // Check if user has an employee record
        if (!$user->employee) {
            return false;
        }

        // This is a simplified team check - you might need to implement your own team logic
        // For now, we'll consider employees in the same department as a team
        return $this->isInSameDepartment($user, $employeeSkill);
    }

    /**
     * Get all permissions for employee skills.
     */
    public static function getAllPermissions(): array
    {
        return [
            'employee-skill.view',
            'employee-skill.create',
            'employee-skill.edit',
            'employee-skill.delete',
            'employee-skill.verify',
            'employee-skill.certify',
            'employee-skill.set-primary',
            'employee-skill.view-own',
            'employee-skill.create-own',
            'employee-skill.edit-own',
            'employee-skill.view-team',
            'employee-skill.view-department',
            'employee-skill.view-all',
            'employee-skill.create-all',
            'employee-skill.edit-all',
            'employee-skill.delete-all',
            'employee-skill.manage-all',
            'employee-skill.export',
            'employee-skill.import',
            'employee-skill.statistics',
            'employee-skill.gap-analysis',
            'employee-skill.certification-management',
        ];
    }

    /**
     * Get basic permissions for regular users.
     */
    public static function getBasicPermissions(): array
    {
        return [
            'employee-skill.view-own',
            'employee-skill.create-own',
            'employee-skill.edit-own',
        ];
    }

    /**
     * Get manager permissions.
     */
    public static function getManagerPermissions(): array
    {
        return [
            'employee-skill.view-team',
            'employee-skill.view-department',
            'employee-skill.verify',
            'employee-skill.set-primary',
            'employee-skill.statistics',
            'employee-skill.gap-analysis',
        ];
    }

    /**
     * Get admin permissions.
     */
    public static function getAdminPermissions(): array
    {
        return [
            'employee-skill.view-all',
            'employee-skill.create-all',
            'employee-skill.edit-all',
            'employee-skill.delete-all',
            'employee-skill.manage-all',
            'employee-skill.export',
            'employee-skill.import',
            'employee-skill.certification-management',
        ];
    }

    /**
     * Get permission descriptions.
     */
    public static function getPermissionDescriptions(): array
    {
        return [
            'employee-skill.view' => 'View individual employee skills',
            'employee-skill.create' => 'Create new employee skills',
            'employee-skill.edit' => 'Edit existing employee skills',
            'employee-skill.delete' => 'Delete employee skills',
            'employee-skill.verify' => 'Verify employee skills',
            'employee-skill.certify' => 'Add certifications to skills',
            'employee-skill.set-primary' => 'Set skills as primary',
            'employee-skill.view-own' => 'View own employee skills',
            'employee-skill.create-own' => 'Create own employee skills',
            'employee-skill.edit-own' => 'Edit own employee skills',
            'employee-skill.view-team' => 'View team member skills',
            'employee-skill.view-department' => 'View department skills',
            'employee-skill.view-all' => 'View all employee skills',
            'employee-skill.create-all' => 'Create skills for any employee',
            'employee-skill.edit-all' => 'Edit skills for any employee',
            'employee-skill.delete-all' => 'Delete skills for any employee',
            'employee-skill.manage-all' => 'Full management of all skills',
            'employee-skill.export' => 'Export skills data',
            'employee-skill.import' => 'Import skills data',
            'employee-skill.statistics' => 'View skills statistics',
            'employee-skill.gap-analysis' => 'View skills gap analysis',
            'employee-skill.certification-management' => 'Manage skill certifications',
        ];
    }
}
