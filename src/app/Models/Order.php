<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Fereydooni\Shopping\app\Enums\OrderStatus;
use Fereydooni\Shopping\app\Enums\PaymentStatus;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'discount_amount',
        'shipping_amount',
        'payment_status',
        'payment_method',
        'shipping_address_id',
        'billing_address_id',
        'placed_at',
        'notes',
        'tracking_number',
        'estimated_delivery',
        'actual_delivery',
        'tax_amount',
        'currency',
        'exchange_rate',
        'coupon_code',
        'coupon_discount',
        'subtotal',
        'grand_total',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'placed_at' => 'datetime',
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'exchange_rate' => 1.000000,
        'tax_amount' => 0,
        'coupon_discount' => 0,
        'subtotal' => 0,
        'grand_total' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', OrderStatus::PENDING);
    }

    /**
     * Scope a query to only include paid orders.
     */
    public function scopePaid(Builder $query): void
    {
        $query->where('status', OrderStatus::PAID);
    }

    /**
     * Scope a query to only include shipped orders.
     */
    public function scopeShipped(Builder $query): void
    {
        $query->where('status', OrderStatus::SHIPPED);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', OrderStatus::COMPLETED);
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled(Builder $query): void
    {
        $query->where('status', OrderStatus::CANCELLED);
    }

    /**
     * Scope a query to only include orders with unpaid payment status.
     */
    public function scopeUnpaid(Builder $query): void
    {
        $query->where('payment_status', PaymentStatus::UNPAID);
    }

    /**
     * Scope a query to only include orders with paid payment status.
     */
    public function scopePaymentPaid(Builder $query): void
    {
        $query->where('payment_status', PaymentStatus::PAID);
    }

    /**
     * Scope a query to only include orders with refunded payment status.
     */
    public function scopeRefunded(Builder $query): void
    {
        $query->where('payment_status', PaymentStatus::REFUNDED);
    }

    /**
     * Scope a query to filter orders by date range.
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('placed_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search orders by multiple fields.
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhere('tracking_number', 'like', "%{$search}%")
              ->orWhere('coupon_code', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope a query to filter orders by payment method.
     */
    public function scopeByPaymentMethod(Builder $query, string $paymentMethod): void
    {
        $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope a query to order by most recent first.
     */
    public function scopeRecent(Builder $query): void
    {
        $query->orderBy('placed_at', 'desc');
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    /**
     * Check if the order is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    /**
     * Check if the order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->status === OrderStatus::SHIPPED;
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    /**
     * Check if the order payment is unpaid.
     */
    public function isPaymentUnpaid(): bool
    {
        return $this->payment_status === PaymentStatus::UNPAID;
    }

    /**
     * Check if the order payment is paid.
     */
    public function isPaymentPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    /**
     * Check if the order payment is refunded.
     */
    public function isPaymentRefunded(): bool
    {
        return $this->payment_status === PaymentStatus::REFUNDED;
    }

    /**
     * Calculate the order subtotal.
     */
    public function calculateSubtotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    /**
     * Calculate the order grand total.
     */
    public function calculateGrandTotal(): float
    {
        return $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount - $this->coupon_discount;
    }

    /**
     * Get the order total in the specified currency.
     */
    public function getTotalInCurrency(string $currency): float
    {
        if ($this->currency === $currency) {
            return $this->grand_total;
        }

        // This would typically use an exchange rate service
        return $this->grand_total * $this->exchange_rate;
    }

    /**
     * Add a note to the order.
     */
    public function addNote(string $note, string $type = 'general'): bool
    {
        $currentNotes = $this->notes ? json_decode($this->notes, true) : [];
        $currentNotes[] = [
            'note' => $note,
            'type' => $type,
            'created_at' => now()->toISOString(),
        ];

        return $this->update(['notes' => json_encode($currentNotes)]);
    }

    /**
     * Get all order notes.
     */
    public function getNotes(): array
    {
        return $this->notes ? json_decode($this->notes, true) : [];
    }

    /**
     * Get notes by type.
     */
    public function getNotesByType(string $type): array
    {
        $notes = $this->getNotes();
        return array_filter($notes, fn($note) => $note['type'] === $type);
    }
}
