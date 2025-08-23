<?php

namespace App\Traits;

use App\Models\EmployeeDepartment;
use App\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasEmployeeDepartmentAnalytics
{
    /**
     * Get department statistics
     */
    public function getDepartmentStatistics(): array
    {
        try {
            $totalDepartments = EmployeeDepartment::count();
            $activeDepartments = EmployeeDepartment::where('is_active', true)->count();
            $inactiveDepartments = EmployeeDepartment::where('is_active', false)->count();

            $departmentsWithManagers = EmployeeDepartment::whereNotNull('manager_id')->count();
            $departmentsWithoutManagers = $totalDepartments - $departmentsWithManagers;

            $rootDepartments = EmployeeDepartment::whereNull('parent_id')->count();
            $childDepartments = $totalDepartments - $rootDepartments;

            return [
                'total_departments' => $totalDepartments,
                'active_departments' => $activeDepartments,
                'inactive_departments' => $inactiveDepartments,
                'departments_with_managers' => $departmentsWithManagers,
                'departments_without_managers' => $departmentsWithoutManagers,
                'root_departments' => $rootDepartments,
                'child_departments' => $childDepartments,
                'hierarchy_depth' => $this->calculateHierarchyDepth(),
                'average_employees_per_department' => $this->calculateAverageEmployeesPerDepartment(),
                'total_budget' => $this->getTotalBudget(),
                'budget_utilization' => $this->getOverallBudgetUtilization()
            ];
        } catch (\Exception $e) {
            return [
                'total_departments' => 0,
                'active_departments' => 0,
                'inactive_departments' => 0,
                'departments_with_managers' => 0,
                'departments_without_managers' => 0,
                'root_departments' => 0,
                'child_departments' => 0,
                'hierarchy_depth' => 0,
                'average_employees_per_department' => 0,
                'total_budget' => 0,
                'budget_utilization' => []
            ];
        }
    }

    /**
     * Get department performance metrics
     */
    public function getDepartmentPerformanceMetrics(int $departmentId): array
    {
        try {
            $department = $this->find($departmentId);
            if (!$department) {
                return [];
            }

            $employeeCount = $this->getDepartmentEmployeeCount($departmentId);
            $budgetUtilization = $this->getDepartmentBudgetUtilization($departmentId);
            $headcountUtilization = $this->getDepartmentHeadcountUtilization($departmentId);

            return [
                'department_id' => $departmentId,
                'department_name' => $department->name,
                'employee_count' => $employeeCount,
                'budget_utilization' => $budgetUtilization,
                'headcount_utilization' => $headcountUtilization,
                'efficiency_score' => $this->calculateEfficiencyScore($departmentId),
                'growth_rate' => $this->calculateGrowthRate($departmentId),
                'turnover_rate' => $this->calculateTurnoverRate($departmentId),
                'productivity_metrics' => $this->getProductivityMetrics($departmentId),
                'cost_per_employee' => $this->calculateCostPerEmployee($departmentId),
                'revenue_per_employee' => $this->calculateRevenuePerEmployee($departmentId)
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get department trends
     */
    public function getDepartmentTrends(string $startDate = null, string $endDate = null): array
    {
        try {
            $startDate = $startDate ?? now()->subMonths(6)->format('Y-m-d');
            $endDate = $endDate ?? now()->format('Y-m-d');

            return [
                'employee_growth' => $this->getEmployeeGrowthTrend($startDate, $endDate),
                'budget_changes' => $this->getBudgetChangeTrend($startDate, $endDate),
                'department_creation' => $this->getDepartmentCreationTrend($startDate, $endDate),
                'manager_changes' => $this->getManagerChangeTrend($startDate, $endDate),
                'hierarchy_changes' => $this->getHierarchyChangeTrend($startDate, $endDate)
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate hierarchy depth
     */
    protected function calculateHierarchyDepth(): int
    {
        try {
            $maxDepth = 0;
            $departments = EmployeeDepartment::all();

            foreach ($departments as $department) {
                $depth = $this->calculateDepartmentDepth($department->id);
                $maxDepth = max($maxDepth, $depth);
            }

            return $maxDepth;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate depth of a specific department
     */
    protected function calculateDepartmentDepth(int $departmentId): int
    {
        try {
            $depth = 0;
            $currentDepartment = $this->find($departmentId);

            while ($currentDepartment && $currentDepartment->parent_id) {
                $depth++;
                $currentDepartment = $this->find($currentDepartment->parent_id);
            }

            return $depth;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate average employees per department
     */
    protected function calculateAverageEmployeesPerDepartment(): float
    {
        try {
            $totalEmployees = $this->getTotalEmployeeCount();
            $totalDepartments = EmployeeDepartment::count();

            return $totalDepartments > 0 ? round($totalEmployees / $totalDepartments, 2) : 0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate efficiency score for department
     */
    protected function calculateEfficiencyScore(int $departmentId): float
    {
        try {
            $budgetUtilization = $this->getDepartmentBudgetUtilization($departmentId);
            $headcountUtilization = $this->getDepartmentHeadcountUtilization($departmentId);

            // Simple efficiency calculation based on utilization ratios
            $budgetScore = min(100, $budgetUtilization);
            $headcountScore = min(100, $headcountUtilization);

            return round(($budgetScore + $headcountScore) / 2, 2);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate growth rate for department
     */
    protected function calculateGrowthRate(int $departmentId): float
    {
        try {
            // This would typically involve historical employee count data
            // For now, return a placeholder value
            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate turnover rate for department
     */
    protected function calculateTurnoverRate(int $departmentId): float
    {
        try {
            // This would typically involve historical employee data
            // For now, return a placeholder value
            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get productivity metrics for department
     */
    protected function getProductivityMetrics(int $departmentId): array
    {
        try {
            // This would typically involve various productivity indicators
            // For now, return placeholder data
            return [
                'projects_completed' => 0,
                'tasks_completed' => 0,
                'average_task_completion_time' => 0,
                'quality_score' => 0,
                'customer_satisfaction' => 0
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate cost per employee for department
     */
    protected function calculateCostPerEmployee(int $departmentId): float
    {
        try {
            $employeeCount = $this->getDepartmentEmployeeCount($departmentId);
            if ($employeeCount <= 0) {
                return 0.0;
            }

            $utilizedBudget = $this->calculateUtilizedBudget($departmentId);
            return round($utilizedBudget / $employeeCount, 2);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate revenue per employee for department
     */
    protected function calculateRevenuePerEmployee(int $departmentId): float
    {
        try {
            // This would typically involve revenue data
            // For now, return a placeholder value
            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get employee growth trend
     */
    protected function getEmployeeGrowthTrend(string $startDate, string $endDate): array
    {
        try {
            // This would typically involve historical employee count data
            // For now, return placeholder data
            return [
                'trend' => 'stable',
                'growth_rate' => 0.0,
                'monthly_data' => []
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get budget change trend
     */
    protected function getBudgetChangeTrend(string $startDate, string $endDate): array
    {
        try {
            // This would typically involve historical budget data
            // For now, return placeholder data
            return [
                'trend' => 'stable',
                'change_rate' => 0.0,
                'monthly_data' => []
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get department creation trend
     */
    protected function getDepartmentCreationTrend(string $startDate, string $endDate): array
    {
        try {
            $departments = EmployeeDepartment::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'total_created' => $departments->sum('count'),
                'daily_data' => $departments->toArray(),
                'average_per_day' => $departments->count() > 0 ? round($departments->sum('count') / $departments->count(), 2) : 0
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get manager change trend
     */
    protected function getManagerChangeTrend(string $startDate, string $endDate): array
    {
        try {
            // This would typically involve tracking manager assignment changes
            // For now, return placeholder data
            return [
                'total_changes' => 0,
                'daily_data' => [],
                'average_per_day' => 0
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get hierarchy change trend
     */
    protected function getHierarchyChangeTrend(string $startDate, string $endDate): array
    {
        try {
            // This would typically involve tracking parent_id changes
            // For now, return placeholder data
            return [
                'total_changes' => 0,
                'daily_data' => [],
                'average_per_day' => 0
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Generate department report
     */
    public function generateDepartmentReport(int $departmentId): array
    {
        try {
            $department = $this->find($departmentId);
            if (!$department) {
                return [];
            }

            return [
                'basic_info' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'code' => $department->code,
                    'status' => $department->status,
                    'created_at' => $department->created_at,
                    'updated_at' => $department->updated_at
                ],
                'hierarchy_info' => [
                    'parent_id' => $department->parent_id,
                    'manager_id' => $department->manager_id,
                    'depth' => $this->calculateDepartmentDepth($departmentId),
                    'children_count' => $this->findByParentId($departmentId)->count()
                ],
                'performance_metrics' => $this->getDepartmentPerformanceMetrics($departmentId),
                'budget_info' => $this->getBudgetSummary($departmentId),
                'employee_info' => [
                    'current_count' => $this->getDepartmentEmployeeCount($departmentId),
                    'headcount_limit' => $department->headcount_limit,
                    'utilization' => $this->getDepartmentHeadcountUtilization($departmentId)
                ]
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Export department analytics data
     */
    public function exportDepartmentAnalytics(array $filters = []): string
    {
        try {
            $data = [
                'summary' => $this->getDepartmentStatistics(),
                'departments' => [],
                'exported_at' => now()->toISOString()
            ];

            $departments = EmployeeDepartment::all();
            foreach ($departments as $department) {
                $data['departments'][] = $this->generateDepartmentReport($department->id);
            }

            return json_encode($data, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return json_encode(['error' => 'Failed to export analytics data']);
        }
    }
}
