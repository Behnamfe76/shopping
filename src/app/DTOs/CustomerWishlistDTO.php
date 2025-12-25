<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\WishlistPriority;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CustomerWishlistDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $customer_id,

        #[Required, IntegerType]
        public int $product_id,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes,

        #[Nullable]
        public ?WishlistPriority $priority,

        #[BooleanType]
        public bool $is_public,

        #[BooleanType]
        public bool $is_notified,

        #[Nullable, Date]
        public ?Carbon $notification_sent_at,

        #[Nullable, Numeric, Min(0)]
        public ?float $price_when_added,

        #[Nullable, Numeric, Min(0)]
        public ?float $current_price,

        #[BooleanType]
        public bool $price_drop_notification,

        #[Nullable]
        public ?Carbon $added_at,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?CustomerDTO $customer,

        #[Nullable]
        public ?ProductDTO $product,
    ) {}

    /**
     * Create DTO from CustomerWishlist model
     */
    public static function fromModel(CustomerWishlist $wishlist): self
    {
        return new self(
            id: $wishlist->id,
            customer_id: $wishlist->customer_id,
            product_id: $wishlist->product_id,
            notes: $wishlist->notes,
            priority: $wishlist->priority,
            is_public: $wishlist->is_public,
            is_notified: $wishlist->is_notified,
            notification_sent_at: $wishlist->notification_sent_at,
            price_when_added: $wishlist->price_when_added,
            current_price: $wishlist->current_price,
            price_drop_notification: $wishlist->price_drop_notification,
            added_at: $wishlist->added_at,
            created_at: $wishlist->created_at,
            updated_at: $wishlist->updated_at,
            customer: $wishlist->customer ? CustomerDTO::fromModel($wishlist->customer) : null,
            product: $wishlist->product ? ProductDTO::fromModel($wishlist->product) : null,
        );
    }

    /**
     * Get validation rules for creating a wishlist item
     */
    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'is_public' => ['boolean'],
            'is_notified' => ['boolean'],
            'notification_sent_at' => ['nullable', 'date'],
            'price_when_added' => ['nullable', 'numeric', 'min:0'],
            'current_price' => ['nullable', 'numeric', 'min:0'],
            'price_drop_notification' => ['boolean'],
            'added_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get validation messages
     */
    public static function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'priority.in' => 'Priority must be one of: low, medium, high, urgent.',
            'price_when_added.numeric' => 'Price when added must be a valid number.',
            'price_when_added.min' => 'Price when added cannot be negative.',
            'current_price.numeric' => 'Current price must be a valid number.',
            'current_price.min' => 'Current price cannot be negative.',
        ];
    }

    /**
     * Check if price has dropped
     */
    public function hasPriceDrop(): bool
    {
        if (! $this->price_when_added || ! $this->current_price) {
            return false;
        }

        return $this->current_price < $this->price_when_added;
    }

    /**
     * Get price drop percentage
     */
    public function getPriceDropPercentage(): ?float
    {
        if (! $this->hasPriceDrop()) {
            return null;
        }

        return round((($this->price_when_added - $this->current_price) / $this->price_when_added) * 100, 2);
    }

    /**
     * Get price drop amount
     */
    public function getPriceDropAmount(): ?float
    {
        if (! $this->hasPriceDrop()) {
            return null;
        }

        return round($this->price_when_added - $this->current_price, 2);
    }

    /**
     * Check if notification should be sent
     */
    public function shouldSendNotification(): bool
    {
        return $this->price_drop_notification &&
               $this->hasPriceDrop() &&
               ! $this->is_notified;
    }

    /**
     * Check if wishlist item is public
     */
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    /**
     * Check if wishlist item is private
     */
    public function isPrivate(): bool
    {
        return ! $this->is_public;
    }

    /**
     * Get priority level as integer for sorting
     */
    public function getPriorityLevel(): int
    {
        return match ($this->priority) {
            WishlistPriority::URGENT => 4,
            WishlistPriority::HIGH => 3,
            WishlistPriority::MEDIUM => 2,
            WishlistPriority::LOW => 1,
            default => 0,
        };
    }
}
