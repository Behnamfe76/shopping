<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\TransactionStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class TransactionDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $order_id,
        public int $user_id,
        public string $gateway,
        public string $transaction_id,
        public float $amount,
        public string $currency,
        public TransactionStatus $status,
        public ?Carbon $payment_date,
        public ?array $response_data,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $order = null,
        public ?array $user = null,
        public ?string $formatted_amount = null,
        public ?string $status_label = null,
        public ?string $gateway_name = null,
    ) {}

    public static function fromModel($transaction): static
    {
        return new static(
            id: $transaction->id,
            order_id: $transaction->order_id,
            user_id: $transaction->user_id,
            gateway: $transaction->gateway,
            transaction_id: $transaction->transaction_id,
            amount: $transaction->amount,
            currency: $transaction->currency,
            status: $transaction->status,
            payment_date: $transaction->payment_date,
            response_data: $transaction->response_data,
            created_at: $transaction->created_at,
            updated_at: $transaction->updated_at,
            order: $transaction->relationLoaded('order') ? $transaction->order->toArray() : null,
            user: $transaction->relationLoaded('user') ? $transaction->user->toArray() : null,
            formatted_amount: $transaction->amount ? number_format($transaction->amount, 2).' '.strtoupper($transaction->currency) : null,
            status_label: $transaction->status?->label(),
            gateway_name: ucfirst(str_replace('_', ' ', $transaction->gateway)),
        );
    }

    public static function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'user_id' => 'required|integer|exists:users,id',
            'gateway' => 'required|string|max:50',
            'transaction_id' => 'required|string|max:255|unique:transactions,transaction_id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:'.implode(',', array_column(TransactionStatus::cases(), 'value')),
            'payment_date' => 'nullable|date',
            'response_data' => 'nullable|array',
        ];
    }

    public static function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The selected order does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'gateway.required' => 'Payment gateway is required.',
            'gateway.max' => 'Gateway name cannot exceed 50 characters.',
            'transaction_id.required' => 'Transaction ID is required.',
            'transaction_id.unique' => 'This transaction ID already exists.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least 0.01.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid transaction status.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'response_data.array' => 'Response data must be an array.',
        ];
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2).' '.strtoupper($this->currency);
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function getGatewayName(): string
    {
        return ucfirst(str_replace('_', ' ', $this->gateway));
    }

    public function isSuccessful(): bool
    {
        return $this->status === TransactionStatus::SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === TransactionStatus::FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === TransactionStatus::REFUNDED;
    }

    public function isInitiated(): bool
    {
        return $this->status === TransactionStatus::INITIATED;
    }

    public function canBeRefunded(): bool
    {
        return $this->status === TransactionStatus::SUCCESS;
    }

    public function canBeProcessed(): bool
    {
        return $this->status === TransactionStatus::INITIATED;
    }
}
