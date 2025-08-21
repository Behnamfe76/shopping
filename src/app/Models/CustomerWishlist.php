<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fereydooni\Shopping\app\Enums\WishlistPriority;

class CustomerWishlist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'product_id',
        'notes',
        'priority',
        'is_public',
        'is_notified',
        'notification_sent_at',
        'price_when_added',
        'current_price',
        'price_drop_notification',
        'added_at',
    ];

    protected $casts = [
        'priority' => WishlistPriority::class,
        'is_public' => 'boolean',
        'is_notified' => 'boolean',
        'notification_sent_at' => 'datetime',
        'price_when_added' => 'decimal:2',
        'current_price' => 'decimal:2',
        'price_drop_notification' => 'boolean',
        'added_at' => 'datetime',
    ];

    protected $attributes = [
        'priority' => WishlistPriority::MEDIUM,
        'is_public' => false,
        'is_notified' => false,
        'price_drop_notification' => true,
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNotified($query)
    {
        return $query->where('is_notified', true);
    }

    public function scopeNotNotified($query)
    {
        return $query->where('is_notified', false);
    }

    public function scopeWithPriceDrops($query)
    {
        return $query->whereNotNull('price_when_added')
                    ->whereNotNull('current_price')
                    ->whereRaw('current_price < price_when_added');
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('added_at', [$startDate, $endDate]);
    }

    public function scopeByPriceRange($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('current_price', [$minPrice, $maxPrice]);
    }

    // Accessors
    public function getHasPriceDropAttribute(): bool
    {
        if (!$this->price_when_added || !$this->current_price) {
            return false;
        }

        return $this->current_price < $this->price_when_added;
    }

    public function getPriceDropPercentageAttribute(): ?float
    {
        if (!$this->has_price_drop) {
            return null;
        }

        return round((($this->price_when_added - $this->current_price) / $this->price_when_added) * 100, 2);
    }

    public function getPriceDropAmountAttribute(): ?float
    {
        if (!$this->has_price_drop) {
            return null;
        }

        return round($this->price_when_added - $this->current_price, 2);
    }

    public function getShouldSendNotificationAttribute(): bool
    {
        return $this->price_drop_notification && 
               $this->has_price_drop && 
               !$this->is_notified;
    }

    public function getPriorityLevelAttribute(): int
    {
        return $this->priority?->level() ?? 0;
    }

    // Mutators
    public function setNotesAttribute($value)
    {
        $this->attributes['notes'] = $value ? trim($value) : null;
    }

    public function setPriceWhenAddedAttribute($value)
    {
        $this->attributes['price_when_added'] = $value ? (float) $value : null;
    }

    public function setCurrentPriceAttribute($value)
    {
        $this->attributes['current_price'] = $value ? (float) $value : null;
    }

    // Methods
    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    public function setPriority(WishlistPriority $priority): bool
    {
        return $this->update(['priority' => $priority]);
    }

    public function markAsNotified(): bool
    {
        return $this->update([
            'is_notified' => true,
            'notification_sent_at' => now(),
        ]);
    }

    public function markAsNotNotified(): bool
    {
        return $this->update([
            'is_notified' => false,
            'notification_sent_at' => null,
        ]);
    }

    public function updateCurrentPrice(float $currentPrice): bool
    {
        return $this->update(['current_price' => $currentPrice]);
    }

    public function checkPriceDrop(): bool
    {
        if (!$this->price_when_added || !$this->current_price) {
            return false;
        }

        return $this->current_price < $this->price_when_added;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isPrivate(): bool
    {
        return !$this->is_public;
    }

    public function hasPriceDrop(): bool
    {
        return $this->has_price_drop;
    }

    public function shouldSendNotification(): bool
    {
        return $this->should_send_notification;
    }
}
