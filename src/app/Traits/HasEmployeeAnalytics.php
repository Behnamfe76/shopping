<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeAnalytics
{
    // Employee statistics
    public function getEmployeeStats(): array
    {
        return $this->repository->getEmployeeStats();
    }

    public function getEmployeeStatsByStatus(): array
    {
        return $this->repository->getEmployeeStatsByStatus();
    }

    public function getEmployeeStatsByDepartment(): array
    {
        return $this->repository->getEmployeeStatsByDepartment();
    }

    public function getEmployeeStatsByEmploymentType(): array
    {
        return $this->repository->getEmployeeStatsByEmploymentType();
    }

    // Employee growth analytics
    public function getEmployeeGrowthStats(string $period = 'monthly'): array
    {
        // Implementation for employee growth statistics over time
        $stats = [
            'period' => $period,
            'total_employees' => $this->repository->getEmployeeCount(),
            'active_employees' => $this->repository->getActiveEmployeeCount(),
            'new_hires' => $this->getNewHiresCount($period),
            'terminations' => $this->getTerminationsCount($period),
            'growth_rate' => $this->calculateGrowthRate($period),
            'turnover_rate' => $this->calculateTurnoverRate($period),
            'retention_rate' => $this->calculateRetentionRate($period),
        ];

        return $stats;
    }

    public function getEmployeeTurnoverStats(): array
    {
        // Implementation for employee turnover statistics
        $stats = [
            'total_turnover_rate' => $this->calculateTurnoverRate('annual'),
            'voluntary_turnover_rate' => $this->calculateVoluntaryTurnoverRate('annual'),
            'involuntary_turnover_rate' => $this->calculateInvoluntaryTurnoverRate('annual'),
            'turnover_by_department' => $this->getTurnoverByDepartment(),
            'turnover_by_employment_type' => $this->getTurnoverByEmploymentType(),
            'turnover_trends' => $this->getTurnoverTrends(),
        ];

        return $stats;
    }

    public function getEmployeeRetentionStats(): array
    {
        // Implementation for employee retention statistics
        $stats = [
            'overall_retention_rate' => $this->calculateRetentionRate('annual'),
            'retention_by_department' => $this->getRetentionByDepartment(),
            'retention_by_employment_type' => $this->getRetentionByEmploymentType(),
            'retention_by_tenure' => $this->getRetentionByTenure(),
            'retention_trends' => $this->getRetentionTrends(),
        ];

        return $stats;
    }

    public function getEmployeePerformanceStats(): array
    {
        // Implementation for employee performance statistics
        $stats = [
            'average_performance_rating' => $this->repository->getAveragePerformanceRating(),
            'performance_distribution' => $this->getPerformanceDistribution(),
            'top_performers_count' => $this->getTopPerformersCount(),
            'performance_by_department' => $this->getPerformanceByDepartment(),
            'performance_trends' => $this->getPerformanceTrends(),
        ];

        return $stats;
    }

    public function getEmployeeSalaryStats(): array
    {
        // Implementation for employee salary statistics
        $stats = [
            'total_salary' => $this->repository->getTotalSalary(),
            'average_salary' => $this->repository->getAverageSalary(),
            'salary_by_department' => $this->getSalaryByDepartment(),
            'salary_by_employment_type' => $this->getSalaryByEmploymentType(),
            'salary_distribution' => $this->getSalaryDistribution(),
            'salary_trends' => $this->getSalaryTrends(),
        ];

        return $stats;
    }

    public function getEmployeeTimeOffStats(): array
    {
        // Implementation for employee time-off statistics
        $stats = [
            'vacation_utilization_rate' => $this->getVacationUtilizationRate(),
            'sick_leave_utilization_rate' => $this->getSickLeaveUtilizationRate(),
            'time_off_by_department' => $this->getTimeOffByDepartment(),
            'time_off_trends' => $this->getTimeOffTrends(),
        ];

        return $stats;
    }

    // Demographic analytics
    public function getEmployeeDemographics(): array
    {
        $employees = $this->repository->findActive();

        $demographics = [
            'total_employees' => $employees->count(),
            'gender_distribution' => $this->getGenderDistribution($employees),
            'age_distribution' => $this->getAgeDistribution($employees),
            'tenure_distribution' => $this->getTenureDistribution($employees),
            'department_distribution' => $this->getDepartmentDistribution($employees),
            'employment_type_distribution' => $this->getEmploymentTypeDistribution($employees),
        ];

        return $demographics;
    }

    public function getGenderDistribution(?Collection $employees = null): array
    {
        $employees = $employees ?: $this->repository->findActive();
        $total = $employees->count();

        if ($total === 0) {
            return [];
        }

        $distribution = [];
        foreach ($employees->pluck('gender')->countBy() as $gender => $count) {
            $distribution[$gender] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2),
            ];
        }

        return $distribution;
    }

    public function getAgeDistribution(?Collection $employees = null): array
    {
        $employees = $employees ?: $this->repository->findActive();
        $total = $employees->count();

        if ($total === 0) {
            return [];
        }

        $ageGroups = [
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '56-65' => 0,
            '65+' => 0,
        ];

        foreach ($employees as $employee) {
            if ($employee->date_of_birth) {
                $age = $employee->date_of_birth->age;

                if ($age >= 18 && $age <= 25) {
                    $ageGroups['18-25']++;
                } elseif ($age >= 26 && $age <= 35) {
                    $ageGroups['26-35']++;
                } elseif ($age >= 36 && $age <= 45) {
                    $ageGroups['36-45']++;
                } elseif ($age >= 46 && $age <= 55) {
                    $ageGroups['46-55']++;
                } elseif ($age >= 56 && $age <= 65) {
                    $ageGroups['56-65']++;
                } else {
                    $ageGroups['65+']++;
                }
            }
        }

        $distribution = [];
        foreach ($ageGroups as $group => $count) {
            $distribution[$group] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
            ];
        }

        return $distribution;
    }

    public function getTenureDistribution(?Collection $employees = null): array
    {
        $employees = $employees ?: $this->repository->findActive();
        $total = $employees->count();

        if ($total === 0) {
            return [];
        }

        $tenureGroups = [
            '0-1 years' => 0,
            '1-3 years' => 0,
            '3-5 years' => 0,
            '5-10 years' => 0,
            '10+ years' => 0,
        ];

        foreach ($employees as $employee) {
            $yearsOfService = $employee->years_of_service;

            if ($yearsOfService < 1) {
                $tenureGroups['0-1 years']++;
            } elseif ($yearsOfService < 3) {
                $tenureGroups['1-3 years']++;
            } elseif ($yearsOfService < 5) {
                $tenureGroups['3-5 years']++;
            } elseif ($yearsOfService < 10) {
                $tenureGroups['5-10 years']++;
            } else {
                $tenureGroups['10+ years']++;
            }
        }

        $distribution = [];
        foreach ($tenureGroups as $group => $count) {
            $distribution[$group] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
            ];
        }

        return $distribution;
    }

    public function getDepartmentDistribution(?Collection $employees = null): array
    {
        $employees = $employees ?: $this->repository->findActive();
        $total = $employees->count();

        if ($total === 0) {
            return [];
        }

        $distribution = [];
        foreach ($employees->pluck('department')->countBy() as $department => $count) {
            $distribution[$department] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2),
            ];
        }

        return $distribution;
    }

    public function getEmploymentTypeDistribution(?Collection $employees = null): array
    {
        $employees = $employees ?: $this->repository->findActive();
        $total = $employees->count();

        if ($total === 0) {
            return [];
        }

        $distribution = [];
        foreach ($employees->pluck('employment_type')->countBy() as $type => $count) {
            $distribution[$type] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2),
            ];
        }

        return $distribution;
    }

    // Performance analytics
    public function getPerformanceDistribution(): array
    {
        $employees = $this->repository->findActive()
            ->filter(fn ($employee) => $employee->hasPerformanceRating());

        $distribution = [
            'excellent' => 0, // 4.5-5.0
            'good' => 0,      // 3.5-4.4
            'average' => 0,   // 2.5-3.4
            'below_average' => 0, // 1.5-2.4
            'poor' => 0,       // 1.0-1.4
        ];

        foreach ($employees as $employee) {
            $rating = $employee->performance_rating;

            if ($rating >= 4.5) {
                $distribution['excellent']++;
            } elseif ($rating >= 3.5) {
                $distribution['good']++;
            } elseif ($rating >= 2.5) {
                $distribution['average']++;
            } elseif ($rating >= 1.5) {
                $distribution['below_average']++;
            } else {
                $distribution['poor']++;
            }
        }

        $total = array_sum($distribution);

        foreach ($distribution as $category => $count) {
            $distribution[$category] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
            ];
        }

        return $distribution;
    }

    public function getTopPerformersCount(): int
    {
        return $this->repository->findActive()
            ->filter(fn ($employee) => $employee->isTopPerformer())
            ->count();
    }

    public function getPerformanceByDepartment(): array
    {
        $departments = $this->repository->findActive()
            ->pluck('department')
            ->unique()
            ->filter();

        $performance = [];

        foreach ($departments as $department) {
            $avgRating = $this->repository->getAveragePerformanceRatingByDepartment($department);
            $employeeCount = $this->repository->getEmployeeCountByDepartment($department);

            $performance[$department] = [
                'average_rating' => $avgRating,
                'employee_count' => $employeeCount,
                'top_performers_count' => $this->repository->findByDepartment($department)
                    ->filter(fn ($e) => $e->isTopPerformer())
                    ->count(),
            ];
        }

        return $performance;
    }

    // Salary analytics
    public function getSalaryByDepartment(): array
    {
        $departments = $this->repository->findActive()
            ->pluck('department')
            ->unique()
            ->filter();

        $salary = [];

        foreach ($departments as $department) {
            $totalSalary = $this->repository->getTotalSalaryByDepartment($department);
            $avgSalary = $this->repository->getAverageSalaryByDepartment($department);
            $employeeCount = $this->repository->getEmployeeCountByDepartment($department);

            $salary[$department] = [
                'total_salary' => $totalSalary,
                'average_salary' => $avgSalary,
                'employee_count' => $employeeCount,
                'salary_per_employee' => $employeeCount > 0 ? $totalSalary / $employeeCount : 0,
            ];
        }

        return $salary;
    }

    public function getSalaryByEmploymentType(): array
    {
        $employmentTypes = $this->repository->findActive()
            ->pluck('employment_type')
            ->unique()
            ->filter();

        $salary = [];

        foreach ($employmentTypes as $type) {
            $employees = $this->repository->findByEmploymentType($type->value);
            $totalSalary = $employees->sum('salary');
            $avgSalary = $employees->avg('salary') ?? 0;
            $employeeCount = $employees->count();

            $salary[$type->value] = [
                'employment_type' => $type->label(),
                'total_salary' => $totalSalary,
                'average_salary' => $avgSalary,
                'employee_count' => $employeeCount,
                'salary_per_employee' => $employeeCount > 0 ? $totalSalary / $employeeCount : 0,
            ];
        }

        return $salary;
    }

    public function getSalaryDistribution(): array
    {
        $employees = $this->repository->findActive()
            ->filter(fn ($employee) => $employee->salary > 0);

        $salaryRanges = [
            '0-50k' => 0,
            '50k-75k' => 0,
            '75k-100k' => 0,
            '100k-150k' => 0,
            '150k-200k' => 0,
            '200k+' => 0,
        ];

        foreach ($employees as $employee) {
            $salary = $employee->salary;

            if ($salary < 50000) {
                $salaryRanges['0-50k']++;
            } elseif ($salary < 75000) {
                $salaryRanges['50k-75k']++;
            } elseif ($salary < 100000) {
                $salaryRanges['75k-100k']++;
            } elseif ($salary < 150000) {
                $salaryRanges['100k-150k']++;
            } elseif ($salary < 200000) {
                $salaryRanges['150k-200k']++;
            } else {
                $salaryRanges['200k+']++;
            }
        }

        $total = array_sum($salaryRanges);

        $distribution = [];
        foreach ($salaryRanges as $range => $count) {
            $distribution[$range] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
            ];
        }

        return $distribution;
    }

    // Helper methods for analytics
    private function getNewHiresCount(string $period): int
    {
        // Implementation for counting new hires in the specified period
        return 0;
    }

    private function getTerminationsCount(string $period): int
    {
        // Implementation for counting terminations in the specified period
        return 0;
    }

    private function calculateGrowthRate(string $period): float
    {
        // Implementation for calculating growth rate
        return 0.0;
    }

    private function calculateTurnoverRate(string $period): float
    {
        // Implementation for calculating turnover rate
        return 0.0;
    }

    private function calculateRetentionRate(string $period): float
    {
        // Implementation for calculating retention rate
        return 0.0;
    }

    private function calculateVoluntaryTurnoverRate(string $period): float
    {
        // Implementation for calculating voluntary turnover rate
        return 0.0;
    }

    private function calculateInvoluntaryTurnoverRate(string $period): float
    {
        // Implementation for calculating involuntary turnover rate
        return 0.0;
    }

    private function getTurnoverByDepartment(): array
    {
        // Implementation for turnover by department
        return [];
    }

    private function getTurnoverByEmploymentType(): array
    {
        // Implementation for turnover by employment type
        return [];
    }

    private function getTurnoverTrends(): array
    {
        // Implementation for turnover trends
        return [];
    }

    private function getRetentionByDepartment(): array
    {
        // Implementation for retention by department
        return [];
    }

    private function getRetentionByEmploymentType(): array
    {
        // Implementation for retention by employment type
        return [];
    }

    private function getRetentionByTenure(): array
    {
        // Implementation for retention by tenure
        return [];
    }

    private function getRetentionTrends(): array
    {
        // Implementation for retention trends
        return [];
    }

    private function getPerformanceTrends(): array
    {
        // Implementation for performance trends
        return [];
    }

    private function getSalaryTrends(): array
    {
        // Implementation for salary trends
        return [];
    }

    private function getVacationUtilizationRate(): float
    {
        // Implementation for vacation utilization rate
        return 0.0;
    }

    private function getSickLeaveUtilizationRate(): float
    {
        // Implementation for sick leave utilization rate
        return 0.0;
    }

    private function getTimeOffByDepartment(): array
    {
        // Implementation for time off by department
        return [];
    }

    private function getTimeOffTrends(): array
    {
        // Implementation for time off trends
        return [];
    }

    // Comprehensive analytics report
    public function generateComprehensiveAnalyticsReport(?string $department = null, string $period = 'current'): array
    {
        $report = [
            'period' => $period,
            'department' => $department,
            'employee_stats' => $this->getEmployeeStats(),
            'demographics' => $this->getEmployeeDemographics(),
            'growth_stats' => $this->getEmployeeGrowthStats($period),
            'turnover_stats' => $this->getEmployeeTurnoverStats(),
            'retention_stats' => $this->getEmployeeRetentionStats(),
            'performance_stats' => $this->getEmployeePerformanceStats(),
            'salary_stats' => $this->getEmployeeSalaryStats(),
            'time_off_stats' => $this->getEmployeeTimeOffStats(),
            'hierarchy_stats' => $this->getHierarchyStats(),
            'benefits_stats' => $this->getBenefitsEnrollmentStats(),
        ];

        return $report;
    }
}
