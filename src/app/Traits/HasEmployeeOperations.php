<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeOperations
{
    protected Employee $model;

    protected string $dtoClass = EmployeeDTO::class;

    // Employee-specific CRUD operations
    public function findByUserId(int $userId): ?Employee
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByUserIdDTO(int $userId): ?EmployeeDTO
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function findByEmail(string $email): ?Employee
    {
        return $this->repository->findByEmail($email);
    }

    public function findByEmailDTO(string $email): ?EmployeeDTO
    {
        return $this->repository->findByEmailDTO($email);
    }

    public function findByPhone(string $phone): ?Employee
    {
        return $this->repository->findByPhone($phone);
    }

    public function findByPhoneDTO(string $phone): ?EmployeeDTO
    {
        return $this->repository->findByPhoneDTO($phone);
    }

    public function findByEmployeeNumber(string $employeeNumber): ?Employee
    {
        return $this->repository->findByEmployeeNumber($employeeNumber);
    }

    public function findByEmployeeNumberDTO(string $employeeNumber): ?EmployeeDTO
    {
        return $this->repository->findByEmployeeNumberDTO($employeeNumber);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByEmploymentType(string $employmentType): Collection
    {
        return $this->repository->findByEmploymentType($employmentType);
    }

    public function findByEmploymentTypeDTO(string $employmentType): Collection
    {
        return $this->repository->findByEmploymentTypeDTO($employmentType);
    }

    public function findByDepartment(string $department): Collection
    {
        return $this->repository->findByDepartment($department);
    }

    public function findByDepartmentDTO(string $department): Collection
    {
        return $this->repository->findByDepartmentDTO($department);
    }

    public function findByPosition(string $position): Collection
    {
        return $this->repository->findByPosition($position);
    }

    public function findByPositionDTO(string $position): Collection
    {
        return $this->repository->findByPositionDTO($position);
    }

    public function findByManagerId(int $managerId): Collection
    {
        return $this->repository->findByManagerId($managerId);
    }

    public function findByManagerIdDTO(int $managerId): Collection
    {
        return $this->repository->findByManagerIdDTO($managerId);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findInactive(): Collection
    {
        return $this->repository->findInactive();
    }

    public function findInactiveDTO(): Collection
    {
        return $this->repository->findInactiveDTO();
    }

    public function findTerminated(): Collection
    {
        return $this->repository->findTerminated();
    }

    public function findTerminatedDTO(): Collection
    {
        return $this->repository->findTerminatedDTO();
    }

    // Employee search operations
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    public function searchByDepartment(string $department, string $query): Collection
    {
        return $this->repository->searchByDepartment($department, $query);
    }

    public function searchByDepartmentDTO(string $department, string $query): Collection
    {
        return $this->repository->searchByDepartmentDTO($department, $query);
    }

    public function searchByPosition(string $position, string $query): Collection
    {
        return $this->repository->searchByPosition($position, $query);
    }

    public function searchByPositionDTO(string $position, string $query): Collection
    {
        return $this->repository->searchByPositionDTO($position, $query);
    }

    // Employee creation and validation
    public function createEmployee(array $data): Employee
    {
        if (! $this->repository->validateEmployee($data)) {
            throw new \InvalidArgumentException('Invalid employee data provided');
        }

        return $this->repository->create($data);
    }

    public function createEmployeeDTO(array $data): EmployeeDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function updateEmployee(Employee $employee, array $data): bool
    {
        return $this->repository->update($employee, $data);
    }

    public function updateEmployeeDTO(Employee $employee, array $data): ?EmployeeDTO
    {
        return $this->repository->updateAndReturnDTO($employee, $data);
    }

    public function deleteEmployee(Employee $employee): bool
    {
        return $this->repository->delete($employee);
    }

    // Employee number management
    public function generateEmployeeNumber(): string
    {
        return $this->repository->generateEmployeeNumber();
    }

    public function isEmployeeNumberUnique(string $employeeNumber): bool
    {
        return $this->repository->isEmployeeNumberUnique($employeeNumber);
    }

    // Employee hierarchy operations
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

    // Employee notes management
    public function addEmployeeNote(Employee $employee, string $note, string $type = 'general'): bool
    {
        return $this->repository->addEmployeeNote($employee, $note, $type);
    }

    public function getEmployeeNotes(Employee $employee): Collection
    {
        return $this->repository->getEmployeeNotes($employee);
    }

    // Employee skills and certifications
    public function updateEmployeeSkills(Employee $employee, array $skills): bool
    {
        return $this->repository->updateEmployeeSkills($employee, $skills);
    }

    public function getEmployeeSkills(int $employeeId): array
    {
        return $this->repository->getEmployeeSkills($employeeId);
    }

    public function updateEmployeeCertifications(Employee $employee, array $certifications): bool
    {
        return $this->repository->updateEmployeeCertifications($employee, $certifications);
    }

    public function getEmployeeCertifications(int $employeeId): array
    {
        return $this->repository->getEmployeeCertifications($employeeId);
    }

    // Employee benefits management
    public function updateEmployeeBenefits(Employee $employee, array $benefits): bool
    {
        return $this->repository->updateEmployeeBenefits($employee, $benefits);
    }

    public function getEmployeeBenefits(int $employeeId): array
    {
        return $this->repository->getEmployeeBenefits($employeeId);
    }

    // Employee statistics
    public function getEmployeeCount(): int
    {
        return $this->repository->getEmployeeCount();
    }

    public function getEmployeeCountByStatus(string $status): int
    {
        return $this->repository->getEmployeeCountByStatus($status);
    }

    public function getEmployeeCountByDepartment(string $department): int
    {
        return $this->repository->getEmployeeCountByDepartment($department);
    }

    public function getEmployeeCountByEmploymentType(string $employmentType): int
    {
        return $this->repository->getEmployeeCountByEmploymentType($employmentType);
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

    // Employee analytics
    public function getEmployeeStats(): array
    {
        return $this->repository->getEmployeeStats();
    }

    public function getEmployeeStatsByStatus(): array
    {
        return $this->repository->getEmployeeStatsByStatus();
    }

    public function getEmployeeStatsByDepartment(): array
    {
        return $this->repository->getEmployeeStatsByDepartment();
    }

    public function getEmployeeStatsByEmploymentType(): array
    {
        return $this->repository->getEmployeeStatsByEmploymentType();
    }
}
