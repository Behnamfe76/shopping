<?php

namespace App\Policies;

use App\Models\EmployeePerformanceReview;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePerformanceReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any performance reviews.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager', 'manager']);
    }

    /**
     * Determine whether the user can view the performance review.
     */
    public function view(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can view all reviews
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can view reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review);
        }

        // Employees can view their own reviews
        if ($user->id === $review->employee_id) {
            return true;
        }

        // Reviewers can view reviews they're assigned to
        if ($user->id === $review->reviewer_id) {
            return true;
        }

        // Approvers can view reviews they need to approve
        if ($user->id === $review->approved_by) {
            return true;
        }

        return $user->hasPermissionTo('view-employee-performance-reviews');
    }

    /**
     * Determine whether the user can create performance reviews.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager', 'manager']);
    }

    /**
     * Determine whether the user can update the performance review.
     */
    public function update(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can update any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can update reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review) &&
                   $this->canEditReview($review);
        }

        // Reviewers can update reviews they're assigned to (if editable)
        if ($user->id === $review->reviewer_id) {
            return $this->canEditReview($review);
        }

        // Employees can update their own reviews (if editable)
        if ($user->id === $review->employee_id) {
            return $this->canEditReview($review);
        }

        return $user->hasPermissionTo('update-employee-performance-reviews');
    }

    /**
     * Determine whether the user can delete the performance review.
     */
    public function delete(User $user, EmployeePerformanceReview $review): bool
    {
        // Only admin and HR managers can delete reviews
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        return $user->hasPermissionTo('delete-employee-performance-reviews');
    }

    /**
     * Determine whether the user can submit the performance review.
     */
    public function submit(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can submit any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can submit reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review) &&
                   $review->canBeSubmitted();
        }

        // Reviewers can submit reviews they're assigned to
        if ($user->id === $review->reviewer_id) {
            return $review->canBeSubmitted();
        }

        return $user->hasPermissionTo('submit-employee-performance-reviews');
    }

    /**
     * Determine whether the user can approve the performance review.
     */
    public function approve(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can approve any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can approve reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review) &&
                   $review->canBeApproved();
        }

        // Users can approve reviews they're assigned to approve
        if ($user->id === $review->approved_by) {
            return $review->canBeApproved();
        }

        return $user->hasPermissionTo('approve-employee-performance-reviews');
    }

    /**
     * Determine whether the user can reject the performance review.
     */
    public function reject(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can reject any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can reject reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review) &&
                   $review->canBeRejected();
        }

        // Users can reject reviews they're assigned to approve
        if ($user->id === $review->approved_by) {
            return $review->canBeRejected();
        }

        return $user->hasPermissionTo('reject-employee-performance-reviews');
    }

    /**
     * Determine whether the user can assign reviewers.
     */
    public function assignReviewer(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can assign reviewers to any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can assign reviewers to reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review);
        }

        return $user->hasPermissionTo('assign-reviewer-employee-performance-reviews');
    }

    /**
     * Determine whether the user can schedule reviews.
     */
    public function schedule(User $user, EmployeePerformanceReview $review): bool
    {
        // Admin and HR managers can schedule any review
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can schedule reviews in their department
        if ($user->hasRole('manager')) {
            return $this->isInUserDepartment($user, $review);
        }

        return $user->hasPermissionTo('schedule-employee-performance-reviews');
    }

    /**
     * Determine whether the user can export reviews.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can import reviews.
     */
    public function import(User $user): bool
    {
        return $user->hasPermissionTo('import-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can generate reports.
     */
    public function generateReport(User $user): bool
    {
        return $user->hasPermissionTo('generate-report-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager', 'manager']);
    }

    /**
     * Determine whether the user can view statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('view-statistics-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager', 'manager']);
    }

    /**
     * Determine whether the user can send reminders.
     */
    public function sendReminders(User $user): bool
    {
        return $user->hasPermissionTo('send-reminders-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can perform bulk approve operations.
     */
    public function bulkApprove(User $user): bool
    {
        return $user->hasPermissionTo('bulk-approve-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can perform bulk reject operations.
     */
    public function bulkReject(User $user): bool
    {
        return $user->hasPermissionTo('bulk-reject-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can view employee-specific reviews.
     */
    public function viewEmployeeReviews(User $user, int $employeeId): bool
    {
        // Admin and HR managers can view any employee's reviews
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can view reviews of employees in their department
        if ($user->hasRole('manager')) {
            return $this->isEmployeeInUserDepartment($user, $employeeId);
        }

        // Users can view their own reviews
        if ($user->id === $employeeId) {
            return true;
        }

        return $user->hasPermissionTo('view-employee-performance-reviews');
    }

    /**
     * Determine whether the user can create reviews for specific employees.
     */
    public function createForEmployee(User $user, int $employeeId): bool
    {
        // Admin and HR managers can create reviews for any employee
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can create reviews for employees in their department
        if ($user->hasRole('manager')) {
            return $this->isEmployeeInUserDepartment($user, $employeeId);
        }

        return $user->hasPermissionTo('create-employee-performance-reviews');
    }

    /**
     * Determine whether the user can view department statistics.
     */
    public function viewDepartmentStatistics(User $user, int $departmentId): bool
    {
        // Admin and HR managers can view any department's statistics
        if ($user->hasRole(['admin', 'hr_manager'])) {
            return true;
        }

        // Managers can view statistics for their own department
        if ($user->hasRole('manager')) {
            return $this->isUserInDepartment($user, $departmentId);
        }

        return $user->hasPermissionTo('view-statistics-employee-performance-reviews');
    }

    /**
     * Determine whether the user can view company-wide statistics.
     */
    public function viewCompanyStatistics(User $user): bool
    {
        return $user->hasPermissionTo('view-statistics-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can manage review templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->hasPermissionTo('manage-templates-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can manage review workflows.
     */
    public function manageWorkflows(User $user): bool
    {
        return $user->hasPermissionTo('manage-workflows-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can access advanced analytics.
     */
    public function accessAdvancedAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('access-advanced-analytics-employee-performance-reviews') ||
               $user->hasRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can manage review settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->hasPermissionTo('manage-settings-employee-performance-reviews') ||
               $user->hasRole(['admin']);
    }

    /**
     * Check if a review can be edited.
     */
    protected function canEditReview(EmployeePerformanceReview $review): bool
    {
        return $review->isEditable() && ! $review->isFinal();
    }

    /**
     * Check if a review is in the user's department.
     */
    protected function isInUserDepartment(User $user, EmployeePerformanceReview $review): bool
    {
        // This would need to be implemented based on your department structure
        // For now, return true for managers
        return true;
    }

    /**
     * Check if an employee is in the user's department.
     */
    protected function isEmployeeInUserDepartment(User $user, int $employeeId): bool
    {
        // This would need to be implemented based on your department structure
        // For now, return true for managers
        return true;
    }

    /**
     * Check if the user is in a specific department.
     */
    protected function isUserInDepartment(User $user, int $departmentId): bool
    {
        // This would need to be implemented based on your department structure
        // For now, return true for managers
        return true;
    }

    /**
     * Get the permissions required for each action.
     */
    public static function getRequiredPermissions(): array
    {
        return [
            'view-any-employee-performance-reviews',
            'view-employee-performance-reviews',
            'create-employee-performance-reviews',
            'update-employee-performance-reviews',
            'delete-employee-performance-reviews',
            'submit-employee-performance-reviews',
            'approve-employee-performance-reviews',
            'reject-employee-performance-reviews',
            'assign-reviewer-employee-performance-reviews',
            'schedule-employee-performance-reviews',
            'export-employee-performance-reviews',
            'import-employee-performance-reviews',
            'generate-report-employee-performance-reviews',
            'view-statistics-employee-performance-reviews',
            'send-reminders-employee-performance-reviews',
            'bulk-approve-employee-performance-reviews',
            'bulk-reject-employee-performance-reviews',
            'manage-templates-employee-performance-reviews',
            'manage-workflows-employee-performance-reviews',
            'access-advanced-analytics-employee-performance-reviews',
            'manage-settings-employee-performance-reviews',
        ];
    }

    /**
     * Get the roles that have access to performance reviews.
     */
    public static function getAccessRoles(): array
    {
        return [
            'admin' => [
                'all' => true,
                'description' => 'Full access to all performance review functionality',
            ],
            'hr_manager' => [
                'all' => true,
                'description' => 'Full access to all performance review functionality',
            ],
            'manager' => [
                'department_only' => true,
                'description' => 'Access to performance reviews within their department',
            ],
            'employee' => [
                'own_only' => true,
                'description' => 'Access to their own performance reviews only',
            ],
            'reviewer' => [
                'assigned_only' => true,
                'description' => 'Access to reviews they are assigned to review',
            ],
        ];
    }

    /**
     * Get the default permissions for each role.
     */
    public static function getDefaultRolePermissions(): array
    {
        return [
            'admin' => self::getRequiredPermissions(),
            'hr_manager' => self::getRequiredPermissions(),
            'manager' => [
                'view-any-employee-performance-reviews',
                'view-employee-performance-reviews',
                'create-employee-performance-reviews',
                'update-employee-performance-reviews',
                'submit-employee-performance-reviews',
                'approve-employee-performance-reviews',
                'reject-employee-performance-reviews',
                'assign-reviewer-employee-performance-reviews',
                'schedule-employee-performance-reviews',
                'generate-report-employee-performance-reviews',
                'view-statistics-employee-performance-reviews',
            ],
            'employee' => [
                'view-employee-performance-reviews',
                'update-employee-performance-reviews',
            ],
            'reviewer' => [
                'view-employee-performance-reviews',
                'update-employee-performance-reviews',
                'submit-employee-performance-reviews',
            ],
        ];
    }
}
