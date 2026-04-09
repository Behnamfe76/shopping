<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Models\Team;
use Fereydooni\Shopping\app\Repositories\Interfaces\TeamRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;

class TeamService
{
    use HasCrudOperations;

    public function __construct(
        protected TeamRepositoryInterface $repository
    ) {
        $this->model = Team::class;
        $this->dtoClass = TeamDTO::class;
    }

    public array $searchableFields = ['name', 'code', 'description'];

    // Basic CRUD Operations
    public function cursorAll(): CursorPaginator
    {
        return $this->repository->cursorAll(10, request()->get('cursor'));
    }

    public function find(int $id): ?Team
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?TeamDTO
    {
        return $this->repository->findDTO($id);
    }

    public function findByCode(string $code): ?Team
    {
        return $this->repository->findByCode($code);
    }

    public function findByCodeDTO(string $code): ?TeamDTO
    {
        return $this->repository->findByCodeDTO($code);
    }

    // Override create method to handle team-specific logic
    public function create(array $data): Team
    {
        $this->validateData($data);

        return $this->repository->create($data);
    }

    // Override createDTO method to handle team-specific logic
    public function createDTO(array $data): TeamDTO
    {
        $this->validateData($data);

        return $this->repository->createAndReturnDTO($data);
    }

    // Override update method to handle team-specific logic
    public function update(Team $team, array $data): bool
    {
        $this->validateData($data, $team);

        return $this->repository->update($team, $data);
    }

    // Override updateDTO method to handle team-specific logic
    public function updateDTO(Team $team, array $data): ?TeamDTO
    {
        $this->validateData($data, $team);

        return $this->repository->updateAndReturnDTO($team, $data);
    }

    // Override delete method to handle team-specific logic
    public function delete(Team $team): bool
    {
        // Check if team has active members (optional - you can allow deletion)
        // if ($this->repository->hasMembers($team)) {
        //     throw new \InvalidArgumentException('Cannot delete team with active members. Please remove members first.');
        // }

        return $this->repository->delete($team);
    }

    // Department Operations
    public function getByDepartment(int $departmentId): Collection
    {
        return $this->repository->getByDepartment($departmentId);
    }

    public function getByDepartmentDTO(int $departmentId): Collection
    {
        return $this->repository->getByDepartmentDTO($departmentId);
    }

    // Member Management
    public function addMember(Team $team, int $employeeId, bool $isManager = false): bool
    {
        // Validate employee exists
        $employee = \Fereydooni\Shopping\app\Models\Employee::find($employeeId);
        if (! $employee) {
            throw new \InvalidArgumentException('Employee not found');
        }

        // Check if employee belongs to the same department
        if ($employee->department_id !== $team->department_id) {
            throw new \InvalidArgumentException('Employee must belong to the same department as the team');
        }

        // Check member limit
        if ($team->member_limit && $team->member_count >= $team->member_limit) {
            throw new \InvalidArgumentException('Team has reached its member limit');
        }

        return $this->repository->addMember($team, $employeeId, $isManager);
    }

    public function removeMember(Team $team, int $employeeId): bool
    {
        // Check if employee is the last manager
        if ($team->isManager($employeeId) && $team->manager_count <= 1) {
            throw new \InvalidArgumentException('Cannot remove the last manager from the team');
        }

        return $this->repository->removeMember($team, $employeeId);
    }

    public function promoteToManager(Team $team, int $employeeId): bool
    {
        if (! $team->isMember($employeeId)) {
            throw new \InvalidArgumentException('Employee is not a member of this team');
        }

        return $this->repository->promoteToManager($team, $employeeId);
    }

    public function demoteFromManager(Team $team, int $employeeId): bool
    {
        if (! $team->isManager($employeeId)) {
            throw new \InvalidArgumentException('Employee is not a manager of this team');
        }

        // Ensure at least one manager remains
        if ($team->manager_count <= 1) {
            throw new \InvalidArgumentException('Team must have at least one manager');
        }

        return $this->repository->demoteFromManager($team, $employeeId);
    }

    public function changeManager(Team $team, int $oldManagerId, int $newManagerId): bool
    {
        if (! $team->isManager($oldManagerId)) {
            throw new \InvalidArgumentException('Old employee is not a manager of this team');
        }

        if (! $team->isMember($newManagerId)) {
            throw new \InvalidArgumentException('New employee is not a member of this team');
        }

        return $this->repository->changeManager($team, $oldManagerId, $newManagerId);
    }

    public function getMembers(Team $team): Collection
    {
        return $this->repository->getMembers($team);
    }

    public function getManagers(Team $team): Collection
    {
        return $this->repository->getManagers($team);
    }

    // Status Operations
    public function findByStatus(TeamStatus $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(TeamStatus $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByLocation(string $location): Collection
    {
        return $this->repository->findByLocation($location);
    }

    // Statistics
    public function getTeamStats(): array
    {
        return $this->repository->getTeamStats();
    }

    public function getTeamStatsByStatus(): array
    {
        return $this->repository->getTeamStatsByStatus();
    }

    // Count Operations
    public function getTeamCount(): int
    {
        return $this->repository->getTeamCount();
    }

    public function getTeamCountByStatus(TeamStatus $status): int
    {
        return $this->repository->getTeamCountByStatus($status);
    }

    public function getTeamCountByDepartment(int $departmentId): int
    {
        return $this->repository->getTeamCountByDepartment($departmentId);
    }

    // Validation
    public function validateTeam(array $data): bool
    {
        return $this->repository->validateTeam($data);
    }

    // Helper Methods
    protected function validateData(array $data, ?Team $team = null): void
    {
        // Validate department if provided
        if (isset($data['department_id']) && $data['department_id']) {
            $department = \Fereydooni\Shopping\app\Models\Department::find($data['department_id']);
            if (! $department) {
                throw new \InvalidArgumentException('Department not found');
            }
        }
    }
}
