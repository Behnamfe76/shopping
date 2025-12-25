<?php

namespace App\Models;

use App\Enums\EmployeePerformanceReviewRating;
use App\Enums\EmployeePerformanceReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePerformanceReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period_start',
        'review_period_end',
        'review_date',
        'next_review_date',
        'overall_rating',
        'performance_score',
        'goals_achieved',
        'goals_missed',
        'strengths',
        'areas_for_improvement',
        'recommendations',
        'employee_comments',
        'reviewer_comments',
        'status',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'review_period_start' => 'date',
        'review_period_end' => 'date',
        'review_date' => 'date',
        'next_review_date' => 'date',
        'overall_rating' => 'decimal:1',
        'performance_score' => 'decimal:1',
        'goals_achieved' => 'array',
        'goals_missed' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $dates = [
        'review_period_start',
        'review_period_end',
        'review_date',
        'next_review_date',
        'approved_at',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByReviewer($query, int $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRatingRange($query, float $minRating, float $maxRating)
    {
        return $query->whereBetween('overall_rating', [$minRating, $maxRating]);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('review_date', [$startDate, $endDate]);
    }

    public function scopeByReviewPeriod($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('review_period_start', [$startDate, $endDate])
            ->orWhereBetween('review_period_end', [$startDate, $endDate]);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUpcoming($query, ?string $date = null)
    {
        $date = $date ?: now()->toDateString();

        return $query->where('next_review_date', '>=', $date);
    }

    // Accessors
    public function getRatingDescriptionAttribute(): string
    {
        return EmployeePerformanceReviewRating::fromFloat($this->overall_rating)->label();
    }

    public function getPerformanceGradeAttribute(): string
    {
        return match (true) {
            $this->performance_score >= 90 => 'A',
            $this->performance_score >= 80 => 'B',
            $this->performance_score >= 70 => 'C',
            $this->performance_score >= 60 => 'D',
            default => 'F',
        };
    }

    public function getGoalsAchievementRateAttribute(): float
    {
        $totalGoals = count($this->goals_achieved ?? []) + count($this->goals_missed ?? []);

        if ($totalGoals === 0) {
            return 0.0;
        }

        return (count($this->goals_achieved ?? []) / $totalGoals) * 100;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function getDaysUntilNextReviewAttribute(): int
    {
        if (! $this->next_review_date) {
            return 0;
        }

        return now()->diffInDays($this->next_review_date, false);
    }

    // Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft() && ! empty($this->overall_rating) && ! empty($this->performance_score);
    }

    public function canBeApproved(): bool
    {
        return $this->isPendingApproval();
    }

    public function canBeRejected(): bool
    {
        return $this->isPendingApproval();
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return EmployeePerformanceReviewStatus::from($this->status)->canTransitionTo(
            EmployeePerformanceReviewStatus::from($newStatus)
        );
    }

    public function submit(): bool
    {
        if (! $this->canBeSubmitted()) {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'review_date' => now(),
        ]);

        return true;
    }

    public function submitForApproval(): bool
    {
        if (! $this->isSubmitted()) {
            return false;
        }

        $this->update(['status' => 'pending_approval']);

        return true;
    }

    public function approve(int $approvedBy): bool
    {
        if (! $this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function reject(int $rejectedBy, ?string $reason = null): bool
    {
        if (! $this->canBeRejected()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'is_approved' => false,
            'reviewer_comments' => $reason ? $this->reviewer_comments."\n\nRejection Reason: ".$reason : $this->reviewer_comments,
        ]);

        return true;
    }

    public function markAsOverdue(): bool
    {
        if ($this->isFinal()) {
            return false;
        }

        $this->update(['status' => 'overdue']);

        return true;
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['approved', 'rejected']);
    }

    public function isEditable(): bool
    {
        return EmployeePerformanceReviewStatus::from($this->status)->isEditable();
    }

    public function getStatusColor(): string
    {
        return EmployeePerformanceReviewStatus::from($this->status)->color();
    }

    public function getStatusIcon(): string
    {
        return EmployeePerformanceReviewStatus::from($this->status)->icon();
    }

    public function getRatingColor(): string
    {
        return EmployeePerformanceReviewRating::fromFloat($this->overall_rating)->color();
    }

    public function getRatingIcon(): string
    {
        return EmployeePerformanceReviewRating::fromFloat($this->overall_rating)->icon();
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return collect(EmployeePerformanceReviewStatus::cases())->mapWithKeys(function ($status) {
            return [$status->value => $status->label()];
        })->toArray();
    }

    public static function getRatingOptions(): array
    {
        return EmployeePerformanceReviewRating::getRange();
    }

    public static function calculateAverageRating(int $employeeId): float
    {
        return static::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->avg('overall_rating') ?? 0.0;
    }

    public static function getOverdueReviews(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('next_review_date', '<', now())
            ->whereNotIn('status', ['approved', 'rejected'])
            ->get();
    }

    public static function getPendingApprovalCount(): int
    {
        return static::where('status', 'pending_approval')->count();
    }

    public static function getCompletedReviewsCount(?string $startDate = null, ?string $endDate = null): int
    {
        $query = static::whereIn('status', ['approved', 'rejected']);

        if ($startDate && $endDate) {
            $query->whereBetween('review_date', [$startDate, $endDate]);
        }

        return $query->count();
    }
}
