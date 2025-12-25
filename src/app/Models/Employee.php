<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'hire_date',
        'termination_date',
        'position',
        'department',
        'manager_id',
        'salary',
        'hourly_rate',
        'employment_type',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_id',
        'social_security_number',
        'bank_account_number',
        'bank_routing_number',
        'benefits_enrolled',
        'vacation_days_used',
        'vacation_days_total',
        'sick_days_used',
        'sick_days_total',
        'performance_rating',
        'last_review_date',
        'next_review_date',
        'training_completed',
        'certifications',
        'skills',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
        'gender' => Gender::class,
        'employment_type' => EmploymentType::class,
        'status' => EmployeeStatus::class,
        'benefits_enrolled' => 'boolean',
        'salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'performance_rating' => 'decimal:2',
        'training_completed' => 'array',
        'certifications' => 'array',
        'skills' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'social_security_number',
        'bank_account_number',
        'bank_routing_number',
        'tax_id',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getRemainingVacationDaysAttribute(): int
    {
        return $this->vacation_days_total - $this->vacation_days_used;
    }

    public function getRemainingSickDaysAttribute(): int
    {
        return $this->sick_days_total - $this->sick_days_used;
    }

    public function getYearsOfServiceAttribute(): int
    {
        return $this->hire_date->diffInYears(now());
    }

    public function getMonthsOfServiceAttribute(): int
    {
        return $this->hire_date->diffInMonths(now());
    }

    public function getDaysOfServiceAttribute(): int
    {
        return $this->hire_date->diffInDays(now());
    }

    public function getAnnualSalaryAttribute(): float
    {
        if ($this->salary) {
            return $this->salary;
        }

        if ($this->hourly_rate) {
            // Assuming 40 hours per week, 52 weeks per year
            return $this->hourly_rate * 40 * 52;
        }

        return 0;
    }

    public function getMonthlySalaryAttribute(): float
    {
        return $this->annual_salary / 12;
    }

    public function getWeeklySalaryAttribute(): float
    {
        return $this->annual_salary / 52;
    }

    public function getDailySalaryAttribute(): float
    {
        return $this->annual_salary / 260; // Assuming 260 working days per year
    }

    public function getHourlySalaryAttribute(): float
    {
        if ($this->hourly_rate) {
            return $this->hourly_rate;
        }

        if ($this->salary) {
            // Assuming 40 hours per week, 52 weeks per year
            return $this->salary / (40 * 52);
        }

        return 0;
    }

    // Status methods
    public function isActive(): bool
    {
        return $this->status === EmployeeStatus::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === EmployeeStatus::INACTIVE;
    }

    public function isTerminated(): bool
    {
        return $this->status === EmployeeStatus::TERMINATED;
    }

    public function isPending(): bool
    {
        return $this->status === EmployeeStatus::PENDING;
    }

    public function isOnLeave(): bool
    {
        return $this->status === EmployeeStatus::ON_LEAVE;
    }

    public function canWork(): bool
    {
        return $this->status->canWork();
    }

    // Employment type methods
    public function isFullTime(): bool
    {
        return $this->employment_type->isFullTime();
    }

    public function isPartTime(): bool
    {
        return $this->employment_type->isPartTime();
    }

    public function isContract(): bool
    {
        return $this->employment_type->isContract();
    }

    public function isTemporary(): bool
    {
        return $this->employment_type->isTemporary();
    }

    public function isIntern(): bool
    {
        return $this->employment_type->isIntern();
    }

    public function isFreelance(): bool
    {
        return $this->employment_type->isFreelance();
    }

    public function isPermanent(): bool
    {
        return $this->employment_type->isPermanent();
    }

    public function isTemporaryOrContract(): bool
    {
        return $this->employment_type->isTemporaryOrContract();
    }

    // Manager methods
    public function hasManager(): bool
    {
        return ! is_null($this->manager_id);
    }

    public function isManager(): bool
    {
        return $this->subordinates()->exists();
    }

    public function isSubordinateOf(Employee $manager): bool
    {
        return $this->manager_id === $manager->id;
    }

    public function isManagerOf(Employee $subordinate): bool
    {
        return $this->id === $subordinate->manager_id;
    }

    // Performance methods
    public function hasPerformanceRating(): bool
    {
        return ! is_null($this->performance_rating);
    }

    public function isTopPerformer(float $threshold = 4.0): bool
    {
        return $this->hasPerformanceRating() && $this->performance_rating >= $threshold;
    }

    public function needsPerformanceReview(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function hasUpcomingReview(int $daysAhead = 30): bool
    {
        return $this->next_review_date &&
               $this->next_review_date->isFuture() &&
               $this->next_review_date->diffInDays(now()) <= $daysAhead;
    }

    // Time-off methods
    public function hasVacationDays(): bool
    {
        return $this->remaining_vacation_days > 0;
    }

    public function hasSickDays(): bool
    {
        return $this->remaining_sick_days > 0;
    }

    public function hasLowVacationDays(int $threshold = 5): bool
    {
        return $this->remaining_vacation_days <= $threshold;
    }

    public function hasLowSickDays(int $threshold = 2): bool
    {
        return $this->remaining_sick_days <= $threshold;
    }

    // Skills and certifications methods
    public function hasSkill(string $skill): bool
    {
        return is_array($this->skills) && in_array($skill, $this->skills);
    }

    public function hasCertification(string $certification): bool
    {
        return is_array($this->certifications) && in_array($certification, $this->certifications);
    }

    public function hasCompletedTraining(string $training): bool
    {
        return is_array($this->training_completed) && in_array($training, $this->training_completed);
    }

    // Address methods
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function hasAddress(): bool
    {
        return ! empty($this->address) || ! empty($this->city);
    }

    // Emergency contact methods
    public function hasEmergencyContact(): bool
    {
        return ! empty($this->emergency_contact_name) && ! empty($this->emergency_contact_phone);
    }

    // Banking methods
    public function hasBankingInfo(): bool
    {
        return ! empty($this->bank_account_number) && ! empty($this->bank_routing_number);
    }

    // Tax methods
    public function hasTaxInfo(): bool
    {
        return ! empty($this->tax_id) || ! empty($this->social_security_number);
    }

    // Benefits methods
    public function isEnrolledInBenefits(): bool
    {
        return $this->benefits_enrolled;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', EmployeeStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', EmployeeStatus::INACTIVE);
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', EmployeeStatus::TERMINATED);
    }

    public function scopePending($query)
    {
        return $query->where('status', EmployeeStatus::PENDING);
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', EmployeeStatus::ON_LEAVE);
    }

    public function scopeFullTime($query)
    {
        return $query->where('employment_type', EmploymentType::FULL_TIME);
    }

    public function scopePartTime($query)
    {
        return $query->where('employment_type', EmploymentType::PART_TIME);
    }

    public function scopeContract($query)
    {
        return $query->where('employment_type', EmploymentType::CONTRACT);
    }

    public function scopeTemporary($query)
    {
        return $query->where('employment_type', EmploymentType::TEMPORARY);
    }

    public function scopeIntern($query)
    {
        return $query->where('employment_type', EmploymentType::INTERN);
    }

    public function scopeFreelance($query)
    {
        return $query->where('employment_type', EmploymentType::FREELANCE);
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByManager($query, int $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeManagers($query)
    {
        return $query->whereHas('subordinates');
    }

    public function scopeSubordinates($query)
    {
        return $query->whereNotNull('manager_id');
    }

    public function scopeTopPerformers($query, float $threshold = 4.0)
    {
        return $query->where('performance_rating', '>=', $threshold);
    }

    public function scopeWithUpcomingReviews($query, int $daysAhead = 30)
    {
        return $query->where('next_review_date', '<=', now()->addDays($daysAhead));
    }

    public function scopeWithLowVacationDays($query, int $threshold = 5)
    {
        return $query->whereRaw('(vacation_days_total - vacation_days_used) <= ?', [$threshold]);
    }

    public function scopeWithLowSickDays($query, int $threshold = 2)
    {
        return $query->whereRaw('(sick_days_total - sick_days_used) <= ?', [$threshold]);
    }

    public function scopeBySalaryRange($query, float $minSalary, float $maxSalary)
    {
        return $query->whereBetween('salary', [$minSalary, $maxSalary]);
    }

    public function scopeByPerformanceRange($query, float $minRating, float $maxRating)
    {
        return $query->whereBetween('performance_rating', [$minRating, $maxRating]);
    }

    public function scopeByHireDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('hire_date', [$startDate, $endDate]);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('employee_number', 'like', "%{$search}%")
                ->orWhere('position', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%");
        });
    }
}
