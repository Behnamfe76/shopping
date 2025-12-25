<?php

namespace App\Policies;

use App\Models\ProviderPerformance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProviderPerformancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any provider performances.
     */
    public function viewAny(User $user): Response
    {
        // Check if user has permission to view provider performances
        if ($user->hasPermissionTo('view provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can view provider performances
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view provider performances.');
    }

    /**
     * Determine whether the user can view the provider performance.
     */
    public function view(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to view provider performances
        if ($user->hasPermissionTo('view provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can view provider performances
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        // Check if user is the provider owner (if applicable)
        if ($user->id === $providerPerformance->provider->user_id ?? null) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this provider performance.');
    }

    /**
     * Determine whether the user can create provider performances.
     */
    public function create(User $user): Response
    {
        // Check if user has permission to create provider performances
        if ($user->hasPermissionTo('create provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can create provider performances
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to create provider performances.');
    }

    /**
     * Determine whether the user can update the provider performance.
     */
    public function update(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to update provider performances
        if ($user->hasPermissionTo('update provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can update provider performances
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        // Check if user is the creator of the performance record
        if ($user->id === $providerPerformance->created_by ?? null) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update this provider performance.');
    }

    /**
     * Determine whether the user can delete the provider performance.
     */
    public function delete(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to delete provider performances
        if ($user->hasPermissionTo('delete provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can delete provider performances
        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to delete provider performances.');
    }

    /**
     * Determine whether the user can restore the provider performance.
     */
    public function restore(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to restore provider performances
        if ($user->hasPermissionTo('restore provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can restore provider performances
        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to restore provider performances.');
    }

    /**
     * Determine whether the user can permanently delete the provider performance.
     */
    public function forceDelete(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to permanently delete provider performances
        if ($user->hasPermissionTo('force delete provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can permanently delete provider performances
        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to permanently delete provider performances.');
    }

    /**
     * Determine whether the user can verify the provider performance.
     */
    public function verify(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to verify provider performances
        if ($user->hasPermissionTo('verify provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can verify provider performances
        if ($user->hasRole(['admin', 'manager', 'supervisor'])) {
            return Response::allow();
        }

        // Check if user is a quality assurance specialist
        if ($user->hasRole(['qa_specialist', 'quality_analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to verify provider performances.');
    }

    /**
     * Determine whether the user can unverify the provider performance.
     */
    public function unverify(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to unverify provider performances
        if ($user->hasPermissionTo('unverify provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can unverify provider performances
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        // Check if user is the one who verified it
        if ($user->id === $providerPerformance->verified_by) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to unverify this provider performance.');
    }

    /**
     * Determine whether the user can calculate performance scores.
     */
    public function calculate(User $user, ProviderPerformance $providerPerformance): Response
    {
        // Check if user has permission to calculate performance scores
        if ($user->hasPermissionTo('calculate provider performance')) {
            return Response::allow();
        }

        // Check if user has role that can calculate performance scores
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to calculate performance scores.');
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user): Response
    {
        // Check if user has permission to view analytics
        if ($user->hasPermissionTo('view provider performance analytics')) {
            return Response::allow();
        }

        // Check if user has role that can view analytics
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance analytics.');
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user): Response
    {
        // Check if user has permission to view reports
        if ($user->hasPermissionTo('view provider performance reports')) {
            return Response::allow();
        }

        // Check if user has role that can view reports
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance reports.');
    }

    /**
     * Determine whether the user can view alerts.
     */
    public function viewAlerts(User $user): Response
    {
        // Check if user has permission to view alerts
        if ($user->hasPermissionTo('view provider performance alerts')) {
            return Response::allow();
        }

        // Check if user has role that can view alerts
        if ($user->hasRole(['admin', 'manager', 'analyst', 'supervisor'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance alerts.');
    }

    /**
     * Determine whether the user can search performances.
     */
    public function search(User $user): Response
    {
        // Check if user has permission to search performances
        if ($user->hasPermissionTo('search provider performances')) {
            return Response::allow();
        }

        // Check if user has role that can search performances
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to search provider performances.');
    }

    /**
     * Determine whether the user can export performance data.
     */
    public function export(User $user): Response
    {
        // Check if user has permission to export performance data
        if ($user->hasPermissionTo('export provider performance data')) {
            return Response::allow();
        }

        // Check if user has role that can export performance data
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to export performance data.');
    }

    /**
     * Determine whether the user can import performance data.
     */
    public function import(User $user): Response
    {
        // Check if user has permission to import performance data
        if ($user->hasPermissionTo('import provider performance data')) {
            return Response::allow();
        }

        // Check if user has role that can import performance data
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to import performance data.');
    }

    /**
     * Determine whether the user can manage performance benchmarks.
     */
    public function manageBenchmarks(User $user): Response
    {
        // Check if user has permission to manage benchmarks
        if ($user->hasPermissionTo('manage provider performance benchmarks')) {
            return Response::allow();
        }

        // Check if user has role that can manage benchmarks
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance benchmarks.');
    }

    /**
     * Determine whether the user can manage performance alerts.
     */
    public function manageAlerts(User $user): Response
    {
        // Check if user has permission to manage alerts
        if ($user->hasPermissionTo('manage provider performance alerts')) {
            return Response::allow();
        }

        // Check if user has role that can manage alerts
        if ($user->hasRole(['admin', 'manager', 'supervisor'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance alerts.');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): Response
    {
        // Check if user has permission to perform bulk operations
        if ($user->hasPermissionTo('bulk operations provider performance')) {
            return Response::allow();
        }

        // Check if user has role that can perform bulk operations
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform bulk operations.');
    }

    /**
     * Determine whether the user can view performance history.
     */
    public function viewHistory(User $user): Response
    {
        // Check if user has permission to view performance history
        if ($user->hasPermissionTo('view provider performance history')) {
            return Response::allow();
        }

        // Check if user has role that can view performance history
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance history.');
    }

    /**
     * Determine whether the user can view performance trends.
     */
    public function viewTrends(User $user): Response
    {
        // Check if user has permission to view performance trends
        if ($user->hasPermissionTo('view provider performance trends')) {
            return Response::allow();
        }

        // Check if user has role that can view performance trends
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance trends.');
    }

    /**
     * Determine whether the user can view performance comparisons.
     */
    public function viewComparisons(User $user): Response
    {
        // Check if user has permission to view performance comparisons
        if ($user->hasPermissionTo('view provider performance comparisons')) {
            return Response::allow();
        }

        // Check if user has role that can view performance comparisons
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance comparisons.');
    }

    /**
     * Determine whether the user can view performance forecasts.
     */
    public function viewForecasts(User $user): Response
    {
        // Check if user has permission to view performance forecasts
        if ($user->hasPermissionTo('view provider performance forecasts')) {
            return Response::allow();
        }

        // Check if user has role that can view performance forecasts
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance forecasts.');
    }

    /**
     * Determine whether the user can manage performance settings.
     */
    public function manageSettings(User $user): Response
    {
        // Check if user has permission to manage performance settings
        if ($user->hasPermissionTo('manage provider performance settings')) {
            return Response::allow();
        }

        // Check if user has role that can manage performance settings
        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance settings.');
    }

    /**
     * Determine whether the user can view performance dashboard.
     */
    public function viewDashboard(User $user): Response
    {
        // Check if user has permission to view performance dashboard
        if ($user->hasPermissionTo('view provider performance dashboard')) {
            return Response::allow();
        }

        // Check if user has role that can view performance dashboard
        if ($user->hasRole(['admin', 'manager', 'analyst', 'supervisor'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance dashboard.');
    }

    /**
     * Determine whether the user can manage performance notifications.
     */
    public function manageNotifications(User $user): Response
    {
        // Check if user has permission to manage performance notifications
        if ($user->hasPermissionTo('manage provider performance notifications')) {
            return Response::allow();
        }

        // Check if user has role that can manage performance notifications
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance notifications.');
    }

    /**
     * Determine whether the user can view performance audit logs.
     */
    public function viewAuditLogs(User $user): Response
    {
        // Check if user has permission to view performance audit logs
        if ($user->hasPermissionTo('view provider performance audit logs')) {
            return Response::allow();
        }

        // Check if user has role that can view performance audit logs
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance audit logs.');
    }

    /**
     * Determine whether the user can manage performance workflows.
     */
    public function manageWorkflows(User $user): Response
    {
        // Check if user has permission to manage performance workflows
        if ($user->hasPermissionTo('manage provider performance workflows')) {
            return Response::allow();
        }

        // Check if user has role that can manage performance workflows
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance workflows.');
    }

    /**
     * Determine whether the user can approve performance changes.
     */
    public function approveChanges(User $user): Response
    {
        // Check if user has permission to approve performance changes
        if ($user->hasPermissionTo('approve provider performance changes')) {
            return Response::allow();
        }

        // Check if user has role that can approve performance changes
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to approve performance changes.');
    }

    /**
     * Determine whether the user can reject performance changes.
     */
    public function rejectChanges(User $user): Response
    {
        // Check if user has permission to reject performance changes
        if ($user->hasPermissionTo('reject provider performance changes')) {
            return Response::allow();
        }

        // Check if user has role that can reject performance changes
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to reject performance changes.');
    }

    /**
     * Determine whether the user can override performance calculations.
     */
    public function overrideCalculations(User $user): Response
    {
        // Check if user has permission to override performance calculations
        if ($user->hasPermissionTo('override provider performance calculations')) {
            return Response::allow();
        }

        // Check if user has role that can override performance calculations
        if ($user->hasRole(['admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to override performance calculations.');
    }

    /**
     * Determine whether the user can manage performance thresholds.
     */
    public function manageThresholds(User $user): Response
    {
        // Check if user has permission to manage performance thresholds
        if ($user->hasPermissionTo('manage provider performance thresholds')) {
            return Response::allow();
        }

        // Check if user has role that can manage performance thresholds
        if ($user->hasRole(['admin', 'manager'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage performance thresholds.');
    }

    /**
     * Determine whether the user can view performance insights.
     */
    public function viewInsights(User $user): Response
    {
        // Check if user has permission to view performance insights
        if ($user->hasPermissionTo('view provider performance insights')) {
            return Response::allow();
        }

        // Check if user has role that can view performance insights
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view performance insights.');
    }

    /**
     * Determine whether the user can generate performance recommendations.
     */
    public function generateRecommendations(User $user): Response
    {
        // Check if user has permission to generate performance recommendations
        if ($user->hasPermissionTo('generate provider performance recommendations')) {
            return Response::allow();
        }

        // Check if user has role that can generate performance recommendations
        if ($user->hasRole(['admin', 'manager', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to generate performance recommendations.');
    }
}
