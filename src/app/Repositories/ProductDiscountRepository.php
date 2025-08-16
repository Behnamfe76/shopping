<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Fereydooni\Shopping\app\DTOs\ProductDiscountDTO;
use Carbon\Carbon;

class ProductDiscountRepository implements ProductDiscountRepositoryInterface
{
    public function all(): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?ProductDiscount
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])->find($id);
    }

    public function findDTO(int $id): ?ProductDiscountDTO
    {
        $discount = $this->find($id);
        return $discount ? ProductDiscountDTO::fromModel($discount) : null;
    }

    public function create(array $data): ProductDiscount
    {
        return ProductDiscount::create($data);
    }

    public function createAndReturnDTO(array $data): ProductDiscountDTO
    {
        $discount = $this->create($data);
        return ProductDiscountDTO::fromModel($discount);
    }

    public function update(ProductDiscount $discount, array $data): bool
    {
        return $discount->update($data);
    }

    public function delete(ProductDiscount $discount): bool
    {
        return $discount->delete();
    }

    public function findByProductId(int $productId): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->byProduct($productId)
            ->orderBy('priority', 'asc')
            ->get();
    }

    public function findActiveByProductId(int $productId): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->byProduct($productId)
            ->active()
            ->orderBy('priority', 'asc')
            ->get();
    }

    public function findByDiscountType(string $discountType): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->byType($discountType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActive(): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->active()
            ->orderBy('priority', 'asc')
            ->get();
    }

    public function findExpired(): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->expired()
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function findUpcoming(): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function findCurrent(): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->current()
            ->orderBy('priority', 'asc')
            ->get();
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->byDateRange($startDate, $endDate)
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function toggleActive(ProductDiscount $discount): bool
    {
        return $discount->update(['is_active' => !$discount->is_active]);
    }

    public function extend(ProductDiscount $discount, string $newEndDate): bool
    {
        $newEndDate = Carbon::parse($newEndDate);

        if ($newEndDate->lte($discount->end_date)) {
            return false;
        }

        return $discount->update(['end_date' => $newEndDate]);
    }

    public function shorten(ProductDiscount $discount, string $newEndDate): bool
    {
        $newEndDate = Carbon::parse($newEndDate);

        if ($newEndDate->gte($discount->end_date)) {
            return false;
        }

        return $discount->update(['end_date' => $newEndDate]);
    }

    public function search(string $query): Collection
    {
        return ProductDiscount::with(['product', 'createdBy', 'updatedBy'])
            ->where(function ($q) use ($query) {
                $q->where('description', 'like', "%{$query}%")
                  ->orWhereHas('product', function ($productQuery) use ($query) {
                      $productQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBestDiscount(int $productId, float $quantity = 1, float $amount = 0): ?ProductDiscount
    {
        $applicableDiscounts = $this->getApplicableDiscounts($productId, $quantity, $amount);

        if ($applicableDiscounts->isEmpty()) {
            return null;
        }

        // Return the discount with the highest priority (lowest number)
        return $applicableDiscounts->sortBy('priority')->first();
    }

    public function getApplicableDiscounts(int $productId, float $quantity = 1, float $amount = 0): Collection
    {
        $discounts = $this->findActiveByProductId($productId);

        return $discounts->filter(function ($discount) use ($quantity, $amount) {
            return $this->validateDiscount($discount, $quantity, $amount);
        });
    }

    public function calculateDiscount(ProductDiscount $discount, float $originalPrice, float $quantity = 1): float
    {
        return $discount->calculateDiscount($originalPrice, $quantity);
    }

    public function validateDiscount(ProductDiscount $discount, float $quantity = 1, float $amount = 0): bool
    {
        return $discount->canBeApplied($quantity, $amount);
    }

    public function incrementUsage(ProductDiscount $discount): bool
    {
        return $discount->incrementUsage();
    }

    public function getDiscountAnalytics(int $discountId): array
    {
        $discount = $this->find($discountId);

        if (!$discount) {
            return [];
        }

        return [
            'total_usage' => $discount->used_count,
            'usage_limit' => $discount->usage_limit,
            'usage_percentage' => $discount->usage_limit ? ($discount->used_count / $discount->usage_limit) * 100 : 0,
            'is_active' => $discount->isActive(),
            'is_expired' => $discount->isExpired(),
            'is_upcoming' => $discount->isUpcoming(),
            'days_remaining' => $discount->end_date->diffInDays(Carbon::now()),
            'created_at' => $discount->created_at,
            'updated_at' => $discount->updated_at,
        ];
    }

    public function getDiscountRevenue(int $discountId): float
    {
        // This would typically involve order calculations
        // For now, returning a placeholder
        return 0.0;
    }

    public function getDiscountSavings(int $discountId): float
    {
        // This would typically involve order calculations
        // For now, returning a placeholder
        return 0.0;
    }
}
