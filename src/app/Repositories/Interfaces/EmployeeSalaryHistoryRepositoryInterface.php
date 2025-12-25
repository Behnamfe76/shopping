<?php

namespace App\Repositories\Interfaces;

use App\DTOs\EmployeeSalaryHistoryDTO;
use App\Models\EmployeeSalaryHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface EmployeeSalaryHistoryRepositoryInterface
{
    /**
     * Get all salary history records
     */
    public function all(): Collection;

    /**
     * Get paginated salary history records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated salary history records
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated salary history records
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find salary history by ID
     */
    public function find(int $id): ?EmployeeSalaryHistory;

    /**
     * Find salary history by ID and return DTO
     */
    public function findDTO(int $id): ?EmployeeSalaryHistoryDTO;

    /**
     * Find salary history by employee ID
     */
    public function findByEmployeeId(int $employeeId): Collection;

    /**
     * Find salary history by employee ID and return DTOs
     */
    public function findByEmployeeIdDTO(int $employeeId): Collection;

    /**
     * Find salary history by change type
     */
    public function findByChangeType(string $changeType): Collection;

    /**
     * Find salary history by change type and return DTOs
     */
    public function findByChangeTypeDTO(string $changeType): Collection;

    /**
     * Find salary history by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find salary history by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find salary history by employee and date range
     */
    public function findByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection;

    /**
     * Find salary history by employee and date range and return DTOs
     */
    public function findByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection;

    /**
     * Find salary history by approver ID
     */
    public function findByApproverId(int $approverId): Collection;

    /**
     * Find salary history by approver ID and return DTOs
     */
    public function findByApproverIdDTO(int $approverId): Collection;

    /**
     * Find salary history by effective date
     */
    public function findByEffectiveDate(string $effectiveDate): Collection;

    /**
     * Find salary history by effective date and return DTOs
     */
    public function findByEffectiveDateDTO(string $effectiveDate): Collection;

    /**
     * Find retroactive salary adjustments
     */
    public function findRetroactive(): Collection;

    /**
     * Find retroactive salary adjustments and return DTOs
     */
    public function findRetroactiveDTO(): Collection;

    /**
     * Find salary history by change amount range
     */
    public function findByChangeAmountRange(float $minAmount, float $maxAmount): Collection;

    /**
     * Find salary history by change amount range and return DTOs
     */
    public function findByChangeAmountRangeDTO(float $minAmount, float $maxAmount): Collection;

    /**
     * Find salary history by change percentage range
     */
    public function findByChangePercentageRange(float $minPercentage, float $maxPercentage): Collection;

    /**
     * Find salary history by change percentage range and return DTOs
     */
    public function findByChangePercentageRangeDTO(float $minPercentage, float $maxPercentage): Collection;

    /**
     * Find latest salary history for employee
     */
    public function findLatestByEmployee(int $employeeId): ?EmployeeSalaryHistory;

    /**
     * Find latest salary history for employee and return DTO
     */
    public function findLatestByEmployeeDTO(int $employeeId): ?EmployeeSalaryHistoryDTO;

    /**
     * Find salary history by employee and change type
     */
    public function findByEmployeeAndType(int $employeeId, string $changeType): Collection;

    /**
     * Find salary history by employee and change type and return DTOs
     */
    public function findByEmployeeAndTypeDTO(int $employeeId, string $changeType): Collection;

    /**
     * Create new salary history record
     */
    public function create(array $data): EmployeeSalaryHistory;

    /**
     * Create new salary history record and return DTO
     */
    public function createAndReturnDTO(array $data): EmployeeSalaryHistoryDTO;

    /**
     * Update salary history record
     */
    public function update(EmployeeSalaryHistory $salaryHistory, array $data): bool;

    /**
     * Update salary history record and return DTO
     */
    public function updateAndReturnDTO(EmployeeSalaryHistory $salaryHistory, array $data): ?EmployeeSalaryHistoryDTO;

    /**
     * Delete salary history record
     */
    public function delete(EmployeeSalaryHistory $salaryHistory): bool;

    /**
     * Approve salary change
     */
    public function approve(EmployeeSalaryHistory $salaryHistory, int $approvedBy): bool;

    /**
     * Reject salary change
     */
    public function reject(EmployeeSalaryHistory $salaryHistory, int $rejectedBy, ?string $reason = null): bool;

    /**
     * Get employee salary history count
     */
    public function getEmployeeSalaryHistoryCount(int $employeeId): int;

    /**
     * Get employee salary history count by type
     */
    public function getEmployeeSalaryHistoryCountByType(int $employeeId, string $changeType): int;

    /**
     * Get employee total salary increase
     */
    public function getEmployeeTotalSalaryIncrease(int $employeeId, ?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get employee total salary decrease
     */
    public function getEmployeeTotalSalaryDecrease(int $employeeId, ?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get employee average salary change
     */
    public function getEmployeeAverageSalaryChange(int $employeeId): float;

    /**
     * Get employee current salary
     */
    public function getEmployeeCurrentSalary(int $employeeId): float;

    /**
     * Get employee starting salary
     */
    public function getEmployeeStartingSalary(int $employeeId): float;

    /**
     * Get employee salary growth
     */
    public function getEmployeeSalaryGrowth(int $employeeId): float;

    /**
     * Get total salary history count
     */
    public function getTotalSalaryHistoryCount(): int;

    /**
     * Get total salary history count by type
     */
    public function getTotalSalaryHistoryCountByType(string $changeType): int;

    /**
     * Get total salary increase
     */
    public function getTotalSalaryIncrease(?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get total salary decrease
     */
    public function getTotalSalaryDecrease(?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get average salary change
     */
    public function getAverageSalaryChange(): float;

    /**
     * Get average salary change by type
     */
    public function getAverageSalaryChangeByType(string $changeType): float;

    /**
     * Get salary change statistics
     */
    public function getSalaryChangeStatistics(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get department salary statistics
     */
    public function getDepartmentSalaryStatistics(int $departmentId, ?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get company salary statistics
     */
    public function getCompanySalaryStatistics(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Search salary history
     */
    public function searchSalaryHistory(string $query): Collection;

    /**
     * Search salary history and return DTOs
     */
    public function searchSalaryHistoryDTO(string $query): Collection;

    /**
     * Search salary history by employee
     */
    public function searchSalaryHistoryByEmployee(int $employeeId, string $query): Collection;

    /**
     * Search salary history by employee and return DTOs
     */
    public function searchSalaryHistoryByEmployeeDTO(int $employeeId, string $query): Collection;

    /**
     * Export salary history data
     */
    public function exportSalaryHistoryData(array $filters = []): string;

    /**
     * Import salary history data
     */
    public function importSalaryHistoryData(string $data): bool;

    /**
     * Get salary trends
     */
    public function getSalaryTrends(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get salary comparison between two employees
     */
    public function getSalaryComparison(int $employeeId1, int $employeeId2): array;
}
