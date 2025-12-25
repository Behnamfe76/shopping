<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\SubscriptionDTO;
use Fereydooni\Shopping\app\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface SubscriptionRepositoryInterface
{
    /**
     * Get all subscriptions
     */
    public function all(): Collection;

    /**
     * Get paginated subscriptions
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated subscriptions
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated subscriptions
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find subscription by ID
     */
    public function find(int $id): ?Subscription;

    /**
     * Find subscription by ID and return DTO
     */
    public function findDTO(int $id): ?SubscriptionDTO;

    /**
     * Find subscriptions by product ID
     */
    public function findByProductId(int $productId): Collection;

    /**
     * Find subscriptions by product ID and return DTOs
     */
    public function findByProductIdDTO(int $productId): Collection;

    /**
     * Find subscriptions by billing cycle
     */
    public function findByBillingCycle(string $billingCycle): Collection;

    /**
     * Find subscriptions by billing cycle and return DTOs
     */
    public function findByBillingCycleDTO(string $billingCycle): Collection;

    /**
     * Find subscriptions by price range
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection;

    /**
     * Find subscriptions by price range and return DTOs
     */
    public function findByPriceRangeDTO(float $minPrice, float $maxPrice): Collection;

    /**
     * Find subscriptions by trial period
     */
    public function findByTrialPeriod(int $trialDays): Collection;

    /**
     * Find subscriptions by trial period and return DTOs
     */
    public function findByTrialPeriodDTO(int $trialDays): Collection;

    /**
     * Create new subscription
     */
    public function create(array $data): Subscription;

    /**
     * Create new subscription and return DTO
     */
    public function createAndReturnDTO(array $data): SubscriptionDTO;

    /**
     * Update subscription
     */
    public function update(Subscription $subscription, array $data): bool;

    /**
     * Update subscription and return DTO
     */
    public function updateAndReturnDTO(Subscription $subscription, array $data): ?SubscriptionDTO;

    /**
     * Delete subscription
     */
    public function delete(Subscription $subscription): bool;

    /**
     * Get total subscription count
     */
    public function getSubscriptionCount(): int;

    /**
     * Get subscription count by product
     */
    public function getSubscriptionCountByProduct(int $productId): int;

    /**
     * Get subscription count by billing cycle
     */
    public function getSubscriptionCountByBillingCycle(string $billingCycle): int;

    /**
     * Search subscriptions
     */
    public function search(string $query): Collection;

    /**
     * Search subscriptions and return DTOs
     */
    public function searchDTO(string $query): Collection;

    /**
     * Get active subscriptions
     */
    public function getActiveSubscriptions(): Collection;

    /**
     * Get active subscriptions and return DTOs
     */
    public function getActiveSubscriptionsDTO(): Collection;

    /**
     * Get trial subscriptions
     */
    public function getTrialSubscriptions(): Collection;

    /**
     * Get trial subscriptions and return DTOs
     */
    public function getTrialSubscriptionsDTO(): Collection;

    /**
     * Validate subscription data
     */
    public function validateSubscription(array $data): bool;

    /**
     * Calculate next billing date
     */
    public function calculateNextBillingDate(Subscription $subscription, ?string $startDate = null): string;

    /**
     * Get subscription revenue
     */
    public function getSubscriptionRevenue(): float;

    /**
     * Get subscription revenue by date range
     */
    public function getSubscriptionRevenueByDateRange(string $startDate, string $endDate): float;

    /**
     * Get popular subscriptions
     */
    public function getPopularSubscriptions(int $limit = 10): Collection;

    /**
     * Get popular subscriptions and return DTOs
     */
    public function getPopularSubscriptionsDTO(int $limit = 10): Collection;
}
