<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('employees.viewAny');
    }

    /**
     * Determine whether the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->can('employees.view') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->can('employees.create');
    }

    /**
     * Determine whether the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->can('employees.update') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('employees.delete');
    }

    /**
     * Determine whether the user can restore the employee.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->can('employees.restore');
    }

    /**
     * Determine whether the user can permanently delete the employee.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->can('employees.forceDelete');
    }

    /**
     * Determine whether the user can activate the employee.
     */
    public function activate(User $user, Employee $employee): bool
    {
        return $user->can('employees.activate');
    }

    /**
     * Determine whether the user can deactivate the employee.
     */
    public function deactivate(User $user, Employee $employee): bool
    {
        return $user->can('employees.deactivate');
    }

    /**
     * Determine whether the user can terminate the employee.
     */
    public function terminate(User $user, Employee $employee): bool
    {
        return $user->can('employees.terminate');
    }

    /**
     * Determine whether the user can rehire the employee.
     */
    public function rehire(User $user, Employee $employee): bool
    {
        return $user->can('employees.rehire');
    }

    /**
     * Determine whether the user can manage salary.
     */
    public function manageSalary(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageSalary');
    }

    /**
     * Determine whether the user can manage performance.
     */
    public function managePerformance(User $user, Employee $employee): bool
    {
        return $user->can('employees.managePerformance');
    }

    /**
     * Determine whether the user can manage time off.
     */
    public function manageTimeOff(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageTimeOff') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can manage benefits.
     */
    public function manageBenefits(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageBenefits');
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('employees.viewAnalytics');
    }

    /**
     * Determine whether the user can export data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('employees.exportData');
    }

    /**
     * Determine whether the user can import data.
     */
    public function importData(User $user): bool
    {
        return $user->can('employees.importData');
    }

    /**
     * Determine whether the user can view sensitive data.
     */
    public function viewSensitiveData(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewSensitiveData');
    }

    /**
     * Determine whether the user can manage employee hierarchy.
     */
    public function manageHierarchy(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageHierarchy');
    }

    /**
     * Determine whether the user can assign managers.
     */
    public function assignManager(User $user, Employee $employee): bool
    {
        return $user->can('employees.assignManager');
    }

    /**
     * Determine whether the user can remove managers.
     */
    public function removeManager(User $user, Employee $employee): bool
    {
        return $user->can('employees.removeManager');
    }

    /**
     * Determine whether the user can view employee notes.
     */
    public function viewNotes(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewNotes') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can add employee notes.
     */
    public function addNotes(User $user, Employee $employee): bool
    {
        return $user->can('employees.addNotes');
    }

    /**
     * Determine whether the user can manage employee skills.
     */
    public function manageSkills(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageSkills') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can manage employee certifications.
     */
    public function manageCertifications(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageCertifications') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can manage employee training.
     */
    public function manageTraining(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageTraining');
    }

    /**
     * Determine whether the user can view employee emergency contacts.
     */
    public function viewEmergencyContacts(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewEmergencyContacts') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can update employee emergency contacts.
     */
    public function updateEmergencyContacts(User $user, Employee $employee): bool
    {
        return $user->can('employees.updateEmergencyContacts') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can view employee banking information.
     */
    public function viewBankingInfo(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewBankingInfo');
    }

    /**
     * Determine whether the user can update employee banking information.
     */
    public function updateBankingInfo(User $user, Employee $employee): bool
    {
        return $user->can('employees.updateBankingInfo');
    }

    /**
     * Determine whether the user can view employee tax information.
     */
    public function viewTaxInfo(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewTaxInfo');
    }

    /**
     * Determine whether the user can update employee tax information.
     */
    public function updateTaxInfo(User $user, Employee $employee): bool
    {
        return $user->can('employees.updateTaxInfo');
    }

    /**
     * Determine whether the user can view employee social security number.
     */
    public function viewSocialSecurityNumber(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewSocialSecurityNumber');
    }

    /**
     * Determine whether the user can update employee social security number.
     */
    public function updateSocialSecurityNumber(User $user, Employee $employee): bool
    {
        return $user->can('employees.updateSocialSecurityNumber');
    }

    /**
     * Determine whether the user can view employee performance reviews.
     */
    public function viewPerformanceReviews(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewPerformanceReviews') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can conduct performance reviews.
     */
    public function conductPerformanceReviews(User $user, Employee $employee): bool
    {
        return $user->can('employees.conductPerformanceReviews');
    }

    /**
     * Determine whether the user can view employee time-off requests.
     */
    public function viewTimeOffRequests(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewTimeOffRequests') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can approve time-off requests.
     */
    public function approveTimeOffRequests(User $user, Employee $employee): bool
    {
        return $user->can('employees.approveTimeOffRequests');
    }

    /**
     * Determine whether the user can reject time-off requests.
     */
    public function rejectTimeOffRequests(User $user, Employee $employee): bool
    {
        return $user->can('employees.rejectTimeOffRequests');
    }

    /**
     * Determine whether the user can view employee benefits information.
     */
    public function viewBenefitsInfo(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewBenefitsInfo') || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can manage employee benefits enrollment.
     */
    public function manageBenefitsEnrollment(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageBenefitsEnrollment');
    }

    /**
     * Determine whether the user can view employee organizational chart.
     */
    public function viewOrganizationalChart(User $user): bool
    {
        return $user->can('employees.viewOrganizationalChart');
    }

    /**
     * Determine whether the user can manage employee retention strategies.
     */
    public function manageRetentionStrategies(User $user): bool
    {
        return $user->can('employees.manageRetentionStrategies');
    }

    /**
     * Determine whether the user can send employee notifications.
     */
    public function sendNotifications(User $user): bool
    {
        return $user->can('employees.sendNotifications');
    }

    /**
     * Determine whether the user can view employee reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('employees.viewReports');
    }

    /**
     * Determine whether the user can generate employee reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('employees.generateReports');
    }

    /**
     * Determine whether the user can view employee dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->can('employees.viewDashboard');
    }

    /**
     * Determine whether the user can manage employee permissions.
     */
    public function managePermissions(User $user, Employee $employee): bool
    {
        return $user->can('employees.managePermissions');
    }

    /**
     * Determine whether the user can view employee audit logs.
     */
    public function viewAuditLogs(User $user, Employee $employee): bool
    {
        return $user->can('employees.viewAuditLogs');
    }

    /**
     * Determine whether the user can manage employee system access.
     */
    public function manageSystemAccess(User $user, Employee $employee): bool
    {
        return $user->can('employees.manageSystemAccess');
    }
}
