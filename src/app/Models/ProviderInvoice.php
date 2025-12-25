<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_amount',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'currency',
        'status',
        'payment_terms',
        'payment_method',
        'reference_number',
        'notes',
        'attachments',
        'sent_at',
        'paid_at',
        'overdue_notice_sent',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'status' => 'string',
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'overdue_notice_sent' => 'datetime',
    ];

    protected $dates = [
        'invoice_date',
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the provider that owns the invoice.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(ProviderPayment::class, 'invoice_id');
    }

    /**
     * Get the total paid amount for this invoice.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get the remaining balance for this invoice.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }

    /**
     * Check if the invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && ! $this->isFullyPaid();
    }
}
