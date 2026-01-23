<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\Department;
use Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function __construct(protected Department $model) {}

    public function all(): Collection
    {
        $select = '*';
        $columns = request()->get('columns', []);
        if (! empty($columns)) {
            $select = $columns;
        }

        return $this->model
            ->select($select)
            ->limit(250)
            ->get();
    }

    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $select = '*';
        $columns = request()->get('columns', []);
        if (! empty($columns)) {
            $select = $columns;
        }

        return $this->model
            ->query()->when(request()->input('search'), function ($query, $input) {
                return $query->whereLike('name', "%$input%");
            })
            ->select($select)
            ->cursorPaginate($perPage, [$columns], 'id', $cursor);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Department
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code): ?Department
    {
        return $this->model->where('code', $code)->first();
    }

    public function findDTO(int $id): ?DepartmentDTO
    {
        $department = $this->find($id);

        return $department ? DepartmentDTO::fromModel($department) : null;
    }

    public function findByCodeDTO(string $code): ?DepartmentDTO
    {
        $department = $this->findByCode($code);

        return $department ? DepartmentDTO::fromModel($department) : null;
    }

    public function create(array $data): Department
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): DepartmentDTO
    {
        $department = $this->create($data);

        return DepartmentDTO::fromModel($department);
    }

    public function update(Department $department, array $data): bool
    {
        return $department->update($data);
    }

    public function updateAndReturnDTO(Department $department, array $data): ?DepartmentDTO
    {
        $updated = $this->update($department, $data);

        return $updated ? DepartmentDTO::fromModel($department->fresh()) : null;
    }

    public function delete(Department $department): bool
    {
        return $department->delete();
    }

    public function getRootDepartments(): Collection
    {
        return $this->model->whereNull('parent_id')->get();
    }

    public function getRootDepartmentsDTO(): Collection
    {
        return $this->getRootDepartments()->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function getWithChildren(): Collection
    {
        return $this->model->with('children')->get();
    }

    public function getTree(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->with('allChildren')
            ->get();
    }

    public function getTreeDTO(): Collection
    {
        return $this->getTree()->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function getByParentId(?int $parentId): Collection
    {
        if ($parentId === null) {
            return $this->getRootDepartments();
        }

        return $this->model->where('parent_id', $parentId)->get();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->get();
    }

    public function getChildrenDTO(int $parentId): Collection
    {
        return $this->getChildren($parentId)->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function getWithAllChildren(int $departmentId): ?Department
    {
        return $this->model->with('allChildren')->find($departmentId);
    }

    public function getWithAllParents(int $departmentId): ?Department
    {
        return $this->model->with('allParents')->find($departmentId);
    }

    public function getAncestors(int $departmentId): \Illuminate\Support\Collection
    {
        $department = $this->find($departmentId);
        if (! $department) {
            return collect([]);
        }

        return collect($department->getAncestors());
    }

    public function getAncestorsDTO(int $departmentId): Collection
    {
        return $this->getAncestors($departmentId)->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function getDescendants(int $departmentId): \Illuminate\Support\Collection
    {
        $department = $this->find($departmentId);
        if (! $department) {
            return collect([]);
        }

        return collect($department->getDescendants());
    }

    public function getDescendantsDTO(int $departmentId): Collection
    {
        return $this->getDescendants($departmentId)->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    public function getWithEmployeesCount(): Collection
    {
        return $this->model->withCount('employees')->get();
    }

    public function hasChildren(Department $department): bool
    {
        return $department->children()->count() > 0;
    }

    public function hasEmployees(Department $department): bool
    {
        return $department->employees()->count() > 0;
    }

    public function moveToParent(Department $department, ?int $newParentId): bool
    {
        return $department->update(['parent_id' => $newParentId]);
    }

    public function moveDepartment(Department $department, ?int $newParentId): bool
    {
        return $this->moveToParent($department, $newParentId);
    }

    public function getPath(Department $department): Collection
    {
        $path = collect([$department]);
        $current = $department;

        while ($current->parent) {
            $current = $current->parent;
            $path->prepend($current);
        }

        return $path;
    }

    public function getDepartmentPath(int $departmentId): Collection
    {
        $department = $this->find($departmentId);
        if (! $department) {
            return collect([]);
        }

        return $this->getPath($department);
    }

    public function getDepartmentPathDTO(int $departmentId): Collection
    {
        return $this->getDepartmentPath($departmentId)->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function getDepth(Department $department): int
    {
        return $department->getDepth();
    }

    public function getByDepth(int $depth): Collection
    {
        return $this->model->all()->filter(function ($department) use ($depth) {
            return $department->getDepth() === $depth;
        });
    }

    public function findByStatus(DepartmentStatus $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function findByStatusDTO(DepartmentStatus $status): Collection
    {
        return $this->findByStatus($status)->map(fn ($department) => DepartmentDTO::fromModel($department));
    }

    public function findByLocation(string $location): Collection
    {
        return $this->model->where('location', $location)->get();
    }

    public function getDepartmentStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'with_manager' => $this->model->whereNotNull('manager_id')->count(),
            'without_manager' => $this->model->whereNull('manager_id')->count(),
            'root_departments' => $this->model->whereNull('parent_id')->count(),
        ];
    }

    public function getDepartmentStatsByStatus(): array
    {
        $stats = [];
        foreach (DepartmentStatus::cases() as $status) {
            $stats[$status->value] = $this->model->where('status', $status)->count();
        }

        return $stats;
    }

    public function getDepartmentCount(): int
    {
        return $this->model->count();
    }

    public function getDepartmentCountByStatus(DepartmentStatus $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getDepartmentCountByParent(int $parentId): int
    {
        return $this->model->where('parent_id', $parentId)->count();
    }

    public function validateDepartment(array $data): bool
    {
        // Additional validation logic if needed
        return true;
    }
}
