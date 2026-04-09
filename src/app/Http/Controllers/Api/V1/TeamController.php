<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Facades\Team as TeamFacade;
use Fereydooni\Shopping\app\Http\Requests\StoreTeamRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateTeamRequest;
use Fereydooni\Shopping\app\Http\Resources\TeamResource;
use Fereydooni\Shopping\app\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Team::class);

        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $teams = match ($paginationType) {
                'simplePaginate' => TeamFacade::simplePaginate($perPage),
                'cursorPaginate' => TeamFacade::cursorPaginate($perPage),
                default => TeamFacade::paginate($perPage),
            };

            return TeamResource::collection($teams)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve teams',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of team statuses.
     */
    public function statuses(): JsonResponse
    {
        Gate::authorize('viewAny', Team::class);

        try {
            return response()->json([
                'data' => array_map(fn ($status) => [
                    'id' => $status->value,
                    'name' => __('teams.statuses.'.$status->value),
                ], TeamStatus::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve team statuses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display all teams.
     */
    public function cursorAll(): JsonResponse
    {
        Gate::authorize('viewAny', Team::class);

        try {
            return response()->json(
                TeamFacade::cursorAll(),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve teams',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created team in storage.
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        Gate::authorize('create', Team::class);

        try {
            $team = TeamFacade::create($request->validated());

            return (new TeamResource($team))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create team',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        try {
            return (new TeamResource($team))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve team',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified team in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        try {
            TeamFacade::update($team, $request->validated());

            return (new TeamResource($team))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update team',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified team from storage.
     */
    public function destroy(Team $team): JsonResponse
    {
        Gate::authorize('delete', $team);

        try {
            TeamFacade::delete($team);

            return response()->json([
                'message' => 'Team deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete team',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove all teams from storage.
     */
    public function destroyAll(): JsonResponse
    {
        Gate::authorize('deleteAll', Team::class);

        try {
            TeamFacade::deleteAll();

            return response()->json([
                'message' => 'All teams deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete all teams',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a selection of teams from storage.
     */
    public function destroySome(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:teams,id',
        ]);
        $ids = $request->input('ids');

        Gate::authorize('deleteSome', Team::class);

        try {
            TeamFacade::deleteSome($ids);

            return response()->json([
                'message' => 'Selected teams deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete selected teams',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team count.
     */
    public function getCount(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Team::class);

        try {
            $count = TeamFacade::getTeamCount();

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get team count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get teams by department.
     */
    public function getByDepartment(int $departmentId, Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Team::class);

        try {
            $teams = TeamFacade::getByDepartmentDTO($departmentId);

            return response()->json([
                'data' => $teams,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get teams by department',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        Gate::authorize('viewStats', Team::class);

        try {
            $stats = TeamFacade::getTeamStats();
            $statsByStatus = TeamFacade::getTeamStatsByStatus();

            return response()->json([
                'stats' => $stats,
                'statsByStatus' => $statsByStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get team statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add member to team.
     */
    public function addMember(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('manageMember', $team);

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'is_manager' => 'boolean',
        ]);

        try {
            TeamFacade::addMember($team, $request->input('employee_id'), $request->boolean('is_manager', false));

            return response()->json([
                'message' => 'Member added successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add member',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove member from team.
     */
    public function removeMember(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('manageMember', $team);

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        try {
            TeamFacade::removeMember($team, $request->input('employee_id'));

            return response()->json([
                'message' => 'Member removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove member',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Promote member to manager.
     */
    public function promoteToManager(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('manageManager', $team);

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        try {
            TeamFacade::promoteToManager($team, $request->input('employee_id'));

            return response()->json([
                'message' => 'Member promoted to manager successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to promote member',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Demote manager to member.
     */
    public function demoteFromManager(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('manageManager', $team);

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        try {
            TeamFacade::demoteFromManager($team, $request->input('employee_id'));

            return response()->json([
                'message' => 'Manager demoted to member successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to demote manager',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change team manager.
     */
    public function changeManager(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('manageManager', $team);

        $request->validate([
            'old_manager_id' => 'required|integer|exists:employees,id',
            'new_manager_id' => 'required|integer|exists:employees,id',
        ]);

        try {
            TeamFacade::changeManager($team, $request->input('old_manager_id'), $request->input('new_manager_id'));

            return response()->json([
                'message' => 'Manager changed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to change manager',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team members.
     */
    public function getMembers(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('view', $team);

        try {
            $members = TeamFacade::getMembers($team);

            return response()->json([
                'data' => $members,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get team members',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team managers.
     */
    public function getManagers(Team $team, Request $request): JsonResponse
    {
        Gate::authorize('view', $team);

        try {
            $managers = TeamFacade::getManagers($team);

            return response()->json([
                'data' => $managers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get team managers',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
