<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeTimeOff;

use Carbon\Carbon;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CalculateTimeOffBalanceAction
{
    protected $repository;

    public function __construct(EmployeeTimeOffRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $employeeId, ?string $timeOffType = null, ?string $year = null): array
    {
        try {
            $year = $year ?? Carbon::now()->year;
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();

            // Get total days/hours used
            $totalDaysUsed = $this->repository->getEmployeeTotalDaysUsed($employeeId, $year);
            $totalHoursUsed = $this->repository->getEmployeeTotalHoursUsed($employeeId, $year);

            // Get counts by type if specified
            $typeCounts = [];
            if ($timeOffType) {
                $typeCounts[$timeOffType] = $this->repository->getEmployeeTimeOffCountByType($employeeId, $timeOffType);
            }

            // Get counts by status
            $statusCounts = [
                'pending' => $this->repository->getEmployeeTimeOffCountByStatus($employeeId, 'pending'),
                'approved' => $this->repository->getEmployeeTimeOffCountByStatus($employeeId, 'approved'),
                'rejected' => $this->repository->getEmployeeTimeOffCountByStatus($employeeId, 'rejected'),
                'cancelled' => $this->repository->getEmployeeTimeOffCountByStatus($employeeId, 'cancelled'),
            ];

            // Calculate remaining balance (assuming standard allocation)
            $standardAllocation = $this->getStandardAllocation($timeOffType);
            $remainingDays = max(0, $standardAllocation - $totalDaysUsed);
            $remainingHours = max(0, ($standardAllocation * 8) - $totalHoursUsed);

            $balance = [
                'employee_id' => $employeeId,
                'year' => $year,
                'time_off_type' => $timeOffType,
                'total_days_used' => $totalDaysUsed,
                'total_hours_used' => $totalHoursUsed,
                'standard_allocation' => $standardAllocation,
                'remaining_days' => $remainingDays,
                'remaining_hours' => $remainingHours,
                'type_counts' => $typeCounts,
                'status_counts' => $statusCounts,
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
            ];

            Log::info('Time-off balance calculated successfully', [
                'employee_id' => $employeeId,
                'year' => $year,
                'balance' => $balance,
            ]);

            return $balance;

        } catch (\Exception $e) {
            Log::error('Failed to calculate time-off balance', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId,
                'time_off_type' => $timeOffType,
                'year' => $year,
            ]);
            throw $e;
        }
    }

    protected function getStandardAllocation(?string $timeOffType): float
    {
        // This would typically come from employee policy or company settings
        // For now, returning standard values
        $allocations = [
            'vacation' => 20.0,
            'sick' => 10.0,
            'personal' => 5.0,
            'bereavement' => 3.0,
            'jury_duty' => 5.0,
            'military' => 15.0,
            'other' => 0.0,
        ];

        if ($timeOffType && isset($allocations[$timeOffType])) {
            return $allocations[$timeOffType];
        }

        // Return total allocation if no specific type
        return array_sum($allocations);
    }
}
