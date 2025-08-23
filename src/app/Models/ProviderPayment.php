<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Carbon\Carbon;

class ProviderPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'invoice_id',
        'payment_number',
        'payment_date',
        'amount',
        'currency',
        'payment_method',
        'reference_number',
        'transaction_id',
        'status',
        'notes',
        'attachments',
        'processed_by',
        'processed_at',
        'reconciled_at',
        'reconciliation_notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'payment_method' => ProviderPaymentMethod::class,
        'status' => ProviderPaymentStatus::class,
        'attachments' => 'array',
        'processed_at' => 'datetime',
        'reconciled_at' => 'datetime',
    ];

    protected $dates = [
        'payment_date',
        'processed_at',
        'reconciled_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the provider that owns the payment.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the invoice that this payment is for.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ProviderInvoice::class, 'invoice_id');
    }

    /**
     * Get the user who processed the payment.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get all notes for this payment.
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(ProviderNote::class, 'notable');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', ProviderPaymentStatus::PENDING);
    }

    /**
     * Scope a query to only include processed payments.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', ProviderPaymentStatus::PROCESSED);
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', ProviderPaymentStatus::COMPLETED);
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', ProviderPaymentStatus::FAILED);
    }

    /**
     * Scope a query to only include cancelled payments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', ProviderPaymentStatus::CANCELLED);
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', ProviderPaymentStatus::REFUNDED);
    }

    /**
     * Scope a query to only include unreconciled payments.
     */
    public function scopeUnreconciled($query)
    {
        return $query->whereNull('reconciled_at');
    }

    /**
     * Scope a query to only include reconciled payments.
     */
    public function scopeReconciled($query)
    {
        return $query->whereNotNull('reconciled_at');
    }

    /**
     * Scope a query to only include payments by method.
     */
    public function scopeByMethod($query, ProviderPaymentMethod $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to only include payments by currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to only include payments within a date range.
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include payments by amount range.
     */
    public function scopeByAmountRange($query, float $minAmount, float $maxAmount)
    {
        return $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }

    /**
     * Check if the payment can be edited.
     */
    public function canBeEdited(): bool
    {
        return $this->status->isEditable();
    }

    /**
     * Check if the payment can be processed.
     */
    public function canBeProcessed(): bool
    {
        return $this->status->isProcessable();
    }

    /**
     * Check if the payment can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status->isCompletable();
    }

    /**
     * Check if the payment can be reconciled.
     */
    public function canBeReconciled(): bool
    {
        return $this->status->isReconcilable();
    }

    /**
     * Check if the payment is reconciled.
     */
    public function isReconciled(): bool
    {
        return !is_null($this->reconciled_at);
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === ProviderPaymentStatus::PENDING;
    }

    /**
     * Check if the payment is processed.
     */
    public function isProcessed(): bool
    {
        return $this->status === ProviderPaymentStatus::PROCESSED;
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === ProviderPaymentStatus::COMPLETED;
    }

    /**
     * Check if the payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === ProviderPaymentStatus::FAILED;
    }

    /**
     * Check if the payment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === ProviderPaymentStatus::CANCELLED;
    }

    /**
     * Check if the payment is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === ProviderPaymentStatus::REFUNDED;
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get the payment status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Get the payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method->label();
    }

    /**
     * Get the payment status color.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    /**
     * Get the payment method icon.
     */
    public function getPaymentMethodIconAttribute(): string
    {
        return $this->payment_method->icon();
    }

    /**
     * Generate a unique payment number.
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix . $date . $random;
    }

    /**
     * Check if a payment number is unique.
     */
    public static function isPaymentNumberUnique(string $paymentNumber): bool
    {
        return !static::where('payment_number', $paymentNumber)->exists();
    }
}
