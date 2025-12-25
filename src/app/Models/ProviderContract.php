<?php

namespace Fereydooni\Shopping\App\Models;

use Fereydooni\Shopping\App\Enums\ContractStatus;
use Fereydooni\Shopping\App\Enums\ContractType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'contract_number',
        'contract_type',
        'title',
        'description',
        'start_date',
        'end_date',
        'terms',
        'conditions',
        'commission_rate',
        'payment_terms',
        'status',
        'signed_by',
        'signed_at',
        'renewal_date',
        'termination_date',
        'termination_reason',
        'auto_renewal',
        'renewal_terms',
        'contract_value',
        'currency',
        'attachments',
        'notes',
    ];

    protected $casts = [
        'contract_type' => ContractType::class,
        'status' => ContractStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'signed_at' => 'datetime',
        'renewal_date' => 'datetime',
        'termination_date' => 'datetime',
        'auto_renewal' => 'boolean',
        'commission_rate' => 'decimal:2',
        'contract_value' => 'decimal:2',
        'payment_terms' => 'array',
        'renewal_terms' => 'array',
        'attachments' => 'array',
        'terms' => 'array',
        'conditions' => 'array',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'signed_at',
        'renewal_date',
        'termination_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'status' => ContractStatus::DRAFT,
        'auto_renewal' => false,
        'commission_rate' => 0.00,
        'contract_value' => 0.00,
        'currency' => 'USD',
    ];

    /**
     * Get the provider that owns the contract.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the user who signed the contract.
     */
    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'), 'signed_by');
    }

    /**
     * Check if the contract is active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive() &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    /**
     * Check if the contract is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if the contract is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->diffInDays(now()) <= $days && ! $this->isExpired();
    }

    /**
     * Check if the contract can be renewed.
     */
    public function canBeRenewed(): bool
    {
        return $this->status->canBeRenewed() && ! $this->isExpired();
    }

    /**
     * Check if the contract can be terminated.
     */
    public function canBeTerminated(): bool
    {
        return $this->status->canBeTerminated();
    }

    /**
     * Check if the contract can be modified.
     */
    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }

    /**
     * Check if the contract is signed.
     */
    public function isSigned(): bool
    {
        return ! is_null($this->signed_at) && ! is_null($this->signed_by);
    }

    /**
     * Get the contract duration in days.
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get the remaining days until expiration.
     */
    public function getRemainingDays(): int
    {
        return max(0, $this->end_date->diffInDays(now()));
    }

    /**
     * Calculate the commission amount for a given value.
     */
    public function calculateCommission(float $value): float
    {
        return $value * ($this->commission_rate / 100);
    }

    /**
     * Get the contract value in the specified currency.
     */
    public function getContractValueInCurrency(?string $currency = null): float
    {
        if ($currency === null || $currency === $this->currency) {
            return $this->contract_value;
        }

        // TODO: Implement currency conversion logic
        return $this->contract_value;
    }

    /**
     * Generate a unique contract number.
     */
    public static function generateContractNumber(): string
    {
        $prefix = 'CON';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = static::generateContractNumber();
            }
        });
    }
}
