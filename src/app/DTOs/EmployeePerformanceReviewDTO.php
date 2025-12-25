<?php

namespace App\DTOs;

use App\Models\Employee;
use App\Models\EmployeePerformanceReview;
use App\Models\User;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class EmployeePerformanceReviewDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $employee_id,
        public int $reviewer_id,
        public string $review_period_start,
        public string $review_period_end,
        public string $review_date,
        public ?string $next_review_date,
        public float $overall_rating,
        public float $performance_score,
        public ?array $goals_achieved = [],
        public ?array $goals_missed = [],
        public ?string $strengths = null,
        public ?string $areas_for_improvement = null,
        public ?string $recommendations = null,
        public ?string $employee_comments = null,
        public ?string $reviewer_comments = null,
        public string $status = 'draft',
        public bool $is_approved = false,
        public ?int $approved_by = null,
        public ?Carbon $approved_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
        // Relationships
        public ?Employee $employee = null,
        public ?User $reviewer = null,
        public ?User $approver = null,
    ) {}

    public static function fromModel(EmployeePerformanceReview $model): self
    {
        return new self(
            id: $model->id,
            employee_id: $model->employee_id,
            reviewer_id: $model->reviewer_id,
            review_period_start: $model->review_period_start,
            review_period_end: $model->review_period_end,
            review_date: $model->review_date,
            next_review_date: $model->next_review_date,
            overall_rating: $model->overall_rating,
            performance_score: $model->performance_score,
            goals_achieved: $model->goals_achieved,
            goals_missed: $model->goals_missed,
            strengths: $model->strengths,
            areas_for_improvement: $model->areas_for_improvement,
            recommendations: $model->recommendations,
            employee_comments: $model->employee_comments,
            reviewer_comments: $model->reviewer_comments,
            status: $model->status,
            is_approved: $model->is_approved,
            approved_by: $model->approved_by,
            approved_at: $model->approved_at,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
            deleted_at: $model->deleted_at,
            employee: $model->employee,
            reviewer: $model->reviewer,
            approver: $model->approver,
        );
    }

    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'reviewer_id' => ['required', 'integer', 'exists:users,id'],
            'review_period_start' => ['required', 'date', 'before:review_period_end'],
            'review_period_end' => ['required', 'date', 'after:review_period_start'],
            'review_date' => ['required', 'date', 'between:review_period_start,review_period_end'],
            'next_review_date' => ['nullable', 'date', 'after:review_date'],
            'overall_rating' => ['required', 'numeric', 'min:1.0', 'max:5.0'],
            'performance_score' => ['required', 'numeric', 'min:0.0', 'max:100.0'],
            'goals_achieved' => ['nullable', 'array'],
            'goals_missed' => ['nullable', 'array'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'areas_for_improvement' => ['nullable', 'string', 'max:1000'],
            'recommendations' => ['nullable', 'string', 'max:1000'],
            'employee_comments' => ['nullable', 'string', 'max:1000'],
            'reviewer_comments' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,submitted,pending_approval,approved,rejected,overdue'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'Selected employee does not exist.',
            'reviewer_id.required' => 'Reviewer ID is required.',
            'reviewer_id.exists' => 'Selected reviewer does not exist.',
            'review_period_start.required' => 'Review period start date is required.',
            'review_period_start.before' => 'Review period start must be before review period end.',
            'review_period_end.required' => 'Review period end date is required.',
            'review_period_end.after' => 'Review period end must be after review period start.',
            'review_date.required' => 'Review date is required.',
            'review_date.between' => 'Review date must be within the review period.',
            'next_review_date.after' => 'Next review date must be after the current review date.',
            'overall_rating.required' => 'Overall rating is required.',
            'overall_rating.min' => 'Overall rating must be at least 1.0.',
            'overall_rating.max' => 'Overall rating cannot exceed 5.0.',
            'performance_score.required' => 'Performance score is required.',
            'performance_score.min' => 'Performance score must be at least 0.0.',
            'performance_score.max' => 'Performance score cannot exceed 100.0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: draft, submitted, pending_approval, approved, rejected, overdue.',
            'approved_by.exists' => 'Selected approver does not exist.',
        ];
    }

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

    public function getRatingDescription(): string
    {
        return match (true) {
            $this->overall_rating >= 4.5 => 'Excellent',
            $this->overall_rating >= 3.5 => 'Above Average',
            $this->overall_rating >= 2.5 => 'Average',
            $this->overall_rating >= 1.5 => 'Below Average',
            default => 'Poor',
        };
    }

    public function getPerformanceGrade(): string
    {
        return match (true) {
            $this->performance_score >= 90 => 'A',
            $this->performance_score >= 80 => 'B',
            $this->performance_score >= 70 => 'C',
            $this->performance_score >= 60 => 'D',
            default => 'F',
        };
    }

    public function getGoalsAchievementRate(): float
    {
        $totalGoals = count($this->goals_achieved ?? []) + count($this->goals_missed ?? []);

        if ($totalGoals === 0) {
            return 0.0;
        }

        return (count($this->goals_achieved ?? []) / $totalGoals) * 100;
    }
}
