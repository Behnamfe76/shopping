<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\DTOs\SubscriptionDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

class SubscriptionService
{
    use HasCrudOperations,
        HasSearchOperations;

    protected SubscriptionRepositoryInterface $repository;
    protected string $dtoClass = SubscriptionDTO::class;

    public function __construct(SubscriptionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = new Subscription();
    }

    // Repository method delegation
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->repository->findByProductIdDTO($productId);
    }

    public function findByBillingCycle(string $billingCycle): Collection
    {
        return $this->repository->findByBillingCycle($billingCycle);
    }

    public function findByBillingCycleDTO(string $billingCycle): Collection
    {
        return $this->repository->findByBillingCycleDTO($billingCycle);
    }

    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->findByPriceRange($minPrice, $maxPrice);
    }

    public function findByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->findByPriceRangeDTO($minPrice, $maxPrice);
    }

    public function findByTrialPeriod(int $trialDays): Collection
    {
        return $this->repository->findByTrialPeriod($trialDays);
    }

    public function findByTrialPeriodDTO(int $trialDays): Collection
    {
        return $this->repository->findByTrialPeriodDTO($trialDays);
    }

    public function getSubscriptionCount(): int
    {
        return $this->repository->getSubscriptionCount();
    }

    public function getSubscriptionCountByProduct(int $productId): int
    {
        return $this->repository->getSubscriptionCountByProduct($productId);
    }

    public function getSubscriptionCountByBillingCycle(string $billingCycle): int
    {
        return $this->repository->getSubscriptionCountByBillingCycle($billingCycle);
    }

    public function getActiveSubscriptions(): Collection
    {
        return $this->repository->getActiveSubscriptions();
    }

    public function getActiveSubscriptionsDTO(): Collection
    {
        return $this->repository->getActiveSubscriptionsDTO();
    }

    public function getTrialSubscriptions(): Collection
    {
        return $this->repository->getTrialSubscriptions();
    }

    public function getTrialSubscriptionsDTO(): Collection
    {
        return $this->repository->getTrialSubscriptionsDTO();
    }

    public function validateSubscription(array $data): bool
    {
        return $this->repository->validateSubscription($data);
    }

    public function calculateNextBillingDate(Subscription $subscription, string $startDate = null): string
    {
        return $this->repository->calculateNextBillingDate($subscription, $startDate);
    }

    public function getSubscriptionRevenue(): float
    {
        return $this->repository->getSubscriptionRevenue();
    }

    public function getSubscriptionRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getSubscriptionRevenueByDateRange($startDate, $endDate);
    }

    public function getPopularSubscriptions(int $limit = 10): Collection
    {
        return $this->repository->getPopularSubscriptions($limit);
    }

    public function getPopularSubscriptionsDTO(int $limit = 10): Collection
    {
        return $this->repository->getPopularSubscriptionsDTO($limit);
    }

    // Business logic methods
    public function createSubscription(array $data): SubscriptionDTO
    {
        if (!$this->validateSubscription($data)) {
            throw new \InvalidArgumentException('Invalid subscription data');
        }

        return $this->repository->createAndReturnDTO($data);
    }

    public function updateSubscription(int $id, array $data): ?SubscriptionDTO
    {
        $subscription = $this->repository->find($id);

        if (!$subscription) {
            throw new \InvalidArgumentException('Subscription not found');
        }

        if (!$this->validateSubscription($data)) {
            throw new \InvalidArgumentException('Invalid subscription data');
        }

        return $this->repository->updateAndReturnDTO($subscription, $data);
    }

    public function deleteSubscription(int $id): bool
    {
        $subscription = $this->repository->find($id);

        if (!$subscription) {
            throw new \InvalidArgumentException('Subscription not found');
        }

        return $this->repository->delete($subscription);
    }

    public function getSubscriptionsByProduct(int $productId): Collection
    {
        return $this->repository->findByProductIdDTO($productId);
    }

    public function getSubscriptionsByBillingCycle(string $billingCycle): Collection
    {
        return $this->repository->findByBillingCycleDTO($billingCycle);
    }

    public function getSubscriptionsByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->findByPriceRangeDTO($minPrice, $maxPrice);
    }

    public function getSubscriptionsWithTrial(): Collection
    {
        return $this->repository->getTrialSubscriptionsDTO();
    }

    public function searchSubscriptions(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    public function getSubscriptionStatistics(): array
    {
        return [
            'total' => $this->getSubscriptionCount(),
            'active' => $this->getActiveSubscriptions()->count(),
            'trial' => $this->getTrialSubscriptions()->count(),
            'revenue' => $this->getSubscriptionRevenue(),
            'popular' => $this->getPopularSubscriptionsDTO(5),
        ];
    }
}
