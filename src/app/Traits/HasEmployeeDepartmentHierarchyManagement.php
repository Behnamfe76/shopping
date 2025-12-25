<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\EmployeeDepartmentDTO;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeDepartmentHierarchyManagement
{
    protected EmployeeDepartmentRepositoryInterface $departmentRepository;

    /**
     * Get root departments (no parent)
     */
    public function getRootDepartments(): Collection
    {
        return $this->departmentRepository->findRoot();
    }

    /**
     * Get root departments as DTOs
     */
    public function getRootDepartmentsDTO(): Collection
    {
        return $this->departmentRepository->findRootDTO();
    }

    /**
     * Get children of a department
     */
    public function getDepartmentChildren(int $parentId): Collection
    {
        return $this->departmentRepository->findChildren($parentId);
    }

    /**
     * Get children of a department as DTOs
     */
    public function getDepartmentChildrenDTO(int $parentId): Collection
    {
        return $this->departmentRepository->findChildrenDTO($parentId);
    }

    /**
     * Get descendants of a department
     */
    public function getDepartmentDescendants(int $parentId): Collection
    {
        return $this->departmentRepository->findDescendants($parentId);
    }

    /**
     * Get descendants of a department as DTOs
     */
    public function getDepartmentDescendantsDTO(int $parentId): Collection
    {
        return $this->departmentRepository->findDescendantsDTO($parentId);
    }

    /**
     * Get ancestors of a department
     */
    public function getDepartmentAncestors(int $departmentId): Collection
    {
        return $this->departmentRepository->findAncestors($departmentId);
    }

    /**
     * Get ancestors of a department as DTOs
     */
    public function getDepartmentAncestorsDTO(int $departmentId): Collection
    {
        return $this->departmentRepository->findAncestorsDTO($departmentId);
    }

    /**
     * Move department to new parent
     */
    public function moveDepartmentToParent(EmployeeDepartment $department, int $newParentId): bool
    {
        return $this->departmentRepository->moveToParent($department, $newParentId);
    }

    /**
     * Get department hierarchy
     */
    public function getDepartmentHierarchy(): array
    {
        return $this->departmentRepository->getDepartmentHierarchy();
    }

    /**
     * Get department tree
     */
    public function getDepartmentTree(): array
    {
        return $this->departmentRepository->getDepartmentTree();
    }

    /**
     * Check if department is root
     */
    public function isDepartmentRoot(EmployeeDepartment $department): bool
    {
        return $department->isRoot();
    }

    /**
     * Check if department is leaf
     */
    public function isDepartmentLeaf(EmployeeDepartment $department): bool
    {
        return $department->isLeaf();
    }

    /**
     * Check if department has children
     */
    public function departmentHasChildren(EmployeeDepartment $department): bool
    {
        return $department->hasChildren();
    }

    /**
     * Check if department has parent
     */
    public function departmentHasParent(EmployeeDepartment $department): bool
    {
        return $department->hasParent();
    }

    /**
     * Get department depth
     */
    public function getDepartmentDepth(EmployeeDepartment $department): int
    {
        return $department->getDepth();
    }

    /**
     * Get department level
     */
    public function getDepartmentLevel(EmployeeDepartment $department): int
    {
        return $department->getLevel();
    }

    /**
     * Get department full name (with hierarchy)
     */
    public function getDepartmentFullName(EmployeeDepartment $department): string
    {
        return $department->full_name;
    }

    /**
     * Get departments by level
     */
    public function getDepartmentsByLevel(int $level): Collection
    {
        $allDepartments = $this->departmentRepository->all();

        return $allDepartments->filter(function ($department) use ($level) {
            return $department->getLevel() === $level;
        });
    }

    /**
     * Get departments by level as DTOs
     */
    public function getDepartmentsByLevelDTO(int $level): Collection
    {
        $departments = $this->getDepartmentsByLevel($level);

        return $departments->map(fn ($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    /**
     * Get maximum department level
     */
    public function getMaxDepartmentLevel(): int
    {
        $allDepartments = $this->departmentRepository->all();
        $maxLevel = 0;

        foreach ($allDepartments as $department) {
            $level = $department->getLevel();
            if ($level > $maxLevel) {
                $maxLevel = $level;
            }
        }

        return $maxLevel;
    }

    /**
     * Get department siblings
     */
    public function getDepartmentSiblings(EmployeeDepartment $department): Collection
    {
        if (! $department->hasParent()) {
            return collect();
        }

        return $this->departmentRepository->findByParentId($department->parent_id)
            ->filter(function ($sibling) use ($department) {
                return $sibling->id !== $department->id;
            });
    }

    /**
     * Get department siblings as DTOs
     */
    public function getDepartmentSiblingsDTO(EmployeeDepartment $department): Collection
    {
        $siblings = $this->getDepartmentSiblings($department);

        return $siblings->map(fn ($sibling) => EmployeeDepartmentDTO::fromModel($sibling));
    }

    /**
     * Check if department is ancestor of another
     */
    public function isDepartmentAncestor(int $ancestorId, int $descendantId): bool
    {
        $ancestors = $this->getDepartmentAncestors($descendantId);

        return $ancestors->contains('id', $ancestorId);
    }

    /**
     * Check if department is descendant of another
     */
    public function isDepartmentDescendant(int $descendantId, int $ancestorId): bool
    {
        $descendants = $this->getDepartmentDescendants($ancestorId);

        return $descendants->contains('id', $descendantId);
    }

    /**
     * Get common ancestor of two departments
     */
    public function getCommonAncestor(int $department1Id, int $department2Id): ?EmployeeDepartment
    {
        $ancestors1 = $this->getDepartmentAncestors($department1Id);
        $ancestors2 = $this->getDepartmentAncestors($department2Id);

        // Find the highest common ancestor
        foreach ($ancestors1 as $ancestor1) {
            if ($ancestors2->contains('id', $ancestor1->id)) {
                return $ancestor1;
            }
        }

        return null;
    }

    /**
     * Get departments at same level
     */
    public function getDepartmentsAtSameLevel(EmployeeDepartment $department): Collection
    {
        $level = $department->getLevel();

        return $this->getDepartmentsByLevel($level);
    }

    /**
     * Get departments at same level as DTOs
     */
    public function getDepartmentsAtSameLevelDTO(EmployeeDepartment $department): Collection
    {
        $departments = $this->getDepartmentsAtSameLevel($department);

        return $departments->map(fn ($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }

    /**
     * Build flat hierarchy list
     */
    public function buildFlatHierarchyList(): array
    {
        $hierarchy = $this->getDepartmentHierarchy();
        $flatList = [];

        $this->flattenHierarchy($hierarchy, $flatList);

        return $flatList;
    }

    /**
     * Helper method to flatten hierarchy
     */
    private function flattenHierarchy(array $hierarchy, array &$flatList, int $level = 0): void
    {
        foreach ($hierarchy as $node) {
            $flatList[] = [
                'id' => $node['id'],
                'name' => $node['name'],
                'code' => $node['code'],
                'level' => $level,
                'indent' => str_repeat('  ', $level),
            ];

            if (! empty($node['children'])) {
                $this->flattenHierarchy($node['children'], $flatList, $level + 1);
            }
        }
    }
}
