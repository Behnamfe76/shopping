<?php

namespace Fereydooni\Shopping\app\Models;

use Carbon\Carbon;
use Fereydooni\Shopping\app\Enums\DiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDiscount extends Model
{
    protected $fillable = [
        'product_id',
        'discount_type',
        'amount',
        'start_date',
        'end_date',
        'is_active',
        'minimum_quantity',
        'maximum_quantity',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'is_first_time_only',
        'is_cumulative',
        'priority',
        'description',
        'conditions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_type' => DiscountType::class,
        'is_active' => 'boolean',
        'minimum_quantity' => 'integer',
        'maximum_quantity' => 'integer',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_first_time_only' => 'boolean',
        'is_cumulative' => 'boolean',
        'priority' => 'integer',
        'conditions' => 'array',
    ];

    protected $attributes = [
        'is_active' => true,
        'used_count' => 0,
        'is_first_time_only' => false,
        'is_cumulative' => false,
        'priority' => 1,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now());
    }

    public function scopeExpired(Builder $query): void
    {
        $query->where('end_date', '<', Carbon::now());
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->where('start_date', '>', Carbon::now());
    }

    public function scopeCurrent(Builder $query): void
    {
        $query->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now());
    }

    public function scopeByProduct(Builder $query, int $productId): void
    {
        $query->where('product_id', $productId);
    }

    public function scopeByType(Builder $query, string $discountType): void
    {
        $query->where('discount_type', $discountType);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active &&
               $this->start_date->isPast() &&
               $this->end_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    public function isCurrent(): bool
    {
        return $this->isActive() && ! $this->isExpired() && ! $this->isUpcoming();
    }

    public function hasReachedUsageLimit(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    public function canBeApplied(float $quantity = 1, float $amount = 0): bool
    {
        if (! $this->isActive() || $this->hasReachedUsageLimit()) {
            return false;
        }

        if ($this->minimum_quantity !== null && $quantity < $this->minimum_quantity) {
            return false;
        }

        if ($this->maximum_quantity !== null && $quantity > $this->maximum_quantity) {
            return false;
        }

        if ($this->minimum_amount !== null && $amount < $this->minimum_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $originalPrice, float $quantity = 1): float
    {
        if (! $this->canBeApplied($quantity, $originalPrice * $quantity)) {
            return 0;
        }

        $discountAmount = match ($this->discount_type) {
            DiscountType::PERCENT => ($originalPrice * $this->amount / 100) * $quantity,
            DiscountType::FIXED => $this->amount * $quantity,
        };

        if ($this->maximum_discount !== null) {
            $discountAmount = min($discountAmount, $this->maximum_discount);
        }

        return round($discountAmount, 2);
    }

    public function incrementUsage(): bool
    {
        return $this->increment('used_count');
    }
}
