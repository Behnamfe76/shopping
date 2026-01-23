<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Facades\Department as DepartmentFacade;
use Fereydooni\Shopping\app\Http\Requests\StoreDepartmentRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateDepartmentRequest;
use Fereydooni\Shopping\app\Http\Resources\DepartmentResource;
use Fereydooni\Shopping\app\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $departments = match ($paginationType) {
                'simplePaginate' => DepartmentFacade::simplePaginate($perPage),
                'cursorPaginate' => DepartmentFacade::cursorPaginate($perPage),
                default => DepartmentFacade::paginate($perPage),
            };

            return DepartmentResource::collection($departments)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve departments',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of department statuses.
     */
    public function statuses(): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            return response()->json([
                'data' => array_map(fn ($status) => [
                    'id' => $status->value,
                    'name' => __('departments.statuses.'.$status->value),
                ], DepartmentStatus::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve department statuses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display all departments.
     */
    public function cursorAll(): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            return response()->json(
                DepartmentFacade::cursorAll(),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve departments',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        Gate::authorize('create', Department::class);

        try {
            $department = DepartmentFacade::create($request->validated());

            return (new DepartmentResource($department))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create department',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): JsonResponse
    {
        Gate::authorize('view', $department);

        try {
            return (new DepartmentResource($department))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve department',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified department in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        Gate::authorize('update', $department);

        try {
            DepartmentFacade::update($department, $request->validated());

            return (new DepartmentResource($department))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update department',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department): JsonResponse
    {
        Gate::authorize('delete', $department);

        try {
            DepartmentFacade::delete($department);

            return response()->json([
                'message' => 'Department deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete department',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove all departments from storage.
     */
    public function destroyAll(): JsonResponse
    {
        Gate::authorize('deleteAll', Department::class);

        try {
            DepartmentFacade::deleteAll();

            return response()->json([
                'message' => 'All departments deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete all departments',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a selection of departments from storage.
     */
    public function destroySome(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:departments,id',
        ]);
        $ids = $request->input('ids');

        Gate::authorize('deleteSome', Department::class);

        try {
            DepartmentFacade::deleteSome($ids);

            return response()->json([
                'message' => 'Selected departments deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete selected departments',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department tree.
     */
    public function tree(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            $tree = DepartmentFacade::getTreeDTO();

            return response()->json([
                'data' => $tree,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load department tree',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department count.
     */
    public function getCount(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            $count = DepartmentFacade::getDepartmentCount();

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get root departments.
     */
    public function getRoot(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        try {
            $rootDepartments = DepartmentFacade::getRootDepartmentsDTO();

            return response()->json([
                'data' => $rootDepartments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get root departments',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department children.
     */
    public function getChildren(Department $department, Request $request): JsonResponse
    {
        Gate::authorize('view', $department);

        try {
            $children = DepartmentFacade::getChildrenDTO($department->id);

            return response()->json([
                'data' => $children,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department children',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department ancestors.
     */
    public function getAncestors(Department $department, Request $request): JsonResponse
    {
        Gate::authorize('view', $department);

        try {
            $ancestors = DepartmentFacade::getAncestorsDTO($department->id);

            return response()->json([
                'data' => $ancestors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department ancestors',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department descendants.
     */
    public function getDescendants(Department $department, Request $request): JsonResponse
    {
        Gate::authorize('view', $department);

        try {
            $descendants = DepartmentFacade::getDescendantsDTO($department->id);

            return response()->json([
                'data' => $descendants,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department descendants',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get department statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        Gate::authorize('viewStats', Department::class);

        try {
            $stats = DepartmentFacade::getDepartmentStats();
            $statsByStatus = DepartmentFacade::getDepartmentStatsByStatus();

            return response()->json([
                'stats' => $stats,
                'statsByStatus' => $statsByStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
