<?php

namespace Fereydooni\Shopping\App\Models;

use Carbon\Carbon;
use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'provider_number',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'website',
        'tax_id',
        'business_license',
        'provider_type',
        'status',
        'rating',
        'total_orders',
        'total_spent',
        'average_order_value',
        'last_order_date',
        'first_order_date',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'bank_name',
        'bank_account_number',
        'bank_routing_number',
        'contact_notes',
        'specializations',
        'certifications',
        'insurance_info',
        'contract_start_date',
        'contract_end_date',
        'commission_rate',
        'discount_rate',
        'shipping_methods',
        'payment_methods',
        'quality_rating',
        'delivery_rating',
        'communication_rating',
        'response_time',
        'on_time_delivery_rate',
        'return_rate',
        'defect_rate',
    ];

    protected $casts = [
        'provider_type' => ProviderType::class,
        'status' => ProviderStatus::class,
        'rating' => 'float',
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'last_order_date' => 'datetime',
        'first_order_date' => 'datetime',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'specializations' => 'array',
        'certifications' => 'array',
        'insurance_info' => 'array',
        'contract_start_date' => 'datetime',
        'contract_end_date' => 'datetime',
        'commission_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'shipping_methods' => 'array',
        'payment_methods' => 'array',
        'quality_rating' => 'float',
        'delivery_rating' => 'float',
        'communication_rating' => 'float',
        'response_time' => 'integer',
        'on_time_delivery_rate' => 'decimal:2',
        'return_rate' => 'decimal:2',
        'defect_rate' => 'decimal:2',
    ];

    protected $dates = [
        'last_order_date',
        'first_order_date',
        'contract_start_date',
        'contract_end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProviderNote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ProviderStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProviderStatus::INACTIVE);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', ProviderStatus::SUSPENDED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ProviderStatus::PENDING);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('status', ProviderStatus::BLACKLISTED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('provider_type', $type);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->whereNotNull('rating')
            ->orderBy('rating', 'desc')
            ->limit($limit);
    }

    public function scopeTopSpenders($query, $limit = 10)
    {
        return $query->orderBy('total_spent', 'desc')
            ->limit($limit);
    }

    public function scopeMostReliable($query, $limit = 10)
    {
        return $query->whereNotNull('quality_rating')
            ->orderBy('quality_rating', 'desc')
            ->limit($limit);
    }

    public function scopeNewest($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    public function scopeLongestServing($query, $limit = 10)
    {
        return $query->whereNotNull('contract_start_date')
            ->orderBy('contract_start_date', 'asc')
            ->limit($limit);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === ProviderStatus::ACTIVE;
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === ProviderStatus::SUSPENDED;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === ProviderStatus::PENDING;
    }

    public function getIsBlacklistedAttribute(): bool
    {
        return $this->status === ProviderStatus::BLACKLISTED;
    }

    public function getHasExpiredContractAttribute(): bool
    {
        return $this->contract_end_date && $this->contract_end_date->isPast();
    }

    public function getContractExpiresInDaysAttribute(): ?int
    {
        if (! $this->contract_end_date) {
            return null;
        }

        return $this->contract_end_date->diffInDays(now(), false);
    }

    public function getOverallScoreAttribute(): float
    {
        $scores = [];

        if ($this->rating) {
            $scores[] = $this->rating;
        }

        if ($this->quality_rating) {
            $scores[] = $this->quality_rating;
        }

        if ($this->delivery_rating) {
            $scores[] = $this->delivery_rating;
        }

        if ($this->communication_rating) {
            $scores[] = $this->communication_rating;
        }

        return empty($scores) ? 0.0 : array_sum($scores) / count($scores);
    }

    public function getCreditUtilizationAttribute(): float
    {
        if ($this->credit_limit <= 0) {
            return 0.0;
        }

        return ($this->current_balance / $this->credit_limit) * 100;
    }

    public function getIsOverCreditLimitAttribute(): bool
    {
        return $this->current_balance > $this->credit_limit;
    }

    // Mutators
    public function setSpecializationsAttribute($value)
    {
        $this->attributes['specializations'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setCertificationsAttribute($value)
    {
        $this->attributes['certifications'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setInsuranceInfoAttribute($value)
    {
        $this->attributes['insurance_info'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setShippingMethodsAttribute($value)
    {
        $this->attributes['shipping_methods'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setPaymentMethodsAttribute($value)
    {
        $this->attributes['payment_methods'] = is_array($value) ? json_encode($value) : $value;
    }

    // Methods
    public function activate(): bool
    {
        return $this->update(['status' => ProviderStatus::ACTIVE]);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => ProviderStatus::INACTIVE]);
    }

    public function suspend(?string $reason = null): bool
    {
        $this->contact_notes = ($this->contact_notes ? $this->contact_notes."\n" : '').
                               'Suspended on '.now()->format('Y-m-d H:i:s').
                               ($reason ? " - Reason: {$reason}" : '');

        return $this->update(['status' => ProviderStatus::SUSPENDED]);
    }

    public function unsuspend(): bool
    {
        return $this->update(['status' => ProviderStatus::ACTIVE]);
    }

    public function blacklist(?string $reason = null): bool
    {
        $this->contact_notes = ($this->contact_notes ? $this->contact_notes."\n" : '').
                               'Blacklisted on '.now()->format('Y-m-d H:i:s').
                               ($reason ? " - Reason: {$reason}" : '');

        return $this->update(['status' => ProviderStatus::BLACKLISTED]);
    }

    public function updateRating(float $rating): bool
    {
        return $this->update(['rating' => max(0, min(5, $rating))]);
    }

    public function updateQualityRating(float $rating): bool
    {
        return $this->update(['quality_rating' => max(0, min(5, $rating))]);
    }

    public function updateDeliveryRating(float $rating): bool
    {
        return $this->update(['delivery_rating' => max(0, min(5, $rating))]);
    }

    public function updateCommunicationRating(float $rating): bool
    {
        return $this->update(['communication_rating' => max(0, min(5, $rating))]);
    }

    public function updateCreditLimit(float $newLimit): bool
    {
        return $this->update(['credit_limit' => max(0, $newLimit)]);
    }

    public function updateCommissionRate(float $newRate): bool
    {
        return $this->update(['commission_rate' => max(0, min(100, $newRate))]);
    }

    public function updateDiscountRate(float $newRate): bool
    {
        return $this->update(['discount_rate' => max(0, min(100, $newRate))]);
    }

    public function extendContract(string $newEndDate): bool
    {
        $date = Carbon::parse($newEndDate);

        return $this->update(['contract_end_date' => $date]);
    }

    public function terminateContract(?string $reason = null): bool
    {
        $this->contact_notes = ($this->contact_notes ? $this->contact_notes."\n" : '').
                               'Contract terminated on '.now()->format('Y-m-d H:i:s').
                               ($reason ? " - Reason: {$reason}" : '');

        return $this->update(['contract_end_date' => now()]);
    }

    public function addNote(string $note, string $type = 'general'): bool
    {
        return $this->notes()->create([
            'note' => $note,
            'type' => $type,
        ]);
    }

    public function updateSpecializations(array $specializations): bool
    {
        return $this->update(['specializations' => $specializations]);
    }

    public function updateCertifications(array $certifications): bool
    {
        return $this->update(['certifications' => $certifications]);
    }

    public function updateInsurance(array $insurance): bool
    {
        return $this->update(['insurance_info' => $insurance]);
    }

    public function calculateScore(): float
    {
        $score = 0;
        $factors = 0;

        // Rating factor (30%)
        if ($this->rating) {
            $score += ($this->rating / 5) * 30;
            $factors++;
        }

        // Quality rating factor (25%)
        if ($this->quality_rating) {
            $score += ($this->quality_rating / 5) * 25;
            $factors++;
        }

        // Delivery rating factor (20%)
        if ($this->delivery_rating) {
            $score += ($this->delivery_rating / 5) * 20;
            $factors++;
        }

        // Communication rating factor (15%)
        if ($this->communication_rating) {
            $score += ($this->communication_rating / 5) * 15;
            $factors++;
        }

        // On-time delivery factor (10%)
        if ($this->on_time_delivery_rate) {
            $score += ($this->on_time_delivery_rate / 100) * 10;
            $factors++;
        }

        return $factors > 0 ? $score : 0;
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'overall_score' => $this->calculateScore(),
            'rating' => $this->rating,
            'quality_rating' => $this->quality_rating,
            'delivery_rating' => $this->delivery_rating,
            'communication_rating' => $this->communication_rating,
            'on_time_delivery_rate' => $this->on_time_delivery_rate,
            'return_rate' => $this->return_rate,
            'defect_rate' => $this->defect_rate,
            'response_time' => $this->response_time,
            'total_orders' => $this->total_orders,
            'total_spent' => $this->total_spent,
            'average_order_value' => $this->average_order_value,
            'credit_utilization' => $this->credit_utilization,
            'contract_status' => $this->has_expired_contract ? 'expired' : 'active',
        ];
    }
}
