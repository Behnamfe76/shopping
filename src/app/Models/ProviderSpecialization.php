<?php

namespace Fereydooni\Shopping\App\Models;

use Fereydooni\Shopping\App\Enums\ProficiencyLevel;
use Fereydooni\Shopping\App\Enums\SpecializationCategory;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderSpecialization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'specialization_name',
        'category',
        'description',
        'years_experience',
        'proficiency_level',
        'certifications',
        'is_primary',
        'is_active',
        'verification_status',
        'verified_at',
        'verified_by',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'category' => SpecializationCategory::class,
        'proficiency_level' => ProficiencyLevel::class,
        'verification_status' => VerificationStatus::class,
        'certifications' => 'array',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'years_experience' => 'integer',
        'notes' => 'array',
    ];

    protected $dates = [
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'is_primary' => false,
        'is_active' => true,
        'verification_status' => VerificationStatus::UNVERIFIED,
        'years_experience' => 0,
    ];

    /**
     * Get the provider that owns the specialization.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the user who verified the specialization.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include active specializations.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive specializations.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include primary specializations.
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to only include non-primary specializations.
     */
    public function scopeNonPrimary(Builder $query): Builder
    {
        return $query->where('is_primary', false);
    }

    /**
     * Scope a query to only include verified specializations.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::VERIFIED);
    }

    /**
     * Scope a query to only include unverified specializations.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::UNVERIFIED);
    }

    /**
     * Scope a query to only include pending specializations.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::PENDING);
    }

    /**
     * Scope a query to only include rejected specializations.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::REJECTED);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, SpecializationCategory $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by proficiency level.
     */
    public function scopeByProficiencyLevel(Builder $query, ProficiencyLevel $level): Builder
    {
        return $query->where('proficiency_level', $level);
    }

    /**
     * Scope a query to filter by experience range.
     */
    public function scopeByExperienceRange(Builder $query, int $minYears, int $maxYears): Builder
    {
        return $query->whereBetween('years_experience', [$minYears, $maxYears]);
    }

    /**
     * Scope a query to filter by provider.
     */
    public function scopeByProvider(Builder $query, int $providerId): Builder
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Scope a query to search specializations.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('specialization_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        });
    }

    /**
     * Check if the specialization is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED;
    }

    /**
     * Check if the specialization is pending verification.
     */
    public function isPending(): bool
    {
        return $this->verification_status === VerificationStatus::PENDING;
    }

    /**
     * Check if the specialization is rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === VerificationStatus::REJECTED;
    }

    /**
     * Check if the specialization is unverified.
     */
    public function isUnverified(): bool
    {
        return $this->verification_status === VerificationStatus::UNVERIFIED;
    }

    /**
     * Check if the specialization is primary.
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Check if the specialization is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the experience level based on years of experience.
     */
    public function getExperienceLevel(): string
    {
        if ($this->years_experience >= 10) {
            return 'Senior';
        } elseif ($this->years_experience >= 5) {
            return 'Mid-level';
        } elseif ($this->years_experience >= 2) {
            return 'Junior';
        } else {
            return 'Entry-level';
        }
    }

    /**
     * Get the verification status label.
     */
    public function getVerificationStatusLabel(): string
    {
        return $this->verification_status->label();
    }

    /**
     * Get the verification status color.
     */
    public function getVerificationStatusColor(): string
    {
        return $this->verification_status->color();
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabel(): string
    {
        return $this->category->label();
    }

    /**
     * Get the category color.
     */
    public function getCategoryColor(): string
    {
        return $this->category->color();
    }

    /**
     * Get the category icon.
     */
    public function getCategoryIcon(): string
    {
        return $this->category->icon();
    }

    /**
     * Get the proficiency level label.
     */
    public function getProficiencyLevelLabel(): string
    {
        return $this->proficiency_level->label();
    }

    /**
     * Get the proficiency level description.
     */
    public function getProficiencyLevelDescription(): string
    {
        return $this->proficiency_level->description();
    }

    /**
     * Get the proficiency level numeric value.
     */
    public function getProficiencyLevelNumericValue(): int
    {
        return $this->proficiency_level->numericValue();
    }

    /**
     * Activate the specialization.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the specialization.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Set as primary specialization.
     */
    public function setPrimary(): bool
    {
        // Remove primary status from other specializations of the same provider
        static::where('provider_id', $this->provider_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        return $this->update(['is_primary' => true]);
    }

    /**
     * Remove primary status.
     */
    public function removePrimary(): bool
    {
        return $this->update(['is_primary' => false]);
    }

    /**
     * Verify the specialization.
     */
    public function verify(int $verifiedBy): bool
    {
        return $this->update([
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);
    }

    /**
     * Reject the specialization.
     */
    public function reject(?string $reason = null): bool
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'type' => 'rejection',
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
        ];

        return $this->update([
            'verification_status' => VerificationStatus::REJECTED,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark as pending verification.
     */
    public function markAsPending(): bool
    {
        return $this->update(['verification_status' => VerificationStatus::PENDING]);
    }

    /**
     * Add a note to the specialization.
     */
    public function addNote(string $note, string $type = 'general'): bool
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'type' => $type,
            'note' => $note,
            'timestamp' => now()->toISOString(),
        ];

        return $this->update(['notes' => $notes]);
    }

    /**
     * Get the age of the specialization in days.
     */
    public function getAgeInDays(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get the time since verification in days.
     */
    public function getDaysSinceVerification(): ?int
    {
        if (! $this->verified_at) {
            return null;
        }

        return $this->verified_at->diffInDays(now());
    }

    /**
     * Check if the specialization needs renewal.
     */
    public function needsRenewal(int $renewalThresholdDays = 365): bool
    {
        if (! $this->verified_at) {
            return false;
        }

        return $this->getDaysSinceVerification() > $renewalThresholdDays;
    }

    /**
     * Get the specializations table name.
     */
    public static function getTableName(): string
    {
        return 'provider_specializations';
    }

    /**
     * Get the boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a specialization, ensure only one primary per provider
        static::creating(function ($specialization) {
            if ($specialization->is_primary) {
                static::where('provider_id', $specialization->provider_id)
                    ->update(['is_primary' => false]);
            }
        });

        // When updating a specialization, ensure only one primary per provider
        static::updating(function ($specialization) {
            if ($specialization->is_primary && $specialization->isDirty('is_primary')) {
                static::where('provider_id', $specialization->provider_id)
                    ->where('id', '!=', $specialization->id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
