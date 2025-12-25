<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\SubscriptionStatus;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UserSubscriptionDTO extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $user_id,

        #[Required, IntegerType, Exists('subscriptions', 'id')]
        public int $subscription_id,

        #[Nullable, IntegerType, Exists('orders', 'id')]
        public ?int $order_id,

        #[Required, Date]
        public string $start_date,

        #[Nullable, Date]
        public ?string $end_date,

        #[Required, StringType, In(['active', 'cancelled', 'expired', 'trialing'])]
        public string $status,

        #[Nullable, Date]
        public ?string $next_billing_date = null,

        #[Nullable]
        public ?Carbon $created_at = null,

        #[Nullable]
        public ?Carbon $updated_at = null,

        #[Nullable]
        public ?int $id = null,

        // Relationship data
        #[Nullable]
        public ?User $user = null,

        #[Nullable]
        public ?SubscriptionDTO $subscription = null,

        #[Nullable]
        public ?OrderDTO $order = null,

        // Calculated fields
        #[Nullable]
        public ?bool $is_active = null,

        #[Nullable]
        public ?bool $is_trial = null,

        #[Nullable]
        public ?bool $is_expired = null,

        #[Nullable]
        public ?bool $is_cancelled = null,

        #[Nullable]
        public ?int $days_remaining = null,

        #[Nullable]
        public ?int $days_until_next_billing = null,
    ) {}

    public static function fromModel(UserSubscription $userSubscription): self
    {
        return new self(
            user_id: $userSubscription->user_id,
            subscription_id: $userSubscription->subscription_id,
            order_id: $userSubscription->order_id,
            start_date: $userSubscription->start_date->format('Y-m-d'),
            end_date: $userSubscription->end_date?->format('Y-m-d'),
            status: $userSubscription->status->value,
            next_billing_date: $userSubscription->next_billing_date?->format('Y-m-d'),
            created_at: $userSubscription->created_at,
            updated_at: $userSubscription->updated_at,
            id: $userSubscription->id,
            user: $userSubscription->user,
            subscription: $userSubscription->subscription ? SubscriptionDTO::fromModel($userSubscription->subscription) : null,
            order: $userSubscription->order ? OrderDTO::fromModel($userSubscription->order) : null,
            is_active: self::calculateIsActive($userSubscription),
            is_trial: self::calculateIsTrial($userSubscription),
            is_expired: self::calculateIsExpired($userSubscription),
            is_cancelled: self::calculateIsCancelled($userSubscription),
            days_remaining: self::calculateDaysRemaining($userSubscription),
            days_until_next_billing: self::calculateDaysUntilNextBilling($userSubscription),
        );
    }

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:active,cancelled,expired,trialing'],
            'next_billing_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.integer' => 'User ID must be a valid integer.',
            'subscription_id.required' => 'Subscription is required.',
            'subscription_id.exists' => 'Selected subscription does not exist.',
            'order_id.integer' => 'Order ID must be a valid integer.',
            'order_id.exists' => 'Selected order does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'next_billing_date.date' => 'Next billing date must be a valid date.',
            'next_billing_date.after_or_equal' => 'Next billing date must be after or equal to start date.',
        ];
    }

    private static function calculateIsActive(UserSubscription $userSubscription): bool
    {
        return $userSubscription->status === SubscriptionStatus::ACTIVE &&
               (! $userSubscription->end_date || $userSubscription->end_date->isFuture());
    }

    private static function calculateIsTrial(UserSubscription $userSubscription): bool
    {
        return $userSubscription->status === SubscriptionStatus::TRIALING;
    }

    private static function calculateIsExpired(UserSubscription $userSubscription): bool
    {
        return $userSubscription->status === SubscriptionStatus::EXPIRED ||
               ($userSubscription->end_date && $userSubscription->end_date->isPast());
    }

    private static function calculateIsCancelled(UserSubscription $userSubscription): bool
    {
        return $userSubscription->status === SubscriptionStatus::CANCELLED;
    }

    private static function calculateDaysRemaining(UserSubscription $userSubscription): ?int
    {
        if (! $userSubscription->end_date) {
            return null;
        }

        return max(0, now()->diffInDays($userSubscription->end_date, false));
    }

    private static function calculateDaysUntilNextBilling(UserSubscription $userSubscription): ?int
    {
        if (! $userSubscription->next_billing_date) {
            return null;
        }

        return max(0, now()->diffInDays($userSubscription->next_billing_date, false));
    }

    public function getStatusLabel(): string
    {
        return SubscriptionStatus::from($this->status)->label();
    }

    public function getFormattedStartDate(): string
    {
        return Carbon::parse($this->start_date)->format('M d, Y');
    }

    public function getFormattedEndDate(): ?string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('M d, Y') : null;
    }

    public function getFormattedNextBillingDate(): ?string
    {
        return $this->next_billing_date ? Carbon::parse($this->next_billing_date)->format('M d, Y') : null;
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'trialing' => 'blue',
            'cancelled' => 'red',
            'expired' => 'gray',
            default => 'gray',
        };
    }
}
