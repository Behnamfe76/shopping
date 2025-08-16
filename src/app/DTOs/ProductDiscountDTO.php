<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\Before;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\Validation\Integer;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Fereydooni\Shopping\app\Enums\DiscountType;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Carbon\Carbon;

class ProductDiscountDTO extends Data
{
    public function __construct(
        #[Required, Integer, Min(1)]
        public int $product_id,
        
        #[Required, StringType, In(['percent', 'fixed'])]
        public DiscountType $discount_type,
        
        #[Required, Numeric, Min(0)]
        public float $amount,
        
        #[Required, Date]
        public Carbon $start_date,
        
        #[Required, Date, After('start_date')]
        public Carbon $end_date,
        
        #[Boolean]
        public bool $is_active = true,
        
        #[Nullable, Integer, Min(1)]
        public ?int $minimum_quantity = null,
        
        #[Nullable, Integer, Min(1)]
        public ?int $maximum_quantity = null,
        
        #[Nullable, Numeric, Min(0)]
        public ?float $minimum_amount = null,
        
        #[Nullable, Numeric, Min(0)]
        public ?float $maximum_discount = null,
        
        #[Nullable, Integer, Min(1)]
        public ?int $usage_limit = null,
        
        #[Integer, Min(0)]
        public int $used_count = 0,
        
        #[Boolean]
        public bool $is_first_time_only = false,
        
        #[Boolean]
        public bool $is_cumulative = false,
        
        #[Integer, Min(1), Max(100)]
        public int $priority = 1,
        
        #[Nullable, StringType, Max(1000)]
        public ?string $description = null,
        
        #[Nullable, Json]
        public ?array $conditions = null,
        
        #[Nullable, Integer]
        public ?int $created_by = null,
        
        #[Nullable, Integer]
        public ?int $updated_by = null,
        
        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $created_at = null,
        
        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $updated_at = null,
        
        public ?int $id = null,
    ) {
    }

    public static function fromModel(ProductDiscount $discount): self
    {
        return new self(
            product_id: $discount->product_id,
            discount_type: $discount->discount_type,
            amount: $discount->amount,
            start_date: $discount->start_date,
            end_date: $discount->end_date,
            is_active: $discount->is_active ?? true,
            minimum_quantity: $discount->minimum_quantity,
            maximum_quantity: $discount->maximum_quantity,
            minimum_amount: $discount->minimum_amount,
            maximum_discount: $discount->maximum_discount,
            usage_limit: $discount->usage_limit,
            used_count: $discount->used_count ?? 0,
            is_first_time_only: $discount->is_first_time_only ?? false,
            is_cumulative: $discount->is_cumulative ?? false,
            priority: $discount->priority ?? 1,
            description: $discount->description,
            conditions: $discount->conditions,
            created_by: $discount->created_by,
            updated_by: $discount->updated_by,
            created_at: $discount->created_at,
            updated_at: $discount->updated_at,
            id: $discount->id,
        );
    }

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'discount_type' => ['required', 'string', 'in:percent,fixed'],
            'amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
            'minimum_quantity' => ['nullable', 'integer', 'min:1'],
            'maximum_quantity' => ['nullable', 'integer', 'min:1'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'used_count' => ['integer', 'min:0'],
            'is_first_time_only' => ['boolean'],
            'is_cumulative' => ['boolean'],
            'priority' => ['integer', 'min:1', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'conditions' => ['nullable', 'array'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'discount_type.required' => 'Discount type is required.',
            'discount_type.in' => 'Discount type must be either percent or fixed.',
            'amount.required' => 'Discount amount is required.',
            'amount.min' => 'Discount amount must be at least 0.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'minimum_quantity.min' => 'Minimum quantity must be at least 1.',
            'maximum_quantity.min' => 'Maximum quantity must be at least 1.',
            'minimum_amount.min' => 'Minimum amount must be at least 0.',
            'maximum_discount.min' => 'Maximum discount must be at least 0.',
            'usage_limit.min' => 'Usage limit must be at least 1.',
            'used_count.min' => 'Used count must be at least 0.',
            'priority.min' => 'Priority must be at least 1.',
            'priority.max' => 'Priority cannot exceed 100.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ];
    }

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
        return $this->isActive() && !$this->isExpired() && !$this->isUpcoming();
    }

    public function hasReachedUsageLimit(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    public function canBeApplied(float $quantity = 1, float $amount = 0): bool
    {
        if (!$this->isActive() || $this->hasReachedUsageLimit()) {
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
        if (!$this->canBeApplied($quantity, $originalPrice * $quantity)) {
            return 0;
        }

        $discountAmount = match($this->discount_type) {
            DiscountType::PERCENT => ($originalPrice * $this->amount / 100) * $quantity,
            DiscountType::FIXED => $this->amount * $quantity,
        };

        if ($this->maximum_discount !== null) {
            $discountAmount = min($discountAmount, $this->maximum_discount);
        }

        return round($discountAmount, 2);
    }

    public function getSavings(float $originalPrice, float $quantity = 1): float
    {
        return $this->calculateDiscount($originalPrice, $quantity);
    }

    public function getFinalPrice(float $originalPrice, float $quantity = 1): float
    {
        $discount = $this->calculateDiscount($originalPrice, $quantity);
        return round(($originalPrice * $quantity) - $discount, 2);
    }
}
