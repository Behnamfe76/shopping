<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Requests\ActivateUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\CancelUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionCollection;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionResource;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionSearchResource;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionStatisticsResource;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\Services\UserSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSubscriptionController extends Controller
{
    protected UserSubscriptionService $userSubscriptionService;

    public function __construct(UserSubscriptionService $userSubscriptionService)
    {
        $this->userSubscriptionService = $userSubscriptionService;
        $this->authorizeResource(UserSubscription::class, 'userSubscription');
    }

    /**
     * Display a listing of user subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->paginate($perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'User subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created user subscription.
     */
    public function store(StoreUserSubscriptionRequest $request): JsonResponse
    {
        $this->authorize('create', UserSubscription::class);

        $data = $request->validated();
        $userSubscription = $this->userSubscriptionService->createUserSubscription($data);

        return response()->json([
            'data' => new UserSubscriptionResource($userSubscription),
            'message' => 'User subscription created successfully.',
        ], 201);
    }

    /**
     * Display the specified user subscription.
     */
    public function show(UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('view', $userSubscription);

        $userSubscriptionDTO = $this->userSubscriptionService->findDTO($userSubscription->id);

        return response()->json([
            'data' => new UserSubscriptionResource($userSubscriptionDTO),
            'message' => 'User subscription retrieved successfully.',
        ]);
    }

    /**
     * Update the specified user subscription.
     */
    public function update(UpdateUserSubscriptionRequest $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('update', $userSubscription);

        $data = $request->validated();
        $updatedUserSubscription = $this->userSubscriptionService->updateUserSubscription($userSubscription->id, $data);

        if (! $updatedUserSubscription) {
            return response()->json([
                'message' => 'Failed to update user subscription.',
            ], 400);
        }

        return response()->json([
            'data' => new UserSubscriptionResource($updatedUserSubscription),
            'message' => 'User subscription updated successfully.',
        ]);
    }

    /**
     * Remove the specified user subscription.
     */
    public function destroy(UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('delete', $userSubscription);

        $deleted = $this->userSubscriptionService->deleteUserSubscription($userSubscription->id);

        if (! $deleted) {
            return response()->json([
                'message' => 'Failed to delete user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription deleted successfully.',
        ]);
    }

    /**
     * Activate the specified user subscription.
     */
    public function activate(ActivateUserSubscriptionRequest $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('activate', $userSubscription);

        $activated = $this->userSubscriptionService->activateUserSubscription($userSubscription->id);

        if (! $activated) {
            return response()->json([
                'message' => 'Failed to activate user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription activated successfully.',
        ]);
    }

    /**
     * Cancel the specified user subscription.
     */
    public function cancel(CancelUserSubscriptionRequest $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('cancel', $userSubscription);

        $reason = $request->get('reason');
        $cancelled = $this->userSubscriptionService->cancelUserSubscription($userSubscription->id, $reason);

        if (! $cancelled) {
            return response()->json([
                'message' => 'Failed to cancel user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription cancelled successfully.',
        ]);
    }

    /**
     * Renew the specified user subscription.
     */
    public function renew(Request $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('renew', $userSubscription);

        $renewed = $this->userSubscriptionService->renewUserSubscription($userSubscription->id);

        if (! $renewed) {
            return response()->json([
                'message' => 'Failed to renew user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription renewed successfully.',
        ]);
    }

    /**
     * Pause the specified user subscription.
     */
    public function pause(Request $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('pause', $userSubscription);

        $reason = $request->get('reason');
        $paused = $this->userSubscriptionService->pauseUserSubscription($userSubscription->id, $reason);

        if (! $paused) {
            return response()->json([
                'message' => 'Failed to pause user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription paused successfully.',
        ]);
    }

    /**
     * Resume the specified user subscription.
     */
    public function resume(Request $request, UserSubscription $userSubscription): JsonResponse
    {
        $this->authorize('resume', $userSubscription);

        $resumed = $this->userSubscriptionService->resumeUserSubscription($userSubscription->id);

        if (! $resumed) {
            return response()->json([
                'message' => 'Failed to resume user subscription.',
            ], 400);
        }

        return response()->json([
            'message' => 'User subscription resumed successfully.',
        ]);
    }

    /**
     * Search user subscriptions.
     */
    public function search(SearchUserSubscriptionRequest $request): JsonResponse
    {
        $this->authorize('search', UserSubscription::class);

        $query = $request->get('query');
        $userId = $request->get('user_id');
        $perPage = $request->get('per_page', 15);

        $userSubscriptions = $this->userSubscriptionService->searchUserSubscriptions($userId, $query, $perPage);

        return response()->json([
            'data' => new UserSubscriptionSearchResource($userSubscriptions),
            'message' => 'User subscriptions search completed successfully.',
        ]);
    }

    /**
     * Get user subscription statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewStatistics', UserSubscription::class);

        $userId = $request->get('user_id');

        if ($userId) {
            $statistics = $this->userSubscriptionService->getUserSubscriptionStatistics($userId);
        } else {
            $statistics = $this->userSubscriptionService->getGlobalSubscriptionStatistics();
        }

        return response()->json([
            'data' => new UserSubscriptionStatisticsResource($statistics),
            'message' => 'User subscription statistics retrieved successfully.',
        ]);
    }

    /**
     * Get user subscription analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', UserSubscription::class);

        $analytics = $this->userSubscriptionService->getSubscriptionAnalytics();

        return response()->json([
            'data' => $analytics,
            'message' => 'User subscription analytics retrieved successfully.',
        ]);
    }

    /**
     * Get user subscription count.
     */
    public function getCount(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $userId = $request->get('user_id');
        $status = $request->get('status');

        if ($userId && $status) {
            $count = $this->userSubscriptionService->getUserSubscriptionCountByStatus($userId, $status);
        } elseif ($userId) {
            $count = $this->userSubscriptionService->getUserSubscriptionCount($userId);
        } else {
            $count = $this->userSubscriptionService->getTotalActiveSubscriptions();
        }

        return response()->json([
            'data' => ['count' => $count],
            'message' => 'User subscription count retrieved successfully.',
        ]);
    }

    /**
     * Get user subscription revenue.
     */
    public function revenue(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', UserSubscription::class);

        $userId = $request->get('user_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($userId) {
            if ($startDate && $endDate) {
                $revenue = $this->userSubscriptionService->getUserSubscriptionRevenueByDateRange($userId, $startDate, $endDate);
            } else {
                $revenue = $this->userSubscriptionService->getUserSubscriptionRevenue($userId);
            }
        } else {
            if ($startDate && $endDate) {
                $revenue = $this->userSubscriptionService->getTotalRevenueByDateRange($startDate, $endDate);
            } else {
                $revenue = $this->userSubscriptionService->getTotalRevenue();
            }
        }

        return response()->json([
            'data' => ['revenue' => $revenue],
            'message' => 'User subscription revenue retrieved successfully.',
        ]);
    }

    /**
     * Get popular subscriptions.
     */
    public function popular(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', UserSubscription::class);

        $limit = $request->get('limit', 10);
        $userSubscriptions = $this->userSubscriptionService->getPopularSubscriptions($limit);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Popular subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get active user subscriptions.
     */
    public function getActive(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('active', $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Active user subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get trial user subscriptions.
     */
    public function getTrial(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('trialing', $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Trial user subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get expired user subscriptions.
     */
    public function getExpired(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('expired', $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Expired user subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get cancelled user subscriptions.
     */
    public function getCancelled(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('cancelled', $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Cancelled user subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get paused user subscriptions.
     */
    public function getPaused(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('paused', $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Paused user subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get upcoming renewals.
     */
    public function getRenewals(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 7);
        $userSubscriptions = $this->userSubscriptionService->getUpcomingRenewals($days);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Upcoming renewals retrieved successfully.',
        ]);
    }

    /**
     * Get expiring trials.
     */
    public function getExpiringTrials(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 3);
        $userSubscriptions = $this->userSubscriptionService->getExpiringTrials($days);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Expiring trials retrieved successfully.',
        ]);
    }

    /**
     * Get expiring subscriptions.
     */
    public function getExpiring(Request $request): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 30);
        $userSubscriptions = $this->userSubscriptionService->getExpiringSubscriptions($days);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'Expiring subscriptions retrieved successfully.',
        ]);
    }

    /**
     * Get user subscriptions by user.
     */
    public function getByUser(Request $request, int $userId): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByUserId($userId, $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'User subscriptions by user retrieved successfully.',
        ]);
    }

    /**
     * Get user subscriptions by subscription.
     */
    public function getBySubscription(Request $request, int $subscriptionId): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findBySubscriptionId($subscriptionId, $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'User subscriptions by subscription retrieved successfully.',
        ]);
    }

    /**
     * Get user subscriptions by status.
     */
    public function getByStatus(Request $request, string $status): JsonResponse
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus($status, $perPage);

        return response()->json([
            'data' => new UserSubscriptionCollection($userSubscriptions),
            'message' => 'User subscriptions by status retrieved successfully.',
        ]);
    }
}
