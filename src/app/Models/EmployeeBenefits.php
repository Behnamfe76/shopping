<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Fereydooni\Shopping\app\Enums\BenefitType;
use Fereydooni\Shopping\app\Enums\BenefitStatus;
use Fereydooni\Shopping\app\Enums\CoverageLevel;
use Fereydooni\Shopping\app\Enums\NetworkType;
use Carbon\Carbon;

class EmployeeBenefits extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_benefits';

    protected $fillable = [
        'employee_id',
        'benefit_type',
        'benefit_name',
        'provider',
        'plan_id',
        'enrollment_date',
        'effective_date',
        'end_date',
        'status',
        'coverage_level',
        'premium_amount',
        'employee_contribution',
        'employer_contribution',
        'total_cost',
        'deductible',
        'co_pay',
        'co_insurance',
        'max_out_of_pocket',
        'network_type',
        'is_active',
        'notes',
        'documents',
    ];

    protected $casts = [
        'benefit_type' => BenefitType::class,
        'status' => BenefitStatus::class,
        'coverage_level' => CoverageLevel::class,
        'network_type' => NetworkType::class,
        'enrollment_date' => 'date',
        'effective_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'employee_contribution' => 'decimal:2',
        'employer_contribution' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'deductible' => 'decimal:2',
        'co_pay' => 'decimal:2',
        'co_insurance' => 'decimal:2',
        'max_out_of_pocket' => 'decimal:2',
        'is_active' => 'boolean',
        'documents' => 'array',
    ];

    protected $dates = [
        'enrollment_date',
        'effective_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the employee that owns the benefits.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the documents for this benefit.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Scope a query to only include active benefits.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include benefits by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('benefit_type', $type);
    }

    /**
     * Scope a query to only include benefits by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include benefits by provider.
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope a query to only include benefits by coverage level.
     */
    public function scopeByCoverageLevel($query, $coverageLevel)
    {
        return $query->where('coverage_level', $coverageLevel);
    }

    /**
     * Scope a query to only include benefits by network type.
     */
    public function scopeByNetworkType($query, $networkType)
    {
        return $query->where('network_type', $networkType);
    }

    /**
     * Scope a query to only include benefits expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $expiryDate = Carbon::now()->addDays($days);
        return $query->where('end_date', '<=', $expiryDate)
                    ->where('end_date', '>', Carbon::now())
                    ->where('status', BenefitStatus::ENROLLED);
    }

    /**
     * Check if the benefit is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === BenefitStatus::ENROLLED;
    }

    /**
     * Check if the benefit is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return $this->end_date->diffInDays(Carbon::now(), false) <= $days;
    }

    /**
     * Check if the benefit can be modified.
     */
    public function canBeModified(): bool
    {
        return in_array($this->status, [
            BenefitStatus::PENDING,
            BenefitStatus::ENROLLED
        ]);
    }

    /**
     * Check if the benefit can be terminated.
     */
    public function canBeTerminated(): bool
    {
        return $this->status === BenefitStatus::ENROLLED;
    }

    /**
     * Check if the benefit can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            BenefitStatus::PENDING,
            BenefitStatus::ENROLLED
        ]);
    }

    /**
     * Calculate the total monthly cost.
     */
    public function getMonthlyCost(): float
    {
        return $this->premium_amount ?? 0.0;
    }

    /**
     * Calculate the total annual cost.
     */
    public function getAnnualCost(): float
    {
        return $this->getMonthlyCost() * 12;
    }

    /**
     * Calculate the employee's monthly contribution.
     */
    public function getEmployeeMonthlyContribution(): float
    {
        return $this->employee_contribution ?? 0.0;
    }

    /**
     * Calculate the employer's monthly contribution.
     */
    public function getEmployerMonthlyContribution(): float
    {
        return $this->employer_contribution ?? 0.0;
    }

    /**
     * Get the remaining days until expiration.
     */
    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return $this->end_date->diffInDays(Carbon::now(), false);
    }

    /**
     * Get the benefit summary.
     */
    public function getBenefitSummary(): array
    {
        return [
            'id' => $this->id,
            'benefit_name' => $this->benefit_name,
            'benefit_type' => $this->benefit_type->value,
            'provider' => $this->provider,
            'status' => $this->status->value,
            'coverage_level' => $this->coverage_level->value,
            'monthly_cost' => $this->getMonthlyCost(),
            'employee_contribution' => $this->getEmployeeMonthlyContribution(),
            'employer_contribution' => $this->getEmployerMonthlyContribution(),
            'effective_date' => $this->effective_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->isActive(),
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($benefit) {
            if (!$benefit->enrollment_date) {
                $benefit->enrollment_date = Carbon::now();
            }

            if (!$benefit->effective_date) {
                $benefit->effective_date = Carbon::now();
            }
        });

        static::updating(function ($benefit) {
            if ($benefit->isDirty('status')) {
                if ($benefit->status === BenefitStatus::ENROLLED) {
                    $benefit->is_active = true;
                } elseif (in_array($benefit->status, [BenefitStatus::TERMINATED, BenefitStatus::CANCELLED])) {
                    $benefit->is_active = false;
                }
            }
        });
    }
}
