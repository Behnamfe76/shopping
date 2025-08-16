<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\DTOs\UserSubscriptionDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

class UserSubscriptionService
{
    use HasCrudOperations,
        HasSearchOperations;

    protected UserSubscriptionRepositoryInterface $repository;
    protected string $dtoClass = UserSubscriptionDTO::class;

    public function __construct(UserSubscriptionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = new UserSubscription();
    }

    // Repository method delegation
    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function findBySubscriptionId(int $subscriptionId): Collection
    {
        return $this->repository->findBySubscriptionId($subscriptionId);
    }

    public function findBySubscriptionIdDTO(int $subscriptionId): Collection
    {
        return $this->repository->findBySubscriptionIdDTO($subscriptionId);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByUserAndSubscription(int $userId, int $subscriptionId): ?UserSubscription
    {
        return $this->repository->findByUserAndSubscription($userId, $subscriptionId);
    }

    public function findByUserAndSubscriptionDTO(int $userId, int $subscriptionId): ?UserSubscriptionDTO
    {
        return $this->repository->findByUserAndSubscriptionDTO($userId, $subscriptionId);
    }

    public function findActiveByUserId(int $userId): Collection
    {
        return $this->repository->findActiveByUserId($userId);
    }

    public function findActiveByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findActiveByUserIdDTO($userId);
    }

    public function findExpiredByUserId(int $userId): Collection
    {
        return $this->repository->findExpiredByUserId($userId);
    }

    public function findExpiredByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findExpiredByUserIdDTO($userId);
    }

    public function findTrialByUserId(int $userId): Collection
    {
        return $this->repository->findTrialByUserId($userId);
    }

    public function findTrialByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findTrialByUserIdDTO($userId);
    }

    public function findCancelledByUserId(int $userId): Collection
    {
        return $this->repository->findCancelledByUserId($userId);
    }

    public function findCancelledByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findCancelledByUserIdDTO($userId);
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    public function findByNextBillingDate(string $date): Collection
    {
        return $this->repository->findByNextBillingDate($date);
    }

    public function findByNextBillingDateDTO(string $date): Collection
    {
        return $this->repository->findByNextBillingDateDTO($date);
    }

    public function activate(UserSubscription $userSubscription): bool
    {
        return $this->repository->activate($userSubscription);
    }

    public function activateAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        return $this->repository->activateAndReturnDTO($userSubscription);
    }

    public function cancel(UserSubscription $userSubscription, string $reason = null): bool
    {
        return $this->repository->cancel($userSubscription, $reason);
    }

    public function cancelAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO
    {
        return $this->repository->cancelAndReturnDTO($userSubscription, $reason);
    }

    public function expire(UserSubscription $userSubscription): bool
    {
        return $this->repository->expire($userSubscription);
    }

    public function expireAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        return $this->repository->expireAndReturnDTO($userSubscription);
    }

    public function renew(UserSubscription $userSubscription): bool
    {
        return $this->repository->renew($userSubscription);
    }

    public function renewAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        return $this->repository->renewAndReturnDTO($userSubscription);
    }

    public function pause(UserSubscription $userSubscription, string $reason = null): bool
    {
        return $this->repository->pause($userSubscription, $reason);
    }

    public function pauseAndReturnDTO(UserSubscription $userSubscription, string $reason = null): ?UserSubscriptionDTO
    {
        return $this->repository->pauseAndReturnDTO($userSubscription, $reason);
    }

    public function resume(UserSubscription $userSubscription): bool
    {
        return $this->repository->resume($userSubscription);
    }

    public function resumeAndReturnDTO(UserSubscription $userSubscription): ?UserSubscriptionDTO
    {
        return $this->repository->resumeAndReturnDTO($userSubscription);
    }

    public function getUserSubscriptionCount(int $userId): int
    {
        return $this->repository->getUserSubscriptionCount($userId);
    }

    public function getUserSubscriptionCountByStatus(int $userId, string $status): int
    {
        return $this->repository->getUserSubscriptionCountByStatus($userId, $status);
    }

    public function getTotalActiveSubscriptions(): int
    {
        return $this->repository->getTotalActiveSubscriptions();
    }

    public function getTotalTrialSubscriptions(): int
    {
        return $this->repository->getTotalTrialSubscriptions();
    }

    public function getTotalExpiredSubscriptions(): int
    {
        return $this->repository->getTotalExpiredSubscriptions();
    }

    public function getTotalCancelledSubscriptions(): int
    {
        return $this->repository->getTotalCancelledSubscriptions();
    }

    public function getTotalPausedSubscriptions(): int
    {
        return $this->repository->getTotalPausedSubscriptions();
    }

    public function search(int $userId, string $query): Collection
    {
        return $this->repository->search($userId, $query);
    }

    public function searchDTO(int $userId, string $query): Collection
    {
        return $this->repository->searchDTO($userId, $query);
    }

    public function getUpcomingRenewals(int $days = 7): Collection
    {
        return $this->repository->getUpcomingRenewals($days);
    }

    public function getUpcomingRenewalsDTO(int $days = 7): Collection
    {
        return $this->repository->getUpcomingRenewalsDTO($days);
    }

    public function getExpiringTrials(int $days = 3): Collection
    {
        return $this->repository->getExpiringTrials($days);
    }

    public function getExpiringTrialsDTO(int $days = 3): Collection
    {
        return $this->repository->getExpiringTrialsDTO($days);
    }

    public function getExpiringSubscriptions(int $days = 30): Collection
    {
        return $this->repository->getExpiringSubscriptions($days);
    }

    public function getExpiringSubscriptionsDTO(int $days = 30): Collection
    {
        return $this->repository->getExpiringSubscriptionsDTO($days);
    }

    public function validateUserSubscription(array $data): bool
    {
        return $this->repository->validateUserSubscription($data);
    }

    public function calculateNextBillingDate(UserSubscription $userSubscription): string
    {
        return $this->repository->calculateNextBillingDate($userSubscription);
    }

    public function checkSubscriptionAvailability(int $subscriptionId): bool
    {
        return $this->repository->checkSubscriptionAvailability($subscriptionId);
    }

    public function getUserSubscriptionRevenue(int $userId): float
    {
        return $this->repository->getUserSubscriptionRevenue($userId);
    }

    public function getUserSubscriptionRevenueByDateRange(int $userId, string $startDate, string $endDate): float
    {
        return $this->repository->getUserSubscriptionRevenueByDateRange($userId, $startDate, $endDate);
    }

    public function getTotalRevenue(): float
    {
        return $this->repository->getTotalRevenue();
    }

    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getTotalRevenueByDateRange($startDate, $endDate);
    }

    public function getUserSubscriptionStatistics(int $userId): array
    {
        return $this->repository->getUserSubscriptionStatistics($userId);
    }

    public function getGlobalSubscriptionStatistics(): array
    {
        return $this->repository->getGlobalSubscriptionStatistics();
    }

    public function getSubscriptionAnalytics(): array
    {
        return $this->repository->getSubscriptionAnalytics();
    }

    public function getChurnRate(): float
    {
        return $this->repository->getChurnRate();
    }

    public function getRetentionRate(): float
    {
        return $this->repository->getRetentionRate();
    }

    // Business logic methods
    public function createUserSubscription(array $data): UserSubscriptionDTO
    {
        if (!$this->validateUserSubscription($data)) {
            throw new \InvalidArgumentException('Invalid user subscription data');
        }

        return $this->repository->createAndReturnDTO($data);
    }

    public function updateUserSubscription(int $id, array $data): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        if (!$this->validateUserSubscription($data)) {
            throw new \InvalidArgumentException('Invalid user subscription data');
        }

        return $this->repository->updateAndReturnDTO($userSubscription, $data);
    }

    public function deleteUserSubscription(int $id): bool
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->delete($userSubscription);
    }

    public function getUserSubscriptions(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function getUserActiveSubscriptions(int $userId): Collection
    {
        return $this->repository->findActiveByUserIdDTO($userId);
    }

    public function getUserTrialSubscriptions(int $userId): Collection
    {
        return $this->repository->findTrialByUserIdDTO($userId);
    }

    public function getUserExpiredSubscriptions(int $userId): Collection
    {
        return $this->repository->findExpiredByUserIdDTO($userId);
    }

    public function getUserCancelledSubscriptions(int $userId): Collection
    {
        return $this->repository->findCancelledByUserIdDTO($userId);
    }

    public function searchUserSubscriptions(int $userId, string $query): Collection
    {
        return $this->repository->searchDTO($userId, $query);
    }

    public function activateUserSubscription(int $id): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->activateAndReturnDTO($userSubscription);
    }

    public function cancelUserSubscription(int $id, string $reason = null): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->cancelAndReturnDTO($userSubscription, $reason);
    }

    public function renewUserSubscription(int $id): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->renewAndReturnDTO($userSubscription);
    }

    public function pauseUserSubscription(int $id, string $reason = null): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->pauseAndReturnDTO($userSubscription, $reason);
    }

    public function resumeUserSubscription(int $id): ?UserSubscriptionDTO
    {
        $userSubscription = $this->repository->find($id);

        if (!$userSubscription) {
            throw new \InvalidArgumentException('User subscription not found');
        }

        return $this->repository->resumeAndReturnDTO($userSubscription);
    }

    public function getUserSubscriptionDashboard(int $userId): array
    {
        return [
            'statistics' => $this->getUserSubscriptionStatistics($userId),
            'active_subscriptions' => $this->getUserActiveSubscriptions($userId),
            'trial_subscriptions' => $this->getUserTrialSubscriptions($userId),
            'expired_subscriptions' => $this->getUserExpiredSubscriptions($userId),
            'cancelled_subscriptions' => $this->getUserCancelledSubscriptions($userId),
            'upcoming_renewals' => $this->getUpcomingRenewalsDTO(7),
            'expiring_trials' => $this->getExpiringTrialsDTO(3),
        ];
    }

    public function getAdminDashboard(): array
    {
        return [
            'global_statistics' => $this->getGlobalSubscriptionStatistics(),
            'analytics' => $this->getSubscriptionAnalytics(),
            'upcoming_renewals' => $this->getUpcomingRenewalsDTO(7),
            'expiring_trials' => $this->getExpiringTrialsDTO(3),
            'expiring_subscriptions' => $this->getExpiringSubscriptionsDTO(30),
        ];
    }
}
