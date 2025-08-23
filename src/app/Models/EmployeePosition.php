<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Fereydooni\Shopping\app\Enums\PositionStatus;
use Fereydooni\Shopping\app\Enums\PositionLevel;

class EmployeePosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'description',
        'department_id',
        'level',
        'salary_min',
        'salary_max',
        'hourly_rate_min',
        'hourly_rate_max',
        'is_active',
        'status',
        'requirements',
        'responsibilities',
        'skills_required',
        'experience_required',
        'education_required',
        'is_remote',
        'is_travel_required',
        'travel_percentage',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'level' => PositionLevel::class,
        'status' => PositionStatus::class,
        'is_active' => 'boolean',
        'is_remote' => 'boolean',
        'is_travel_required' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'hourly_rate_min' => 'decimal:2',
        'hourly_rate_max' => 'decimal:2',
        'travel_percentage' => 'decimal:2',
        'experience_required' => 'integer',
        'requirements' => 'array',
        'responsibilities' => 'array',
        'skills_required' => 'array',
        'education_required' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function manager(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'manager_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', PositionStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhere('status', '!=', PositionStatus::ACTIVE);
    }

    public function scopeHiring($query)
    {
        return $query->where('status', PositionStatus::HIRING);
    }

    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    public function scopeByLevel($query, PositionLevel $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByStatus($query, PositionStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeBySalaryRange($query, float $minSalary, float $maxSalary)
    {
        return $query->whereBetween('salary_min', [$minSalary, $maxSalary])
                    ->orWhereBetween('salary_max', [$minSalary, $maxSalary]);
    }

    public function scopeByHourlyRateRange($query, float $minRate, float $maxRate)
    {
        return $query->whereBetween('hourly_rate_min', [$minRate, $maxRate])
                    ->orWhereBetween('hourly_rate_max', [$minRate, $maxRate]);
    }

    public function scopeByExperienceLevel($query, int $minExperience)
    {
        return $query->where('experience_required', '<=', $minExperience);
    }

    public function scopeBySkills($query, array $skills)
    {
        return $query->whereJsonContains('skills_required', $skills);
    }

    public function scopeTravelRequired($query)
    {
        return $query->where('is_travel_required', true);
    }

    // Accessors
    public function getFullTitleAttribute(): string
    {
        return "{$this->title} ({$this->level->label()})";
    }

    public function getSalaryRangeAttribute(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return '$' . number_format($this->salary_min, 0) . ' - $' . number_format($this->salary_max, 0);
        }
        return 'Not specified';
    }

    public function getHourlyRateRangeAttribute(): string
    {
        if ($this->hourly_rate_min && $this->hourly_rate_max) {
            return '$' . number_format($this->hourly_rate_min, 2) . ' - $' . number_format($this->hourly_rate_max, 2);
        }
        return 'Not specified';
    }

    public function getAverageSalaryAttribute(): float
    {
        if ($this->salary_min && $this->salary_max) {
            return ($this->salary_min + $this->salary_max) / 2;
        }
        return 0;
    }

    public function getAverageHourlyRateAttribute(): float
    {
        if ($this->hourly_rate_min && $this->hourly_rate_max) {
            return ($this->hourly_rate_min + $this->hourly_rate_max) / 2;
        }
        return 0;
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === PositionStatus::HIRING || $this->status === PositionStatus::ACTIVE;
    }

    public function getRequiresTravelAttribute(): bool
    {
        return $this->is_travel_required && $this->travel_percentage > 0;
    }

    // Methods
    public function activate(): bool
    {
        $this->update([
            'is_active' => true,
            'status' => PositionStatus::ACTIVE
        ]);
        return true;
    }

    public function deactivate(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => PositionStatus::INACTIVE
        ]);
        return true;
    }

    public function setHiring(): bool
    {
        $this->update([
            'is_active' => true,
            'status' => PositionStatus::HIRING
        ]);
        return true;
    }

    public function setFrozen(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => PositionStatus::FROZEN
        ]);
        return true;
    }

    public function archive(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => PositionStatus::ARCHIVED
        ]);
        return true;
    }

    public function updateSalaryRange(float $minSalary, float $maxSalary): bool
    {
        return $this->update([
            'salary_min' => $minSalary,
            'salary_max' => $maxSalary
        ]);
    }

    public function updateHourlyRateRange(float $minRate, float $maxRate): bool
    {
        return $this->update([
            'hourly_rate_min' => $minRate,
            'hourly_rate_max' => $maxRate
        ]);
    }

    public function addSkillRequirement(string $skill): bool
    {
        $skills = $this->skills_required ?? [];
        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            return $this->update(['skills_required' => $skills]);
        }
        return true;
    }

    public function removeSkillRequirement(string $skill): bool
    {
        $skills = $this->skills_required ?? [];
        $skills = array_filter($skills, fn($s) => $s !== $skill);
        return $this->update(['skills_required' => array_values($skills)]);
    }

    public function hasSkill(string $skill): bool
    {
        return in_array($skill, $this->skills_required ?? []);
    }

    public function matchesEmployeeSkills(array $employeeSkills): bool
    {
        if (empty($this->skills_required)) {
            return true;
        }

        $requiredSkills = $this->skills_required;
        $matchingSkills = array_intersect($requiredSkills, $employeeSkills);

        return count($matchingSkills) >= (count($requiredSkills) * 0.7); // 70% match required
    }

    public function isRemoteEligible(): bool
    {
        return $this->is_remote;
    }

    public function getTravelPercentage(): float
    {
        return $this->travel_percentage ?? 0;
    }

    public function getRequirements(): array
    {
        return $this->requirements ?? [];
    }

    public function getResponsibilities(): array
    {
        return $this->responsibilities ?? [];
    }

    public function getSkillsRequired(): array
    {
        return $this->skills_required ?? [];
    }

    public function getEducationRequired(): array
    {
        return $this->education_required ?? [];
    }

    public function getExperienceRequired(): int
    {
        return $this->experience_required ?? 0;
    }

    public function getLevelHierarchy(): int
    {
        return $this->level->getHierarchyLevel();
    }

    public function requiresManagement(): bool
    {
        return $this->level->isManagement();
    }

    public function canPromoteTo(PositionLevel $newLevel): bool
    {
        return $newLevel->getHierarchyLevel() > $this->level->getHierarchyLevel();
    }

    public function getPromotionPath(): array
    {
        $currentLevel = $this->level->getHierarchyLevel();
        $promotionLevels = [];

        foreach (PositionLevel::cases() as $level) {
            if ($level->getHierarchyLevel() > $currentLevel) {
                $promotionLevels[] = $level;
            }
        }

        return $promotionLevels;
    }
}
