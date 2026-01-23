<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\Department;
use Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;

class DepartmentService
{
    use HasCrudOperations;

    public function __construct(
        protected DepartmentRepositoryInterface $repository
    ) {
        $this->model = Department::class;
        $this->dtoClass = DepartmentDTO::class;
    }

    public array $searchableFields = ['name', 'code', 'description'];

    // Basic CRUD Operations
    public function cursorAll(): CursorPaginator
    {
        return $this->repository->cursorAll(10, request()->get('cursor'));
    }

    public function find(int $id): ?Department
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?DepartmentDTO
    {
        return $this->repository->findDTO($id);
    }

    public function findByCode(string $code): ?Department
    {
        return $this->repository->findByCode($code);
    }

    public function findByCodeDTO(string $code): ?DepartmentDTO
    {
        return $this->repository->findByCodeDTO($code);
    }

    // Override create method to handle department-specific logic
    public function create(array $data): Department
    {
        $this->validateData($data);

        return $this->repository->create($data);
    }

    // Override createDTO method to handle department-specific logic
    public function createDTO(array $data): DepartmentDTO
    {
        $this->validateData($data);

        return $this->repository->createAndReturnDTO($data);
    }

    // Override update method to handle department-specific logic
    public function update(Department $department, array $data): bool
    {
        $this->validateData($data, $department);

        return $this->repository->update($department, $data);
    }

    // Override updateDTO method to handle department-specific logic
    public function updateDTO(Department $department, array $data): ?DepartmentDTO
    {
        $this->validateData($data, $department);

        return $this->repository->updateAndReturnDTO($department, $data);
    }

    // Override delete method to handle department-specific logic
    public function delete(Department $department): bool
    {
        // Check if department has children
        if ($this->repository->hasChildren($department)) {
            throw new \InvalidArgumentException('Cannot delete department with children. Please reassign or delete child departments first.');
        }

        // Check if department has employees
        if ($this->repository->hasEmployees($department)) {
            throw new \InvalidArgumentException('Cannot delete department with employees. Please reassign employees first.');
        }

        return $this->repository->delete($department);
    }

    // Hierarchical Operations
    public function getRootDepartments(): Collection
    {
        return $this->repository->getRootDepartments();
    }

    public function getRootDepartmentsDTO(): Collection
    {
        return $this->repository->getRootDepartmentsDTO();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->repository->getChildren($parentId);
    }

    public function getChildrenDTO(int $parentId): Collection
    {
        return $this->repository->getChildrenDTO($parentId);
    }

    public function getAncestors(int $departmentId): \Illuminate\Support\Collection
    {
        return $this->repository->getAncestors($departmentId);
    }

    public function getAncestorsDTO(int $departmentId): Collection
    {
        return $this->repository->getAncestorsDTO($departmentId);
    }

    public function getDescendants(int $departmentId): \Illuminate\Support\Collection
    {
        return $this->repository->getDescendants($departmentId);
    }

    public function getDescendantsDTO(int $departmentId): Collection
    {
        return $this->repository->getDescendantsDTO($departmentId);
    }

    public function getTree(): Collection
    {
        return $this->repository->getTree();
    }

    public function getTreeDTO(): Collection
    {
        return $this->repository->getTreeDTO();
    }

    public function moveDepartment(Department $department, ?int $newParentId): bool
    {
        // Prevent circular references
        if ($newParentId && $this->wouldCreateCircularReference($department, $newParentId)) {
            throw new \InvalidArgumentException('Cannot move department: would create circular reference');
        }

        return $this->repository->moveDepartment($department, $newParentId);
    }

    // Status Operations
    public function findByStatus(DepartmentStatus $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(DepartmentStatus $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByLocation(string $location): Collection
    {
        return $this->repository->findByLocation($location);
    }

    // Statistics
    public function getDepartmentStats(): array
    {
        return $this->repository->getDepartmentStats();
    }

    public function getDepartmentStatsByStatus(): array
    {
        return $this->repository->getDepartmentStatsByStatus();
    }

    // Path and Depth
    public function getDepartmentPath(int $departmentId): Collection
    {
        return $this->repository->getDepartmentPath($departmentId);
    }

    public function getDepartmentPathDTO(int $departmentId): Collection
    {
        return $this->repository->getDepartmentPathDTO($departmentId);
    }

    public function getDepth(Department $department): int
    {
        return $this->repository->getDepth($department);
    }

    public function getByDepth(int $depth): Collection
    {
        return $this->repository->getByDepth($depth);
    }

    // Count Operations
    public function getDepartmentCount(): int
    {
        return $this->repository->getDepartmentCount();
    }

    public function getDepartmentCountByStatus(DepartmentStatus $status): int
    {
        return $this->repository->getDepartmentCountByStatus($status);
    }

    public function getDepartmentCountByParent(int $parentId): int
    {
        return $this->repository->getDepartmentCountByParent($parentId);
    }

    // Validation
    public function validateDepartment(array $data): bool
    {
        return $this->repository->validateDepartment($data);
    }

    // Helper Methods
    protected function validateData(array $data, ?Department $department = null): void
    {
        // Additional department-specific validation can be added here
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = Department::find($data['parent_id']);
            if (! $parent) {
                throw new \InvalidArgumentException('Parent department not found');
            }

            // Prevent circular references
            if ($department && $this->wouldCreateCircularReference($department, $data['parent_id'])) {
                throw new \InvalidArgumentException('Cannot move department: would create circular reference');
            }
        }

        // Validate manager if provided
        if (isset($data['manager_id']) && $data['manager_id']) {
            $manager = \Fereydooni\Shopping\app\Models\Employee::find($data['manager_id']);
            if (! $manager) {
                throw new \InvalidArgumentException('Manager not found');
            }
        }
    }

    protected function wouldCreateCircularReference(Department $department, int $newParentId): bool
    {
        if ($department->id === $newParentId) {
            return true;
        }

        $parent = Department::find($newParentId);
        if (! $parent) {
            return false;
        }

        // Check if new parent is a descendant of current department
        $descendants = $this->getDescendants($department->id);

        return $descendants->contains('id', $newParentId);
    }
}
