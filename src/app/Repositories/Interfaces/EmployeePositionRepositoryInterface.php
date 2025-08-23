<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\DTOs\EmployeePositionDTO;

interface EmployeePositionRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?EmployeePosition;
    public function findDTO(int $id): ?EmployeePositionDTO;
    public function findByTitle(string $title): ?EmployeePosition;
    public function findByTitleDTO(string $title): ?EmployeePositionDTO;
    public function findByCode(string $code): ?EmployeePosition;
    public function findByCodeDTO(string $code): ?EmployeePositionDTO;

    // Filter operations
    public function findByDepartmentId(int $departmentId): Collection;
    public function findByDepartmentIdDTO(int $departmentId): Collection;
    public function findByLevel(string $level): Collection;
    public function findByLevelDTO(string $level): Collection;
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;

    // Salary and rate operations
    public function findBySalaryRange(float $minSalary, float $maxSalary): Collection;
    public function findBySalaryRangeDTO(float $minSalary, float $maxSalary): Collection;
    public function findByHourlyRateRange(float $minRate, float $maxRate): Collection;
    public function findByHourlyRateRangeDTO(float $minRate, float $maxRate): Collection;

    // Status-based operations
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findInactive(): Collection;
    public function findInactiveDTO(): Collection;
    public function findHiring(): Collection;
    public function findHiringDTO(): Collection;

    // Work arrangement operations
    public function findRemote(): Collection;
    public function findRemoteDTO(): Collection;
    public function findTravelRequired(): Collection;
    public function findTravelRequiredDTO(): Collection;

    // Skills and experience operations
    public function findBySkills(array $skills): Collection;
    public function findBySkillsDTO(array $skills): Collection;
    public function findByExperienceLevel(int $minExperience): Collection;
    public function findByExperienceLevelDTO(int $minExperience): Collection;

    // Create and update operations
    public function create(array $data): EmployeePosition;
    public function createAndReturnDTO(array $data): EmployeePositionDTO;
    public function update(EmployeePosition $position, array $data): bool;
    public function updateAndReturnDTO(EmployeePosition $position, array $data): ?EmployeePositionDTO;
    public function delete(EmployeePosition $position): bool;

    // Status management operations
    public function activate(EmployeePosition $position): bool;
    public function deactivate(EmployeePosition $position): bool;
    public function archive(EmployeePosition $position): bool;
    public function setHiring(EmployeePosition $position): bool;
    public function setFrozen(EmployeePosition $position): bool;

    // Salary and rate management
    public function updateSalaryRange(EmployeePosition $position, float $minSalary, float $maxSalary): bool;
    public function updateHourlyRateRange(EmployeePosition $position, float $minRate, float $maxRate): bool;

    // Skills management
    public function addSkillRequirement(EmployeePosition $position, string $skill): bool;
    public function removeSkillRequirement(EmployeePosition $position, string $skill): bool;

    // Analytics and statistics
    public function getPositionEmployeeCount(int $positionId): int;
    public function getPositionAverageSalary(int $positionId): float;
    public function getPositionSalaryRange(int $positionId): array;
    public function getTotalPositionCount(): int;
    public function getTotalPositionCountByStatus(string $status): int;
    public function getTotalPositionCountByLevel(string $level): int;
    public function getTotalPositionCountByDepartment(int $departmentId): int;
    public function getTotalActivePositions(): int;
    public function getTotalHiringPositions(): int;
    public function getTotalRemotePositions(): int;
    public function getAverageSalaryByLevel(string $level): float;
    public function getAverageSalaryByDepartment(int $departmentId): float;

    // Search operations
    public function searchPositions(string $query): Collection;
    public function searchPositionsDTO(string $query): Collection;
    public function searchPositionsByDepartment(int $departmentId, string $query): Collection;
    public function searchPositionsByDepartmentDTO(int $departmentId, string $query): Collection;

    // Import/Export operations
    public function exportPositionData(array $filters = []): string;
    public function importPositionData(string $data): bool;

    // Advanced analytics
    public function getPositionStatistics(): array;
    public function getDepartmentPositionStatistics(int $departmentId): array;
    public function getPositionTrends(string $startDate = null, string $endDate = null): array;
    public function getSalaryAnalysis(int $positionId = null): array;
}
