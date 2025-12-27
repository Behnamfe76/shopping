<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Facades\User as UserFacade;
use Fereydooni\Shopping\app\Http\Resources\UserResource;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Services\userService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private userService $userervice
    ) {}

    /**
     * Display a listing of Users.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $user = match ($paginationType) {
                'simplePaginate' => UserFacade::simplePaginate($perPage),
                'cursorPaginate' => UserFacade::cursorPaginate($perPage),
                default => UserFacade::paginate($perPage),
            };

            return UserResource::collection($user)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve users',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of category statuses.
     */
    public function roles(): JsonResponse
    {
        Gate::authorize('viewRoles', User::class);

        try {
            return response()->json([
                'data' => Role::cursorPaginate(10),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve User roles',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created User.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        try {
            $category = UserFacade::create($request->validated());

            return (new UserResource($category))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified User.
     */
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        try {
            return (new UserResource($user))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified User.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        try {
            UserFacade::update($user, $request->validated());

            return (new UserResource($user))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified User.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        try {
            $deleted = $this->userService->deleteUser($user);

            if (! $deleted) {
                return response()->json([
                    'message' => 'Failed to delete User',
                ], 500);
            }

            return response()->json([
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate the specified User.
     */
    public function activate(User $user): JsonResponse
    {
        $this->authorize('activate', $user);

        try {
            $activated = $this->userService->activateUser($user);

            if (! $activated) {
                return response()->json([
                    'message' => 'Failed to activate User',
                ], 500);
            }

            return response()->json([
                'message' => 'User activated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to activate User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate the specified User.
     */
    public function deactivate(User $user): JsonResponse
    {
        $this->authorize('deactivate', $user);

        try {
            $deactivated = $this->userService->deactivateUser($user);

            if (! $deactivated) {
                return response()->json([
                    'message' => 'Failed to deactivate User',
                ], 500);
            }

            return response()->json([
                'message' => 'User deactivated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deactivate User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suspend the specified User.
     */
    public function suspend(Request $request, User $user): JsonResponse
    {
        $this->authorize('suspend', $user);

        try {
            $reason = $request->get('reason');
            $suspended = $this->userService->suspendUser($user, $reason);

            if (! $suspended) {
                return response()->json([
                    'message' => 'Failed to suspend User',
                ], 500);
            }

            return response()->json([
                'message' => 'User suspended successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to suspend User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
