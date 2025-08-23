<?php

namespace App\Traits;

use App\Models\EmployeeSalaryHistory;
use App\DTOs\EmployeeSalaryHistoryDTO;
use App\Repositories\Interfaces\EmployeeSalaryHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

trait HasEmployeeSalaryHistoryRetroactiveManagement
{
    /**
     * Create retroactive salary adjustment
     */
    public function createRetroactiveSalaryAdjustment(array $data): EmployeeSalaryHistory
    {
        try {
            // Validate retroactive data
            $this->validateRetroactiveData($data);

            // Set retroactive flag
            $data['is_retroactive'] = true;

            // Calculate retroactive period
            $startDate = Carbon::parse($data['retroactive_start_date']);
            $endDate = Carbon::parse($data['retroactive_end_date']);
            $data['retroactive_period_days'] = $startDate->diffInDays($endDate) + 1;

            // Calculate daily rate difference
            $dailyRateDifference = ($data['new_salary'] - $data['old_salary']) / 365;
            $data['retroactive_adjustment_amount'] = $dailyRateDifference * $data['retroactive_period_days'];

            // Create the salary history record
            $salaryHistory = app(EmployeeSalaryHistoryRepositoryInterface::class)->create($data);

            Log::info('Retroactive salary adjustment created via trait', [
                'id' => $salaryHistory->id,
                'employee_id' => $data['employee_id'],
                'retroactive_period' => $data['retroactive_period_days'] . ' days',
                'adjustment_amount' => $data['retroactive_adjustment_amount']
            ]);

            return $salaryHistory;

        } catch (\Exception $e) {
            Log::error('Failed to create retroactive salary adjustment via trait', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Get all retroactive salary adjustments
     */
    public function getAllRetroactiveAdjustments(): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findRetroactive();
    }

    /**
     * Get retroactive salary adjustments by employee
     */
    public function getRetroactiveAdjustmentsByEmployee(int $employeeId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByEmployeeId($employeeId)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->is_retroactive;
            });
    }

    /**
     * Get retroactive salary adjustments by date range
     */
    public function getRetroactiveAdjustmentsByDateRange(string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByDateRange($startDate, $endDate)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->is_retroactive;
            });
    }

    /**
     * Get retroactive salary adjustments by employee and date range
     */
    public function getRetroactiveAdjustmentsByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByEmployeeAndDateRange($employeeId, $startDate, $endDate)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->is_retroactive;
            });
    }

    /**
     * Calculate total retroactive adjustment amount for employee
     */
    public function calculateTotalRetroactiveAdjustmentAmount(int $employeeId, string $startDate = null, string $endDate = null): float
    {
        $adjustments = $this->getRetroactiveAdjustmentsByEmployee($employeeId);

        if ($startDate && $endDate) {
            $adjustments = $adjustments->filter(function ($adjustment) use ($startDate, $endDate) {
                return $adjustment->retroactive_start_date >= $startDate &&
                       $adjustment->retroactive_end_date <= $endDate;
            });
        }

        return $adjustments->sum('retroactive_adjustment_amount');
    }

    /**
     * Calculate total retroactive adjustment amount for company
     */
    public function calculateTotalRetroactiveAdjustmentAmountForCompany(string $startDate = null, string $endDate = null): float
    {
        $adjustments = $this->getAllRetroactiveAdjustments();

        if ($startDate && $endDate) {
            $adjustments = $adjustments->filter(function ($adjustment) use ($startDate, $endDate) {
                return $adjustment->retroactive_start_date >= $startDate &&
                       $adjustment->retroactive_end_date <= $endDate;
            });
        }

        return $adjustments->sum('retroactive_adjustment_amount');
    }

    /**
     * Get retroactive adjustment statistics
     */
    public function getRetroactiveAdjustmentStatistics(): array
    {
        $adjustments = $this->getAllRetroactiveAdjustments();

        return [
            'total_adjustments' => $adjustments->count(),
            'total_amount' => $adjustments->sum('retroactive_adjustment_amount'),
            'average_amount' => $adjustments->avg('retroactive_adjustment_amount'),
            'by_employee' => $adjustments->groupBy('employee_id')->map(function ($employeeAdjustments) {
                return [
                    'count' => $employeeAdjustments->count(),
                    'total_amount' => $employeeAdjustments->sum('retroactive_adjustment_amount'),
                    'average_amount' => $employeeAdjustments->avg('retroactive_adjustment_amount'),
                ];
            })->toArray(),
            'by_type' => $adjustments->groupBy('change_type')->map(function ($typeAdjustments) {
                return [
                    'count' => $typeAdjustments->count(),
                    'total_amount' => $typeAdjustments->sum('retroactive_adjustment_amount'),
                    'average_amount' => $typeAdjustments->avg('retroactive_adjustment_amount'),
                ];
            })->toArray(),
        ];
    }

    /**
     * Get retroactive adjustment statistics by date range
     */
    public function getRetroactiveAdjustmentStatisticsByDateRange(string $startDate, string $endDate): array
    {
        $adjustments = $this->getRetroactiveAdjustmentsByDateRange($startDate, $endDate);

        return [
            'total_adjustments' => $adjustments->count(),
            'total_amount' => $adjustments->sum('retroactive_adjustment_amount'),
            'average_amount' => $adjustments->avg('retroactive_adjustment_amount'),
            'period_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
        ];
    }

    /**
     * Get retroactive adjustment statistics by employee
     */
    public function getRetroactiveAdjustmentStatisticsByEmployee(int $employeeId): array
    {
        $adjustments = $this->getRetroactiveAdjustmentsByEmployee($employeeId);

        return [
            'total_adjustments' => $adjustments->count(),
            'total_amount' => $adjustments->sum('retroactive_adjustment_amount'),
            'average_amount' => $adjustments->avg('retroactive_adjustment_amount'),
            'by_type' => $adjustments->groupBy('change_type')->map(function ($typeAdjustments) {
                return [
                    'count' => $typeAdjustments->count(),
                    'total_amount' => $typeAdjustments->sum('retroactive_adjustment_amount'),
                    'average_amount' => $typeAdjustments->avg('retroactive_adjustment_amount'),
                ];
            })->toArray(),
        ];
    }

    /**
     * Check if employee has retroactive adjustments
     */
    public function hasRetroactiveAdjustments(int $employeeId): bool
    {
        return $this->getRetroactiveAdjustmentsByEmployee($employeeId)->count() > 0;
    }

    /**
     * Check if employee has retroactive adjustments in date range
     */
    public function hasRetroactiveAdjustmentsInDateRange(int $employeeId, string $startDate, string $endDate): bool
    {
        return $this->getRetroactiveAdjustmentsByEmployeeAndDateRange($employeeId, $startDate, $endDate)->count() > 0;
    }

    /**
     * Get count of retroactive adjustments
     */
    public function getRetroactiveAdjustmentsCount(): int
    {
        return $this->getAllRetroactiveAdjustments()->count();
    }

    /**
     * Get count of retroactive adjustments by employee
     */
    public function getRetroactiveAdjustmentsCountByEmployee(int $employeeId): int
    {
        return $this->getRetroactiveAdjustmentsByEmployee($employeeId)->count();
    }

    /**
     * Get count of retroactive adjustments by date range
     */
    public function getRetroactiveAdjustmentsCountByDateRange(string $startDate, string $endDate): int
    {
        return $this->getRetroactiveAdjustmentsByDateRange($startDate, $endDate)->count();
    }

    /**
     * Calculate retroactive period overlap
     */
    public function calculateRetroactivePeriodOverlap(int $employeeId, string $startDate, string $endDate): array
    {
        $existingAdjustments = $this->getRetroactiveAdjustmentsByEmployee($employeeId);
        $overlaps = [];

        $newStartDate = Carbon::parse($startDate);
        $newEndDate = Carbon::parse($endDate);

        foreach ($existingAdjustments as $adjustment) {
            $existingStartDate = Carbon::parse($adjustment->retroactive_start_date);
            $existingEndDate = Carbon::parse($adjustment->retroactive_end_date);

            // Check for overlap
            if ($newStartDate <= $existingEndDate && $newEndDate >= $existingStartDate) {
                $overlapStart = max($newStartDate, $existingStartDate);
                $overlapEnd = min($newEndDate, $existingEndDate);
                $overlapDays = $overlapStart->diffInDays($overlapEnd) + 1;

                $overlaps[] = [
                    'adjustment_id' => $adjustment->id,
                    'overlap_start' => $overlapStart->toDateString(),
                    'overlap_end' => $overlapEnd->toDateString(),
                    'overlap_days' => $overlapDays,
                    'existing_adjustment' => $adjustment,
                ];
            }
        }

        return $overlaps;
    }

    /**
     * Check if retroactive period has conflicts
     */
    public function hasRetroactivePeriodConflicts(int $employeeId, string $startDate, string $endDate): bool
    {
        $overlaps = $this->calculateRetroactivePeriodOverlap($employeeId, $startDate, $endDate);
        return count($overlaps) > 0;
    }

    /**
     * Get retroactive adjustment recommendations
     */
    public function getRetroactiveAdjustmentRecommendations(int $employeeId, float $oldSalary, float $newSalary): array
    {
        $recommendations = [];

        // Calculate daily rate difference
        $dailyRateDifference = ($newSalary - $oldSalary) / 365;

        // Get current date
        $currentDate = Carbon::now();

        // Calculate days since last salary change
        $lastSalaryChange = app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findLatestByEmployee($employeeId);

        if ($lastSalaryChange) {
            $lastChangeDate = Carbon::parse($lastSalaryChange->effective_date);
            $daysSinceLastChange = $lastChangeDate->diffInDays($currentDate);

            if ($daysSinceLastChange > 0) {
                $retroactiveAmount = $dailyRateDifference * $daysSinceLastChange;

                $recommendations[] = [
                    'type' => 'retroactive_adjustment',
                    'start_date' => $lastChangeDate->toDateString(),
                    'end_date' => $currentDate->toDateString(),
                    'days' => $daysSinceLastChange,
                    'daily_rate_difference' => $dailyRateDifference,
                    'total_adjustment_amount' => $retroactiveAmount,
                    'reason' => 'Salary change effective from last change date',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Validate retroactive data
     */
    protected function validateRetroactiveData(array $data): void
    {
        $requiredFields = ['employee_id', 'old_salary', 'new_salary', 'change_type', 'retroactive_start_date', 'retroactive_end_date'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' is missing for retroactive adjustment");
            }
        }

        if ($data['old_salary'] < 0 || $data['new_salary'] < 0) {
            throw new \InvalidArgumentException('Salary amounts cannot be negative');
        }

        $startDate = Carbon::parse($data['retroactive_start_date']);
        $endDate = Carbon::parse($data['retroactive_end_date']);

        if ($startDate->isAfter($endDate)) {
            throw new \InvalidArgumentException('Retroactive start date must be before end date');
        }

        if ($endDate->isAfter(Carbon::now())) {
            throw new \InvalidArgumentException('Retroactive end date cannot be in the future');
        }

        // Check for conflicts
        if ($this->hasRetroactivePeriodConflicts($data['employee_id'], $data['retroactive_start_date'], $data['retroactive_end_date'])) {
            throw new \InvalidArgumentException('Retroactive period conflicts with existing adjustments');
        }
    }
}
