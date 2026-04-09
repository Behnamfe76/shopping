<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface TeamRepositoryInterface
{
    /**
     * Get all teams
     */
    public function all(): Collection;

    /**
     * Get paginated teams (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated teams
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated teams
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Get cursor paginated teams
     */
    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find team by ID
     */
    public function find(int $id): ?Team;

    /**
     * Find team by ID and return DTO
     */
    public function findDTO(int $id): ?TeamDTO;

    /**
     * Find team by code
     */
    public function findByCode(string $code): ?Team;

    /**
     * Find team by code and return DTO
     */
    public function findByCodeDTO(string $code): ?TeamDTO;

    /**
     * Create a new team
     */
    public function create(array $data): Team;

    /**
     * Create a new team and return DTO
     */
    public function createAndReturnDTO(array $data): TeamDTO;

    /**
     * Update team
     */
    public function update(Team $team, array $data): bool;

    /**
     * Update team and return DTO
     */
    public function updateAndReturnDTO(Team $team, array $data): ?TeamDTO;

    /**
     * Delete team
     */
    public function delete(Team $team): bool;

    /**
     * Get teams by department
     */
    public function getByDepartment(int $departmentId): Collection;

    /**
     * Get teams by department as DTOs
     */
    public function getByDepartmentDTO(int $departmentId): Collection;

    /**
     * Search teams by name
     */
    public function search(string $query): Collection;

    /**
     * Get teams with members count
     */
    public function getWithMembersCount(): Collection;

    /**
     * Check if team has members
     */
    public function hasMembers(Team $team): bool;

    /**
     * Check if team has managers
     */
    public function hasManagers(Team $team): bool;

    /**
     * Get teams by status
     */
    public function findByStatus(TeamStatus $status): Collection;

    /**
     * Get teams by status as DTOs
     */
    public function findByStatusDTO(TeamStatus $status): Collection;

    /**
     * Get teams by location
     */
    public function findByLocation(string $location): Collection;

    /**
     * Get team statistics
     */
    public function getTeamStats(): array;

    /**
     * Get team statistics by status
     */
    public function getTeamStatsByStatus(): array;

    /**
     * Get team count
     */
    public function getTeamCount(): int;

    /**
     * Get team count by status
     */
    public function getTeamCountByStatus(TeamStatus $status): int;

    /**
     * Get team count by department
     */
    public function getTeamCountByDepartment(int $departmentId): int;

    /**
     * Add member to team
     */
    public function addMember(Team $team, int $employeeId, bool $isManager = false): bool;

    /**
     * Remove member from team
     */
    public function removeMember(Team $team, int $employeeId): bool;

    /**
     * Promote member to manager
     */
    public function promoteToManager(Team $team, int $employeeId): bool;

    /**
     * Demote manager to member
     */
    public function demoteFromManager(Team $team, int $employeeId): bool;

    /**
     * Change team manager
     */
    public function changeManager(Team $team, int $oldManagerId, int $newManagerId): bool;

    /**
     * Get team members
     */
    public function getMembers(Team $team): Collection;

    /**
     * Get team managers
     */
    public function getManagers(Team $team): Collection;

    /**
     * Validate team data
     */
    public function validateTeam(array $data): bool;
}
