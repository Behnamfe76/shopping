<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\RoleStoreRequest;
use Fereydooni\Shopping\app\Http\Requests\RoleUpdateRequest;
use Fereydooni\Shopping\app\Http\Resources\RoleResource;
use Fereydooni\Shopping\app\Models\Role;
use Fereydooni\Shopping\app\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {
        $user = auth()->user();
        if (! $user->hasRole('super-admin')) {
            abort(403);
        }
    }

    /**
     * Display a listing of Roles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $roles = match ($paginationType) {
                'simplePaginate' => $this->roleService->simplePaginate($perPage),
                'cursorPaginate' => $this->roleService->cursorPaginate($perPage),
                default => $this->roleService->paginate($perPage),
            };

            return RoleResource::collection($roles)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve Roles',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of category statuses.
     */
    public function roles(Request $request): JsonResponse
    {
        try {
            return response()->json($this->roleService->cursorAll(cursor: $request->get('cursor')));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve roles',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created Role.
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {

        try {
            $role = $this->roleService->create($request->validated());

            return (new RoleResource($role))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified Role.
     */
    public function show(Role $role): JsonResponse
    {
        try {
            return (new RoleResource($role))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified Role.
     */
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        try {
            $this->roleService->update($role, $request->validated());

            return (new RoleResource($role))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified Role.
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $deleted = $this->roleService->delete($role);

            if (! $deleted) {
                return response()->json([
                    'message' => 'Failed to delete Role',
                ], 500);
            }

            return response()->json([
                'message' => 'Role deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
