<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\GreaterThanOrEqualTo;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\Enums\BillingCycle;
use Illuminate\Support\Carbon;

class SubscriptionDTO extends Data
{
    public function __construct(
        #[Required, IntegerType, Exists('products', 'id')]
        public int $product_id,

        #[Required, StringType, Min(1), Max(255)]
        public string $name,

        #[Required, StringType, In(['daily', 'weekly', 'monthly', 'yearly'])]
        public string $billing_cycle,

        #[Required, IntegerType, Min(1), Max(365)]
        public int $billing_interval,

        #[Required, Numeric, Min(0), GreaterThanOrEqualTo(0)]
        public float $price,

        #[Nullable, IntegerType, Min(0), Max(365)]
        public ?int $trial_period_days = null,

        #[Nullable]
        public ?Carbon $created_at = null,

        #[Nullable]
        public ?Carbon $updated_at = null,

        #[Nullable]
        public ?int $id = null,

        // Calculated fields
        #[Nullable]
        public ?float $total_price = null,

        #[Nullable]
        public ?Carbon $next_billing_date = null,

        // Relationship data
        #[Nullable]
        public ?ProductDTO $product = null,

        #[Nullable]
        public ?int $user_subscriptions_count = null,
    ) {
    }

    public static function fromModel(Subscription $subscription): self
    {
        return new self(
            product_id: $subscription->product_id,
            name: $subscription->name,
            billing_cycle: $subscription->billing_cycle->value,
            billing_interval: $subscription->billing_interval,
            price: $subscription->price ?? 0.0,
            trial_period_days: $subscription->trial_period_days,
            created_at: $subscription->created_at,
            updated_at: $subscription->updated_at,
            id: $subscription->id,
            total_price: self::calculateTotalPrice($subscription),
            next_billing_date: self::calculateNextBillingDate($subscription),
            product: $subscription->product ? ProductDTO::fromModel($subscription->product) : null,
            user_subscriptions_count: $subscription->user_subscriptions_count ?? 0,
        );
    }

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'billing_cycle' => ['required', 'string', 'in:daily,weekly,monthly,yearly'],
            'billing_interval' => ['required', 'integer', 'min:1', 'max:365'],
            'price' => ['required', 'numeric', 'min:0'],
            'trial_period_days' => ['nullable', 'integer', 'min:0', 'max:365'],
        ];
    }

    public static function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'name.required' => 'Subscription name is required.',
            'name.min' => 'Subscription name must be at least 1 character.',
            'name.max' => 'Subscription name cannot exceed 255 characters.',
            'billing_cycle.required' => 'Billing cycle is required.',
            'billing_cycle.in' => 'Invalid billing cycle selected.',
            'billing_interval.required' => 'Billing interval is required.',
            'billing_interval.min' => 'Billing interval must be at least 1.',
            'billing_interval.max' => 'Billing interval cannot exceed 365.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'trial_period_days.integer' => 'Trial period days must be a whole number.',
            'trial_period_days.min' => 'Trial period days cannot be negative.',
            'trial_period_days.max' => 'Trial period days cannot exceed 365.',
        ];
    }

    private static function calculateTotalPrice(Subscription $subscription): float
    {
        return $subscription->price * $subscription->billing_interval;
    }

    private static function calculateNextBillingDate(Subscription $subscription, ?string $startDate = null): ?Carbon
    {
        if (!$startDate) {
            $startDate = now();
        }

        $start = Carbon::parse($startDate);

        return match($subscription->billing_cycle) {
            BillingCycle::DAILY => $start->addDays($subscription->billing_interval),
            BillingCycle::WEEKLY => $start->addWeeks($subscription->billing_interval),
            BillingCycle::MONTHLY => $start->addMonths($subscription->billing_interval),
            BillingCycle::YEARLY => $start->addYears($subscription->billing_interval),
        };
    }

    public function getBillingCycleLabel(): string
    {
        return BillingCycle::from($this->billing_cycle)->label();
    }

    public function hasTrialPeriod(): bool
    {
        return $this->trial_period_days !== null && $this->trial_period_days > 0;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    public function getFormattedTotalPrice(): string
    {
        return number_format($this->total_price ?? 0, 2);
    }
}
