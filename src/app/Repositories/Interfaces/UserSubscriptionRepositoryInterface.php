<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\DTOs\UserSubscriptionDTO;

interface UserSubscriptionRepositoryInterface
{
    /**
     * Get all user subscriptions
     */
    public function all(): Collection;

    /**
     * Get paginated user subscriptions
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated user subscriptions
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated user subscriptions
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    /**
     * Find user subscription by ID
     */
    public function find(int $id): ?UserSubscription;

    /**
     * Find user subscription by ID and return DTO
     */
    public function findDTO(int $id): ?UserSubscriptionDTO;

    /**
     * Find user subscriptions by user ID
     */
    public function findByUserId(int $userId): Collection;

    /**
     * Find user subscriptions by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection;

    /**
     * Find user subscriptions by subscription ID
     */
    public function findBySubscriptionId(int $subscriptionId): Collection;

    /**
     * Find user subscriptions by subscription ID and return DTOs
     */
    public function findBySubscriptionIdDTO(int $subscriptionId): Collection;

    /**
     * Find user subscriptions by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find user subscriptions by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find user subscription by user and subscription
     */
    public function findByUserAndSubscription(int $userId, int $subscriptionId): ?UserSubscription;

    /**
     * Find user subscription by user and subscription and return DTO
     */
    public function findByUserAndSubscriptionDTO(int $userId, int $subscriptionId): ?UserSubscriptionDTO;

    /**
     * Find active user subscriptions by user ID
     */
    public function findActiveByUserId(int $userId): Collection;

    /**
     * Find active user subscriptions by user ID and return DTOs
     */
    public function findActiveByUserIdDTO(int $userId): Collection;

    /**
     * Find expired user subscriptions by user ID
     */
    public function findExpiredByUserId(int $userId): Collection;

    /**
     * Find expired user subscriptions by user ID and return DTOs
     */
    public function findExpiredByUserIdDTO(int $userId): Collection;

    /**
     * Find trial user subscriptions by user ID
     */
    public function findTrialByUserId(int $userId): Collection;

    /**
     * Find trial user subscriptions by user ID and return DTOs
     */
    public function findTrialByUserIdDTO(int $userId): Collection;

    /**
     * Find cancelled user subscriptions by user ID
     */
    public function findCancelledByUserId(int $userId): Collection;

    /**
     * Find cancelled user subscriptions by user ID and return DTOs
     */
    public function findCancelledByUserIdDTO(int $userId): Collection;

    /**
     * Find user subscriptions by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find user subscriptions by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find user subscriptions by next billing date
     */
    public function findByNextBillingDate(string $date): Collection;

    /**
     * Find user subscriptions by next billing date and return DTOs
     */
    public function findByNextBillingDateDTO(string $date): Collection;

    /**
     * Create new user subscription
     */
    public function create(array $data): UserSubscription;

    /**
     * Create new user subscription and return DTO
     */
    public function createAndReturnDTO(array $data): UserSubscriptionDTO;

    /**
     * Update user subscription
     */
    public function update(UserSubscription $userSubscription, array $data): bool;

    /**
     * Update user subscription and return DTO
     */
    public function updateAndReturnDTO(UserSubscription $userSubscription, array $data): ?UserSubscriptionDTO;

    /**
     * Delete user subscription
     */
    public function delete(UserSubscription $userSubscription): bool;

    /**
     * Activate user subscription
     */
    public function activate(UserSubscription $userSubscription): bool;

    /**
     * Activate user subscription and return DTO
     */
    public function activateAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO;

    /**
     * Cancel user subscription
     */
    public function cancel(UserSubscription $userSubscription, string $reason = null): bool;

    /**
     * Cancel user subscription and return DTO
     */
    public function cancelAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO;

    /**
     * Expire user subscription
     */
    public function expire(UserSubscription $userSubscription): bool;

    /**
     * Expire user subscription and return DTO
     */
    public function expireAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO;

    /**
     * Renew user subscription
     */
    public function renew(UserSubscription $userSubscription): bool;

    /**
     * Renew user subscription and return DTO
     */
    public function renewAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO;

    /**
     * Pause user subscription
     */
    public function pause(UserSubscription $userSubscription, string $reason = null): bool;

    /**
     * Pause user subscription and return DTO
     */
    public function pauseAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO;

    /**
     * Resume user subscription
     */
    public function resume(UserSubscription $userSubscription): bool;

    /**
     * Resume user subscription and return DTO
     */
    public function resumeAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO;

    /**
     * Get user subscription count
     */
    public function getUserSubscriptionCount(int $userId): int;

    /**
     * Get user subscription count by status
     */
    public function getUserSubscriptionCountByStatus(int $userId, string $status): int;

    /**
     * Get total active subscriptions
     */
    public function getTotalActiveSubscriptions(): int;

    /**
     * Get total trial subscriptions
     */
    public function getTotalTrialSubscriptions(): int;

    /**
     * Get total expired subscriptions
     */
    public function getTotalExpiredSubscriptions(): int;

    /**
     * Get total cancelled subscriptions
     */
    public function getTotalCancelledSubscriptions(): int;

    /**
     * Get total paused subscriptions
     */
    public function getTotalPausedSubscriptions(): int;

    /**
     * Search user subscriptions
     */
    public function search(int $userId, string $query): Collection;

    /**
     * Search user subscriptions and return DTOs
     */
    public function searchDTO(int $userId, string $query): Collection;

    /**
     * Get upcoming renewals
     */
    public function getUpcomingRenewals(int $days = 7): Collection;

    /**
     * Get upcoming renewals and return DTOs
     */
    public function getUpcomingRenewalsDTO(int $days = 7): Collection;

    /**
     * Get expiring trials
     */
    public function getExpiringTrials(int $days = 3): Collection;

    /**
     * Get expiring trials and return DTOs
     */
    public function getExpiringTrialsDTO(int $days = 3): Collection;

    /**
     * Get expiring subscriptions
     */
    public function getExpiringSubscriptions(int $days = 30): Collection;

    /**
     * Get expiring subscriptions and return DTOs
     */
    public function getExpiringSubscriptionsDTO(int $days = 30): Collection;

    /**
     * Validate user subscription data
     */
    public function validateUserSubscription(array $data): bool;

    /**
     * Calculate next billing date
     */
    public function calculateNextBillingDate(UserSubscription $userSubscription): string;

    /**
     * Check subscription availability
     */
    public function checkSubscriptionAvailability(int $subscriptionId): bool;

    /**
     * Get user subscription revenue
     */
    public function getUserSubscriptionRevenue(int $userId): float;

    /**
     * Get user subscription revenue by date range
     */
    public function getUserSubscriptionRevenueByDateRange(int $userId, string $startDate, string $endDate): float;

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float;

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float;

    /**
     * Get user subscription statistics
     */
    public function getUserSubscriptionStatistics(int $userId): array;

    /**
     * Get global subscription statistics
     */
    public function getGlobalSubscriptionStatistics(): array;

    /**
     * Get subscription analytics
     */
    public function getSubscriptionAnalytics(): array;

    /**
     * Get churn rate
     */
    public function getChurnRate(): float;

    /**
     * Get retention rate
     */
    public function getRetentionRate(): float;
}
