<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeTimeOffManagement
{
    // Vacation days management
    public function addVacationDays(Employee $employee, int $days): bool
    {
        return $this->repository->addVacationDays($employee, $days);
    }

    public function useVacationDays(Employee $employee, int $days): bool
    {
        return $this->repository->useVacationDays($employee, $days);
    }

    public function getVacationDaysBalance(Employee $employee): int
    {
        return $employee->remaining_vacation_days;
    }

    public function hasVacationDaysAvailable(Employee $employee, int $days): bool
    {
        return $employee->remaining_vacation_days >= $days;
    }

    // Sick days management
    public function addSickDays(Employee $employee, int $days): bool
    {
        return $this->repository->addSickDays($employee, $days);
    }

    public function useSickDays(Employee $employee, int $days): bool
    {
        return $this->repository->useSickDays($employee, $days);
    }

    public function getSickDaysBalance(Employee $employee): int
    {
        return $employee->remaining_sick_days;
    }

    public function hasSickDaysAvailable(Employee $employee, int $days): bool
    {
        return $employee->remaining_sick_days >= $days;
    }

    // Time-off queries
    public function getEmployeesWithLowVacationDays(int $threshold = 5): Collection
    {
        return $this->repository->getEmployeesWithLowVacationDays($threshold);
    }

    public function getEmployeesWithLowVacationDaysDTO(int $threshold = 5): Collection
    {
        return $this->repository->getEmployeesWithLowVacationDaysDTO($threshold);
    }

    public function getEmployeesWithLowSickDays(int $threshold = 2): Collection
    {
        return $this->repository->findActive()
            ->filter(fn ($employee) => $employee->hasLowSickDays($threshold));
    }

    public function getEmployeesWithLowSickDaysDTO(int $threshold = 2): Collection
    {
        return $this->getEmployeesWithLowSickDays($threshold)
            ->map(fn ($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesWithNoVacationDays(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn ($employee) => ! $employee->hasVacationDays());
    }

    public function getEmployeesWithNoVacationDaysDTO(): Collection
    {
        return $this->getEmployeesWithNoVacationDays()
            ->map(fn ($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesWithNoSickDays(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn ($employee) => ! $employee->hasSickDays());
    }

    public function getEmployeesWithNoSickDaysDTO(): Collection
    {
        return $this->getEmployeesWithNoSickDays()
            ->map(fn ($employee) => EmployeeDTO::fromModel($employee));
    }

    // Time-off analytics
    public function getTimeOffStats(): array
    {
        $employees = $this->repository->findActive();

        $stats = [
            'total_employees' => $employees->count(),
            'vacation_stats' => [
                'total_days_allocated' => $employees->sum('vacation_days_total'),
                'total_days_used' => $employees->sum('vacation_days_used'),
                'total_days_remaining' => $employees->sum('vacation_days_total') - $employees->sum('vacation_days_used'),
                'average_days_allocated' => $employees->avg('vacation_days_total'),
                'average_days_used' => $employees->avg('vacation_days_used'),
                'average_days_remaining' => $employees->avg('vacation_days_total') - $employees->avg('vacation_days_used'),
                'utilization_rate' => $employees->sum('vacation_days_total') > 0
                    ? round(($employees->sum('vacation_days_used') / $employees->sum('vacation_days_total')) * 100, 2)
                    : 0,
            ],
            'sick_stats' => [
                'total_days_allocated' => $employees->sum('sick_days_total'),
                'total_days_used' => $employees->sum('sick_days_used'),
                'total_days_remaining' => $employees->sum('sick_days_total') - $employees->sum('sick_days_used'),
                'average_days_allocated' => $employees->avg('sick_days_total'),
                'average_days_used' => $employees->avg('sick_days_used'),
                'average_days_remaining' => $employees->avg('sick_days_total') - $employees->avg('sick_days_used'),
                'utilization_rate' => $employees->sum('sick_days_total') > 0
                    ? round(($employees->sum('sick_days_used') / $employees->sum('sick_days_total')) * 100, 2)
                    : 0,
            ],
        ];

        return $stats;
    }

    public function getTimeOffStatsByDepartment(string $department): array
    {
        $employees = $this->repository->findByDepartment($department);

        if ($employees->isEmpty()) {
            return [];
        }

        $stats = [
            'department' => $department,
            'total_employees' => $employees->count(),
            'vacation_stats' => [
                'total_days_allocated' => $employees->sum('vacation_days_total'),
                'total_days_used' => $employees->sum('vacation_days_used'),
                'total_days_remaining' => $employees->sum('vacation_days_total') - $employees->sum('vacation_days_used'),
                'average_days_allocated' => $employees->avg('vacation_days_total'),
                'average_days_used' => $employees->avg('vacation_days_used'),
                'average_days_remaining' => $employees->avg('vacation_days_total') - $employees->avg('vacation_days_used'),
                'utilization_rate' => $employees->sum('vacation_days_total') > 0
                    ? round(($employees->sum('vacation_days_used') / $employees->sum('vacation_days_total')) * 100, 2)
                    : 0,
            ],
            'sick_stats' => [
                'total_days_allocated' => $employees->sum('sick_days_total'),
                'total_days_used' => $employees->sum('sick_days_used'),
                'total_days_remaining' => $employees->sum('sick_days_total') - $employees->sum('sick_days_used'),
                'average_days_allocated' => $employees->avg('sick_days_total'),
                'average_days_used' => $employees->avg('sick_days_used'),
                'average_days_remaining' => $employees->avg('sick_days_total') - $employees->avg('sick_days_used'),
                'utilization_rate' => $employees->sum('sick_days_total') > 0
                    ? round(($employees->sum('sick_days_used') / $employees->sum('sick_days_total')) * 100, 2)
                    : 0,
            ],
        ];

        return $stats;
    }

    public function getTimeOffStatsByEmploymentType(string $employmentType): array
    {
        $employees = $this->repository->findByEmploymentType($employmentType);

        if ($employees->isEmpty()) {
            return [];
        }

        $stats = [
            'employment_type' => $employmentType,
            'total_employees' => $employees->count(),
            'vacation_stats' => [
                'total_days_allocated' => $employees->sum('vacation_days_total'),
                'total_days_used' => $employees->sum('vacation_days_used'),
                'total_days_remaining' => $employees->sum('vacation_days_total') - $employees->sum('vacation_days_used'),
                'average_days_allocated' => $employees->avg('vacation_days_total'),
                'average_days_used' => $employees->avg('vacation_days_used'),
                'average_days_remaining' => $employees->avg('vacation_days_total') - $employees->avg('vacation_days_used'),
                'utilization_rate' => $employees->sum('vacation_days_total') > 0
                    ? round(($employees->sum('vacation_days_used') / $employees->sum('vacation_days_total')) * 100, 2)
                    : 0,
            ],
            'sick_stats' => [
                'total_days_allocated' => $employees->sum('sick_days_total'),
                'total_days_used' => $employees->sum('sick_days_used'),
                'total_days_remaining' => $employees->sum('sick_days_total') - $employees->sum('sick_days_used'),
                'average_days_allocated' => $employees->avg('sick_days_total'),
                'average_days_used' => $employees->avg('sick_days_used'),
                'average_days_remaining' => $employees->avg('sick_days_total') - $employees->avg('sick_days_used'),
                'utilization_rate' => $employees->sum('sick_days_total') > 0
                    ? round(($employees->sum('sick_days_used') / $employees->sum('sick_days_total')) * 100, 2)
                    : 0,
            ],
        ];

        return $stats;
    }

    // Time-off alerts and notifications
    public function getEmployeesNeedingTimeOffAlert(): array
    {
        $alerts = [
            'low_vacation' => $this->getEmployeesWithLowVacationDays(3)->toArray(),
            'no_vacation' => $this->getEmployeesWithNoVacationDays()->toArray(),
            'low_sick_days' => $this->getEmployeesWithLowSickDays(1)->toArray(),
            'no_sick_days' => $this->getEmployeesWithNoSickDays()->toArray(),
        ];

        return $alerts;
    }

    // Time-off planning
    public function getTimeOffForecast(int $months = 12): array
    {
        // Implementation for time-off forecasting
        return [];
    }

    public function getOptimalTimeOffSchedule(?string $department = null): array
    {
        // Implementation for optimal time-off scheduling
        return [];
    }

    // Time-off policies
    public function getTimeOffPolicy(Employee $employee): array
    {
        $policy = [
            'vacation_days' => [
                'total' => $employee->vacation_days_total,
                'used' => $employee->vacation_days_used,
                'remaining' => $employee->remaining_vacation_days,
                'carryover_limit' => 5, // Example policy
                'advance_notice_days' => 14, // Example policy
            ],
            'sick_days' => [
                'total' => $employee->sick_days_total,
                'used' => $employee->sick_days_used,
                'remaining' => $employee->remaining_sick_days,
                'carryover_limit' => 3, // Example policy
                'doctor_note_required_after' => 3, // Example policy
            ],
            'employment_type' => $employee->employment_type->value,
            'department' => $employee->department,
        ];

        return $policy;
    }

    // Time-off requests (placeholder for future implementation)
    public function submitTimeOffRequest(Employee $employee, string $type, string $startDate, string $endDate, ?string $reason = null): bool
    {
        // Implementation for time-off request submission
        return true;
    }

    public function approveTimeOffRequest(int $requestId): bool
    {
        // Implementation for time-off request approval
        return true;
    }

    public function rejectTimeOffRequest(int $requestId, ?string $reason = null): bool
    {
        // Implementation for time-off request rejection
        return true;
    }

    public function getPendingTimeOffRequests(): array
    {
        // Implementation for getting pending time-off requests
        return [];
    }

    // Time-off reporting
    public function generateTimeOffReport(?string $department = null, string $period = 'current'): array
    {
        $employees = $department
            ? $this->repository->findByDepartment($department)
            : $this->repository->findActive();

        $report = [
            'period' => $period,
            'department' => $department,
            'total_employees' => $employees->count(),
            'time_off_stats' => $this->getTimeOffStats(),
            'employees_low_vacation' => $this->getEmployeesWithLowVacationDays(5)->count(),
            'employees_no_vacation' => $this->getEmployeesWithNoVacationDays()->count(),
            'employees_low_sick_days' => $this->getEmployeesWithLowSickDays(2)->count(),
            'employees_no_sick_days' => $this->getEmployeesWithNoSickDays()->count(),
            'top_vacation_users' => $employees->sortByDesc('vacation_days_used')->take(5)->values(),
            'top_sick_day_users' => $employees->sortByDesc('sick_days_used')->take(5)->values(),
        ];

        return $report;
    }
}
