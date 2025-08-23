<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\DTOs\EmployeeDepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;

class EmployeeDepartmentRepository implements EmployeeDepartmentRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'employee_department_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeeDepartment $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with(['parent', 'manager', 'children'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['parent', 'manager', 'children'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['parent', 'manager', 'children'])
            ->orderBy('name')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['parent', 'manager', 'children'])
            ->orderBy('id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // Find operations
    public function find(int $id): ?EmployeeDepartment
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['parent', 'manager', 'children', 'employees'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeDepartmentDTO
    {
        $department = $this->find($id);
        return $department ? EmployeeDepartmentDTO::fromModel($department) : null;
    }

    // Find by name and code
    public function findByName(string $name): ?EmployeeDepartment
    {
        return Cache::remember($this->cachePrefix . 'find_by_name_' . md5($name), $this->cacheTtl, function () use ($name) {
            return $this->model->where('name', $name)->first();
        });
    }

    public function findByNameDTO(string $name): ?EmployeeDepartmentDTO
    {
        $department = $this->findByName($name);
        return $department ? EmployeeDepartmentDTO::fromModel($department) : null;
    }

    public function findByCode(string $code): ?EmployeeDepartment
    {
        return Cache::remember($this->cachePrefix . 'find_by_code_' . md5($code), $this->cacheTtl, function () use ($code) {
            return $this->model->where('code', $code)->first();
        });
    }

    public function findByCodeDTO(string $code): ?EmployeeDepartmentDTO
    {
        $department = $this->findByCode($code);
        return $department ? EmployeeDepartmentDTO::fromModel($department) : null;
    }

    // Find by relationships
    public function findByManagerId(int $managerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_by_manager_' . $managerId, $this->cacheTtl, function () use ($managerId) {
            return $this->model->where('manager_id', $managerId)->get();
        });
    }

    public function findByManagerIdDTO(int $managerId): Collection
    {
        $departments = $this->findByManagerId($managerId);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findByParentId(int $parentId): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_by_parent_' . $parentId, $this->cacheTtl, function () use ($parentId) {
            return $this->model->where('parent_id', $parentId)->get();
        });
    }

    public function findByParentIdDTO(int $parentId): Collection
    {
        $departments = $this->findByParentId($parentId);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    // Find by status and filters
    public function findByStatus(string $status): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_by_status_' . $status, $this->cacheTtl, function () use ($status) {
            return $this->model->where('status', $status)->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        $departments = $this->findByStatus($status);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findByLocation(string $location): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_by_location_' . md5($location), $this->cacheTtl, function () use ($location) {
            return $this->model->where('location', $location)->get();
        });
    }

    public function findByLocationDTO(string $location): Collection
    {
        $departments = $this->findByLocation($location);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_active', $this->cacheTtl, function () {
            return $this->model->active()->get();
        });
    }

    public function findActiveDTO(): Collection
    {
        $departments = $this->findActive();
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findInactive(): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_inactive', $this->cacheTtl, function () {
            return $this->model->inactive()->get();
        });
    }

    public function findInactiveDTO(): Collection
    {
        $departments = $this->findInactive();
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    // Hierarchy queries
    public function findRoot(): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_root', $this->cacheTtl, function () {
            return $this->model->root()->get();
        });
    }

    public function findRootDTO(): Collection
    {
        $departments = $this->findRoot();
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findChildren(int $parentId): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_children_' . $parentId, $this->cacheTtl, function () use ($parentId) {
            return $this->model->where('parent_id', $parentId)->get();
        });
    }

    public function findChildrenDTO(int $parentId): Collection
    {
        $departments = $this->findChildren($parentId);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findDescendants(int $parentId): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_descendants_' . $parentId, $this->cacheTtl, function () use ($parentId) {
            $department = $this->find($parentId);
            return $department ? collect($department->getDescendants()) : collect();
        });
    }

    public function findDescendantsDTO(int $parentId): Collection
    {
        $departments = $this->findDescendants($parentId);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    public function findAncestors(int $departmentId): Collection
    {
        return Cache::remember($this->cachePrefix . 'find_ancestors_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);
            return $department ? collect($department->getAncestors()) : collect();
        });
    }

    public function findAncestorsDTO(int $departmentId): Collection
    {
        $departments = $this->findAncestors($departmentId);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    // Create and update operations
    public function create(array $data): EmployeeDepartment
    {
        try {
            DB::beginTransaction();

            $department = $this->model->create($data);

            $this->clearCache();

            DB::commit();

            Log::info('EmployeeDepartment created', ['id' => $department->id, 'name' => $department->name]);

            return $department;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create EmployeeDepartment', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeDepartmentDTO
    {
        $department = $this->create($data);
        return EmployeeDepartmentDTO::fromModel($department);
    }

    public function update(EmployeeDepartment $department, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $department->update($data);

            $this->clearCache();

            DB::commit();

            if ($result) {
                Log::info('EmployeeDepartment updated', ['id' => $department->id, 'name' => $department->name]);
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update EmployeeDepartment', ['error' => $e->getMessage(), 'id' => $department->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeDepartment $department, array $data): ?EmployeeDepartmentDTO
    {
        $result = $this->update($department, $data);
        return $result ? EmployeeDepartmentDTO::fromModel($department->fresh()) : null;
    }

    // Delete operations
    public function delete(EmployeeDepartment $department): bool
    {
        try {
            DB::beginTransaction();

            $result = $department->delete();

            $this->clearCache();

            DB::commit();

            if ($result) {
                Log::info('EmployeeDepartment deleted', ['id' => $department->id, 'name' => $department->name]);
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete EmployeeDepartment', ['error' => $e->getMessage(), 'id' => $department->id]);
            throw $e;
        }
    }

    // Status management
    public function activate(EmployeeDepartment $department): bool
    {
        return $this->update($department, [
            'is_active' => true,
            'status' => DepartmentStatus::ACTIVE
        ]);
    }

    public function deactivate(EmployeeDepartment $department): bool
    {
        return $this->update($department, [
            'is_active' => false,
            'status' => DepartmentStatus::INACTIVE
        ]);
    }

    public function archive(EmployeeDepartment $department): bool
    {
        return $this->update($department, [
            'is_active' => false,
            'status' => DepartmentStatus::ARCHIVED
        ]);
    }

    // Manager operations
    public function assignManager(EmployeeDepartment $department, int $managerId): bool
    {
        return $this->update($department, ['manager_id' => $managerId]);
    }

    public function removeManager(EmployeeDepartment $department): bool
    {
        return $this->update($department, ['manager_id' => null]);
    }

    // Hierarchy operations
    public function moveToParent(EmployeeDepartment $department, int $newParentId): bool
    {
        return $this->update($department, ['parent_id' => $newParentId]);
    }

    // Analytics and metrics
    public function getDepartmentEmployeeCount(int $departmentId): int
    {
        return Cache::remember($this->cachePrefix . 'employee_count_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);
            return $department ? $department->employee_count : 0;
        });
    }

    public function getDepartmentBudget(int $departmentId): float
    {
        return Cache::remember($this->cachePrefix . 'budget_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);
            return $department ? ($department->budget ?? 0) : 0;
        });
    }

    public function getDepartmentBudgetUtilization(int $departmentId): float
    {
        return Cache::remember($this->cachePrefix . 'budget_utilization_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);
            return $department ? $department->budget_utilization : 0;
        });
    }

    public function getDepartmentHeadcountUtilization(int $departmentId): float
    {
        return Cache::remember($this->cachePrefix . 'headcount_utilization_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);
            return $department ? $department->headcount_utilization : 0;
        });
    }

    // Statistics
    public function getTotalDepartmentCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_count', $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalDepartmentCountByStatus(string $status): int
    {
        return Cache::remember($this->cachePrefix . 'total_count_status_' . $status, $this->cacheTtl, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    public function getTotalEmployeeCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_employee_count', $this->cacheTtl, function () {
            return $this->model->withCount('employees')->get()->sum('employees_count');
        });
    }

    public function getTotalBudget(): float
    {
        return Cache::remember($this->cachePrefix . 'total_budget', $this->cacheTtl, function () {
            return $this->model->sum('budget');
        });
    }

    // Hierarchy and tree operations
    public function getDepartmentHierarchy(): array
    {
        return Cache::remember($this->cachePrefix . 'hierarchy', $this->cacheTtl, function () {
            $rootDepartments = $this->findRoot();
            $hierarchy = [];

            foreach ($rootDepartments as $root) {
                $hierarchy[] = $this->buildHierarchyNode($root);
            }

            return $hierarchy;
        });
    }

    public function getDepartmentTree(): array
    {
        return Cache::remember($this->cachePrefix . 'tree', $this->cacheTtl, function () {
            return $this->getDepartmentHierarchy();
        });
    }

    // Search operations
    public function searchDepartments(string $query): Collection
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->get();
    }

    public function searchDepartmentsDTO(string $query): Collection
    {
        $departments = $this->searchDepartments($query);
        return $departments->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    // Import/Export operations
    public function exportDepartmentData(array $filters = []): string
    {
        // Implementation for exporting department data
        // This would typically generate CSV, Excel, or JSON format
        return json_encode($this->all()->toArray());
    }

    public function importDepartmentData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $departments = json_decode($data, true);

            foreach ($departments as $departmentData) {
                $this->create($departmentData);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import department data', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // Analytics and reporting
    public function getDepartmentStatistics(): array
    {
        return Cache::remember($this->cachePrefix . 'statistics', $this->cacheTtl, function () {
            return [
                'total_departments' => $this->getTotalDepartmentCount(),
                'active_departments' => $this->getTotalDepartmentCountByStatus(DepartmentStatus::ACTIVE->value),
                'inactive_departments' => $this->getTotalDepartmentCountByStatus(DepartmentStatus::INACTIVE->value),
                'archived_departments' => $this->getTotalDepartmentCountByStatus(DepartmentStatus::ARCHIVED->value),
                'total_employees' => $this->getTotalEmployeeCount(),
                'total_budget' => $this->getTotalBudget(),
                'root_departments' => $this->findRoot()->count(),
                'departments_with_managers' => $this->model->whereNotNull('manager_id')->count(),
            ];
        });
    }

    public function getDepartmentPerformanceMetrics(int $departmentId): array
    {
        return Cache::remember($this->cachePrefix . 'performance_metrics_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $department = $this->find($departmentId);

            if (!$department) {
                return [];
            }

            return [
                'employee_count' => $this->getDepartmentEmployeeCount($departmentId),
                'budget' => $this->getDepartmentBudget($departmentId),
                'budget_utilization' => $this->getDepartmentBudgetUtilization($departmentId),
                'headcount_utilization' => $this->getDepartmentHeadcountUtilization($departmentId),
                'depth' => $department->depth,
                'level' => $department->level,
                'has_children' => $department->hasChildren(),
                'is_root' => $department->isRoot(),
            ];
        });
    }

    public function getDepartmentTrends(?string $startDate = null, ?string $endDate = null): array
    {
        // Implementation for getting department trends over time
        // This would typically analyze changes in employee count, budget, etc.
        return [
            'growth_rate' => 0,
            'budget_changes' => [],
            'employee_changes' => [],
            'status_changes' => [],
        ];
    }

    // Helper methods
    protected function buildHierarchyNode(EmployeeDepartment $department): array
    {
        $node = [
            'id' => $department->id,
            'name' => $department->name,
            'code' => $department->code,
            'status' => $department->status->value,
            'employee_count' => $department->employee_count,
            'children' => [],
        ];

        if ($department->hasChildren()) {
            foreach ($department->children as $child) {
                $node['children'][] = $this->buildHierarchyNode($child);
            }
        }

        return $node;
    }

    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'all');
        Cache::forget($this->cachePrefix . 'hierarchy');
        Cache::forget($this->cachePrefix . 'tree');
        Cache::forget($this->cachePrefix . 'statistics');
        Cache::forget($this->cachePrefix . 'find_active');
        Cache::forget($this->cachePrefix . 'find_inactive');
        Cache::forget($this->cachePrefix . 'find_root');
    }
}
