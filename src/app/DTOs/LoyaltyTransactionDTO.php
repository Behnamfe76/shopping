<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class LoyaltyTransactionDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType, Exists('customers', 'id')]
        public int $customer_id,

        #[Required, IntegerType, Exists('users', 'id')]
        public int $user_id,

        #[Required, StringType, In(['earned', 'redeemed', 'expired', 'reversed', 'bonus', 'adjustment'])]
        public LoyaltyTransactionType $transaction_type,

        #[Required, IntegerType, Min(1)]
        public int $points,

        #[Required, Numeric, Min(0)]
        public float $points_value,

        #[Required, StringType, In(['order', 'product', 'campaign', 'manual', 'system'])]
        public LoyaltyReferenceType $reference_type,

        #[Nullable, IntegerType]
        public ?int $reference_id,

        #[Nullable, StringType, Max(500)]
        public ?string $description,

        #[Nullable, StringType, Max(500)]
        public ?string $reason,

        #[Required, StringType, In(['pending', 'completed', 'failed', 'reversed', 'expired'])]
        public LoyaltyTransactionStatus $status,

        #[Nullable, Date]
        public ?Carbon $expires_at,

        #[Nullable, Date]
        public ?Carbon $reversed_at,

        #[Nullable, IntegerType, Exists('users', 'id')]
        public ?int $reversed_by,

        #[Nullable]
        public ?array $metadata,

        #[Nullable, Date]
        public ?Carbon $created_at,

        #[Nullable, Date]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?CustomerDTO $customer,

        #[Nullable]
        public ?array $user,

        #[Nullable]
        public ?array $reversed_by_user,

        #[Nullable]
        public ?array $reference,
    ) {}

    public static function fromModel(LoyaltyTransaction $transaction): self
    {
        return new self(
            id: $transaction->id,
            customer_id: $transaction->customer_id,
            user_id: $transaction->user_id,
            transaction_type: $transaction->transaction_type,
            points: $transaction->points,
            points_value: $transaction->points_value,
            reference_type: $transaction->reference_type,
            reference_id: $transaction->reference_id,
            description: $transaction->description,
            reason: $transaction->reason,
            status: $transaction->status,
            expires_at: $transaction->expires_at,
            reversed_at: $transaction->reversed_at,
            reversed_by: $transaction->reversed_by,
            metadata: $transaction->metadata,
            created_at: $transaction->created_at,
            updated_at: $transaction->updated_at,
            customer: $transaction->customer ? CustomerDTO::fromModel($transaction->customer) : null,
            user: $transaction->user ? [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ] : null,
            reversed_by_user: $transaction->reversedByUser ? [
                'id' => $transaction->reversedByUser->id,
                'name' => $transaction->reversedByUser->name,
                'email' => $transaction->reversedByUser->email,
            ] : null,
            reference: $transaction->reference ? [
                'type' => $transaction->reference_type->value,
                'id' => $transaction->reference_id,
                'data' => $transaction->reference,
            ] : null,
        );
    }

    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'transaction_type' => ['required', 'string', 'in:earned,redeemed,expired,reversed,bonus,adjustment'],
            'points' => ['required', 'integer', 'min:1'],
            'points_value' => ['required', 'numeric', 'min:0'],
            'reference_type' => ['required', 'string', 'in:order,product,campaign,manual,system'],
            'reference_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:500'],
            'reason' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:pending,completed,failed,reversed,expired'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'reversed_by' => ['nullable', 'integer', 'exists:users,id'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'transaction_type.required' => 'Transaction type is required.',
            'transaction_type.in' => 'Invalid transaction type.',
            'points.required' => 'Points are required.',
            'points.min' => 'Points must be at least 1.',
            'points_value.required' => 'Points value is required.',
            'points_value.min' => 'Points value must be at least 0.',
            'reference_type.required' => 'Reference type is required.',
            'reference_type.in' => 'Invalid reference type.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'expires_at.after' => 'Expiration date must be in the future.',
            'reversed_by.exists' => 'The selected user for reversal does not exist.',
        ];
    }

    // Helper methods
    public function isPositive(): bool
    {
        return $this->transaction_type->isPositive();
    }

    public function isNegative(): bool
    {
        return $this->transaction_type->isNegative();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isReversed(): bool
    {
        return $this->status === LoyaltyTransactionStatus::REVERSED;
    }

    public function isActive(): bool
    {
        return $this->status === LoyaltyTransactionStatus::COMPLETED && ! $this->isExpired();
    }

    public function getFormattedPoints(): string
    {
        return number_format($this->points);
    }

    public function getFormattedPointsValue(): string
    {
        return '$'.number_format($this->points_value, 2);
    }
}
