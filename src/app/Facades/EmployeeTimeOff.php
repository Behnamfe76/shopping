<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Actions\EmployeeTimeOff\CreateEmployeeTimeOffAction;
use Fereydooni\Shopping\app\Actions\EmployeeTimeOff\ApproveEmployeeTimeOffAction;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeTimeOff extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'employee-time-off';
    }

    public static function create(array $data): EmployeeTimeOffDTO
    {
        $action = app(CreateEmployeeTimeOffAction::class);
        return $action->execute($data);
    }

    public static function approve(EmployeeTimeOff $timeOff, int $approvedBy): EmployeeTimeOffDTO
    {
        $action = app(ApproveEmployeeTimeOffAction::class);
        return $action->execute($timeOff, $approvedBy);
    }

    public static function find(int $id): ?EmployeeTimeOff
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->find($id);
    }

    public static function findDTO(int $id): ?EmployeeTimeOffDTO
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findDTO($id);
    }

    public static function findByEmployee(int $employeeId): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByEmployeeId($employeeId);
    }

    public static function findByEmployeeDTO(int $employeeId): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByEmployeeIdDTO($employeeId);
    }

    public static function findByStatus(string $status): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByStatus($status);
    }

    public static function findByStatusDTO(string $status): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByStatusDTO($status);
    }

    public static function findPending(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findPending();
    }

    public static function findPendingDTO(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findPendingDTO();
    }

    public static function findApproved(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findApproved();
    }

    public static function findApprovedDTO(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findApprovedDTO();
    }

    public static function findRejected(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findRejected();
    }

    public static function findRejectedDTO(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findRejectedDTO();
    }

    public static function findCancelled(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findCancelled();
    }

    public static function findCancelledDTO(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findCancelledDTO();
    }

    public static function findUrgent(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findUrgent();
    }

    public static function findUrgentDTO(): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findUrgentDTO();
    }

    public static function findByDateRange(string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByDateRange($startDate, $endDate);
    }

    public static function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByDateRangeDTO($startDate, $endDate);
    }

    public static function findByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByEmployeeAndDateRange($employeeId, $startDate, $endDate);
    }

    public static function findByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findByEmployeeAndDateRangeDTO($employeeId, $startDate, $endDate);
    }

    public static function findOverlapping(int $employeeId, string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findOverlapping($employeeId, $startDate, $endDate);
    }

    public static function findOverlappingDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->findOverlappingDTO($employeeId, $startDate, $endDate);
    }

    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->paginate($perPage);
    }

    public static function search(string $query): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->searchTimeOff($query);
    }

    public static function searchDTO(string $query): Collection
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->searchTimeOffDTO($query);
    }

    public static function getStatistics(int $employeeId = null): array
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->getTimeOffStatistics($employeeId);
    }

    public static function getCompanyStatistics(): array
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->getCompanyTimeOffStatistics();
    }

    public static function getPendingCount(): int
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->getPendingApprovalCount();
    }

    public static function getUrgentCount(): int
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->getUrgentRequestCount();
    }

    public static function getEmployeeBalance(int $employeeId, string $timeOffType, string $year = null): array
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);

        return [
            'total_days_used' => $repository->getEmployeeTotalDaysUsed($employeeId, $year),
            'total_hours_used' => $repository->getEmployeeTotalHoursUsed($employeeId, $year),
            'remaining_days' => $repository->getEmployeeRemainingDays($employeeId, $timeOffType, $year),
            'remaining_hours' => $repository->getEmployeeRemainingHours($employeeId, $timeOffType, $year),
        ];
    }

    public static function export(array $filters = []): string
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->exportTimeOffData($filters);
    }

    public static function import(string $data): bool
    {
        $repository = app(EmployeeTimeOffRepositoryInterface::class);
        return $repository->importTimeOffData($data);
    }
}

