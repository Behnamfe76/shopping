<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface EmployeeTimeOffRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?EmployeeTimeOff;

    public function findDTO(int $id): ?EmployeeTimeOffDTO;

    public function findByEmployeeId(int $employeeId): Collection;

    public function findByEmployeeIdDTO(int $employeeId): Collection;

    public function findByUserId(int $userId): Collection;

    public function findByUserIdDTO(int $userId): Collection;

    public function findByStatus(string $status): Collection;

    public function findByStatusDTO(string $status): Collection;

    public function findByTimeOffType(string $timeOffType): Collection;

    public function findByTimeOffTypeDTO(string $timeOffType): Collection;

    // Date range operations
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    public function findByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection;

    public function findByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection;

    // Status-based operations
    public function findPending(): Collection;

    public function findPendingDTO(): Collection;

    public function findApproved(): Collection;

    public function findApprovedDTO(): Collection;

    public function findRejected(): Collection;

    public function findRejectedDTO(): Collection;

    public function findCancelled(): Collection;

    public function findCancelledDTO(): Collection;

    public function findUrgent(): Collection;

    public function findUrgentDTO(): Collection;

    // Overlap detection
    public function findOverlapping(int $employeeId, string $startDate, string $endDate): Collection;

    public function findOverlappingDTO(int $employeeId, string $startDate, string $endDate): Collection;

    // Approver operations
    public function findByApproverId(int $approverId): Collection;

    public function findByApproverIdDTO(int $approverId): Collection;

    // Time-based operations
    public function findUpcoming(int $employeeId, ?string $date = null): Collection;

    public function findUpcomingDTO(int $employeeId, ?string $date = null): Collection;

    public function findPast(int $employeeId, ?string $date = null): Collection;

    public function findPastDTO(int $employeeId, ?string $date = null): Collection;

    // Create and update operations
    public function create(array $data): EmployeeTimeOff;

    public function createAndReturnDTO(array $data): EmployeeTimeOffDTO;

    public function update(EmployeeTimeOff $timeOff, array $data): bool;

    public function updateAndReturnDTO(EmployeeTimeOff $timeOff, array $data): ?EmployeeTimeOffDTO;

    public function delete(EmployeeTimeOff $timeOff): bool;

    // Workflow operations
    public function approve(EmployeeTimeOff $timeOff, int $approvedBy): bool;

    public function reject(EmployeeTimeOff $timeOff, int $rejectedBy, ?string $reason = null): bool;

    public function cancel(EmployeeTimeOff $timeOff, ?string $reason = null): bool;

    public function markAsUrgent(EmployeeTimeOff $timeOff): bool;

    public function removeUrgentFlag(EmployeeTimeOff $timeOff): bool;

    // Count operations
    public function getEmployeeTimeOffCount(int $employeeId): int;

    public function getEmployeeTimeOffCountByType(int $employeeId, string $timeOffType): int;

    public function getEmployeeTimeOffCountByStatus(int $employeeId, string $status): int;

    public function getEmployeeTotalDaysUsed(int $employeeId, ?string $year = null): float;

    public function getEmployeeTotalHoursUsed(int $employeeId, ?string $year = null): float;

    public function getEmployeeRemainingDays(int $employeeId, string $timeOffType, ?string $year = null): float;

    public function getEmployeeRemainingHours(int $employeeId, string $timeOffType, ?string $year = null): float;

    public function getTotalTimeOffCount(): int;

    public function getTotalTimeOffCountByType(string $timeOffType): int;

    public function getTotalTimeOffCountByStatus(string $status): int;

    public function getPendingApprovalCount(): int;

    public function getUrgentRequestCount(): int;

    public function getOverlappingRequestCount(int $employeeId, string $startDate, string $endDate): int;

    // Search operations
    public function searchTimeOff(string $query): Collection;

    public function searchTimeOffDTO(string $query): Collection;

    public function searchTimeOffByEmployee(int $employeeId, string $query): Collection;

    public function searchTimeOffByEmployeeDTO(int $employeeId, string $query): Collection;

    // Import/Export operations
    public function exportTimeOffData(array $filters = []): string;

    public function importTimeOffData(string $data): bool;

    // Statistics operations
    public function getTimeOffStatistics(?int $employeeId = null): array;

    public function getDepartmentTimeOffStatistics(int $departmentId): array;

    public function getCompanyTimeOffStatistics(): array;
}
