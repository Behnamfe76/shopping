<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;

interface EmployeeBenefitsRepositoryInterface
{
    /**
     * Get all employee benefits
     */
    public function all(): Collection;

    /**
     * Get paginated employee benefits
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated employee benefits
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated employee benefits
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    /**
     * Find employee benefit by ID
     */
    public function find(int $id): ?EmployeeBenefits;

    /**
     * Find employee benefit by ID and return DTO
     */
    public function findDTO(int $id): ?EmployeeBenefitsDTO;

    /**
     * Find benefits by employee ID
     */
    public function findByEmployeeId(int $employeeId): Collection;

    /**
     * Find benefits by employee ID and return DTOs
     */
    public function findByEmployeeIdDTO(int $employeeId): Collection;

    /**
     * Find benefits by benefit type
     */
    public function findByBenefitType(string $benefitType): Collection;

    /**
     * Find benefits by benefit type and return DTOs
     */
    public function findByBenefitTypeDTO(string $benefitType): Collection;

    /**
     * Find benefits by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find benefits by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find benefits by provider
     */
    public function findByProvider(string $provider): Collection;

    /**
     * Find benefits by provider and return DTOs
     */
    public function findByProviderDTO(string $provider): Collection;

    /**
     * Find benefits by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find benefits by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find benefits by employee and type
     */
    public function findByEmployeeAndType(int $employeeId, string $benefitType): Collection;

    /**
     * Find benefits by employee and type and return DTOs
     */
    public function findByEmployeeAndTypeDTO(int $employeeId, string $benefitType): Collection;

    /**
     * Find active benefits
     */
    public function findActive(): Collection;

    /**
     * Find active benefits and return DTOs
     */
    public function findActiveDTO(): Collection;

    /**
     * Find pending benefits
     */
    public function findPending(): Collection;

    /**
     * Find pending benefits and return DTOs
     */
    public function findPendingDTO(): Collection;

    /**
     * Find terminated benefits
     */
    public function findTerminated(): Collection;

    /**
     * Find terminated benefits and return DTOs
     */
    public function findTerminatedDTO(): Collection;

    /**
     * Find benefits by effective date
     */
    public function findByEffectiveDate(string $effectiveDate): Collection;

    /**
     * Find benefits by effective date and return DTOs
     */
    public function findByEffectiveDateDTO(string $effectiveDate): Collection;

    /**
     * Find benefits by enrollment date
     */
    public function findByEnrollmentDate(string $enrollmentDate): Collection;

    /**
     * Find benefits by enrollment date and return DTOs
     */
    public function findByEnrollmentDateDTO(string $enrollmentDate): Collection;

    /**
     * Find benefits expiring soon
     */
    public function findExpiringSoon(int $days = 30): Collection;

    /**
     * Find benefits expiring soon and return DTOs
     */
    public function findExpiringSoonDTO(int $days = 30): Collection;

    /**
     * Find benefits by coverage level
     */
    public function findByCoverageLevel(string $coverageLevel): Collection;

    /**
     * Find benefits by coverage level and return DTOs
     */
    public function findByCoverageLevelDTO(string $coverageLevel): Collection;

    /**
     * Find benefits by network type
     */
    public function findByNetworkType(string $networkType): Collection;

    /**
     * Find benefits by network type and return DTOs
     */
    public function findByNetworkTypeDTO(string $networkType): Collection;

    /**
     * Create new employee benefit
     */
    public function create(array $data): EmployeeBenefits;

    /**
     * Create new employee benefit and return DTO
     */
    public function createAndReturnDTO(array $data): EmployeeBenefitsDTO;

    /**
     * Update employee benefit
     */
    public function update(EmployeeBenefits $benefit, array $data): bool;

    /**
     * Update employee benefit and return DTO
     */
    public function updateAndReturnDTO(EmployeeBenefits $benefit, array $data): ?EmployeeBenefitsDTO;

    /**
     * Delete employee benefit
     */
    public function delete(EmployeeBenefits $benefit): bool;

    /**
     * Enroll employee in benefits
     */
    public function enroll(EmployeeBenefits $benefit, string $effectiveDate = null): bool;

    /**
     * Terminate employee benefits
     */
    public function terminate(EmployeeBenefits $benefit, string $endDate = null, string $reason = null): bool;

    /**
     * Cancel employee benefits
     */
    public function cancel(EmployeeBenefits $benefit, string $reason = null): bool;

    /**
     * Activate employee benefits
     */
    public function activate(EmployeeBenefits $benefit): bool;

    /**
     * Deactivate employee benefits
     */
    public function deactivate(EmployeeBenefits $benefit): bool;

    /**
     * Update benefit costs
     */
    public function updateCosts(EmployeeBenefits $benefit, array $costData): bool;

    /**
     * Get employee benefits count
     */
    public function getEmployeeBenefitsCount(int $employeeId): int;

    /**
     * Get employee benefits count by type
     */
    public function getEmployeeBenefitsCountByType(int $employeeId, string $benefitType): int;

    /**
     * Get employee benefits count by status
     */
    public function getEmployeeBenefitsCountByStatus(int $employeeId, string $status): int;

    /**
     * Get employee total monthly cost
     */
    public function getEmployeeTotalMonthlyCost(int $employeeId): float;

    /**
     * Get employee total annual cost
     */
    public function getEmployeeTotalAnnualCost(int $employeeId): float;

    /**
     * Get employee contribution
     */
    public function getEmployeeContribution(int $employeeId): float;

    /**
     * Get employer contribution
     */
    public function getEmployerContribution(int $employeeId): float;

    /**
     * Get total benefits count
     */
    public function getTotalBenefitsCount(): int;

    /**
     * Get total benefits count by type
     */
    public function getTotalBenefitsCountByType(string $benefitType): int;

    /**
     * Get total benefits count by status
     */
    public function getTotalBenefitsCountByStatus(string $status): int;

    /**
     * Get total monthly cost
     */
    public function getTotalMonthlyCost(): float;

    /**
     * Get total annual cost
     */
    public function getTotalAnnualCost(): float;

    /**
     * Get total employee contribution
     */
    public function getTotalEmployeeContribution(): float;

    /**
     * Get total employer contribution
     */
    public function getTotalEmployerContribution(): float;

    /**
     * Get active enrollments count
     */
    public function getActiveEnrollmentsCount(): int;

    /**
     * Get pending enrollments count
     */
    public function getPendingEnrollmentsCount(): int;

    /**
     * Get expiring enrollments count
     */
    public function getExpiringEnrollmentsCount(int $days = 30): int;

    /**
     * Search benefits
     */
    public function searchBenefits(string $query): Collection;

    /**
     * Search benefits and return DTOs
     */
    public function searchBenefitsDTO(string $query): Collection;

    /**
     * Search benefits by employee
     */
    public function searchBenefitsByEmployee(int $employeeId, string $query): Collection;

    /**
     * Search benefits by employee and return DTOs
     */
    public function searchBenefitsByEmployeeDTO(int $employeeId, string $query): Collection;

    /**
     * Export benefits data
     */
    public function exportBenefitsData(array $filters = []): string;

    /**
     * Import benefits data
     */
    public function importBenefitsData(string $data): bool;

    /**
     * Get benefits statistics
     */
    public function getBenefitsStatistics(int $employeeId = null): array;

    /**
     * Get department benefits statistics
     */
    public function getDepartmentBenefitsStatistics(int $departmentId): array;

    /**
     * Get company benefits statistics
     */
    public function getCompanyBenefitsStatistics(): array;

    /**
     * Get cost analysis
     */
    public function getCostAnalysis(int $employeeId = null): array;

    /**
     * Get enrollment trends
     */
    public function getEnrollmentTrends(string $startDate = null, string $endDate = null): array;
}
