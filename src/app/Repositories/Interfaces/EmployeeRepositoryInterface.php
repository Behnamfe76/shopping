<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;

interface EmployeeRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?Employee;
    public function findDTO(int $id): ?EmployeeDTO;
    public function findByUserId(int $userId): ?Employee;
    public function findByUserIdDTO(int $userId): ?EmployeeDTO;
    public function findByEmail(string $email): ?Employee;
    public function findByEmailDTO(string $email): ?EmployeeDTO;
    public function findByPhone(string $phone): ?Employee;
    public function findByPhoneDTO(string $phone): ?EmployeeDTO;
    public function findByEmployeeNumber(string $employeeNumber): ?Employee;
    public function findByEmployeeNumberDTO(string $employeeNumber): ?EmployeeDTO;

    // Status-based queries
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findInactive(): Collection;
    public function findInactiveDTO(): Collection;
    public function findTerminated(): Collection;
    public function findTerminatedDTO(): Collection;

    // Employment type queries
    public function findByEmploymentType(string $employmentType): Collection;
    public function findByEmploymentTypeDTO(string $employmentType): Collection;

    // Department and position queries
    public function findByDepartment(string $department): Collection;
    public function findByDepartmentDTO(string $department): Collection;
    public function findByPosition(string $position): Collection;
    public function findByPositionDTO(string $position): Collection;

    // Manager-subordinate queries
    public function findByManagerId(int $managerId): Collection;
    public function findByManagerIdDTO(int $managerId): Collection;

    // Date range queries
    public function findByHireDateRange(string $startDate, string $endDate): Collection;
    public function findByHireDateRangeDTO(string $startDate, string $endDate): Collection;

    // Salary and performance queries
    public function findBySalaryRange(float $minSalary, float $maxSalary): Collection;
    public function findBySalaryRangeDTO(float $minSalary, float $maxSalary): Collection;
    public function findByPerformanceRating(float $minRating, float $maxRating): Collection;
    public function findByPerformanceRatingDTO(float $minRating, float $maxRating): Collection;

    // Create and Update operations
    public function create(array $data): Employee;
    public function createAndReturnDTO(array $data): EmployeeDTO;
    public function update(Employee $employee, array $data): bool;
    public function updateAndReturnDTO(Employee $employee, array $data): ?EmployeeDTO;
    public function delete(Employee $employee): bool;

    // Status management
    public function activate(Employee $employee): bool;
    public function deactivate(Employee $employee): bool;
    public function terminate(Employee $employee, string $reason = null, string $terminationDate = null): bool;
    public function rehire(Employee $employee, string $hireDate = null): bool;

    // Position and department management
    public function updateSalary(Employee $employee, float $newSalary, string $effectiveDate = null): bool;
    public function updatePosition(Employee $employee, string $newPosition, string $effectiveDate = null): bool;
    public function updateDepartment(Employee $employee, string $newDepartment, string $effectiveDate = null): bool;

    // Manager assignment
    public function assignManager(Employee $employee, int $managerId): bool;
    public function removeManager(Employee $employee): bool;

    // Time-off management
    public function addVacationDays(Employee $employee, int $days): bool;
    public function useVacationDays(Employee $employee, int $days): bool;
    public function addSickDays(Employee $employee, int $days): bool;
    public function useSickDays(Employee $employee, int $days): bool;

    // Performance management
    public function updatePerformanceRating(Employee $employee, float $rating, string $reviewDate = null): bool;

    // Statistics
    public function getEmployeeCount(): int;
    public function getEmployeeCountByStatus(string $status): int;
    public function getEmployeeCountByDepartment(string $department): int;
    public function getEmployeeCountByEmploymentType(string $employmentType): int;
    public function getActiveEmployeeCount(): int;
    public function getInactiveEmployeeCount(): int;
    public function getTerminatedEmployeeCount(): int;

    // Salary statistics
    public function getTotalSalary(): float;
    public function getAverageSalary(): float;
    public function getTotalSalaryByDepartment(string $department): float;
    public function getAverageSalaryByDepartment(string $department): float;

    // Performance statistics
    public function getAveragePerformanceRating(): float;
    public function getAveragePerformanceRatingByDepartment(string $department): float;

    // Search operations
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByDepartment(string $department, string $query): Collection;
    public function searchByDepartmentDTO(string $department, string $query): Collection;
    public function searchByPosition(string $position, string $query): Collection;
    public function searchByPositionDTO(string $position, string $query): Collection;

    // Top performers and analytics
    public function getTopPerformers(int $limit = 10): Collection;
    public function getTopPerformersDTO(int $limit = 10): Collection;
    public function getLongestServing(int $limit = 10): Collection;
    public function getLongestServingDTO(int $limit = 10): Collection;
    public function getNewestHires(int $limit = 10): Collection;
    public function getNewestHiresDTO(int $limit = 10): Collection;

    // Salary range queries
    public function getEmployeesBySalaryRange(float $minSalary, float $maxSalary): Collection;
    public function getEmployeesBySalaryRangeDTO(float $minSalary, float $maxSalary): Collection;

    // Review and time-off queries
    public function getEmployeesWithUpcomingReviews(int $daysAhead = 30): Collection;
    public function getEmployeesWithUpcomingReviewsDTO(int $daysAhead = 30): Collection;
    public function getEmployeesWithLowVacationDays(int $threshold = 5): Collection;
    public function getEmployeesWithLowVacationDaysDTO(int $threshold = 5): Collection;

    // Skills and certifications
    public function getEmployeesByCertification(string $certification): Collection;
    public function getEmployeesByCertificationDTO(string $certification): Collection;
    public function getEmployeesBySkill(string $skill): Collection;
    public function getEmployeesBySkillDTO(string $skill): Collection;

    // Validation and utilities
    public function validateEmployee(array $data): bool;
    public function generateEmployeeNumber(): string;
    public function isEmployeeNumberUnique(string $employeeNumber): bool;

    // Analytics and reporting
    public function getEmployeeStats(): array;
    public function getEmployeeStatsByStatus(): array;
    public function getEmployeeStatsByDepartment(): array;
    public function getEmployeeStatsByEmploymentType(): array;
    public function getEmployeeGrowthStats(string $period = 'monthly'): array;
    public function getEmployeeTurnoverStats(): array;
    public function getEmployeeRetentionStats(): array;
    public function getEmployeePerformanceStats(): array;
    public function getEmployeeSalaryStats(): array;
    public function getEmployeeTimeOffStats(): array;

    // Hierarchy management
    public function getEmployeeHierarchy(int $employeeId): array;
    public function getEmployeeSubordinates(int $employeeId): Collection;
    public function getEmployeeSubordinatesDTO(int $employeeId): Collection;
    public function getEmployeeManagers(int $employeeId): Collection;
    public function getEmployeeManagersDTO(int $employeeId): Collection;

    // Notes and additional data
    public function addEmployeeNote(Employee $employee, string $note, string $type = 'general'): bool;
    public function getEmployeeNotes(Employee $employee): Collection;

    // Benefits management
    public function updateEmployeeBenefits(Employee $employee, array $benefits): bool;
    public function getEmployeeBenefits(int $employeeId): array;

    // Skills and certifications management
    public function updateEmployeeSkills(Employee $employee, array $skills): bool;
    public function getEmployeeSkills(int $employeeId): array;
    public function updateEmployeeCertifications(Employee $employee, array $certifications): bool;
    public function getEmployeeCertifications(int $employeeId): array;
}

