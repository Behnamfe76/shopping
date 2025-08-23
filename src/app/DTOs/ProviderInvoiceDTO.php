<?php

namespace Fereydooni\Shopping\App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Fereydooni\Shopping\App\Enums\PaymentTerms;
use Carbon\Carbon;

class ProviderInvoiceDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id = null,

        #[Required, IntegerType]
        public int $provider_id,

        #[Required, StringType, Max(50)]
        public string $invoice_number,

        #[Required, Date]
        public string $invoice_date,

        #[Required, Date, AfterOrEqual('invoice_date')]
        public string $due_date,

        #[Required, Numeric, Min(0)]
        public float $total_amount,

        #[Required, Numeric, Min(0)]
        public float $subtotal,

        #[Required, Numeric, Min(0)]
        public float $tax_amount,

        #[Required, Numeric, Min(0)]
        public float $discount_amount,

        #[Required, Numeric, Min(0)]
        public float $shipping_amount,

        #[Required, StringType, Max(3)]
        public string $currency,

        #[Required, In(InvoiceStatus::class)]
        public string $status,

        #[Required, In(PaymentTerms::class)]
        public string $payment_terms,

        #[Nullable, StringType, Max(100)]
        public ?string $payment_method = null,

        #[Nullable, StringType, Max(100)]
        public ?string $reference_number = null,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes = null,

        #[Nullable]
        public ?array $attachments = null,

        #[Nullable, Date]
        public ?string $sent_at = null,

        #[Nullable, Date]
        public ?string $paid_at = null,

        #[Nullable, Date]
        public ?string $overdue_notice_sent = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $created_at = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $updated_at = null,

        // Computed properties
        public ?float $total_paid = null,
        public ?float $remaining_balance = null,
        public ?bool $is_overdue = null,
        public ?bool $is_fully_paid = null,
        public ?int $days_overdue = null,
        public ?int $days_until_due = null,
    ) {}

    /**
     * Create DTO from model
     */
    public static function fromModel(ProviderInvoice $invoice): static
    {
        return new static(
            id: $invoice->id,
            provider_id: $invoice->provider_id,
            invoice_number: $invoice->invoice_number,
            invoice_date: $invoice->invoice_date->format('Y-m-d'),
            due_date: $invoice->due_date->format('Y-m-d'),
            total_amount: $invoice->total_amount,
            subtotal: $invoice->subtotal ?? 0,
            tax_amount: $invoice->tax_amount ?? 0,
            discount_amount: $invoice->discount_amount ?? 0,
            shipping_amount: $invoice->shipping_amount ?? 0,
            currency: $invoice->currency ?? 'USD',
            status: $invoice->status,
            payment_terms: $invoice->payment_terms,
            payment_method: $invoice->payment_method,
            reference_number: $invoice->reference_number,
            notes: $invoice->notes,
            attachments: $invoice->attachments,
            sent_at: $invoice->sent_at?->format('Y-m-d'),
            paid_at: $invoice->paid_at?->format('Y-m-d'),
            overdue_notice_sent: $invoice->overdue_notice_sent?->format('Y-m-d'),
            created_at: $invoice->created_at,
            updated_at: $invoice->updated_at,
            total_paid: $invoice->total_paid ?? 0,
            remaining_balance: $invoice->remaining_balance ?? 0,
            is_overdue: $invoice->isOverdue(),
            is_fully_paid: $invoice->isFullyPaid(),
            days_overdue: $invoice->isOverdue() ? $invoice->due_date->diffInDays(now()) : 0,
            days_until_due: $invoice->due_date->isFuture() ? $invoice->due_date->diffInDays(now()) : 0,
        );
    }

    /**
     * Get validation rules
     */
    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:provider_invoices,invoice_number'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'shipping_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'status' => ['required', 'string', 'in:' . implode(',', InvoiceStatus::all())],
            'payment_terms' => ['required', 'string', 'in:' . implode(',', PaymentTerms::all())],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],
            'sent_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'overdue_notice_sent' => ['nullable', 'date'],
        ];
    }

    /**
     * Get validation messages
     */
    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'invoice_number.required' => 'Invoice number is required.',
            'invoice_number.unique' => 'Invoice number must be unique.',
            'invoice_date.required' => 'Invoice date is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be after or equal to invoice date.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be greater than or equal to 0.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.min' => 'Subtotal must be greater than or equal to 0.',
            'tax_amount.required' => 'Tax amount is required.',
            'tax_amount.min' => 'Tax amount must be greater than or equal to 0.',
            'discount_amount.required' => 'Discount amount is required.',
            'discount_amount.min' => 'Discount amount must be greater than or equal to 0.',
            'shipping_amount.required' => 'Shipping amount is required.',
            'shipping_amount.min' => 'Shipping amount must be greater than or equal to 0.',
            'currency.required' => 'Currency is required.',
            'currency.max' => 'Currency must not exceed 3 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'payment_terms.required' => 'Payment terms are required.',
            'payment_terms.in' => 'Invalid payment terms selected.',
        ];
    }

    /**
     * Calculate total amount from components
     */
    public function calculateTotalAmount(): float
    {
        return $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->is_fully_paid) {
            return false;
        }

        $dueDate = Carbon::parse($this->due_date);
        return $dueDate->isPast();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $dueDate = Carbon::parse($this->due_date);
        return $dueDate->diffInDays(now());
    }

    /**
     * Get days until due
     */
    public function getDaysUntilDue(): int
    {
        $dueDate = Carbon::parse($this->due_date);

        if ($dueDate->isPast()) {
            return 0;
        }

        return $dueDate->diffInDays(now());
    }

    /**
     * Check if invoice can be edited
     */
    public function canBeEdited(): bool
    {
        $status = InvoiceStatus::from($this->status);
        return $status->isEditable();
    }

    /**
     * Check if invoice can be sent
     */
    public function canBeSent(): bool
    {
        return $this->status === InvoiceStatus::DRAFT->value;
    }

    /**
     * Check if invoice can be marked as paid
     */
    public function canBeMarkedAsPaid(): bool
    {
        return in_array($this->status, [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ]);
    }

    /**
     * Check if invoice can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            InvoiceStatus::DRAFT->value,
            InvoiceStatus::SENT->value
        ]);
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return InvoiceStatus::from($this->status)->label();
    }

    /**
     * Get payment terms label
     */
    public function getPaymentTermsLabel(): string
    {
        return PaymentTerms::from($this->payment_terms)->label();
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        return InvoiceStatus::from($this->status)->color();
    }
}
