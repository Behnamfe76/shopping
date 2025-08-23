<?php

namespace App\Facades;

use App\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;
use App\Repositories\EmployeeDepartmentRepository;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \App\Models\EmployeeDepartment|null find(int $id)
 * @method static \App\DTOs\EmployeeDepartmentDTO|null findDTO(int $id)
 * @method static \App\Models\EmployeeDepartment|null findByName(string $name)
 * @method static \App\DTOs\EmployeeDepartmentDTO|null findByNameDTO(string $name)
 * @method static \App\Models\EmployeeDepartment|null findByCode(string $code)
 * @method static \App\DTOs\EmployeeDepartmentDTO|null findByCodeDTO(string $code)
 * @method static Collection findByManagerId(int $managerId)
 * @method static Collection findByManagerIdDTO(int $managerId)
 * @method static Collection findByParentId(int $parentId)
 * @method static Collection findByParentIdDTO(int $parentId)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Collection findByLocation(string $location)
 * @method static Collection findByLocationDTO(string $location)
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findInactive()
 * @method static Collection findInactiveDTO()
 * @method static Collection findRoot()
 * @method static Collection findRootDTO()
 * @method static Collection findChildren(int $parentId)
 * @method static Collection findChildrenDTO(int $parentId)
 * @method static Collection findDescendants(int $parentId)
 * @method static Collection findDescendantsDTO(int $parentId)
 * @method static Collection findAncestors(int $departmentId)
 * @method static Collection findAncestorsDTO(int $departmentId)
 * @method static \App\Models\EmployeeDepartment create(array $data)
 * @method static \App\DTOs\EmployeeDepartmentDTO createAndReturnDTO(array $data)
 * @method static bool update(\App\Models\EmployeeDepartment $department, array $data)
 * @method static \App\DTOs\EmployeeDepartmentDTO|null updateAndReturnDTO(\App\Models\EmployeeDepartment $department, array $data)
 * @method static bool delete(\App\Models\EmployeeDepartment $department)
 * @method static bool activate(\App\Models\EmployeeDepartment $department)
 * @method static bool deactivate(\App\Models\EmployeeDepartment $department)
 * @method static bool archive(\App\Models\EmployeeDepartment $department)
 * @method static bool assignManager(\App\Models\EmployeeDepartment $department, int $managerId)
 * @method static bool removeManager(\App\Models\EmployeeDepartment $department)
 * @method static bool moveToParent(\App\Models\EmployeeDepartment $department, int $newParentId)
 * @method static int getDepartmentEmployeeCount(int $departmentId)
 * @method static float getDepartmentBudget(int $departmentId)
 * @method static float getDepartmentBudgetUtilization(int $departmentId)
 * @method static float getDepartmentHeadcountUtilization(int $departmentId)
 * @method static int getTotalDepartmentCount()
 * @method static int getTotalDepartmentCountByStatus(string $status)
 * @method static int getTotalEmployeeCount()
 * @method static float getTotalBudget()
 * @method static array getDepartmentHierarchy()
 * @method static array getDepartmentTree()
 * @method static Collection searchDepartments(string $query)
 * @method static Collection searchDepartmentsDTO(string $query)
 * @method static string exportDepartmentData(array $filters = [])
 * @method static bool importDepartmentData(string $data)
 * @method static array getDepartmentStatistics()
 * @method static array getDepartmentPerformanceMetrics(int $departmentId)
 * @method static array getDepartmentTrends(string $startDate = null, string $endDate = null)
 * @method static bool setDepartmentBudget(int $departmentId, float $budget)
 * @method static bool isDepartmentOverBudget(int $departmentId)
 * @method static bool isDepartmentApproachingBudgetLimit(int $departmentId, float $threshold = 80.0)
 * @method static float getRemainingBudget(int $departmentId)
 * @method static array getBudgetSummary(int $departmentId)
 * @method static array getBudgetTrends(int $departmentId, string $startDate = null, string $endDate = null)
 * @method static bool updateBudgetAllocation(int $departmentId, float $newBudget, string $reason = '')
 * @method static array getOverallBudgetUtilization()
 * @method static int calculateHierarchyDepth()
 * @method static int calculateDepartmentDepth(int $departmentId)
 * @method static float calculateAverageEmployeesPerDepartment()
 * @method static float calculateEfficiencyScore(int $departmentId)
 * @method static float calculateGrowthRate(int $departmentId)
 * @method static float calculateTurnoverRate(int $departmentId)
 * @method static array getProductivityMetrics(int $departmentId)
 * @method static float calculateCostPerEmployee(int $departmentId)
 * @method static float calculateRevenuePerEmployee(int $departmentId)
 * @method static array generateDepartmentReport(int $departmentId)
 * @method static string exportDepartmentAnalytics(array $filters = [])
 */
class EmployeeDepartment extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return EmployeeDepartmentRepositoryInterface::class;
    }

    /**
     * Get the repository instance
     */
    public static function repository(): EmployeeDepartmentRepositoryInterface
    {
        return app(EmployeeDepartmentRepositoryInterface::class);
    }

    /**
     * Get the repository implementation instance
     */
    public static function implementation(): EmployeeDepartmentRepository
    {
        return app(EmployeeDepartmentRepository::class);
    }

    /**
     * Create a new department with validation
     */
    public static function createDepartment(array $data): \App\Models\EmployeeDepartment
    {
        try {
            // Validate required fields
            $requiredFields = ['name', 'code'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \InvalidArgumentException("Field '{$field}' is required");
                }
            }

            // Check if code already exists
            if (static::repository()->findByCode($data['code'])) {
                throw new \InvalidArgumentException("Department code '{$data['code']}' already exists");
            }

            // Check if name already exists
            if (static::repository()->findByName($data['name'])) {
                throw new \InvalidArgumentException("Department name '{$data['name']}' already exists");
            }

            return static::repository()->create($data);
        } catch (\Exception $e) {
            \Log::error('Error creating department', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update department with validation
     */
    public static function updateDepartment(int $departmentId, array $data): bool
    {
        try {
            $department = static::repository()->find($departmentId);
            if (!$department) {
                throw new \InvalidArgumentException("Department with ID {$departmentId} not found");
            }

            // Check if code already exists (excluding current department)
            if (isset($data['code'])) {
                $existingDepartment = static::repository()->findByCode($data['code']);
                if ($existingDepartment && $existingDepartment->id !== $departmentId) {
                    throw new \InvalidArgumentException("Department code '{$data['code']}' already exists");
                }
            }

            // Check if name already exists (excluding current department)
            if (isset($data['name'])) {
                $existingDepartment = static::repository()->findByName($data['name']);
                if ($existingDepartment && $existingDepartment->id !== $departmentId) {
                    throw new \InvalidArgumentException("Department name '{$data['name']}' already exists");
                }
            }

            return static::repository()->update($department, $data);
        } catch (\Exception $e) {
            \Log::error('Error updating department', [
                'department_id' => $departmentId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete department with validation
     */
    public static function deleteDepartment(int $departmentId): bool
    {
        try {
            $department = static::repository()->find($departmentId);
            if (!$department) {
                throw new \InvalidArgumentException("Department with ID {$departmentId} not found");
            }

            // Check if department has employees
            $employeeCount = static::repository()->getDepartmentEmployeeCount($departmentId);
            if ($employeeCount > 0) {
                throw new \InvalidArgumentException("Cannot delete department with {$employeeCount} employees");
            }

            // Check if department has children
            $childrenCount = static::repository()->findByParentId($departmentId)->count();
            if ($childrenCount > 0) {
                throw new \InvalidArgumentException("Cannot delete department with {$childrenCount} child departments");
            }

            return static::repository()->delete($department);
        } catch (\Exception $e) {
            \Log::error('Error deleting department', [
                'department_id' => $departmentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assign manager to department
     */
    public static function assignManager(int $departmentId, int $managerId): bool
    {
        try {
            $department = static::repository()->find($departmentId);
            if (!$department) {
                throw new \InvalidArgumentException("Department with ID {$departmentId} not found");
            }

            // Validate manager exists (this would typically check against Employee model)
            // For now, we'll assume the manager ID is valid

            return static::repository()->assignManager($department, $managerId);
        } catch (\Exception $e) {
            \Log::error('Error assigning manager', [
                'department_id' => $departmentId,
                'manager_id' => $managerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Move department to new parent
     */
    public static function moveDepartment(int $departmentId, int $newParentId): bool
    {
        try {
            $department = static::repository()->find($departmentId);
            if (!$department) {
                throw new \InvalidArgumentException("Department with ID {$departmentId} not found");
            }

            // Check if new parent exists
            if ($newParentId !== 0) {
                $newParent = static::repository()->find($newParentId);
                if (!$newParent) {
                    throw new \InvalidArgumentException("Parent department with ID {$newParentId} not found");
                }

                // Check for circular reference
                if ($newParentId === $departmentId) {
                    throw new \InvalidArgumentException("Department cannot be its own parent");
                }

                // Check if new parent is a descendant of current department
                $descendants = static::repository()->findDescendants($departmentId);
                if ($descendants->contains('id', $newParentId)) {
                    throw new \InvalidArgumentException("Cannot move department to its own descendant");
                }
            }

            return static::repository()->moveToParent($department, $newParentId);
        } catch (\Exception $e) {
            \Log::error('Error moving department', [
                'department_id' => $departmentId,
                'new_parent_id' => $newParentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get department hierarchy as tree
     */
    public static function getHierarchyTree(): array
    {
        try {
            return static::repository()->getDepartmentTree();
        } catch (\Exception $e) {
            \Log::error('Error getting hierarchy tree', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Search departments with pagination
     */
    public static function searchDepartmentsPaginated(string $query, int $perPage = 15): LengthAwarePaginator
    {
        try {
            $departments = static::repository()->searchDepartments($query);

            // Convert to paginator
            $page = request()->get('page', 1);
            $offset = ($page - 1) * $perPage;
            $items = $departments->slice($offset, $perPage);

            return new LengthAwarePaginator(
                $items,
                $departments->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } catch (\Exception $e) {
            \Log::error('Error searching departments paginated', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            return new LengthAwarePaginator([], 0, $perPage, 1);
        }
    }

    /**
     * Get department analytics summary
     */
    public static function getAnalyticsSummary(): array
    {
        try {
            return [
                'statistics' => static::repository()->getDepartmentStatistics(),
                'budget_overview' => static::repository()->getOverallBudgetUtilization(),
                'hierarchy_info' => [
                    'depth' => static::repository()->calculateHierarchyDepth(),
                    'root_departments' => static::repository()->findRoot()->count(),
                    'child_departments' => static::repository()->findAll()->whereNotNull('parent_id')->count()
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting analytics summary', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Export department data
     */
    public static function exportData(array $filters = []): string
    {
        try {
            return static::repository()->exportDepartmentData($filters);
        } catch (\Exception $e) {
            \Log::error('Error exporting department data', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return json_encode(['error' => 'Failed to export data']);
        }
    }

    /**
     * Import department data
     */
    public static function importData(string $data): bool
    {
        try {
            return static::repository()->importDepartmentData($data);
        } catch (\Exception $e) {
            \Log::error('Error importing department data', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
