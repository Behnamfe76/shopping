<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fereydooni\Shopping\App\Enums\InsuranceType;
use Fereydooni\Shopping\App\Enums\InsuranceStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProviderInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'insurance_type',
        'policy_number',
        'provider_name',
        'coverage_amount',
        'start_date',
        'end_date',
        'status',
        'documents',
        'verification_status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'insurance_type' => InsuranceType::class,
        'status' => InsuranceStatus::class,
        'verification_status' => VerificationStatus::class,
        'coverage_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'verified_at' => 'datetime',
        'documents' => 'array',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the provider that owns the insurance.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the user who verified the insurance.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the insurance is currently active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive() &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    /**
     * Check if the insurance has expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if the insurance is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->diffInDays(now()) <= $days && !$this->isExpired();
    }

    /**
     * Check if the insurance is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED;
    }

    /**
     * Check if the insurance is pending verification.
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === VerificationStatus::PENDING;
    }

    /**
     * Check if the insurance is rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === VerificationStatus::REJECTED;
    }

    /**
     * Get the remaining days until expiration.
     */
    public function getDaysUntilExpiration(): int
    {
        return max(0, $this->end_date->diffInDays(now()));
    }

    /**
     * Get the insurance duration in days.
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get the insurance duration in months.
     */
    public function getDurationInMonths(): int
    {
        return $this->start_date->diffInMonths($this->end_date);
    }

    /**
     * Get the insurance duration in years.
     */
    public function getDurationInYears(): int
    {
        return $this->start_date->diffInYears($this->end_date);
    }

    /**
     * Check if the insurance covers a specific date.
     */
    public function coversDate(Carbon $date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the insurance covers the current date.
     */
    public function coversCurrentDate(): bool
    {
        return $this->coversDate(now());
    }

    /**
     * Get the insurance status based on dates.
     */
    public function getDateBasedStatus(): InsuranceStatus
    {
        if ($this->isExpired()) {
            return InsuranceStatus::EXPIRED;
        }

        if ($this->start_date > now()) {
            return InsuranceStatus::PENDING;
        }

        return InsuranceStatus::ACTIVE;
    }

    /**
     * Update the status based on dates.
     */
    public function updateStatusBasedOnDates(): bool
    {
        $newStatus = $this->getDateBasedStatus();

        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            return $this->save();
        }

        return true;
    }

    /**
     * Add a document to the insurance.
     */
    public function addDocument(string $documentPath): bool
    {
        $documents = $this->documents ?? [];
        $documents[] = $documentPath;
        $this->documents = $documents;

        return $this->save();
    }

    /**
     * Remove a document from the insurance.
     */
    public function removeDocument(string $documentPath): bool
    {
        $documents = $this->documents ?? [];
        $documents = array_filter($documents, fn($doc) => $doc !== $documentPath);
        $this->documents = array_values($documents);

        return $this->save();
    }

    /**
     * Verify the insurance.
     */
    public function verify(int $verifiedBy, string $notes = null): bool
    {
        $this->verification_status = VerificationStatus::VERIFIED;
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();

        if ($notes) {
            $this->notes = $notes;
        }

        return $this->save();
    }

    /**
     * Reject the insurance.
     */
    public function reject(int $rejectedBy, string $reason): bool
    {
        $this->verification_status = VerificationStatus::REJECTED;
        $this->verified_by = $rejectedBy;
        $this->verified_at = now();
        $this->notes = $reason;

        return $this->save();
    }

    /**
     * Activate the insurance.
     */
    public function activate(): bool
    {
        $this->status = InsuranceStatus::ACTIVE;
        return $this->save();
    }

    /**
     * Deactivate the insurance.
     */
    public function deactivate(): bool
    {
        $this->status = InsuranceStatus::SUSPENDED;
        return $this->save();
    }

    /**
     * Cancel the insurance.
     */
    public function cancel(string $reason = null): bool
    {
        $this->status = InsuranceStatus::CANCELLED;
        if ($reason) {
            $this->notes = $reason;
        }
        return $this->save();
    }

    /**
     * Renew the insurance with new data.
     */
    public function renew(array $renewalData): bool
    {
        $this->fill($renewalData);
        $this->verification_status = VerificationStatus::PENDING;
        $this->verified_by = null;
        $this->verified_at = null;

        return $this->save();
    }

    /**
     * Get the insurance type label.
     */
    public function getInsuranceTypeLabel(): string
    {
        return $this->insurance_type->label();
    }

    /**
     * Get the status label.
     */
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    /**
     * Get the verification status label.
     */
    public function getVerificationStatusLabel(): string
    {
        return $this->verification_status->label();
    }

    /**
     * Scope to get active insurance.
     */
    public function scopeActive($query)
    {
        return $query->where('status', InsuranceStatus::ACTIVE);
    }

    /**
     * Scope to get expired insurance.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', InsuranceStatus::EXPIRED);
    }

    /**
     * Scope to get expiring soon insurance.
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>', now());
    }

    /**
     * Scope to get verified insurance.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', VerificationStatus::VERIFIED);
    }

    /**
     * Scope to get pending verification insurance.
     */
    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', VerificationStatus::PENDING);
    }

    /**
     * Scope to get insurance by type.
     */
    public function scopeByType($query, string $insuranceType)
    {
        return $query->where('insurance_type', $insuranceType);
    }

    /**
     * Scope to get insurance by provider.
     */
    public function scopeByProvider($query, int $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Scope to get insurance by policy number.
     */
    public function scopeByPolicyNumber($query, string $policyNumber)
    {
        return $query->where('policy_number', $policyNumber);
    }

    /**
     * Scope to get insurance by provider name.
     */
    public function scopeByProviderName($query, string $providerName)
    {
        return $query->where('provider_name', 'like', "%{$providerName}%");
    }

    /**
     * Scope to get insurance by date range.
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get insurance by coverage amount range.
     */
    public function scopeByCoverageAmountRange($query, float $minAmount, float $maxAmount)
    {
        return $query->whereBetween('coverage_amount', [$minAmount, $maxAmount]);
    }
}
