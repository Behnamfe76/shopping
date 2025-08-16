<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\Services\UserSubscriptionService;
use Fereydooni\Shopping\app\Http\Requests\StoreUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\ActivateUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\CancelUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchUserSubscriptionRequest;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionResource;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionCollection;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionSearchResource;
use Fereydooni\Shopping\app\Http\Resources\UserSubscriptionStatisticsResource;

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
    public function index(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->paginate($perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'statistics' => $this->userSubscriptionService->getGlobalSubscriptionStatistics(),
        ]);
    }

    /**
     * Show the form for creating a new user subscription.
     */
    public function create(): View
    {
        $this->authorize('create', UserSubscription::class);

        return view('shopping.user-subscriptions.create');
    }

    /**
     * Store a newly created user subscription.
     */
    public function store(StoreUserSubscriptionRequest $request): RedirectResponse
    {
        $this->authorize('create', UserSubscription::class);

        $data = $request->validated();
        $userSubscription = $this->userSubscriptionService->createUserSubscription($data);

        return redirect()
            ->route('shopping.user-subscriptions.show', $userSubscription)
            ->with('success', 'User subscription created successfully.');
    }

    /**
     * Display the specified user subscription.
     */
    public function show(UserSubscription $userSubscription): View
    {
        $this->authorize('view', $userSubscription);

        $userSubscriptionDTO = $this->userSubscriptionService->findDTO($userSubscription->id);

        return view('shopping.user-subscriptions.show', [
            'userSubscription' => $userSubscriptionDTO,
        ]);
    }

    /**
     * Show the form for editing the specified user subscription.
     */
    public function edit(UserSubscription $userSubscription): View
    {
        $this->authorize('update', $userSubscription);

        $userSubscriptionDTO = $this->userSubscriptionService->findDTO($userSubscription->id);

        return view('shopping.user-subscriptions.edit', [
            'userSubscription' => $userSubscriptionDTO,
        ]);
    }

    /**
     * Update the specified user subscription.
     */
    public function update(UpdateUserSubscriptionRequest $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('update', $userSubscription);

        $data = $request->validated();
        $updatedUserSubscription = $this->userSubscriptionService->updateUserSubscription($userSubscription->id, $data);

        if (!$updatedUserSubscription) {
            return back()->with('error', 'Failed to update user subscription.');
        }

        return redirect()
            ->route('shopping.user-subscriptions.show', $userSubscription)
            ->with('success', 'User subscription updated successfully.');
    }

    /**
     * Remove the specified user subscription.
     */
    public function destroy(UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('delete', $userSubscription);

        $deleted = $this->userSubscriptionService->deleteUserSubscription($userSubscription->id);

        if (!$deleted) {
            return back()->with('error', 'Failed to delete user subscription.');
        }

        return redirect()
            ->route('shopping.user-subscriptions.index')
            ->with('success', 'User subscription deleted successfully.');
    }

    /**
     * Activate the specified user subscription.
     */
    public function activate(ActivateUserSubscriptionRequest $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('activate', $userSubscription);

        $activated = $this->userSubscriptionService->activateUserSubscription($userSubscription->id);

        if (!$activated) {
            return back()->with('error', 'Failed to activate user subscription.');
        }

        return back()->with('success', 'User subscription activated successfully.');
    }

    /**
     * Cancel the specified user subscription.
     */
    public function cancel(CancelUserSubscriptionRequest $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('cancel', $userSubscription);

        $reason = $request->get('reason');
        $cancelled = $this->userSubscriptionService->cancelUserSubscription($userSubscription->id, $reason);

        if (!$cancelled) {
            return back()->with('error', 'Failed to cancel user subscription.');
        }

        return back()->with('success', 'User subscription cancelled successfully.');
    }

    /**
     * Renew the specified user subscription.
     */
    public function renew(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('renew', $userSubscription);

        $renewed = $this->userSubscriptionService->renewUserSubscription($userSubscription->id);

        if (!$renewed) {
            return back()->with('error', 'Failed to renew user subscription.');
        }

        return back()->with('success', 'User subscription renewed successfully.');
    }

    /**
     * Pause the specified user subscription.
     */
    public function pause(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('pause', $userSubscription);

        $reason = $request->get('reason');
        $paused = $this->userSubscriptionService->pauseUserSubscription($userSubscription->id, $reason);

        if (!$paused) {
            return back()->with('error', 'Failed to pause user subscription.');
        }

        return back()->with('success', 'User subscription paused successfully.');
    }

    /**
     * Resume the specified user subscription.
     */
    public function resume(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        $this->authorize('resume', $userSubscription);

        $resumed = $this->userSubscriptionService->resumeUserSubscription($userSubscription->id);

        if (!$resumed) {
            return back()->with('error', 'Failed to resume user subscription.');
        }

        return back()->with('success', 'User subscription resumed successfully.');
    }

    /**
     * Search user subscriptions.
     */
    public function search(SearchUserSubscriptionRequest $request): View
    {
        $this->authorize('search', UserSubscription::class);

        $query = $request->get('query');
        $userId = $request->get('user_id');
        $perPage = $request->get('per_page', 15);

        $userSubscriptions = $this->userSubscriptionService->searchUserSubscriptions($userId, $query, $perPage);

        return view('shopping.user-subscriptions.search', [
            'userSubscriptions' => $userSubscriptions,
            'query' => $query,
            'userId' => $userId,
        ]);
    }

    /**
     * Get user subscription statistics.
     */
    public function statistics(Request $request): View
    {
        $this->authorize('viewStatistics', UserSubscription::class);

        $userId = $request->get('user_id');

        if ($userId) {
            $statistics = $this->userSubscriptionService->getUserSubscriptionStatistics($userId);
        } else {
            $statistics = $this->userSubscriptionService->getGlobalSubscriptionStatistics();
        }

        return view('shopping.user-subscriptions.statistics', [
            'statistics' => $statistics,
            'userId' => $userId,
        ]);
    }

    /**
     * Get user subscription analytics.
     */
    public function analytics(Request $request): View
    {
        $this->authorize('viewAnalytics', UserSubscription::class);

        $analytics = $this->userSubscriptionService->getSubscriptionAnalytics();

        return view('shopping.user-subscriptions.analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get active user subscriptions.
     */
    public function active(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('active', $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'active',
        ]);
    }

    /**
     * Get trial user subscriptions.
     */
    public function trial(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('trialing', $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'trial',
        ]);
    }

    /**
     * Get expired user subscriptions.
     */
    public function expired(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('expired', $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'expired',
        ]);
    }

    /**
     * Get cancelled user subscriptions.
     */
    public function cancelled(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('cancelled', $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'cancelled',
        ]);
    }

    /**
     * Get paused user subscriptions.
     */
    public function paused(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus('paused', $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'paused',
        ]);
    }

    /**
     * Get upcoming renewals.
     */
    public function renewals(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 7);
        $userSubscriptions = $this->userSubscriptionService->getUpcomingRenewals($days);

        return view('shopping.user-subscriptions.renewals', [
            'userSubscriptions' => $userSubscriptions,
            'days' => $days,
        ]);
    }

    /**
     * Get expiring trials.
     */
    public function expiringTrials(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 3);
        $userSubscriptions = $this->userSubscriptionService->getExpiringTrials($days);

        return view('shopping.user-subscriptions.expiring-trials', [
            'userSubscriptions' => $userSubscriptions,
            'days' => $days,
        ]);
    }

    /**
     * Get expiring subscriptions.
     */
    public function expiring(Request $request): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $days = $request->get('days', 30);
        $userSubscriptions = $this->userSubscriptionService->getExpiringSubscriptions($days);

        return view('shopping.user-subscriptions.expiring', [
            'userSubscriptions' => $userSubscriptions,
            'days' => $days,
        ]);
    }

    /**
     * Get user subscription revenue.
     */
    public function revenue(Request $request): View
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

        return view('shopping.user-subscriptions.revenue', [
            'revenue' => $revenue,
            'userId' => $userId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Get popular subscriptions.
     */
    public function popular(Request $request): View
    {
        $this->authorize('viewAnalytics', UserSubscription::class);

        $limit = $request->get('limit', 10);
        $userSubscriptions = $this->userSubscriptionService->getPopularSubscriptions($limit);

        return view('shopping.user-subscriptions.popular', [
            'userSubscriptions' => $userSubscriptions,
            'limit' => $limit,
        ]);
    }

    /**
     * Get user subscriptions by user.
     */
    public function byUser(Request $request, int $userId): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByUserId($userId, $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'user',
            'userId' => $userId,
        ]);
    }

    /**
     * Get user subscriptions by subscription.
     */
    public function bySubscription(Request $request, int $subscriptionId): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findBySubscriptionId($subscriptionId, $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'subscription',
            'subscriptionId' => $subscriptionId,
        ]);
    }

    /**
     * Get user subscriptions by status.
     */
    public function byStatus(Request $request, string $status): View
    {
        $this->authorize('viewAny', UserSubscription::class);

        $perPage = $request->get('per_page', 15);
        $userSubscriptions = $this->userSubscriptionService->findByStatus($status, $perPage);

        return view('shopping.user-subscriptions.index', [
            'userSubscriptions' => $userSubscriptions,
            'filter' => 'status',
            'status' => $status,
        ]);
    }
}
