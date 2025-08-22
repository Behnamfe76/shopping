<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeStatusManagement
{
    // Employee status management operations
    public function activateEmployee(Employee $employee): bool
    {
        return $this->repository->activate($employee);
    }

    public function deactivateEmployee(Employee $employee): bool
    {
        return $this->repository->deactivate($employee);
    }

    public function terminateEmployee(Employee $employee, string $reason = null, string $terminationDate = null): bool
    {
        return $this->repository->terminate($employee, $reason, $terminationDate);
    }

    public function rehireEmployee(Employee $employee, string $hireDate = null): bool
    {
        return $this->repository->rehire($employee, $hireDate);
    }

    // Status-based queries
    public function findEmployeesByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findEmployeesByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findActiveEmployees(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveEmployeesDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findInactiveEmployees(): Collection
    {
        return $this->repository->findInactive();
    }

    public function findInactiveEmployeesDTO(): Collection
    {
        return $this->repository->findInactiveDTO();
    }

    public function findTerminatedEmployees(): Collection
    {
        return $this->repository->findTerminated();
    }

    public function findTerminatedEmployeesDTO(): Collection
    {
        return $this->repository->findTerminatedDTO();
    }

    public function findPendingEmployees(): Collection
    {
        return $this->repository->findByStatus(EmployeeStatus::PENDING->value);
    }

    public function findPendingEmployeesDTO(): Collection
    {
        return $this->repository->findByStatusDTO(EmployeeStatus::PENDING->value);
    }

    public function findEmployeesOnLeave(): Collection
    {
        return $this->repository->findByStatus(EmployeeStatus::ON_LEAVE->value);
    }

    public function findEmployeesOnLeaveDTO(): Collection
    {
        return $this->repository->findByStatusDTO(EmployeeStatus::ON_LEAVE->value);
    }

    // Status validation methods
    public function canActivateEmployee(Employee $employee): bool
    {
        return in_array($employee->status, [
            EmployeeStatus::INACTIVE,
            EmployeeStatus::PENDING,
            EmployeeStatus::ON_LEAVE
        ]);
    }

    public function canDeactivateEmployee(Employee $employee): bool
    {
        return $employee->status === EmployeeStatus::ACTIVE;
    }

    public function canTerminateEmployee(Employee $employee): bool
    {
        return in_array($employee->status, [
            EmployeeStatus::ACTIVE,
            EmployeeStatus::INACTIVE,
            EmployeeStatus::PENDING,
            EmployeeStatus::ON_LEAVE
        ]);
    }

    public function canRehireEmployee(Employee $employee): bool
    {
        return $employee->status === EmployeeStatus::TERMINATED;
    }

    // Status transition methods
    public function transitionEmployeeStatus(Employee $employee, EmployeeStatus $newStatus, array $options = []): bool
    {
        switch ($newStatus) {
            case EmployeeStatus::ACTIVE:
                return $this->activateEmployee($employee);

            case EmployeeStatus::INACTIVE:
                return $this->deactivateEmployee($employee);

            case EmployeeStatus::TERMINATED:
                $reason = $options['reason'] ?? null;
                $terminationDate = $options['termination_date'] ?? null;
                return $this->terminateEmployee($employee, $reason, $terminationDate);

            case EmployeeStatus::ON_LEAVE:
                return $this->repository->update($employee, ['status' => $newStatus]);

            case EmployeeStatus::PENDING:
                return $this->repository->update($employee, ['status' => $newStatus]);

            default:
                return false;
        }
    }

    // Status statistics
    public function getEmployeeCountByStatus(string $status): int
    {
        return $this->repository->getEmployeeCountByStatus($status);
    }

    public function getActiveEmployeeCount(): int
    {
        return $this->repository->getActiveEmployeeCount();
    }

    public function getInactiveEmployeeCount(): int
    {
        return $this->repository->getInactiveEmployeeCount();
    }

    public function getTerminatedEmployeeCount(): int
    {
        return $this->repository->getTerminatedEmployeeCount();
    }

    public function getPendingEmployeeCount(): int
    {
        return $this->repository->getEmployeeCountByStatus(EmployeeStatus::PENDING->value);
    }

    public function getOnLeaveEmployeeCount(): int
    {
        return $this->repository->getEmployeeCountByStatus(EmployeeStatus::ON_LEAVE->value);
    }

    // Status analytics
    public function getEmployeeStatusDistribution(): array
    {
        $stats = $this->repository->getEmployeeStatsByStatus();

        $total = array_sum($stats);
        $distribution = [];

        foreach ($stats as $status => $count) {
            $distribution[$status] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0
            ];
        }

        return $distribution;
    }

    public function getEmployeeStatusTrends(string $period = 'monthly'): array
    {
        // Implementation for status trends over time
        return [];
    }

    // Bulk status operations
    public function bulkActivateEmployees(array $employeeIds): array
    {
        $results = [];

        foreach ($employeeIds as $employeeId) {
            $employee = $this->repository->find($employeeId);
            if ($employee && $this->canActivateEmployee($employee)) {
                $results[$employeeId] = $this->activateEmployee($employee);
            } else {
                $results[$employeeId] = false;
            }
        }

        return $results;
    }

    public function bulkDeactivateEmployees(array $employeeIds): array
    {
        $results = [];

        foreach ($employeeIds as $employeeId) {
            $employee = $this->repository->find($employeeId);
            if ($employee && $this->canDeactivateEmployee($employee)) {
                $results[$employeeId] = $this->deactivateEmployee($employee);
            } else {
                $results[$employeeId] = false;
            }
        }

        return $results;
    }

    public function bulkTerminateEmployees(array $employeeIds, string $reason = null): array
    {
        $results = [];

        foreach ($employeeIds as $employeeId) {
            $employee = $this->repository->find($employeeId);
            if ($employee && $this->canTerminateEmployee($employee)) {
                $results[$employeeId] = $this->terminateEmployee($employee, $reason);
            } else {
                $results[$employeeId] = false;
            }
        }

        return $results;
    }

    // Status workflow methods
    public function processEmployeeOnboarding(Employee $employee): bool
    {
        // Transition from PENDING to ACTIVE
        if ($employee->status === EmployeeStatus::PENDING) {
            return $this->activateEmployee($employee);
        }

        return false;
    }

    public function processEmployeeOffboarding(Employee $employee, string $reason = null): bool
    {
        // Transition to TERMINATED
        if ($this->canTerminateEmployee($employee)) {
            return $this->terminateEmployee($employee, $reason);
        }

        return false;
    }

    public function processEmployeeLeave(Employee $employee): bool
    {
        // Transition to ON_LEAVE
        if ($employee->status === EmployeeStatus::ACTIVE) {
            return $this->repository->update($employee, ['status' => EmployeeStatus::ON_LEAVE]);
        }

        return false;
    }

    public function processEmployeeReturnFromLeave(Employee $employee): bool
    {
        // Transition from ON_LEAVE to ACTIVE
        if ($employee->status === EmployeeStatus::ON_LEAVE) {
            return $this->activateEmployee($employee);
        }

        return false;
    }
}

