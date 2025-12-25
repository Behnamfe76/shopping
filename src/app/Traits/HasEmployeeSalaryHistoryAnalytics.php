<?php

namespace App\Traits;

use App\Repositories\Interfaces\EmployeeSalaryHistoryRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

trait HasEmployeeSalaryHistoryAnalytics
{
    /**
     * Get comprehensive salary analytics for employee
     */
    public function getEmployeeSalaryAnalytics(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);

            $analytics = [
                'employee_id' => $employeeId,
                'current_salary' => $repository->getEmployeeCurrentSalary($employeeId),
                'starting_salary' => $repository->getEmployeeStartingSalary($employeeId),
                'total_increases' => $repository->getEmployeeTotalSalaryIncrease($employeeId, $startDate, $endDate),
                'total_decreases' => $repository->getEmployeeTotalSalaryDecrease($employeeId, $startDate, $endDate),
                'average_change' => $repository->getEmployeeAverageSalaryChange($employeeId),
                'growth_percentage' => $repository->getEmployeeSalaryGrowth($employeeId),
                'history_count' => $repository->getEmployeeSalaryHistoryCount($employeeId),
                'by_change_type' => $this->getEmployeeSalaryAnalyticsByChangeType($employeeId, $startDate, $endDate),
                'by_year' => $this->getEmployeeSalaryAnalyticsByYear($employeeId, $startDate, $endDate),
                'trends' => $this->getEmployeeSalaryTrends($employeeId, $startDate, $endDate),
                'comparison' => $this->getEmployeeSalaryComparison($employeeId, $startDate, $endDate),
            ];

            return $analytics;

        } catch (\Exception $e) {
            Log::error('Failed to get employee salary analytics via trait', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId,
            ]);
            throw $e;
        }
    }

    /**
     * Get company-wide salary analytics
     */
    public function getCompanySalaryAnalytics(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);

            $analytics = [
                'total_employees' => $this->getTotalEmployeesWithSalaryHistory(),
                'total_changes' => $repository->getTotalSalaryHistoryCount(),
                'total_increases' => $repository->getTotalSalaryIncrease($startDate, $endDate),
                'total_decreases' => $repository->getTotalSalaryDecrease($startDate, $endDate),
                'average_change' => $repository->getAverageSalaryChange(),
                'by_change_type' => $this->getCompanySalaryAnalyticsByChangeType($startDate, $endDate),
                'by_department' => $this->getCompanySalaryAnalyticsByDepartment($startDate, $endDate),
                'by_year' => $this->getCompanySalaryAnalyticsByYear($startDate, $endDate),
                'trends' => $this->getCompanySalaryTrends($startDate, $endDate),
                'percentiles' => $this->getCompanySalaryPercentiles($startDate, $endDate),
            ];

            return $analytics;

        } catch (\Exception $e) {
            Log::error('Failed to get company salary analytics via trait', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get department salary analytics
     */
    public function getDepartmentSalaryAnalytics(int $departmentId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);

            $analytics = [
                'department_id' => $departmentId,
                'total_employees' => $this->getTotalEmployeesInDepartment($departmentId),
                'total_changes' => $repository->getDepartmentSalaryStatistics($departmentId, $startDate, $endDate)['total_changes'] ?? 0,
                'total_increases' => $repository->getDepartmentSalaryStatistics($departmentId, $startDate, $endDate)['total_increase_amount'] ?? 0,
                'total_decreases' => $repository->getDepartmentSalaryStatistics($departmentId, $startDate, $endDate)['total_decrease_amount'] ?? 0,
                'average_change' => $repository->getDepartmentSalaryStatistics($departmentId, $startDate, $endDate)['average_change'] ?? 0,
                'by_change_type' => $this->getDepartmentSalaryAnalyticsByChangeType($departmentId, $startDate, $endDate),
                'by_employee' => $this->getDepartmentSalaryAnalyticsByEmployee($departmentId, $startDate, $endDate),
                'trends' => $this->getDepartmentSalaryTrends($departmentId, $startDate, $endDate),
            ];

            return $analytics;

        } catch (\Exception $e) {
            Log::error('Failed to get department salary analytics via trait', [
                'error' => $e->getMessage(),
                'department_id' => $departmentId,
            ]);
            throw $e;
        }
    }

    /**
     * Get salary growth analysis
     */
    public function getSalaryGrowthAnalysis(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
            $allChanges = $repository->all();

            if ($startDate && $endDate) {
                $allChanges = $allChanges->filter(function ($change) use ($startDate, $endDate) {
                    return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
                });
            }

            $growthAnalysis = [
                'total_employees' => $allChanges->unique('employee_id')->count(),
                'employees_with_increases' => $allChanges->where('change_amount', '>', 0)->unique('employee_id')->count(),
                'employees_with_decreases' => $allChanges->where('change_amount', '<', 0)->unique('employee_id')->count(),
                'average_growth_rate' => $allChanges->where('change_amount', '>', 0)->avg('change_percentage'),
                'average_decline_rate' => abs($allChanges->where('change_amount', '<', 0)->avg('change_percentage')),
                'growth_distribution' => $this->getGrowthDistribution($allChanges),
                'top_growers' => $this->getTopSalaryGrowers($allChanges, 10),
                'growth_trends' => $this->getGrowthTrends($allChanges),
            ];

            return $growthAnalysis;

        } catch (\Exception $e) {
            Log::error('Failed to get salary growth analysis via trait', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get salary comparison between employees
     */
    public function getSalaryComparison(int $employeeId1, int $employeeId2): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);

            $comparison = [
                'employee1' => $this->getEmployeeSalaryAnalytics($employeeId1),
                'employee2' => $this->getEmployeeSalaryAnalytics($employeeId2),
                'comparison' => [
                    'salary_difference' => $repository->getEmployeeCurrentSalary($employeeId1) - $repository->getEmployeeCurrentSalary($employeeId2),
                    'growth_difference' => $repository->getEmployeeSalaryGrowth($employeeId1) - $repository->getEmployeeSalaryGrowth($employeeId2),
                    'change_frequency_comparison' => $this->compareChangeFrequency($employeeId1, $employeeId2),
                ],
            ];

            return $comparison;

        } catch (\Exception $e) {
            Log::error('Failed to get salary comparison via trait', [
                'error' => $e->getMessage(),
                'employee1' => $employeeId1,
                'employee2' => $employeeId2,
            ]);
            throw $e;
        }
    }

    /**
     * Get salary forecasting data
     */
    public function getSalaryForecastingData(int $employeeId, int $months = 12): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
            $currentSalary = $repository->getEmployeeCurrentSalary($employeeId);
            $growthRate = $repository->getEmployeeSalaryGrowth($employeeId);
            $changeFrequency = $repository->getEmployeeSalaryHistoryCount($employeeId);

            $forecast = [];
            $projectedSalary = $currentSalary;

            for ($month = 1; $month <= $months; $month++) {
                $monthlyGrowthRate = $growthRate / 12; // Annual growth divided by 12 months
                $projectedSalary = $projectedSalary * (1 + ($monthlyGrowthRate / 100));

                $forecast[] = [
                    'month' => $month,
                    'projected_salary' => round($projectedSalary, 2),
                    'monthly_growth' => round($monthlyGrowthRate, 2),
                    'cumulative_growth' => round((($projectedSalary - $currentSalary) / $currentSalary) * 100, 2),
                ];
            }

            return [
                'employee_id' => $employeeId,
                'current_salary' => $currentSalary,
                'annual_growth_rate' => $growthRate,
                'change_frequency' => $changeFrequency,
                'forecast_months' => $months,
                'monthly_projections' => $forecast,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get salary forecasting data via trait', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId,
            ]);
            throw $e;
        }
    }

    /**
     * Get salary benchmarking data
     */
    public function getSalaryBenchmarkingData(int $employeeId): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
            $currentSalary = $repository->getEmployeeCurrentSalary($employeeId);

            // Get all current salaries for benchmarking
            $allCurrentSalaries = $this->getAllCurrentSalaries();

            $benchmarking = [
                'employee_id' => $employeeId,
                'current_salary' => $currentSalary,
                'percentile_25' => $this->calculatePercentile($allCurrentSalaries, 25),
                'percentile_50' => $this->calculatePercentile($allCurrentSalaries, 50),
                'percentile_75' => $this->calculatePercentile($allCurrentSalaries, 75),
                'percentile_90' => $this->calculatePercentile($allCurrentSalaries, 90),
                'percentile_95' => $this->calculatePercentile($allCurrentSalaries, 95),
                'min_salary' => min($allCurrentSalaries),
                'max_salary' => max($allCurrentSalaries),
                'average_salary' => array_sum($allCurrentSalaries) / count($allCurrentSalaries),
                'salary_rank' => $this->getSalaryRank($currentSalary, $allCurrentSalaries),
                'market_position' => $this->getMarketPosition($currentSalary, $allCurrentSalaries),
            ];

            return $benchmarking;

        } catch (\Exception $e) {
            Log::error('Failed to get salary benchmarking data via trait', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId,
            ]);
            throw $e;
        }
    }

    /**
     * Get salary equity analysis
     */
    public function getSalaryEquityAnalysis(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
            $allChanges = $repository->all();

            if ($startDate && $endDate) {
                $allChanges = $allChanges->filter(function ($change) use ($startDate, $endDate) {
                    return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
                });
            }

            $equityAnalysis = [
                'total_changes' => $allChanges->count(),
                'gender_analysis' => $this->analyzeSalaryByGender($allChanges),
                'age_analysis' => $this->analyzeSalaryByAge($allChanges),
                'tenure_analysis' => $this->analyzeSalaryByTenure($allChanges),
                'education_analysis' => $this->analyzeSalaryByEducation($allChanges),
                'equity_gaps' => $this->identifyEquityGaps($allChanges),
            ];

            return $equityAnalysis;

        } catch (\Exception $e) {
            Log::error('Failed to get salary equity analysis via trait', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Export analytics data
     */
    public function exportAnalyticsData(array $filters = [], string $format = 'json'): string
    {
        try {
            $data = [];

            if (isset($filters['employee_id'])) {
                $data = $this->getEmployeeSalaryAnalytics($filters['employee_id'], $filters['start_date'] ?? null, $filters['end_date'] ?? null);
            } elseif (isset($filters['department_id'])) {
                $data = $this->getDepartmentSalaryAnalytics($filters['department_id'], $filters['start_date'] ?? null, $filters['end_date'] ?? null);
            } else {
                $data = $this->getCompanySalaryAnalytics($filters['start_date'] ?? null, $filters['end_date'] ?? null);
            }

            switch ($format) {
                case 'json':
                    return json_encode($data, JSON_PRETTY_PRINT);
                case 'csv':
                    return $this->convertToCsv($data);
                default:
                    return json_encode($data, JSON_PRETTY_PRINT);
            }

        } catch (\Exception $e) {
            Log::error('Failed to export analytics data via trait', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ]);
            throw $e;
        }
    }

    // Helper methods for analytics calculations

    protected function getEmployeeSalaryAnalyticsByChangeType(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->findByEmployeeId($employeeId);

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->groupBy('change_type')->map(function ($typeChanges) {
            return [
                'count' => $typeChanges->count(),
                'total_amount' => $typeChanges->sum('change_amount'),
                'average_amount' => $typeChanges->avg('change_amount'),
                'average_percentage' => $typeChanges->avg('change_percentage'),
            ];
        })->toArray();
    }

    protected function getEmployeeSalaryAnalyticsByYear(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->findByEmployeeId($employeeId);

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->groupBy(function ($change) {
            return Carbon::parse($change->effective_date)->year;
        })->map(function ($yearChanges) {
            return [
                'count' => $yearChanges->count(),
                'total_amount' => $yearChanges->sum('change_amount'),
                'average_amount' => $yearChanges->avg('change_amount'),
                'average_percentage' => $yearChanges->avg('change_percentage'),
            ];
        })->toArray();
    }

    protected function getEmployeeSalaryTrends(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->findByEmployeeId($employeeId);

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->sortBy('effective_date')->map(function ($change) {
            return [
                'date' => $change->effective_date,
                'change_amount' => $change->change_amount,
                'change_percentage' => $change->change_percentage,
                'change_type' => $change->change_type,
                'cumulative_change' => 0, // This would need to be calculated
            ];
        })->toArray();
    }

    protected function getEmployeeSalaryComparison(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $employeeChanges = $repository->findByEmployeeId($employeeId);

        if ($startDate && $endDate) {
            $employeeChanges = $employeeChanges->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        $allChanges = $repository->all();
        if ($startDate && $endDate) {
            $allChanges = $allChanges->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return [
            'employee_average_change' => $employeeChanges->avg('change_amount'),
            'company_average_change' => $allChanges->avg('change_amount'),
            'employee_change_frequency' => $employeeChanges->count(),
            'company_average_frequency' => $allChanges->count() / $allChanges->unique('employee_id')->count(),
            'performance_relative_to_company' => $employeeChanges->avg('change_amount') - $allChanges->avg('change_amount'),
        ];
    }

    // Additional helper methods would be implemented here...
    protected function getTotalEmployeesWithSalaryHistory(): int
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->all()->unique('employee_id')->count();
    }

    protected function getTotalEmployeesInDepartment(int $departmentId): int
    {
        // This would need to be implemented based on your employee model structure
        return 0;
    }

    protected function getCompanySalaryAnalyticsByChangeType(?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->all();

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->groupBy('change_type')->map(function ($typeChanges) {
            return [
                'count' => $typeChanges->count(),
                'total_amount' => $typeChanges->sum('change_amount'),
                'average_amount' => $typeChanges->avg('change_amount'),
                'average_percentage' => $typeChanges->avg('change_percentage'),
            ];
        })->toArray();
    }

    protected function getCompanySalaryAnalyticsByDepartment(?string $startDate = null, ?string $endDate = null): array
    {
        // This would need to be implemented based on your department structure
        return [];
    }

    protected function getCompanySalaryAnalyticsByYear(?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->all();

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->groupBy(function ($change) {
            return Carbon::parse($change->effective_date)->year;
        })->map(function ($yearChanges) {
            return [
                'count' => $yearChanges->count(),
                'total_amount' => $yearChanges->sum('change_amount'),
                'average_amount' => $yearChanges->avg('change_amount'),
                'average_percentage' => $yearChanges->avg('change_percentage'),
            ];
        })->toArray();
    }

    protected function getCompanySalaryTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $changes = $repository->all();

        if ($startDate && $endDate) {
            $changes = $changes->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        return $changes->sortBy('effective_date')->map(function ($change) {
            return [
                'date' => $change->effective_date,
                'change_amount' => $change->change_amount,
                'change_percentage' => $change->change_percentage,
                'change_type' => $change->change_type,
            ];
        })->toArray();
    }

    protected function getCompanySalaryPercentiles(?string $startDate = null, ?string $endDate = null): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $allChanges = $repository->all();

        if ($startDate && $endDate) {
            $allChanges = $allChanges->filter(function ($change) use ($startDate, $endDate) {
                return $change->effective_date >= $startDate && $change->effective_date <= $endDate;
            });
        }

        $currentSalaries = $this->getAllCurrentSalaries();

        return [
            'p25' => $this->calculatePercentile($currentSalaries, 25),
            'p50' => $this->calculatePercentile($currentSalaries, 50),
            'p75' => $this->calculatePercentile($currentSalaries, 75),
            'p90' => $this->calculatePercentile($currentSalaries, 90),
            'p95' => $this->calculatePercentile($currentSalaries, 95),
        ];
    }

    protected function getAllCurrentSalaries(): array
    {
        $repository = app(EmployeeSalaryHistoryRepositoryInterface::class);
        $allChanges = $repository->all();

        $currentSalaries = [];
        foreach ($allChanges->unique('employee_id') as $employeeId) {
            $latest = $repository->findLatestByEmployee($employeeId);
            if ($latest) {
                $currentSalaries[] = $latest->new_salary;
            }
        }

        return $currentSalaries;
    }

    protected function calculatePercentile(array $values, int $percentile): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values);
        $index = ceil(count($values) * $percentile / 100) - 1;

        return $values[$index] ?? 0;
    }

    protected function getSalaryRank(float $salary, array $allSalaries): int
    {
        if (empty($allSalaries)) {
            return 0;
        }

        sort($allSalaries);
        $rank = 1;
        foreach ($allSalaries as $s) {
            if ($s > $salary) {
                $rank++;
            }
        }

        return $rank;
    }

    protected function getMarketPosition(float $salary, array $allSalaries): string
    {
        if (empty($allSalaries)) {
            return 'unknown';
        }

        $percentile = ($this->getSalaryRank($salary, $allSalaries) / count($allSalaries)) * 100;

        if ($percentile <= 25) {
            return 'below_market';
        } elseif ($percentile <= 50) {
            return 'market_lower';
        } elseif ($percentile <= 75) {
            return 'market_upper';
        } else {
            return 'above_market';
        }
    }

    protected function convertToCsv(array $data): string
    {
        // Simple CSV conversion - in production, use a proper CSV library
        $csv = '';
        $this->arrayToCsv($data, $csv);

        return $csv;
    }

    protected function arrayToCsv(array $data, string &$csv, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $currentKey = $prefix ? $prefix.'.'.$key : $key;

            if (is_array($value)) {
                $this->arrayToCsv($value, $csv, $currentKey);
            } else {
                $csv .= $currentKey.','.$value."\n";
            }
        }
    }

    // Additional helper methods for equity analysis and other analytics...
    protected function analyzeSalaryByGender(Collection $changes): array
    {
        // Implementation would depend on your employee model structure
        return [];
    }

    protected function analyzeSalaryByAge(Collection $changes): array
    {
        // Implementation would depend on your employee model structure
        return [];
    }

    protected function analyzeSalaryByTenure(Collection $changes): array
    {
        // Implementation would depend on your employee model structure
        return [];
    }

    protected function analyzeSalaryByEducation(Collection $changes): array
    {
        // Implementation would depend on your employee model structure
        return [];
    }

    protected function identifyEquityGaps(Collection $changes): array
    {
        // Implementation would depend on your employee model structure
        return [];
    }

    protected function getGrowthDistribution(Collection $changes): array
    {
        // Implementation for growth distribution analysis
        return [];
    }

    protected function getTopSalaryGrowers(Collection $changes, int $limit): array
    {
        // Implementation for top salary growers
        return [];
    }

    protected function getGrowthTrends(Collection $changes): array
    {
        // Implementation for growth trends
        return [];
    }

    protected function compareChangeFrequency(int $employeeId1, int $employeeId2): array
    {
        // Implementation for change frequency comparison
        return [];
    }

    protected function getDepartmentSalaryAnalyticsByChangeType(int $departmentId, ?string $startDate = null, ?string $endDate = null): array
    {
        // Implementation for department analytics by change type
        return [];
    }

    protected function getDepartmentSalaryAnalyticsByEmployee(int $departmentId, ?string $startDate = null, ?string $endDate = null): array
    {
        // Implementation for department analytics by employee
        return [];
    }

    protected function getDepartmentSalaryTrends(int $departmentId, ?string $startDate = null, ?string $endDate = null): array
    {
        // Implementation for department salary trends
        return [];
    }
}
