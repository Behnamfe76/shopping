<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Fereydooni\Shopping\app\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface DepartmentRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?Department;

    public function findDTO(int $id): ?DepartmentDTO;

    public function findByName(string $name): ?Department;

    public function findByNameDTO(string $name): ?DepartmentDTO;

    public function findByCode(string $code): ?Department;

    public function findByCodeDTO(string $code): ?DepartmentDTO;

    // Find by relationships
    public function findByManagerId(int $managerId): Collection;

    public function findByManagerIdDTO(int $managerId): Collection;

    public function findByParentId(int $parentId): Collection;

    public function findByParentIdDTO(int $parentId): Collection;

    // Find by status and filters
    public function findByStatus(string $status): Collection;

    public function findByStatusDTO(string $status): Collection;

    public function findByLocation(string $location): Collection;

    public function findByLocationDTO(string $location): Collection;

    // Status-based queries
    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findInactive(): Collection;

    public function findInactiveDTO(): Collection;

    // Hierarchy queries
    public function findRoot(): Collection;

    public function findRootDTO(): Collection;

    public function findChildren(int $parentId): Collection;

    public function findChildrenDTO(int $parentId): Collection;

    public function findDescendants(int $parentId): Collection;

    public function findDescendantsDTO(int $parentId): Collection;

    public function findAncestors(int $departmentId): Collection;

    public function findAncestorsDTO(int $departmentId): Collection;

    // Create and update operations
    public function create(array $data): Department;

    public function createAndReturnDTO(array $data): DepartmentDTO;

    public function update(Department $department, array $data): bool;

    public function updateAndReturnDTO(Department $department, array $data): ?DepartmentDTO;

    // Delete operations
    public function delete(Department $department): bool;

    // Status management
    public function activate(Department $department): bool;

    public function deactivate(Department $department): bool;

    public function archive(Department $department): bool;

    // Manager operations
    public function assignManager(Department $department, int $managerId): bool;

    public function removeManager(Department $department): bool;

    // Hierarchy operations
    public function moveToParent(Department $department, int $newParentId): bool;

    // Analytics and metrics
    public function getDepartmentEmployeeCount(int $departmentId): int;

    public function getDepartmentBudget(int $departmentId): float;

    public function getDepartmentBudgetUtilization(int $departmentId): float;

    public function getDepartmentHeadcountUtilization(int $departmentId): float;

    // Statistics
    public function getTotalDepartmentCount(): int;

    public function getTotalDepartmentCountByStatus(string $status): int;

    public function getTotalEmployeeCount(): int;

    public function getTotalBudget(): float;

    // Hierarchy and tree operations
    public function getDepartmentHierarchy(): array;

    public function getDepartmentTree(): array;

    // Search operations
    public function searchDepartments(string $query): Collection;

    public function searchDepartmentsDTO(string $query): Collection;

    // Import/Export operations
    public function exportDepartmentData(array $filters = []): string;

    public function importDepartmentData(string $data): bool;

    // Analytics and reporting
    public function getDepartmentStatistics(): array;

    public function getDepartmentPerformanceMetrics(int $departmentId): array;

    public function getDepartmentTrends(?string $startDate = null, ?string $endDate = null): array;
}
