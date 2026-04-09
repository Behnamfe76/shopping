<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Models\Team;
use Fereydooni\Shopping\app\Repositories\Interfaces\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class TeamRepository implements TeamRepositoryInterface
{
    public function __construct(protected Team $model) {}

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

    public function find(int $id): ?Team
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code): ?Team
    {
        return $this->model->where('code', $code)->first();
    }

    public function findDTO(int $id): ?TeamDTO
    {
        $team = $this->find($id);

        return $team ? TeamDTO::fromModel($team) : null;
    }

    public function findByCodeDTO(string $code): ?TeamDTO
    {
        $team = $this->findByCode($code);

        return $team ? TeamDTO::fromModel($team) : null;
    }

    public function create(array $data): Team
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): TeamDTO
    {
        $team = $this->create($data);

        return TeamDTO::fromModel($team);
    }

    public function update(Team $team, array $data): bool
    {
        return $team->update($data);
    }

    public function updateAndReturnDTO(Team $team, array $data): ?TeamDTO
    {
        $updated = $this->update($team, $data);

        return $updated ? TeamDTO::fromModel($team->fresh()) : null;
    }

    public function delete(Team $team): bool
    {
        return $team->delete();
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    public function getByDepartmentDTO(int $departmentId): Collection
    {
        return $this->getByDepartment($departmentId)->map(fn ($team) => TeamDTO::fromModel($team));
    }

    public function getWithMembersCount(): Collection
    {
        return $this->model->withCount('members')->get();
    }

    public function hasMembers(Team $team): bool
    {
        return $team->activeMembers()->count() > 0;
    }

    public function hasManagers(Team $team): bool
    {
        return $team->activeManagers()->count() > 0;
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    public function findByStatus(TeamStatus $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function findByStatusDTO(TeamStatus $status): Collection
    {
        return $this->findByStatus($status)->map(fn ($team) => TeamDTO::fromModel($team));
    }

    public function findByLocation(string $location): Collection
    {
        return $this->model->where('location', $location)->get();
    }

    public function getTeamStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'with_members' => $this->model->has('members')->count(),
            'without_members' => $this->model->doesntHave('members')->count(),
        ];
    }

    public function getTeamStatsByStatus(): array
    {
        $stats = [];
        foreach (TeamStatus::cases() as $status) {
            $stats[$status->value] = $this->model->where('status', $status)->count();
        }

        return $stats;
    }

    public function getTeamCount(): int
    {
        return $this->model->count();
    }

    public function getTeamCountByStatus(TeamStatus $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getTeamCountByDepartment(int $departmentId): int
    {
        return $this->model->where('department_id', $departmentId)->count();
    }

    public function addMember(Team $team, int $employeeId, bool $isManager = false): bool
    {
        return $team->addMember($employeeId, $isManager);
    }

    public function removeMember(Team $team, int $employeeId): bool
    {
        return $team->removeMember($employeeId);
    }

    public function promoteToManager(Team $team, int $employeeId): bool
    {
        return $team->promoteToManager($employeeId);
    }

    public function demoteFromManager(Team $team, int $employeeId): bool
    {
        return $team->demoteFromManager($employeeId);
    }

    public function changeManager(Team $team, int $oldManagerId, int $newManagerId): bool
    {
        return $team->changeManager($oldManagerId, $newManagerId);
    }

    public function getMembers(Team $team): Collection
    {
        return $team->activeMembers;
    }

    public function getManagers(Team $team): Collection
    {
        return $team->activeManagers;
    }

    public function validateTeam(array $data): bool
    {
        // Additional validation logic if needed
        return true;
    }
}
