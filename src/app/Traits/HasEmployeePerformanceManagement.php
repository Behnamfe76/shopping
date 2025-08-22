<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeePerformanceManagement
{
    // Performance rating management
    public function updateEmployeePerformanceRating(Employee $employee, float $rating, string $reviewDate = null): bool
    {
        return $this->repository->updatePerformanceRating($employee, $rating, $reviewDate);
    }

    public function getEmployeePerformanceRating(int $employeeId): ?float
    {
        $employee = $this->repository->find($employeeId);
        return $employee ? $employee->performance_rating : null;
    }

    public function hasEmployeePerformanceRating(int $employeeId): bool
    {
        $employee = $this->repository->find($employeeId);
        return $employee ? $employee->hasPerformanceRating() : false;
    }

    // Performance-based queries
    public function findByPerformanceRating(float $minRating, float $maxRating): Collection
    {
        return $this->repository->findByPerformanceRating($minRating, $maxRating);
    }

    public function findByPerformanceRatingDTO(float $minRating, float $maxRating): Collection
    {
        return $this->repository->findByPerformanceRatingDTO($minRating, $maxRating);
    }

    public function getTopPerformers(int $limit = 10): Collection
    {
        return $this->repository->getTopPerformers($limit);
    }

    public function getTopPerformersDTO(int $limit = 10): Collection
    {
        return $this->repository->getTopPerformersDTO($limit);
    }

    public function getTopPerformersByDepartment(string $department, int $limit = 10): Collection
    {
        return $this->repository->findByDepartment($department)
            ->filter(fn($employee) => $employee->hasPerformanceRating())
            ->sortByDesc('performance_rating')
            ->take($limit);
    }

    public function getTopPerformersByDepartmentDTO(string $department, int $limit = 10): Collection
    {
        return $this->getTopPerformersByDepartment($department, $limit)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Performance review management
    public function schedulePerformanceReview(Employee $employee, string $reviewDate): bool
    {
        return $this->repository->update($employee, ['next_review_date' => $reviewDate]);
    }

    public function getEmployeesWithUpcomingReviews(int $daysAhead = 30): Collection
    {
        return $this->repository->getEmployeesWithUpcomingReviews($daysAhead);
    }

    public function getEmployeesWithUpcomingReviewsDTO(int $daysAhead = 30): Collection
    {
        return $this->repository->getEmployeesWithUpcomingReviewsDTO($daysAhead);
    }

    public function getEmployeesNeedingReviews(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->needsPerformanceReview());
    }

    public function getEmployeesNeedingReviewsDTO(): Collection
    {
        return $this->getEmployeesNeedingReviews()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesOverdueForReview(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->next_review_date && $employee->next_review_date->isPast());
    }

    public function getEmployeesOverdueForReviewDTO(): Collection
    {
        return $this->getEmployeesOverdueForReview()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Performance analytics
    public function getAveragePerformanceRating(): float
    {
        return $this->repository->getAveragePerformanceRating();
    }

    public function getAveragePerformanceRatingByDepartment(string $department): float
    {
        return $this->repository->getAveragePerformanceRatingByDepartment($department);
    }

    public function getPerformanceRatingDistribution(): array
    {
        $employees = $this->repository->findActive()
            ->filter(fn($employee) => $employee->hasPerformanceRating());

        $distribution = [
            'excellent' => 0, // 4.5-5.0
            'good' => 0,      // 3.5-4.4
            'average' => 0,   // 2.5-3.4
            'below_average' => 0, // 1.5-2.4
            'poor' => 0       // 1.0-1.4
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
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0
            ];
        }

        return $distribution;
    }

    public function getPerformanceTrends(string $period = 'monthly'): array
    {
        // Implementation for performance trends over time
        return [];
    }

    // Performance validation
    public function isValidPerformanceRating(float $rating): bool
    {
        return $rating >= 1.0 && $rating <= 5.0;
    }

    public function getPerformanceRatingLabel(float $rating): string
    {
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 3.5) return 'Good';
        if ($rating >= 2.5) return 'Average';
        if ($rating >= 1.5) return 'Below Average';
        return 'Poor';
    }

    public function getPerformanceRatingColor(float $rating): string
    {
        if ($rating >= 4.5) return 'green';
        if ($rating >= 3.5) return 'blue';
        if ($rating >= 2.5) return 'yellow';
        if ($rating >= 1.5) return 'orange';
        return 'red';
    }

    // Performance improvement tracking
    public function getPerformanceImprovement(Employee $employee, int $months = 12): ?float
    {
        // This would typically query a performance history table
        // For now, we'll return null as placeholder
        return null;
    }

    public function getEmployeesWithPerformanceImprovement(int $months = 12): Collection
    {
        // Implementation for finding employees with performance improvement
        return collect();
    }

    public function getEmployeesWithPerformanceDecline(int $months = 12): Collection
    {
        // Implementation for finding employees with performance decline
        return collect();
    }

    // Performance goals and targets
    public function setPerformanceGoal(Employee $employee, float $targetRating, string $targetDate): bool
    {
        // Implementation for setting performance goals
        return true;
    }

    public function getPerformanceGoals(Employee $employee): array
    {
        // Implementation for getting performance goals
        return [];
    }

    public function checkPerformanceGoalAchievement(Employee $employee): array
    {
        // Implementation for checking if performance goals are achieved
        return [];
    }

    // Performance comparison
    public function compareEmployeePerformance(int $employeeId1, int $employeeId2): array
    {
        $employee1 = $this->repository->find($employeeId1);
        $employee2 = $this->repository->find($employeeId2);

        if (!$employee1 || !$employee2) {
            return [];
        }

        return [
            'employee1' => [
                'id' => $employee1->id,
                'name' => $employee1->full_name,
                'rating' => $employee1->performance_rating,
                'label' => $employee1->performance_rating ? $this->getPerformanceRatingLabel($employee1->performance_rating) : 'No Rating'
            ],
            'employee2' => [
                'id' => $employee2->id,
                'name' => $employee2->full_name,
                'rating' => $employee2->performance_rating,
                'label' => $employee2->performance_rating ? $this->getPerformanceRatingLabel($employee2->performance_rating) : 'No Rating'
            ],
            'difference' => ($employee1->performance_rating ?? 0) - ($employee2->performance_rating ?? 0)
        ];
    }

    public function getDepartmentPerformanceComparison(): array
    {
        $departments = $this->repository->findActive()
            ->pluck('department')
            ->unique()
            ->filter();

        $comparison = [];

        foreach ($departments as $department) {
            $avgRating = $this->getAveragePerformanceRatingByDepartment($department);
            $employeeCount = $this->repository->getEmployeeCountByDepartment($department);

            $comparison[$department] = [
                'average_rating' => $avgRating,
                'employee_count' => $employeeCount,
                'label' => $this->getPerformanceRatingLabel($avgRating),
                'color' => $this->getPerformanceRatingColor($avgRating)
            ];
        }

        // Sort by average rating descending
        uasort($comparison, fn($a, $b) => $b['average_rating'] <=> $a['average_rating']);

        return $comparison;
    }

    // Performance reporting
    public function generatePerformanceReport(string $department = null, string $period = 'current'): array
    {
        $employees = $department
            ? $this->repository->findByDepartment($department)
            : $this->repository->findActive();

        $report = [
            'total_employees' => $employees->count(),
            'employees_with_rating' => $employees->filter(fn($e) => $e->hasPerformanceRating())->count(),
            'average_rating' => $employees->filter(fn($e) => $e->hasPerformanceRating())->avg('performance_rating') ?? 0,
            'rating_distribution' => $this->getPerformanceRatingDistribution(),
            'top_performers' => $employees->filter(fn($e) => $e->isTopPerformer())->take(5)->values(),
            'needs_review' => $employees->filter(fn($e) => $e->needsPerformanceReview())->count(),
            'overdue_reviews' => $employees->filter(fn($e) => $e->next_review_date && $e->next_review_date->isPast())->count()
        ];

        return $report;
    }
}

