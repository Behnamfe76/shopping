<?php

namespace App\Traits;

use App\Models\EmployeeDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasEmployeeDepartmentBudgetManagement
{
    /**
     * Get department budget
     */
    public function getDepartmentBudget(int $departmentId): float
    {
        try {
            $cacheKey = "department_budget_{$departmentId}";

            return Cache::remember($cacheKey, 3600, function () use ($departmentId) {
                $department = $this->find($departmentId);

                return $department ? (float) $department->budget : 0.0;
            });
        } catch (\Exception $e) {
            Log::error('Error getting department budget', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Set department budget
     */
    public function setDepartmentBudget(int $departmentId, float $budget): bool
    {
        try {
            $department = $this->find($departmentId);
            if (! $department) {
                return false;
            }

            $department->budget = $budget;
            $result = $department->save();

            if ($result) {
                Cache::forget("department_budget_{$departmentId}");
                Cache::forget("department_budget_utilization_{$departmentId}");

                // Log budget change
                Log::info('Department budget updated', [
                    'department_id' => $departmentId,
                    'old_budget' => $department->getOriginal('budget'),
                    'new_budget' => $budget,
                    'updated_by' => Auth::id(),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error setting department budget', [
                'department_id' => $departmentId,
                'budget' => $budget,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get department budget utilization percentage
     */
    public function getDepartmentBudgetUtilization(int $departmentId): float
    {
        try {
            $cacheKey = "department_budget_utilization_{$departmentId}";

            return Cache::remember($cacheKey, 1800, function () use ($departmentId) {
                $department = $this->find($departmentId);
                if (! $department || $department->budget <= 0) {
                    return 0.0;
                }

                $utilizedBudget = $this->calculateUtilizedBudget($departmentId);

                return min(100.0, ($utilizedBudget / $department->budget) * 100);
            });
        } catch (\Exception $e) {
            Log::error('Error calculating budget utilization', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Calculate utilized budget for a department
     */
    protected function calculateUtilizedBudget(int $departmentId): float
    {
        try {
            // This would typically involve calculating from various sources:
            // - Employee salaries
            // - Department expenses
            // - Project costs
            // - Equipment costs
            // For now, we'll use a placeholder calculation

            $department = $this->find($departmentId);
            if (! $department) {
                return 0.0;
            }

            // Placeholder: calculate based on employee count and average salary
            $employeeCount = $this->getDepartmentEmployeeCount($departmentId);
            $averageSalary = 50000; // This should come from actual employee data

            return $employeeCount * $averageSalary;
        } catch (\Exception $e) {
            Log::error('Error calculating utilized budget', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Check if department is over budget
     */
    public function isDepartmentOverBudget(int $departmentId): bool
    {
        $utilization = $this->getDepartmentBudgetUtilization($departmentId);

        return $utilization > 100.0;
    }

    /**
     * Check if department is approaching budget limit
     */
    public function isDepartmentApproachingBudgetLimit(int $departmentId, float $threshold = 80.0): bool
    {
        $utilization = $this->getDepartmentBudgetUtilization($departmentId);

        return $utilization >= $threshold;
    }

    /**
     * Get remaining budget for department
     */
    public function getRemainingBudget(int $departmentId): float
    {
        try {
            $department = $this->find($departmentId);
            if (! $department) {
                return 0.0;
            }

            $utilizedBudget = $this->calculateUtilizedBudget($departmentId);

            return max(0.0, $department->budget - $utilizedBudget);
        } catch (\Exception $e) {
            Log::error('Error calculating remaining budget', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get budget summary for department
     */
    public function getBudgetSummary(int $departmentId): array
    {
        try {
            $department = $this->find($departmentId);
            if (! $department) {
                return [
                    'total_budget' => 0.0,
                    'utilized_budget' => 0.0,
                    'remaining_budget' => 0.0,
                    'utilization_percentage' => 0.0,
                    'is_over_budget' => false,
                    'is_approaching_limit' => false,
                ];
            }

            $utilizedBudget = $this->calculateUtilizedBudget($departmentId);
            $remainingBudget = max(0.0, $department->budget - $utilizedBudget);
            $utilizationPercentage = $department->budget > 0 ? ($utilizedBudget / $department->budget) * 100 : 0;

            return [
                'total_budget' => (float) $department->budget,
                'utilized_budget' => $utilizedBudget,
                'remaining_budget' => $remainingBudget,
                'utilization_percentage' => min(100.0, $utilizationPercentage),
                'is_over_budget' => $utilizationPercentage > 100.0,
                'is_approaching_limit' => $utilizationPercentage >= 80.0,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting budget summary', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'total_budget' => 0.0,
                'utilized_budget' => 0.0,
                'remaining_budget' => 0.0,
                'utilization_percentage' => 0.0,
                'is_over_budget' => false,
                'is_approaching_limit' => false,
            ];
        }
    }

    /**
     * Get budget trends for department
     */
    public function getBudgetTrends(int $departmentId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            // This would typically involve historical budget data
            // For now, return placeholder data
            return [
                'monthly_spending' => [],
                'budget_variance' => [],
                'forecast' => [],
                'recommendations' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Error getting budget trends', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Update budget allocation
     */
    public function updateBudgetAllocation(int $departmentId, float $newBudget, string $reason = ''): bool
    {
        try {
            $department = $this->find($departmentId);
            if (! $department) {
                return false;
            }

            $oldBudget = $department->budget;
            $result = $this->setDepartmentBudget($departmentId, $newBudget);

            if ($result) {
                // Log budget allocation change
                Log::info('Department budget allocation updated', [
                    'department_id' => $departmentId,
                    'old_budget' => $oldBudget,
                    'new_budget' => $newBudget,
                    'reason' => $reason,
                    'updated_by' => Auth::id(),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error updating budget allocation', [
                'department_id' => $departmentId,
                'new_budget' => $newBudget,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get total budget across all departments
     */
    public function getTotalBudget(): float
    {
        try {
            return Cache::remember('total_department_budget', 3600, function () {
                return EmployeeDepartment::sum('budget');
            });
        } catch (\Exception $e) {
            Log::error('Error getting total budget', [
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get budget utilization across all departments
     */
    public function getOverallBudgetUtilization(): array
    {
        try {
            $departments = EmployeeDepartment::all();
            $totalBudget = 0.0;
            $totalUtilized = 0.0;

            foreach ($departments as $department) {
                $totalBudget += $department->budget;
                $totalUtilized += $this->calculateUtilizedBudget($department->id);
            }

            return [
                'total_budget' => $totalBudget,
                'total_utilized' => $totalUtilized,
                'overall_utilization' => $totalBudget > 0 ? ($totalUtilized / $totalBudget) * 100 : 0,
                'departments_count' => $departments->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting overall budget utilization', [
                'error' => $e->getMessage(),
            ]);

            return [
                'total_budget' => 0.0,
                'total_utilized' => 0.0,
                'overall_utilization' => 0.0,
                'departments_count' => 0,
            ];
        }
    }
}
