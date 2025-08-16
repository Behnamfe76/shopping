<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\DTOs\UserSubscriptionDTO;
use Fereydooni\Shopping\app\Enums\SubscriptionStatus;
use Fereydooni\Shopping\app\Enums\BillingCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserSubscriptionRepository implements UserSubscriptionRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection
    {
        return UserSubscription::with(['user', 'subscription', 'order'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return UserSubscription::with(['user', 'subscription', 'order'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return UserSubscription::with(['user', 'subscription', 'order'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return UserSubscription::with(['user', 'subscription', 'order'])->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?UserSubscription
    {
        return UserSubscription::with(['user', 'subscription', 'order'])->find($id);
    }

    public function findDTO(int $id): ?UserSubscriptionDTO
    {
        $userSubscription = $this->find($id);
        return $userSubscription ? UserSubscriptionDTO::fromModel($userSubscription) : null;
    }

    public function findByUserId(int $userId): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        $userSubscriptions = $this->findByUserId($userId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findBySubscriptionId(int $subscriptionId): Collection
    {
        return UserSubscription::where('subscription_id', $subscriptionId)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findBySubscriptionIdDTO(int $subscriptionId): Collection
    {
        $userSubscriptions = $this->findBySubscriptionId($subscriptionId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findByStatus(string $status): Collection
    {
        return UserSubscription::where('status', $status)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $userSubscriptions = $this->findByStatus($status);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findByUserAndSubscription(int $userId, int $subscriptionId): ?UserSubscription
    {
        return UserSubscription::where('user_id', $userId)
            ->where('subscription_id', $subscriptionId)
            ->with(['user', 'subscription', 'order'])
            ->first();
    }

    public function findByUserAndSubscriptionDTO(int $userId, int $subscriptionId): ?UserSubscriptionDTO
    {
        $userSubscription = $this->findByUserAndSubscription($userId, $subscriptionId);
        return $userSubscription ? UserSubscriptionDTO::fromModel($userSubscription) : null;
    }

    public function findActiveByUserId(int $userId): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findActiveByUserIdDTO(int $userId): Collection
    {
        $userSubscriptions = $this->findActiveByUserId($userId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findExpiredByUserId(int $userId): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', SubscriptionStatus::EXPIRED)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findExpiredByUserIdDTO(int $userId): Collection
    {
        $userSubscriptions = $this->findExpiredByUserId($userId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findTrialByUserId(int $userId): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', SubscriptionStatus::TRIALING)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findTrialByUserIdDTO(int $userId): Collection
    {
        $userSubscriptions = $this->findTrialByUserId($userId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findCancelledByUserId(int $userId): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', SubscriptionStatus::CANCELLED)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findCancelledByUserIdDTO(int $userId): Collection
    {
        $userSubscriptions = $this->findCancelledByUserId($userId);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return UserSubscription::whereBetween('start_date', [$startDate, $endDate])
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $userSubscriptions = $this->findByDateRange($startDate, $endDate);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function findByNextBillingDate(string $date): Collection
    {
        return UserSubscription::where('next_billing_date', $date)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function findByNextBillingDateDTO(string $date): Collection
    {
        $userSubscriptions = $this->findByNextBillingDate($date);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function create(array $data): UserSubscription
    {
        return UserSubscription::create($data);
    }

    public function createAndReturnDTO(array $data): UserSubscriptionDTO
    {
        $userSubscription = $this->create($data);
        return UserSubscriptionDTO::fromModel($userSubscription);
    }

    public function update(UserSubscription $userSubscription, array $data): bool
    {
        return $userSubscription->update($data);
    }

    public function updateAndReturnDTO(UserSubscription $userSubscription, array $data): ?UserSubscriptionDTO
    {
        $updated = $this->update($userSubscription, $data);
        return $updated ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function delete(UserSubscription $userSubscription): bool
    {
        return $userSubscription->delete();
    }

    public function activate(UserSubscription $userSubscription): bool
    {
        return $userSubscription->update(['status' => SubscriptionStatus::ACTIVE]);
    }

    public function activateAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        $activated = $this->activate($userSubscription);
        return $activated ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function cancel(UserSubscription $userSubscription, string $reason = null): bool
    {
        return $userSubscription->update([
            'status' => SubscriptionStatus::CANCELLED,
            'end_date' => now(),
        ]);
    }

    public function cancelAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO
    {
        $cancelled = $this->cancel($userSubscription, $reason);
        return $cancelled ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function expire(UserSubscription $userSubscription): bool
    {
        return $userSubscription->update(['status' => SubscriptionStatus::EXPIRED]);
    }

    public function expireAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        $expired = $this->expire($userSubscription);
        return $expired ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function renew(UserSubscription $userSubscription): bool
    {
        $subscription = $userSubscription->subscription;
        $nextBillingDate = $this->calculateNextBillingDate($userSubscription);

        return $userSubscription->update([
            'status' => SubscriptionStatus::ACTIVE,
            'next_billing_date' => $nextBillingDate,
        ]);
    }

    public function renewAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        $renewed = $this->renew($userSubscription);
        return $renewed ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function pause(UserSubscription $userSubscription, string $reason = null): bool
    {
        // Note: You might want to add a 'paused' status to the enum
        return $userSubscription->update([
            'status' => SubscriptionStatus::CANCELLED, // Using cancelled as pause for now
        ]);
    }

    public function pauseAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO
    {
        $paused = $this->pause($userSubscription, $reason);
        return $paused ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function resume(UserSubscription $userSubscription): bool
    {
        return $userSubscription->update(['status' => SubscriptionStatus::ACTIVE]);
    }

    public function resumeAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        $resumed = $this->resume($userSubscription);
        return $resumed ? UserSubscriptionDTO::fromModel($userSubscription->fresh()) : null;
    }

    public function getUserSubscriptionCount(int $userId): int
    {
        return UserSubscription::where('user_id', $userId)->count();
    }

    public function getUserSubscriptionCountByStatus(int $userId, string $status): int
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', $status)
            ->count();
    }

    public function getTotalActiveSubscriptions(): int
    {
        return UserSubscription::where('status', SubscriptionStatus::ACTIVE)->count();
    }

    public function getTotalTrialSubscriptions(): int
    {
        return UserSubscription::where('status', SubscriptionStatus::TRIALING)->count();
    }

    public function getTotalExpiredSubscriptions(): int
    {
        return UserSubscription::where('status', SubscriptionStatus::EXPIRED)->count();
    }

    public function getTotalCancelledSubscriptions(): int
    {
        return UserSubscription::where('status', SubscriptionStatus::CANCELLED)->count();
    }

    public function getTotalPausedSubscriptions(): int
    {
        // Note: You might want to add a 'paused' status to the enum
        return 0; // For now, returning 0 as we don't have a paused status
    }

    public function search(int $userId, string $query): Collection
    {
        return UserSubscription::where('user_id', $userId)
            ->whereHas('subscription', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function searchDTO(int $userId, string $query): Collection
    {
        $userSubscriptions = $this->search($userId, $query);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function getUpcomingRenewals(int $days = 7): Collection
    {
        $date = now()->addDays($days)->format('Y-m-d');
        return UserSubscription::where('next_billing_date', '<=', $date)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function getUpcomingRenewalsDTO(int $days = 7): Collection
    {
        $userSubscriptions = $this->getUpcomingRenewals($days);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function getExpiringTrials(int $days = 3): Collection
    {
        $date = now()->addDays($days)->format('Y-m-d');
        return UserSubscription::where('end_date', '<=', $date)
            ->where('status', SubscriptionStatus::TRIALING)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function getExpiringTrialsDTO(int $days = 3): Collection
    {
        $userSubscriptions = $this->getExpiringTrials($days);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function getExpiringSubscriptions(int $days = 30): Collection
    {
        $date = now()->addDays($days)->format('Y-m-d');
        return UserSubscription::where('end_date', '<=', $date)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->with(['user', 'subscription', 'order'])
            ->get();
    }

    public function getExpiringSubscriptionsDTO(int $days = 30): Collection
    {
        $userSubscriptions = $this->getExpiringSubscriptions($days);
        return $userSubscriptions->map(fn($userSubscription) => UserSubscriptionDTO::fromModel($userSubscription));
    }

    public function validateUserSubscription(array $data): bool
    {
        $validator = validator($data, UserSubscriptionDTO::rules(), UserSubscriptionDTO::messages());
        return !$validator->fails();
    }

    public function calculateNextBillingDate(UserSubscription $userSubscription): string
    {
        $subscription = $userSubscription->subscription;
        $currentDate = $userSubscription->next_billing_date ?? $userSubscription->start_date;

        $start = Carbon::parse($currentDate);

        $nextBillingDate = match($subscription->billing_cycle) {
            BillingCycle::DAILY => $start->addDays($subscription->billing_interval),
            BillingCycle::WEEKLY => $start->addWeeks($subscription->billing_interval),
            BillingCycle::MONTHLY => $start->addMonths($subscription->billing_interval),
            BillingCycle::YEARLY => $start->addYears($subscription->billing_interval),
        };

        return $nextBillingDate->format('Y-m-d');
    }

    public function checkSubscriptionAvailability(int $subscriptionId): bool
    {
        return Subscription::where('id', $subscriptionId)->exists();
    }

    public function getUserSubscriptionRevenue(int $userId): float
    {
        return UserSubscription::join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->where('user_subscriptions.user_id', $userId)
            ->where('user_subscriptions.status', SubscriptionStatus::ACTIVE)
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getUserSubscriptionRevenueByDateRange(int $userId, string $startDate, string $endDate): float
    {
        return UserSubscription::join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->where('user_subscriptions.user_id', $userId)
            ->where('user_subscriptions.status', SubscriptionStatus::ACTIVE)
            ->whereBetween('user_subscriptions.created_at', [$startDate, $endDate])
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getTotalRevenue(): float
    {
        return UserSubscription::join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->where('user_subscriptions.status', SubscriptionStatus::ACTIVE)
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return UserSubscription::join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->where('user_subscriptions.status', SubscriptionStatus::ACTIVE)
            ->whereBetween('user_subscriptions.created_at', [$startDate, $endDate])
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getUserSubscriptionStatistics(int $userId): array
    {
        return [
            'total' => $this->getUserSubscriptionCount($userId),
            'active' => $this->getUserSubscriptionCountByStatus($userId, SubscriptionStatus::ACTIVE->value),
            'trial' => $this->getUserSubscriptionCountByStatus($userId, SubscriptionStatus::TRIALING->value),
            'expired' => $this->getUserSubscriptionCountByStatus($userId, SubscriptionStatus::EXPIRED->value),
            'cancelled' => $this->getUserSubscriptionCountByStatus($userId, SubscriptionStatus::CANCELLED->value),
            'revenue' => $this->getUserSubscriptionRevenue($userId),
        ];
    }

    public function getGlobalSubscriptionStatistics(): array
    {
        return [
            'total_active' => $this->getTotalActiveSubscriptions(),
            'total_trial' => $this->getTotalTrialSubscriptions(),
            'total_expired' => $this->getTotalExpiredSubscriptions(),
            'total_cancelled' => $this->getTotalCancelledSubscriptions(),
            'total_paused' => $this->getTotalPausedSubscriptions(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }

    public function getSubscriptionAnalytics(): array
    {
        return [
            'churn_rate' => $this->getChurnRate(),
            'retention_rate' => $this->getRetentionRate(),
            'statistics' => $this->getGlobalSubscriptionStatistics(),
        ];
    }

    public function getChurnRate(): float
    {
        $totalSubscriptions = UserSubscription::count();
        $cancelledSubscriptions = $this->getTotalCancelledSubscriptions();

        return $totalSubscriptions > 0 ? ($cancelledSubscriptions / $totalSubscriptions) * 100 : 0;
    }

    public function getRetentionRate(): float
    {
        return 100 - $this->getChurnRate();
    }
}
