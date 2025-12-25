<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'user_id',
        'transaction_type',
        'points',
        'points_value',
        'reference_type',
        'reference_id',
        'description',
        'reason',
        'status',
        'expires_at',
        'reversed_at',
        'reversed_by',
        'metadata',
    ];

    protected $casts = [
        'transaction_type' => LoyaltyTransactionType::class,
        'status' => LoyaltyTransactionStatus::class,
        'reference_type' => LoyaltyReferenceType::class,
        'points' => 'integer',
        'points_value' => 'decimal:2',
        'expires_at' => 'datetime',
        'reversed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $dates = [
        'expires_at',
        'reversed_at',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function reversedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reversed_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, LoyaltyTransactionType $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, LoyaltyTransactionStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByReferenceType($query, LoyaltyReferenceType $referenceType)
    {
        return $query->where('reference_type', $referenceType);
    }

    public function scopeActive($query)
    {
        return $query->where('status', LoyaltyTransactionStatus::COMPLETED)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', LoyaltyTransactionStatus::COMPLETED)
            ->where('expires_at', '<=', now());
    }

    public function scopeReversed($query)
    {
        return $query->where('status', LoyaltyTransactionStatus::REVERSED);
    }

    public function scopeEarned($query)
    {
        return $query->whereIn('transaction_type', [
            LoyaltyTransactionType::EARNED,
            LoyaltyTransactionType::BONUS,
            LoyaltyTransactionType::ADJUSTMENT,
        ]);
    }

    public function scopeRedeemed($query)
    {
        return $query->where('transaction_type', LoyaltyTransactionType::REDEEMED);
    }

    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsReversedAttribute(): bool
    {
        return $this->status === LoyaltyTransactionStatus::REVERSED;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === LoyaltyTransactionStatus::COMPLETED && ! $this->is_expired;
    }

    public function getFormattedPointsAttribute(): string
    {
        return number_format($this->points);
    }

    public function getFormattedPointsValueAttribute(): string
    {
        return '$'.number_format($this->points_value, 2);
    }

    // Mutators
    public function setPointsAttribute($value)
    {
        $this->attributes['points'] = abs($value);
    }

    public function setPointsValueAttribute($value)
    {
        $this->attributes['points_value'] = abs($value);
    }

    // Methods
    public function reverse(?string $reason = null, ?int $reversedBy = null): bool
    {
        $this->update([
            'status' => LoyaltyTransactionStatus::REVERSED,
            'reversed_at' => now(),
            'reversed_by' => $reversedBy,
            'reason' => $reason ?? $this->reason,
        ]);

        return true;
    }

    public function markAsExpired(): bool
    {
        return $this->update([
            'status' => LoyaltyTransactionStatus::EXPIRED,
        ]);
    }

    public function calculatePointsValue(): float
    {
        // This could be configurable based on business rules
        return $this->points * 0.01; // 1 point = $0.01
    }
}
