<?php

namespace App\Models;

use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSkill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'skill_name',
        'skill_category',
        'proficiency_level',
        'years_experience',
        'certification_required',
        'certification_name',
        'certification_date',
        'certification_expiry',
        'is_verified',
        'verified_by',
        'verified_at',
        'is_active',
        'is_primary',
        'is_required',
        'skill_description',
        'keywords',
        'tags',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'skill_category' => SkillCategory::class,
        'proficiency_level' => ProficiencyLevel::class,
        'years_experience' => 'integer',
        'certification_required' => 'boolean',
        'certification_date' => 'date',
        'certification_expiry' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'is_required' => 'boolean',
        'keywords' => 'array',
        'tags' => 'array',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_verified' => false,
        'is_active' => true,
        'is_primary' => false,
        'is_required' => false,
        'certification_required' => false,
        'years_experience' => 0,
    ];

    /**
     * Get the employee that owns the skill.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who verified the skill.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified skills.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include unverified skills.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope a query to only include primary skills.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to only include required skills.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to only include certified skills.
     */
    public function scopeCertified($query)
    {
        return $query->whereNotNull('certification_name');
    }

    /**
     * Scope a query to only include skills with expiring certifications.
     */
    public function scopeExpiringCertifications($query, int $days = 30)
    {
        return $query->whereNotNull('certification_expiry')
            ->where('certification_expiry', '<=', now()->addDays($days))
            ->where('certification_expiry', '>', now());
    }

    /**
     * Scope a query to only include skills by category.
     */
    public function scopeByCategory($query, SkillCategory $category)
    {
        return $query->where('skill_category', $category);
    }

    /**
     * Scope a query to only include skills by proficiency level.
     */
    public function scopeByProficiencyLevel($query, ProficiencyLevel $level)
    {
        return $query->where('proficiency_level', $level);
    }

    /**
     * Scope a query to only include skills by employee.
     */
    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Check if the skill certification is expiring soon.
     */
    public function isCertificationExpiring(int $days = 30): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->isBetween(
            now(),
            now()->addDays($days)
        );
    }

    /**
     * Check if the skill certification has expired.
     */
    public function isCertificationExpired(): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->isPast();
    }

    /**
     * Get the skill's proficiency level as a numeric value.
     */
    public function getProficiencyNumericValue(): int
    {
        return $this->proficiency_level->numericValue();
    }

    /**
     * Check if the skill is a technical skill.
     */
    public function isTechnical(): bool
    {
        return $this->skill_category === SkillCategory::TECHNICAL;
    }

    /**
     * Check if the skill is a soft skill.
     */
    public function isSoftSkill(): bool
    {
        return $this->skill_category === SkillCategory::SOFT_SKILLS;
    }

    /**
     * Check if the skill is a language.
     */
    public function isLanguage(): bool
    {
        return $this->skill_category === SkillCategory::LANGUAGES;
    }

    /**
     * Get the skill's display name with category.
     */
    public function getDisplayName(): string
    {
        return "{$this->skill_name} ({$this->skill_category->label()})";
    }

    /**
     * Get the skill's full description with proficiency level.
     */
    public function getFullDescription(): string
    {
        $description = "{$this->skill_name} - {$this->proficiency_level->label()}";

        if ($this->years_experience > 0) {
            $description .= " ({$this->years_experience} years)";
        }

        if ($this->certification_name) {
            $description .= " - Certified: {$this->certification_name}";
        }

        return $description;
    }
}
