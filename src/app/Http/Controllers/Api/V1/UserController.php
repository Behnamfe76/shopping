<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Fereydooni\Shopping\app\Facades\User as UserFacade;
use Fereydooni\Shopping\app\Http\Resources\UserResource;
use Fereydooni\Shopping\app\Http\Requests\StoreUserRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct() {}

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
     * Display all users.
     */
    public function cursorAll(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        try {
            return response()->json(
                UserFacade::cursorAll(perPage: $request->get('per_page', 10),cursor: $request->get('cursor')),
            );

            // return (new CategoryCollection($users))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve users',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created User.
     */
    public function store(StoreUserRequest $request): JsonResponse
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
    public function update(UpdateUserRequest $request, User $user): JsonResponse
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
        Gate::authorize('delete', $user);

        try {
            $deleted = UserFacade::delete($user);

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
