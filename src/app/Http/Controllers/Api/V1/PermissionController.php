<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Fereydooni\Shopping\app\Services\PermissionService;
use Fereydooni\Shopping\app\Http\Resources\PermissionResource;
use Fereydooni\Shopping\app\Http\Requests\PermissionStoreRequest;
use Fereydooni\Shopping\app\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {
        $user = auth()->user();
        if(!$user->hasRole('super-admin')) {
            abort(403);
        }
    }

    /**
     * Display a listing of Permissions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $permissions = match ($paginationType) {
                'simplePaginate' => $this->permissionService->simplePaginate($perPage),
                'cursorPaginate' => $this->permissionService->cursorPaginate($perPage),
                default => $this->permissionService->paginate($perPage),
            };

            return PermissionResource::collection($permissions)->response()->setStatusCode(200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve Permissions',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of category statuses.
     */
    public function permissions(): JsonResponse
    {
        try {
            return response()->json([
                'data' => Permission::cursorPaginate(10),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve Permission Permissions',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created Permission.
     */
    public function store(PermissionStoreRequest $request): JsonResponse
    {
        try {
            $permission = $this->permissionService->create($request->validated());

            return (new PermissionResource($permission))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create Permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified Permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        try {
            return (new PermissionResource($permission))->response();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified Permission.
     */
    public function update(PermissionUpdateRequest $request, Permission $permission): JsonResponse
    {
        try {
            $this->permissionService->update($permission, $request->validated());

            return (new PermissionResource($permission))->response();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified Permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            $deleted = $this->permissionService->delete($permission);

            if (! $deleted) {
                return response()->json([
                    'message' => 'Failed to delete Permission',
                ], 500);
            }

            return response()->json([
                'message' => 'Permission deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
