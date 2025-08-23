<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\DTOs\EmployeeDepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeDepartmentRepositoryInterface;

trait HasEmployeeDepartmentStatusManagement
{
    protected EmployeeDepartmentRepositoryInterface $departmentRepository;

    /**
     * Activate a department
     */
    public function activateDepartment(EmployeeDepartment $department): bool
    {
        return $this->departmentRepository->activate($department);
    }

    /**
     * Deactivate a department
     */
    public function deactivateDepartment(EmployeeDepartment $department): bool
    {
        return $this->departmentRepository->deactivate($department);
    }

    /**
     * Archive a department
     */
    public function archiveDepartment(EmployeeDepartment $department): bool
    {
        return $this->departmentRepository->archive($department);
    }

    /**
     * Get all active departments
     */
    public function getActiveDepartments(): Collection
    {
        return $this->departmentRepository->findActive();
    }

    /**
     * Get all active departments as DTOs
     */
    public function getActiveDepartmentsDTO(): Collection
    {
        return $this->departmentRepository->findActiveDTO();
    }

    /**
     * Get all inactive departments
     */
    public function getInactiveDepartments(): Collection
    {
        return $this->departmentRepository->findInactive();
    }

    /**
     * Get all inactive departments as DTOs
     */
    public function getInactiveDepartmentsDTO(): Collection
    {
        return $this->departmentRepository->findInactiveDTO();
    }

    /**
     * Get departments by status
     */
    public function getDepartmentsByStatus(string $status): Collection
    {
        return $this->departmentRepository->findByStatus($status);
    }

    /**
     * Get departments by status as DTOs
     */
    public function getDepartmentsByStatusDTO(string $status): Collection
    {
        return $this->departmentRepository->findByStatusDTO($status);
    }

    /**
     * Get active department count
     */
    public function getActiveDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus(DepartmentStatus::ACTIVE->value);
    }

    /**
     * Get inactive department count
     */
    public function getInactiveDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus(DepartmentStatus::INACTIVE->value);
    }

    /**
     * Get archived department count
     */
    public function getArchivedDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus(DepartmentStatus::ARCHIVED->value);
    }

    /**
     * Get pending department count
     */
    public function getPendingDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus(DepartmentStatus::PENDING->value);
    }

    /**
     * Get suspended department count
     */
    public function getSuspendedDepartmentCount(): int
    {
        return $this->departmentRepository->getTotalDepartmentCountByStatus(DepartmentStatus::SUSPENDED->value);
    }

    /**
     * Check if department is active
     */
    public function isDepartmentActive(EmployeeDepartment $department): bool
    {
        return $department->status->isActive();
    }

    /**
     * Check if department is inactive
     */
    public function isDepartmentInactive(EmployeeDepartment $department): bool
    {
        return $department->status->isInactive();
    }

    /**
     * Check if department is archived
     */
    public function isDepartmentArchived(EmployeeDepartment $department): bool
    {
        return $department->status->isArchived();
    }

    /**
     * Check if department can operate
     */
    public function canDepartmentOperate(EmployeeDepartment $department): bool
    {
        return $department->status->canOperate();
    }

    /**
     * Check if department is visible
     */
    public function isDepartmentVisible(EmployeeDepartment $department): bool
    {
        return $department->status->isVisible();
    }

    /**
     * Get department status label
     */
    public function getDepartmentStatusLabel(EmployeeDepartment $department): string
    {
        return $department->status->label();
    }

    /**
     * Get department status color
     */
    public function getDepartmentStatusColor(EmployeeDepartment $department): string
    {
        return $department->status->color();
    }

    /**
     * Get department status short label
     */
    public function getDepartmentStatusShortLabel(EmployeeDepartment $department): string
    {
        return $department->status->shortLabel();
    }

    /**
     * Bulk activate departments
     */
    public function bulkActivateDepartments(array $departmentIds): array
    {
        $results = [];

        foreach ($departmentIds as $id) {
            $department = $this->departmentRepository->find($id);
            if ($department) {
                $results[$id] = $this->activateDepartment($department);
            } else {
                $results[$id] = false;
            }
        }

        return $results;
    }

    /**
     * Bulk deactivate departments
     */
    public function bulkDeactivateDepartments(array $departmentIds): array
    {
        $results = [];

        foreach ($departmentIds as $id) {
            $department = $this->departmentRepository->find($id);
            if ($department) {
                $results[$id] = $this->deactivateDepartment($department);
            } else {
                $results[$id] = false;
            }
        }

        return $results;
    }

    /**
     * Bulk archive departments
     */
    public function bulkArchiveDepartments(array $departmentIds): array
    {
        $results = [];

        foreach ($departmentIds as $id) {
            $department = $this->departmentRepository->find($id);
            if ($department) {
                $results[$id] = $this->archiveDepartment($department);
            } else {
                $results[$id] = false;
            }
        }

        return $results;
    }

    /**
     * Get department status summary
     */
    public function getDepartmentStatusSummary(): array
    {
        return [
            'active' => $this->getActiveDepartmentCount(),
            'inactive' => $this->getInactiveDepartmentCount(),
            'archived' => $this->getArchivedDepartmentCount(),
            'pending' => $this->getPendingDepartmentCount(),
            'suspended' => $this->getSuspendedDepartmentCount(),
            'total' => $this->departmentRepository->getTotalDepartmentCount(),
        ];
    }

    /**
     * Get departments that can operate
     */
    public function getOperationalDepartments(): Collection
    {
        return $this->getActiveDepartments()->filter(function ($department) {
            return $this->canDepartmentOperate($department);
        });
    }

    /**
     * Get departments that can operate as DTOs
     */
    public function getOperationalDepartmentsDTO(): Collection
    {
        return $this->getOperationalDepartments()->map(fn($dept) => EmployeeDepartmentDTO::fromModel($dept));
    }
}
