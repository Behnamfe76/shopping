<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Fereydooni\Shopping\App\Enums\CertificationCategory;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderCertification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'certification_name',
        'certification_number',
        'issuing_organization',
        'category',
        'description',
        'issue_date',
        'expiry_date',
        'renewal_date',
        'status',
        'verification_status',
        'verification_url',
        'attachment_path',
        'credits_earned',
        'is_recurring',
        'renewal_period',
        'renewal_requirements',
        'verified_at',
        'verified_by',
        'notes',
    ];

    protected $casts = [
        'category' => CertificationCategory::class,
        'status' => CertificationStatus::class,
        'verification_status' => VerificationStatus::class,
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'verified_at' => 'datetime',
        'credits_earned' => 'integer',
        'is_recurring' => 'boolean',
        'renewal_period' => 'integer',
        'renewal_requirements' => 'array',
        'notes' => 'array',
    ];

    protected $dates = [
        'issue_date',
        'expiry_date',
        'renewal_date',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the provider that owns the certification.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the user who verified the certification.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include active certifications.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', CertificationStatus::ACTIVE);
    }

    /**
     * Scope a query to only include expired certifications.
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('status', CertificationStatus::EXPIRED);
    }

    /**
     * Scope a query to only include verified certifications.
     */
    public function scopeVerified(Builder $query): void
    {
        $query->where('verification_status', VerificationStatus::VERIFIED);
    }

    /**
     * Scope a query to only include unverified certifications.
     */
    public function scopeUnverified(Builder $query): void
    {
        $query->where('verification_status', VerificationStatus::UNVERIFIED);
    }

    /**
     * Scope a query to only include pending verification certifications.
     */
    public function scopePendingVerification(Builder $query): void
    {
        $query->where('verification_status', VerificationStatus::PENDING);
    }

    /**
     * Scope a query to only include recurring certifications.
     */
    public function scopeRecurring(Builder $query): void
    {
        $query->where('is_recurring', true);
    }

    /**
     * Scope a query to only include certifications expiring soon.
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): void
    {
        $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
              ->where('status', CertificationStatus::ACTIVE);
    }

    /**
     * Scope a query to only include certifications by category.
     */
    public function scopeByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    /**
     * Scope a query to only include certifications by status.
     */
    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope a query to only include certifications by verification status.
     */
    public function scopeByVerificationStatus(Builder $query, string $verificationStatus): void
    {
        $query->where('verification_status', $verificationStatus);
    }

    /**
     * Scope a query to only include certifications by provider.
     */
    public function scopeByProvider(Builder $query, int $providerId): void
    {
        $query->where('provider_id', $providerId);
    }

    /**
     * Check if the certification is active.
     */
    public function isActive(): bool
    {
        return $this->status === CertificationStatus::ACTIVE;
    }

    /**
     * Check if the certification is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === CertificationStatus::EXPIRED;
    }

    /**
     * Check if the certification is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === CertificationStatus::SUSPENDED;
    }

    /**
     * Check if the certification is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->status === CertificationStatus::REVOKED;
    }

    /**
     * Check if the certification is pending renewal.
     */
    public function isPendingRenewal(): bool
    {
        return $this->status === CertificationStatus::PENDING_RENEWAL;
    }

    /**
     * Check if the certification is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED;
    }

    /**
     * Check if the certification is unverified.
     */
    public function isUnverified(): bool
    {
        return $this->verification_status === VerificationStatus::UNVERIFIED;
    }

    /**
     * Check if the certification is pending verification.
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === VerificationStatus::PENDING;
    }

    /**
     * Check if the certification is rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === VerificationStatus::REJECTED;
    }

    /**
     * Check if the certification requires update.
     */
    public function requiresUpdate(): bool
    {
        return $this->verification_status === VerificationStatus::REQUIRES_UPDATE;
    }

    /**
     * Check if the certification is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expiry_date || $this->isExpired()) {
            return false;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false) <= $days;
    }

    /**
     * Check if the certification needs renewal.
     */
    public function needsRenewal(): bool
    {
        if (!$this->is_recurring || !$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false) <= 90;
    }

    /**
     * Get the days until expiration.
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false);
    }

    /**
     * Get the days since issue.
     */
    public function daysSinceIssue(): ?int
    {
        if (!$this->issue_date) {
            return null;
        }

        return $this->issue_date->diffInDays(Carbon::now(), false);
    }

    /**
     * Get the certification age in years.
     */
    public function getAgeInYears(): ?float
    {
        if (!$this->issue_date) {
            return null;
        }

        return $this->issue_date->diffInYears(Carbon::now(), true);
    }

    /**
     * Check if the certification is valid for use.
     */
    public function isValid(): bool
    {
        return $this->isActive() && $this->isVerified();
    }

    /**
     * Get the display name for the category.
     */
    public function getCategoryLabel(): string
    {
        return $this->category?->label() ?? 'Unknown';
    }

    /**
     * Get the display name for the status.
     */
    public function getStatusLabel(): string
    {
        return $this->status?->label() ?? 'Unknown';
    }

    /**
     * Get the display name for the verification status.
     */
    public function getVerificationStatusLabel(): string
    {
        return $this->verification_status?->label() ?? 'Unknown';
    }

    /**
     * Get the color class for the status.
     */
    public function getStatusColor(): string
    {
        return $this->status?->color() ?? 'secondary';
    }

    /**
     * Get the color class for the verification status.
     */
    public function getVerificationStatusColor(): string
    {
        return $this->verification_status?->color() ?? 'secondary';
    }

    /**
     * Get the renewal requirements as a formatted string.
     */
    public function getRenewalRequirementsText(): string
    {
        if (empty($this->renewal_requirements)) {
            return 'No specific requirements';
        }

        return implode(', ', $this->renewal_requirements);
    }

    /**
     * Get the notes as a formatted string.
     */
    public function getNotesText(): string
    {
        if (empty($this->notes)) {
            return '';
        }

        return implode("\n", $this->notes);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return asset('storage/' . $this->attachment_path);
    }

    /**
     * Check if the certification has an attachment.
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the verification URL.
     */
    public function getVerificationUrl(): ?string
    {
        return $this->verification_url;
    }

    /**
     * Check if the certification has a verification URL.
     */
    public function hasVerificationUrl(): bool
    {
        return !empty($this->verification_url);
    }
}
