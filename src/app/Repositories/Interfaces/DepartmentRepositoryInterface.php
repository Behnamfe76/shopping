<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface DepartmentRepositoryInterface
{
    /**
     * Get all departments
     */
    public function all(): Collection;

    /**
     * Get paginated departments (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated departments
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated departments
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Get cursor paginated departments
     */
    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find department by ID
     */
    public function find(int $id): ?Department;

    /**
     * Find department by ID and return DTO
     */
    public function findDTO(int $id): ?DepartmentDTO;

    /**
     * Find department by code
     */
    public function findByCode(string $code): ?Department;

    /**
     * Find department by code and return DTO
     */
    public function findByCodeDTO(string $code): ?DepartmentDTO;

    /**
     * Create a new department
     */
    public function create(array $data): Department;

    /**
     * Create a new department and return DTO
     */
    public function createAndReturnDTO(array $data): DepartmentDTO;

    /**
     * Update department
     */
    public function update(Department $department, array $data): bool;

    /**
     * Update department and return DTO
     */
    public function updateAndReturnDTO(Department $department, array $data): ?DepartmentDTO;

    /**
     * Delete department
     */
    public function delete(Department $department): bool;

    /**
     * Get root departments (no parent)
     */
    public function getRootDepartments(): Collection;

    /**
     * Get root departments as DTOs
     */
    public function getRootDepartmentsDTO(): Collection;

    /**
     * Get departments with children
     */
    public function getWithChildren(): Collection;

    /**
     * Get department tree (hierarchical)
     */
    public function getTree(): Collection;

    /**
     * Get department tree as DTOs
     */
    public function getTreeDTO(): Collection;

    /**
     * Get departments by parent ID
     */
    public function getByParentId(?int $parentId): Collection;

    /**
     * Get children by parent ID
     */
    public function getChildren(int $parentId): Collection;

    /**
     * Get children by parent ID as DTOs
     */
    public function getChildrenDTO(int $parentId): Collection;

    /**
     * Get department with all children
     */
    public function getWithAllChildren(int $departmentId): ?Department;

    /**
     * Get department with all parents
     */
    public function getWithAllParents(int $departmentId): ?Department;

    /**
     * Get department's ancestors
     */
    public function getAncestors(int $departmentId): \Illuminate\Support\Collection;

    /**
     * Get department's ancestors as DTOs
     */
    public function getAncestorsDTO(int $departmentId): Collection;

    /**
     * Get department's descendants
     */
    public function getDescendants(int $departmentId): \Illuminate\Support\Collection;

    /**
     * Get department's descendants as DTOs
     */
    public function getDescendantsDTO(int $departmentId): Collection;

    /**
     * Search departments by name
     */
    public function search(string $query): Collection;

    /**
     * Get departments with employees count
     */
    public function getWithEmployeesCount(): Collection;

    /**
     * Check if department has children
     */
    public function hasChildren(Department $department): bool;

    /**
     * Check if department has employees
     */
    public function hasEmployees(Department $department): bool;

    /**
     * Move department to new parent
     */
    public function moveToParent(Department $department, ?int $newParentId): bool;

    /**
     * Move department
     */
    public function moveDepartment(Department $department, ?int $newParentId): bool;

    /**
     * Get department path (breadcrumb)
     */
    public function getPath(Department $department): Collection;

    /**
     * Get department path as DTOs
     */
    public function getDepartmentPath(int $departmentId): Collection;

    /**
     * Get department path as DTOs
     */
    public function getDepartmentPathDTO(int $departmentId): Collection;

    /**
     * Get department depth level
     */
    public function getDepth(Department $department): int;

    /**
     * Get departments by depth level
     */
    public function getByDepth(int $depth): Collection;

    /**
     * Get departments by status
     */
    public function findByStatus(DepartmentStatus $status): Collection;

    /**
     * Get departments by status as DTOs
     */
    public function findByStatusDTO(DepartmentStatus $status): Collection;

    /**
     * Get departments by location
     */
    public function findByLocation(string $location): Collection;

    /**
     * Get department statistics
     */
    public function getDepartmentStats(): array;

    /**
     * Get department statistics by status
     */
    public function getDepartmentStatsByStatus(): array;

    /**
     * Get department count
     */
    public function getDepartmentCount(): int;

    /**
     * Get department count by status
     */
    public function getDepartmentCountByStatus(DepartmentStatus $status): int;

    /**
     * Get department count by parent
     */
    public function getDepartmentCountByParent(int $parentId): int;

    /**
     * Validate department data
     */
    public function validateDepartment(array $data): bool;
}
