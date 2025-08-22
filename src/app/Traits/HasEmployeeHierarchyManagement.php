<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeHierarchyManagement
{
    // Manager assignment operations
    public function assignManager(Employee $employee, int $managerId): bool
    {
        return $this->repository->assignManager($employee, $managerId);
    }

    public function removeManager(Employee $employee): bool
    {
        return $this->repository->removeManager($employee);
    }

    public function changeManager(Employee $employee, int $newManagerId): bool
    {
        return $this->repository->assignManager($employee, $newManagerId);
    }

    // Hierarchy queries
    public function getEmployeeHierarchy(int $employeeId): array
    {
        return $this->repository->getEmployeeHierarchy($employeeId);
    }

    public function getEmployeeSubordinates(int $employeeId): Collection
    {
        return $this->repository->getEmployeeSubordinates($employeeId);
    }

    public function getEmployeeSubordinatesDTO(int $employeeId): Collection
    {
        return $this->repository->getEmployeeSubordinatesDTO($employeeId);
    }

    public function getEmployeeManagers(int $employeeId): Collection
    {
        return $this->repository->getEmployeeManagers($employeeId);
    }

    public function getEmployeeManagersDTO(int $employeeId): Collection
    {
        return $this->repository->getEmployeeManagersDTO($employeeId);
    }

    public function getDirectSubordinates(int $employeeId): Collection
    {
        return $this->repository->findByManagerId($employeeId);
    }

    public function getDirectSubordinatesDTO(int $employeeId): Collection
    {
        return $this->repository->findByManagerIdDTO($employeeId);
    }

    public function getDirectManager(int $employeeId): ?Employee
    {
        $employee = $this->repository->find($employeeId);
        return $employee ? $employee->manager : null;
    }

    public function getDirectManagerDTO(int $employeeId): ?EmployeeDTO
    {
        $manager = $this->getDirectManager($employeeId);
        return $manager ? EmployeeDTO::fromModel($manager) : null;
    }

    // Manager queries
    public function getAllManagers(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->isManager());
    }

    public function getAllManagersDTO(): Collection
    {
        return $this->getAllManagers()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getManagersByDepartment(string $department): Collection
    {
        return $this->repository->findByDepartment($department)
            ->filter(fn($employee) => $employee->isManager());
    }

    public function getManagersByDepartmentDTO(string $department): Collection
    {
        return $this->getManagersByDepartment($department)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getManagersWithSubordinates(int $minSubordinates = 1): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->subordinates()->count() >= $minSubordinates);
    }

    public function getManagersWithSubordinatesDTO(int $minSubordinates = 1): Collection
    {
        return $this->getManagersWithSubordinates($minSubordinates)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Subordinate queries
    public function getAllSubordinates(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->hasManager());
    }

    public function getAllSubordinatesDTO(): Collection
    {
        return $this->getAllSubordinates()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getSubordinatesByDepartment(string $department): Collection
    {
        return $this->repository->findByDepartment($department)
            ->filter(fn($employee) => $employee->hasManager());
    }

    public function getSubordinatesByDepartmentDTO(string $department): Collection
    {
        return $this->getSubordinatesByDepartment($department)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesWithoutManager(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => !$employee->hasManager());
    }

    public function getEmployeesWithoutManagerDTO(): Collection
    {
        return $this->getEmployeesWithoutManager()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Hierarchy validation
    public function canAssignManager(Employee $employee, int $managerId): bool
    {
        // Check if manager exists and is active
        $manager = $this->repository->find($managerId);
        if (!$manager || !$manager->isActive()) {
            return false;
        }

        // Check if employee exists and is active
        if (!$employee->isActive()) {
            return false;
        }

        // Check for circular references
        if ($employee->id === $managerId) {
            return false;
        }

        // Check if the proposed manager is already a subordinate of the employee
        $managerHierarchy = $this->getEmployeeManagers($managerId);
        foreach ($managerHierarchy as $hierarchyManager) {
            if ($hierarchyManager->id === $employee->id) {
                return false;
            }
        }

        return true;
    }

    public function validateHierarchyAssignment(Employee $employee, int $managerId): array
    {
        $result = [
            'valid' => false,
            'errors' => [],
            'warnings' => []
        ];

        // Check if manager exists
        $manager = $this->repository->find($managerId);
        if (!$manager) {
            $result['errors'][] = 'Manager does not exist';
            return $result;
        }

        // Check if manager is active
        if (!$manager->isActive()) {
            $result['errors'][] = 'Manager is not active';
            return $result;
        }

        // Check if employee is active
        if (!$employee->isActive()) {
            $result['errors'][] = 'Employee is not active';
            return $result;
        }

        // Check for self-assignment
        if ($employee->id === $managerId) {
            $result['errors'][] = 'Employee cannot be assigned as their own manager';
            return $result;
        }

        // Check for circular references
        $managerHierarchy = $this->getEmployeeManagers($managerId);
        foreach ($managerHierarchy as $hierarchyManager) {
            if ($hierarchyManager->id === $employee->id) {
                $result['errors'][] = 'Circular reference detected in hierarchy';
                return $result;
            }
        }

        // Check if manager is in different department (warning)
        if ($employee->department !== $manager->department) {
            $result['warnings'][] = 'Manager is in a different department';
        }

        // Check manager's span of control (warning)
        $currentSubordinates = $manager->subordinates()->count();
        if ($currentSubordinates >= 10) {
            $result['warnings'][] = 'Manager already has many subordinates (' . $currentSubordinates . ')';
        }

        $result['valid'] = true;
        return $result;
    }

    // Hierarchy analytics
    public function getHierarchyStats(): array
    {
        $employees = $this->repository->findActive();
        $totalEmployees = $employees->count();

        if ($totalEmployees === 0) {
            return [
                'total_employees' => 0,
                'managers_count' => 0,
                'subordinates_count' => 0,
                'employees_without_manager' => 0,
                'average_span_of_control' => 0,
                'max_span_of_control' => 0,
                'min_span_of_control' => 0,
                'hierarchy_levels' => 0
            ];
        }

        $managers = $employees->filter(fn($e) => $e->isManager());
        $subordinates = $employees->filter(fn($e) => $e->hasManager());
        $employeesWithoutManager = $employees->filter(fn($e) => !$e->hasManager());

        $spanOfControl = $managers->map(fn($m) => $m->subordinates()->count());
        $maxSpanOfControl = $spanOfControl->max() ?? 0;
        $minSpanOfControl = $spanOfControl->min() ?? 0;
        $averageSpanOfControl = $spanOfControl->avg() ?? 0;

        return [
            'total_employees' => $totalEmployees,
            'managers_count' => $managers->count(),
            'subordinates_count' => $subordinates->count(),
            'employees_without_manager' => $employeesWithoutManager->count(),
            'average_span_of_control' => round($averageSpanOfControl, 2),
            'max_span_of_control' => $maxSpanOfControl,
            'min_span_of_control' => $minSpanOfControl,
            'hierarchy_levels' => $this->calculateHierarchyLevels(),
            'management_ratio' => $totalEmployees > 0 ? round(($managers->count() / $totalEmployees) * 100, 2) : 0
        ];
    }

    public function getHierarchyStatsByDepartment(): array
    {
        $departments = $this->repository->findActive()
            ->pluck('department')
            ->unique()
            ->filter();

        $stats = [];

        foreach ($departments as $department) {
            $employees = $this->repository->findByDepartment($department);
            $totalEmployees = $employees->count();

            if ($totalEmployees === 0) {
                continue;
            }

            $managers = $employees->filter(fn($e) => $e->isManager());
            $subordinates = $employees->filter(fn($e) => $e->hasManager());
            $employeesWithoutManager = $employees->filter(fn($e) => !$e->hasManager());

            $spanOfControl = $managers->map(fn($m) => $m->subordinates()->count());
            $averageSpanOfControl = $spanOfControl->avg() ?? 0;

            $stats[$department] = [
                'total_employees' => $totalEmployees,
                'managers_count' => $managers->count(),
                'subordinates_count' => $subordinates->count(),
                'employees_without_manager' => $employeesWithoutManager->count(),
                'average_span_of_control' => round($averageSpanOfControl, 2),
                'management_ratio' => round(($managers->count() / $totalEmployees) * 100, 2)
            ];
        }

        return $stats;
    }

    // Hierarchy visualization
    public function getOrganizationalChart(int $rootEmployeeId = null): array
    {
        if ($rootEmployeeId) {
            $rootEmployee = $this->repository->find($rootEmployeeId);
            if (!$rootEmployee) {
                return [];
            }
        } else {
            // Find top-level employees (those without managers)
            $rootEmployees = $this->getEmployeesWithoutManager();
            if ($rootEmployees->isEmpty()) {
                return [];
            }
            $rootEmployee = $rootEmployees->first();
        }

        return $this->buildOrganizationalChart($rootEmployee);
    }

    public function getOrganizationalChartByDepartment(string $department): array
    {
        $departmentEmployees = $this->repository->findByDepartment($department);
        $rootEmployees = $departmentEmployees->filter(fn($e) => !$e->hasManager());

        $charts = [];
        foreach ($rootEmployees as $rootEmployee) {
            $charts[] = $this->buildOrganizationalChart($rootEmployee);
        }

        return $charts;
    }

    // Helper methods
    private function buildOrganizationalChart(Employee $employee): array
    {
        $chart = [
            'id' => $employee->id,
            'name' => $employee->full_name,
            'position' => $employee->position,
            'department' => $employee->department,
            'email' => $employee->email,
            'subordinates' => []
        ];

        $subordinates = $employee->subordinates;
        foreach ($subordinates as $subordinate) {
            $chart['subordinates'][] = $this->buildOrganizationalChart($subordinate);
        }

        return $chart;
    }

    private function calculateHierarchyLevels(): int
    {
        // Find the deepest hierarchy level
        $maxLevel = 0;
        $employees = $this->repository->findActive();

        foreach ($employees as $employee) {
            $level = $this->calculateEmployeeLevel($employee);
            $maxLevel = max($maxLevel, $level);
        }

        return $maxLevel;
    }

    private function calculateEmployeeLevel(Employee $employee): int
    {
        if (!$employee->hasManager()) {
            return 1;
        }

        $manager = $employee->manager;
        return 1 + $this->calculateEmployeeLevel($manager);
    }

    // Hierarchy reporting
    public function generateHierarchyReport(string $department = null): array
    {
        $employees = $department
            ? $this->repository->findByDepartment($department)
            : $this->repository->findActive();

        $report = [
            'department' => $department,
            'total_employees' => $employees->count(),
            'hierarchy_stats' => $this->getHierarchyStats(),
            'managers' => $employees->filter(fn($e) => $e->isManager())->count(),
            'subordinates' => $employees->filter(fn($e) => $e->hasManager())->count(),
            'employees_without_manager' => $employees->filter(fn($e) => !$e->hasManager())->count(),
            'organizational_chart' => $department ? $this->getOrganizationalChartByDepartment($department) : $this->getOrganizationalChart()
        ];

        return $report;
    }
}

