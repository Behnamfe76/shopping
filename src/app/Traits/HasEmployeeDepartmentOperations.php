<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\DTOs\EmployeeDepartmentDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;

trait HasEmployeeDepartmentOperations
{
    protected EmployeeDepartmentRepositoryInterface $departmentRepository;

    /**
     * Get all departments
     */
    public function getAllDepartments(): Collection
    {
        return $this->departmentRepository->all();
    }

    /**
     * Get all departments as DTOs
     */
    public function getAllDepartmentsDTO(): Collection
    {
        return $this->getAllDepartments()->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    /**
     * Paginate departments
     */
    public function paginateDepartments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->departmentRepository->paginate($perPage);
    }

    /**
     * Find department by ID
     */
    public function findDepartment(int $id): ?EmployeeDepartment
    {
        return $this->departmentRepository->find($id);
    }

    /**
     * Find department by ID as DTO
     */
    public function findDepartmentDTO(int $id): ?EmployeeDepartmentDTO
    {
        return $this->departmentRepository->findDTO($id);
    }

    /**
     * Find department by name
     */
    public function findDepartmentByName(string $name): ?EmployeeDepartment
    {
        return $this->departmentRepository->findByName($name);
    }

    /**
     * Find department by name as DTO
     */
    public function findDepartmentByNameDTO(string $name): ?EmployeeDepartmentDTO
    {
        return $this->departmentRepository->findByNameDTO($name);
    }

    /**
     * Find department by code
     */
    public function findDepartmentByCode(string $code): ?EmployeeDepartment
    {
        return $this->departmentRepository->findByCode($code);
    }

    /**
     * Find department by code as DTO
     */
    public function findDepartmentByCodeDTO(string $code): ?EmployeeDepartmentDTO
    {
        return $this->departmentRepository->findByCodeDTO($code);
    }

    /**
     * Create new department
     */
    public function createDepartment(array $data): EmployeeDepartment
    {
        return $this->departmentRepository->create($data);
    }

    /**
     * Create new department and return DTO
     */
    public function createDepartmentDTO(array $data): EmployeeDepartmentDTO
    {
        return $this->departmentRepository->createAndReturnDTO($data);
    }

    /**
     * Update department
     */
    public function updateDepartment(EmployeeDepartment $department, array $data): bool
    {
        return $this->departmentRepository->update($department, $data);
    }

    /**
     * Update department and return DTO
     */
    public function updateDepartmentDTO(EmployeeDepartment $department, array $data): ?EmployeeDepartmentDTO
    {
        return $this->departmentRepository->updateAndReturnDTO($department, $data);
    }

    /**
     * Delete department
     */
    public function deleteDepartment(EmployeeDepartment $department): bool
    {
        return $this->departmentRepository->delete($department);
    }

    /**
     * Search departments
     */
    public function searchDepartments(string $query): Collection
    {
        return $this->departmentRepository->searchDepartments($query);
    }

    /**
     * Search departments as DTOs
     */
    public function searchDepartmentsDTO(string $query): Collection
    {
        return $this->departmentRepository->searchDepartmentsDTO($query);
    }

    /**
     * Get department statistics
     */
    public function getDepartmentStatistics(): array
    {
        return $this->departmentRepository->getDepartmentStatistics();
    }

    /**
     * Export department data
     */
    public function exportDepartmentData(array $filters = []): string
    {
        return $this->departmentRepository->exportDepartmentData($filters);
    }

    /**
     * Import department data
     */
    public function importDepartmentData(string $data): bool
    {
        return $this->departmentRepository->importDepartmentData($data);
    }

    /**
     * Check if department exists
     */
    public function departmentExists(int $id): bool
    {
        return $this->findDepartment($id) !== null;
    }

    /**
     * Check if department exists by code
     */
    public function departmentExistsByCode(string $code): bool
    {
        return $this->findDepartmentByCode($code) !== null;
    }

    /**
     * Check if department exists by name
     */
    public function departmentExistsByName(string $name): bool
    {
        return $this->findDepartmentByName($name) !== null;
    }

    /**
     * Get department count
     */
    public function getDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCount();
    }

    /**
     * Get department count by status
     */
    public function getDepartmentCountByStatus(string $status): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus($status);
    }
}
