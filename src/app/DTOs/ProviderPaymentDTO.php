<?php

namespace Fereydooni\Shopping\App\DTOs;

use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Models\User;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProviderPaymentDTO extends Data
{
    public function __construct(
        #[IntegerType, Nullable]
        public ?int $id,

        #[IntegerType, Exists(Provider::class, 'id')]
        public int $provider_id,

        #[IntegerType, Exists(ProviderInvoice::class, 'id'), Nullable]
        public ?int $invoice_id,

        #[StringType, Min(5), Max(50)]
        public string $payment_number,

        #[Date]
        public string $payment_date,

        #[FloatType, Min(0.01)]
        public float $amount,

        #[StringType, Min(3), Max(3)]
        public string $currency,

        #[In(ProviderPaymentMethod::class)]
        public string $payment_method,

        #[StringType, Max(255), Nullable]
        public ?string $reference_number = null,

        #[StringType, Max(255), Nullable]
        public ?string $transaction_id = null,

        #[In(ProviderPaymentStatus::class)]
        public string $status = ProviderPaymentStatus::PENDING,

        #[StringType, Max(1000), Nullable]
        public ?string $notes = null,

        #[ArrayType, Nullable]
        public ?array $attachments = null,

        #[IntegerType, Exists(User::class, 'id'), Nullable]
        public ?int $processed_by = null,

        #[Date, Nullable]
        public ?string $processed_at = null,

        #[Date, Nullable]
        public ?string $reconciled_at = null,

        #[StringType, Max(1000), Nullable]
        public ?string $reconciliation_notes = null,

        #[Date, Nullable]
        public ?string $created_at = null,

        #[Date, Nullable]
        public ?string $updated_at = null,
    ) {}

    /**
     * Create DTO from ProviderPayment model.
     */
    public static function fromModel(ProviderPayment $payment): self
    {
        return new self(
            id: $payment->id,
            provider_id: $payment->provider_id,
            invoice_id: $payment->invoice_id,
            payment_number: $payment->payment_number,
            payment_date: $payment->payment_date->format('Y-m-d'),
            amount: $payment->amount,
            currency: $payment->currency,
            payment_method: $payment->payment_method->value,
            reference_number: $payment->reference_number,
            transaction_id: $payment->transaction_id,
            status: $payment->status->value,
            notes: $payment->notes,
            attachments: $payment->attachments,
            processed_by: $payment->processed_by,
            processed_at: $payment->processed_at?->format('Y-m-d'),
            reconciled_at: $payment->reconciled_at?->format('Y-m-d'),
            reconciliation_notes: $payment->reconciliation_notes,
            created_at: $payment->created_at->format('Y-m-d'),
            updated_at: $payment->updated_at->format('Y-m-d'),
        );
    }

    /**
     * Get validation rules for creating a payment.
     */
    public static function rules(): array
    {
        return [
            'provider_id' => 'required|integer|exists:providers,id',
            'invoice_id' => 'nullable|integer|exists:provider_invoices,id',
            'payment_number' => 'required|string|min:5|max:50|unique:provider_payments,payment_number',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|min:3|max:3',
            'payment_method' => 'required|string|in:'.implode(',', array_column(ProviderPaymentMethod::cases(), 'value')),
            'reference_number' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'status' => 'required|string|in:'.implode(',', array_column(ProviderPaymentStatus::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
            'processed_by' => 'nullable|integer|exists:users,id',
            'processed_at' => 'nullable|date',
            'reconciled_at' => 'nullable|date',
            'reconciliation_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get validation rules for updating a payment.
     */
    public static function updateRules(int $paymentId): array
    {
        $rules = self::rules();
        $rules['payment_number'] = 'required|string|min:5|max:50|unique:provider_payments,payment_number,'.$paymentId;

        return $rules;
    }

    /**
     * Get validation messages.
     */
    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'invoice_id.exists' => 'Selected invoice does not exist.',
            'payment_number.required' => 'Payment number is required.',
            'payment_number.unique' => 'Payment number must be unique.',
            'payment_number.min' => 'Payment number must be at least 5 characters.',
            'payment_number.max' => 'Payment number cannot exceed 50 characters.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
            'currency.required' => 'Currency is required.',
            'currency.min' => 'Currency must be 3 characters.',
            'currency.max' => 'Currency must be 3 characters.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method.',
            'reference_number.max' => 'Reference number cannot exceed 255 characters.',
            'transaction_id.max' => 'Transaction ID cannot exceed 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'attachments.array' => 'Attachments must be an array.',
            'processed_by.exists' => 'Selected processor does not exist.',
            'processed_at.date' => 'Processed date must be a valid date.',
            'reconciled_at.date' => 'Reconciled date must be a valid date.',
            'reconciliation_notes.max' => 'Reconciliation notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Convert DTO to array for database operations.
     */
    public function toArray(): array
    {
        return array_filter([
            'provider_id' => $this->provider_id,
            'invoice_id' => $this->invoice_id,
            'payment_number' => $this->payment_number,
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'reference_number' => $this->reference_number,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'notes' => $this->notes,
            'attachments' => $this->attachments,
            'processed_by' => $this->processed_by,
            'processed_at' => $this->processed_at,
            'reconciled_at' => $this->reconciled_at,
            'reconciliation_notes' => $this->reconciliation_notes,
        ], fn ($value) => ! is_null($value));
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency.' '.number_format($this->amount, 2);
    }

    /**
     * Check if the payment can be edited.
     */
    public function canBeEdited(): bool
    {
        $status = ProviderPaymentStatus::from($this->status);

        return $status->isEditable();
    }

    /**
     * Check if the payment can be processed.
     */
    public function canBeProcessed(): bool
    {
        $status = ProviderPaymentStatus::from($this->status);

        return $status->isProcessable();
    }

    /**
     * Check if the payment can be completed.
     */
    public function canBeCompleted(): bool
    {
        $status = ProviderPaymentStatus::from($this->status);

        return $status->isCompletable();
    }

    /**
     * Check if the payment can be reconciled.
     */
    public function canBeReconciled(): bool
    {
        $status = ProviderPaymentStatus::from($this->status);

        return $status->isReconcilable();
    }
}
