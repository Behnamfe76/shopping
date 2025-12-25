<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProductDiscountDTO;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductDiscountRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?ProductDiscount;

    public function findDTO(int $id): ?ProductDiscountDTO;

    public function create(array $data): ProductDiscount;

    public function createAndReturnDTO(array $data): ProductDiscountDTO;

    public function update(ProductDiscount $discount, array $data): bool;

    public function delete(ProductDiscount $discount): bool;

    // Product-specific queries
    public function findByProductId(int $productId): Collection;

    public function findActiveByProductId(int $productId): Collection;

    // Discount type queries
    public function findByDiscountType(string $discountType): Collection;

    // Status-based queries
    public function findActive(): Collection;

    public function findExpired(): Collection;

    public function findUpcoming(): Collection;

    public function findCurrent(): Collection;

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;

    // Status management
    public function toggleActive(ProductDiscount $discount): bool;

    public function extend(ProductDiscount $discount, string $newEndDate): bool;

    public function shorten(ProductDiscount $discount, string $newEndDate): bool;

    // Search functionality
    public function search(string $query): Collection;

    // Discount application and calculation
    public function getBestDiscount(int $productId, float $quantity = 1, float $amount = 0): ?ProductDiscount;

    public function getApplicableDiscounts(int $productId, float $quantity = 1, float $amount = 0): Collection;

    public function calculateDiscount(ProductDiscount $discount, float $originalPrice, float $quantity = 1): float;

    public function validateDiscount(ProductDiscount $discount, float $quantity = 1, float $amount = 0): bool;

    // Usage tracking
    public function incrementUsage(ProductDiscount $discount): bool;

    // Analytics and reporting
    public function getDiscountAnalytics(int $discountId): array;

    public function getDiscountRevenue(int $discountId): float;

    public function getDiscountSavings(int $discountId): float;
}
