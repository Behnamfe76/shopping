<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\ProductDiscountDTO;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasDateRangeManagement;
use Fereydooni\Shopping\app\Traits\HasFinancialOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductDiscountService
{
    use HasAnalyticsOperations, HasCrudOperations, HasDateRangeManagement, HasFinancialOperations, HasStatusToggle;

    public function __construct(
        private ProductDiscountRepositoryInterface $repository
    ) {}

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?ProductDiscount
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductDiscountDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductDiscount
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductDiscountDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductDiscount $discount, array $data): bool
    {
        return $this->repository->update($discount, $data);
    }

    public function delete(ProductDiscount $discount): bool
    {
        return $this->repository->delete($discount);
    }

    // Product-specific queries
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function findActiveByProductId(int $productId): Collection
    {
        return $this->repository->findActiveByProductId($productId);
    }

    // Discount type queries
    public function findByDiscountType(string $discountType): Collection
    {
        return $this->repository->findByDiscountType($discountType);
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findExpired(): Collection
    {
        return $this->repository->findExpired();
    }

    public function findUpcoming(): Collection
    {
        return $this->repository->findUpcoming();
    }

    public function findCurrent(): Collection
    {
        return $this->repository->findCurrent();
    }

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    // Status management
    public function toggleActive(ProductDiscount $discount): bool
    {
        return $this->repository->toggleActive($discount);
    }

    public function extend(ProductDiscount $discount, string $newEndDate): bool
    {
        return $this->repository->extend($discount, $newEndDate);
    }

    public function shorten(ProductDiscount $discount, string $newEndDate): bool
    {
        return $this->repository->shorten($discount, $newEndDate);
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    // Discount application and calculation
    public function getBestDiscount(int $productId, float $quantity = 1, float $amount = 0): ?ProductDiscount
    {
        return $this->repository->getBestDiscount($productId, $quantity, $amount);
    }

    public function getApplicableDiscounts(int $productId, float $quantity = 1, float $amount = 0): Collection
    {
        return $this->repository->getApplicableDiscounts($productId, $quantity, $amount);
    }

    public function calculateDiscount(ProductDiscount $discount, float $originalPrice, float $quantity = 1): float
    {
        return $this->repository->calculateDiscount($discount, $originalPrice, $quantity);
    }

    public function validateDiscount(ProductDiscount $discount, float $quantity = 1, float $amount = 0): bool
    {
        return $this->repository->validateDiscount($discount, $quantity, $amount);
    }

    // Usage tracking
    public function incrementUsage(ProductDiscount $discount): bool
    {
        return $this->repository->incrementUsage($discount);
    }

    // Analytics and reporting
    public function getDiscountAnalytics(int $discountId): array
    {
        return $this->repository->getDiscountAnalytics($discountId);
    }

    public function getDiscountRevenue(int $discountId): float
    {
        return $this->repository->getDiscountRevenue($discountId);
    }

    public function getDiscountSavings(int $discountId): float
    {
        return $this->repository->getDiscountSavings($discountId);
    }

    // Additional business logic methods
    public function applyDiscountToProduct(int $productId, float $originalPrice, float $quantity = 1): array
    {
        $bestDiscount = $this->getBestDiscount($productId, $quantity, $originalPrice * $quantity);

        if (! $bestDiscount) {
            return [
                'discount_applied' => false,
                'original_price' => $originalPrice,
                'final_price' => $originalPrice,
                'discount_amount' => 0,
                'savings_percentage' => 0,
            ];
        }

        $discountAmount = $this->calculateDiscount($bestDiscount, $originalPrice, $quantity);
        $finalPrice = ($originalPrice * $quantity) - $discountAmount;
        $savingsPercentage = $originalPrice > 0 ? ($discountAmount / ($originalPrice * $quantity)) * 100 : 0;

        return [
            'discount_applied' => true,
            'discount_id' => $bestDiscount->id,
            'discount_type' => $bestDiscount->discount_type->value,
            'original_price' => $originalPrice,
            'final_price' => round($finalPrice, 2),
            'discount_amount' => $discountAmount,
            'savings_percentage' => round($savingsPercentage, 2),
        ];
    }

    public function getDiscountRecommendations(int $productId): array
    {
        $currentDiscounts = $this->findActiveByProductId($productId);
        $expiredDiscounts = $this->findByProductId($productId)->filter->isExpired();

        return [
            'current_discounts' => $currentDiscounts->count(),
            'expired_discounts' => $expiredDiscounts->count(),
            'recommendations' => [
                'create_new_discount' => $currentDiscounts->isEmpty(),
                'extend_expiring_discounts' => $currentDiscounts->filter(fn ($d) => $d->end_date->diffInDays(now()) <= 7)->count(),
                'cleanup_expired_discounts' => $expiredDiscounts->count(),
            ],
        ];
    }
}
