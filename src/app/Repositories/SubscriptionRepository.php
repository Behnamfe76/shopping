<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\DTOs\SubscriptionDTO;
use Fereydooni\Shopping\app\Enums\BillingCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection
    {
        return Subscription::with(['product', 'userSubscriptions'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Subscription::with(['product', 'userSubscriptions'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return Subscription::with(['product', 'userSubscriptions'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return Subscription::with(['product', 'userSubscriptions'])->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Subscription
    {
        return Subscription::with(['product', 'userSubscriptions'])->find($id);
    }

    public function findDTO(int $id): ?SubscriptionDTO
    {
        $subscription = $this->find($id);
        return $subscription ? SubscriptionDTO::fromModel($subscription) : null;
    }

    public function findByProductId(int $productId): Collection
    {
        return Subscription::where('product_id', $productId)
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        $subscriptions = $this->findByProductId($productId);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function findByBillingCycle(string $billingCycle): Collection
    {
        return Subscription::where('billing_cycle', $billingCycle)
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function findByBillingCycleDTO(string $billingCycle): Collection
    {
        $subscriptions = $this->findByBillingCycle($billingCycle);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return Subscription::whereBetween('price', [$minPrice, $maxPrice])
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function findByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        $subscriptions = $this->findByPriceRange($minPrice, $maxPrice);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function findByTrialPeriod(int $trialDays): Collection
    {
        return Subscription::where('trial_period_days', $trialDays)
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function findByTrialPeriodDTO(int $trialDays): Collection
    {
        $subscriptions = $this->findByTrialPeriod($trialDays);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function createAndReturnDTO(array $data): SubscriptionDTO
    {
        $subscription = $this->create($data);
        return SubscriptionDTO::fromModel($subscription);
    }

    public function update(Subscription $subscription, array $data): bool
    {
        return $subscription->update($data);
    }

    public function updateAndReturnDTO(Subscription $subscription, array $data): ?SubscriptionDTO
    {
        $updated = $this->update($subscription, $data);
        return $updated ? SubscriptionDTO::fromModel($subscription->fresh()) : null;
    }

    public function delete(Subscription $subscription): bool
    {
        return $subscription->delete();
    }

    public function getSubscriptionCount(): int
    {
        return Subscription::count();
    }

    public function getSubscriptionCountByProduct(int $productId): int
    {
        return Subscription::where('product_id', $productId)->count();
    }

    public function getSubscriptionCountByBillingCycle(string $billingCycle): int
    {
        return Subscription::where('billing_cycle', $billingCycle)->count();
    }

    public function search(string $query): Collection
    {
        return Subscription::where('name', 'like', "%{$query}%")
            ->orWhereHas('product', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        $subscriptions = $this->search($query);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function getActiveSubscriptions(): Collection
    {
        return Subscription::whereHas('userSubscriptions', function ($query) {
            $query->where('status', 'active');
        })->with(['product', 'userSubscriptions'])->get();
    }

    public function getActiveSubscriptionsDTO(): Collection
    {
        $subscriptions = $this->getActiveSubscriptions();
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function getTrialSubscriptions(): Collection
    {
        return Subscription::where('trial_period_days', '>', 0)
            ->with(['product', 'userSubscriptions'])
            ->get();
    }

    public function getTrialSubscriptionsDTO(): Collection
    {
        $subscriptions = $this->getTrialSubscriptions();
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }

    public function validateSubscription(array $data): bool
    {
        $validator = validator($data, SubscriptionDTO::rules(), SubscriptionDTO::messages());
        return !$validator->fails();
    }

    public function calculateNextBillingDate(Subscription $subscription, string $startDate = null): string
    {
        if (!$startDate) {
            $startDate = now();
        }

        $start = Carbon::parse($startDate);

        $nextBillingDate = match($subscription->billing_cycle) {
            BillingCycle::DAILY => $start->addDays($subscription->billing_interval),
            BillingCycle::WEEKLY => $start->addWeeks($subscription->billing_interval),
            BillingCycle::MONTHLY => $start->addMonths($subscription->billing_interval),
            BillingCycle::YEARLY => $start->addYears($subscription->billing_interval),
        };

        return $nextBillingDate->format('Y-m-d');
    }

    public function getSubscriptionRevenue(): float
    {
        return Subscription::join('user_subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscription_id')
            ->where('user_subscriptions.status', 'active')
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getSubscriptionRevenueByDateRange(string $startDate, string $endDate): float
    {
        return Subscription::join('user_subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscription_id')
            ->where('user_subscriptions.status', 'active')
            ->whereBetween('user_subscriptions.created_at', [$startDate, $endDate])
            ->sum(DB::raw('subscriptions.price * subscriptions.billing_interval'));
    }

    public function getPopularSubscriptions(int $limit = 10): Collection
    {
        return Subscription::withCount('userSubscriptions')
            ->with(['product', 'userSubscriptions'])
            ->orderBy('user_subscriptions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPopularSubscriptionsDTO(int $limit = 10): Collection
    {
        $subscriptions = $this->getPopularSubscriptions($limit);
        return $subscriptions->map(fn($subscription) => SubscriptionDTO::fromModel($subscription));
    }
}
