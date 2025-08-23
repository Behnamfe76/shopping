<?php

namespace Fereydooni\Shopping\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Fereydooni\Shopping\Enums\TrainingType;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingMethod;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Carbon\Carbon;

class EmployeeTrainingDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $employee_id,

        #[Required, StringType, In(['technical', 'soft_skills', 'compliance', 'safety', 'leadership', 'product', 'other'])]
        public string $training_type,

        #[Required, StringType, Min(1), Max(255)]
        public string $training_name,

        #[Required, StringType, Min(1), Max(255)]
        public string $provider,

        #[Nullable, StringType, Max(1000)]
        public ?string $description,

        #[Nullable, Date]
        public ?string $start_date,

        #[Nullable, Date]
        public ?string $end_date,

        #[Nullable, Date]
        public ?string $completion_date,

        #[Required, StringType, In(['not_started', 'in_progress', 'completed', 'failed', 'cancelled'])]
        public string $status,

        #[Nullable, Numeric, Min(0), Max(100)]
        public ?float $score,

        #[Nullable, StringType, Max(10)]
        public ?string $grade,

        #[Nullable, StringType, Max(100)]
        public ?string $certificate_number,

        #[Nullable, Url, Max(500)]
        public ?string $certificate_url,

        #[Nullable, Numeric, Min(0)]
        public ?float $hours_completed,

        #[Nullable, Numeric, Min(0)]
        public ?float $total_hours,

        #[Nullable, Numeric, Min(0)]
        public ?float $cost,

        #[Required, BooleanType]
        public bool $is_mandatory,

        #[Required, BooleanType]
        public bool $is_certification,

        #[Required, BooleanType]
        public bool $is_renewable,

        #[Nullable, Date]
        public ?string $renewal_date,

        #[Nullable, Date]
        public ?string $expiry_date,

        #[Nullable, StringType, Max(255)]
        public ?string $instructor,

        #[Nullable, StringType, Max(255)]
        public ?string $location,

        #[Required, StringType, In(['in_person', 'online', 'hybrid', 'self_study', 'workshop', 'seminar'])]
        public string $training_method,

        #[Nullable, ArrayType]
        public ?array $materials,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes,

        #[Nullable, ArrayType]
        public ?array $attachments,

        #[Nullable, StringType, Max(500)]
        public ?string $failure_reason,

        #[Nullable, StringType, Max(500)]
        public ?string $cancellation_reason,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $created_at,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $updated_at,

        #[Nullable]
        public ?string $deleted_at,

        // Computed properties
        #[Nullable]
        public ?float $completion_percentage,

        #[Nullable]
        public ?float $remaining_hours,

        #[Nullable]
        public ?bool $is_expired,

        #[Nullable]
        public ?bool $is_expiring_soon,

        #[Nullable]
        public ?bool $can_be_renewed,

        #[Nullable]
        public ?bool $is_overdue,

        #[Nullable]
        public ?int $days_until_expiry,

        #[Nullable]
        public ?int $days_until_start,

        #[Nullable]
        public ?int $days_until_end,

        // Employee relationship
        #[Nullable]
        public ?array $employee,
    ) {
    }

    /**
     * Create DTO from model.
     */
    public static function fromModel(EmployeeTraining $training): self
    {
        return new self(
            id: $training->id,
            employee_id: $training->employee_id,
            training_type: $training->training_type->value,
            training_name: $training->training_name,
            provider: $training->provider,
            description: $training->description,
            start_date: $training->start_date?->toDateString(),
            end_date: $training->end_date?->toDateString(),
            completion_date: $training->completion_date?->toDateString(),
            status: $training->status->value,
            score: $training->score,
            grade: $training->grade,
            certificate_number: $training->certificate_number,
            certificate_url: $training->certificate_url,
            hours_completed: $training->hours_completed,
            total_hours: $training->total_hours,
            cost: $training->cost,
            is_mandatory: $training->is_mandatory,
            is_certification: $training->is_certification,
            is_renewable: $training->is_renewable,
            renewal_date: $training->renewal_date?->toDateString(),
            expiry_date: $training->expiry_date?->toDateString(),
            instructor: $training->instructor,
            location: $training->location,
            training_method: $training->training_method->value,
            materials: $training->materials,
            notes: $training->notes,
            attachments: $training->attachments,
            failure_reason: $training->failure_reason,
            cancellation_reason: $training->cancellation_reason,
            created_at: $training->created_at?->toISOString(),
            updated_at: $training->updated_at?->toISOString(),
            deleted_at: $training->deleted_at?->toISOString(),
            completion_percentage: $training->completion_percentage,
            remaining_hours: $training->remaining_hours,
            is_expired: $training->isExpired(),
            is_expiring_soon: $training->isExpiringSoon(),
            can_be_renewed: $training->canBeRenewed(),
            is_overdue: $training->isOverdue(),
            days_until_expiry: $training->days_until_expiry,
            days_until_start: $training->days_until_start,
            days_until_end: $training->days_until_end,
            employee: $training->employee ? [
                'id' => $training->employee->id,
                'first_name' => $training->employee->first_name,
                'last_name' => $training->employee->last_name,
                'email' => $training->employee->email,
                'employee_id' => $training->employee->employee_id,
            ] : null,
        );
    }

    /**
     * Get validation rules for creating training.
     */
    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'training_type' => ['required', 'string', 'in:' . implode(',', TrainingType::values())],
            'training_name' => ['required', 'string', 'min:1', 'max:255'],
            'provider' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:' . implode(',', TrainingStatus::values())],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade' => ['nullable', 'string', 'max:10'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'certificate_url' => ['nullable', 'url', 'max:500'],
            'hours_completed' => ['nullable', 'numeric', 'min:0'],
            'total_hours' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'is_mandatory' => ['required', 'boolean'],
            'is_certification' => ['required', 'boolean'],
            'is_renewable' => ['required', 'boolean'],
            'renewal_date' => ['nullable', 'date', 'after:today'],
            'expiry_date' => ['nullable', 'date', 'after:today'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'training_method' => ['required', 'string', 'in:' . implode(',', TrainingMethod::values())],
            'materials' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],
            'failure_reason' => ['nullable', 'string', 'max:500'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get validation messages.
     */
    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'Selected employee does not exist.',
            'training_type.required' => 'Training type is required.',
            'training_type.in' => 'Invalid training type selected.',
            'training_name.required' => 'Training name is required.',
            'training_name.min' => 'Training name must be at least 1 character.',
            'training_name.max' => 'Training name cannot exceed 255 characters.',
            'provider.required' => 'Training provider is required.',
            'provider.min' => 'Provider name must be at least 1 character.',
            'provider.max' => 'Provider name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'completion_date.date' => 'Completion date must be a valid date.',
            'completion_date.after_or_equal' => 'Completion date must be after or equal to start date.',
            'status.required' => 'Training status is required.',
            'status.in' => 'Invalid training status selected.',
            'score.numeric' => 'Score must be a number.',
            'score.min' => 'Score cannot be negative.',
            'score.max' => 'Score cannot exceed 100.',
            'grade.max' => 'Grade cannot exceed 10 characters.',
            'certificate_number.max' => 'Certificate number cannot exceed 100 characters.',
            'certificate_url.url' => 'Certificate URL must be a valid URL.',
            'certificate_url.max' => 'Certificate URL cannot exceed 500 characters.',
            'hours_completed.numeric' => 'Hours completed must be a number.',
            'hours_completed.min' => 'Hours completed cannot be negative.',
            'total_hours.numeric' => 'Total hours must be a number.',
            'total_hours.min' => 'Total hours cannot be negative.',
            'cost.numeric' => 'Cost must be a number.',
            'cost.min' => 'Cost cannot be negative.',
            'is_mandatory.required' => 'Mandatory flag is required.',
            'is_certification.required' => 'Certification flag is required.',
            'is_renewable.required' => 'Renewable flag is required.',
            'renewal_date.date' => 'Renewal date must be a valid date.',
            'renewal_date.after' => 'Renewal date must be in the future.',
            'expiry_date.date' => 'Expiry date must be a valid date.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'instructor.max' => 'Instructor name cannot exceed 255 characters.',
            'location.max' => 'Location cannot exceed 255 characters.',
            'training_method.required' => 'Training method is required.',
            'training_method.in' => 'Invalid training method selected.',
            'materials.array' => 'Materials must be an array.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'attachments.array' => 'Attachments must be an array.',
            'failure_reason.max' => 'Failure reason cannot exceed 500 characters.',
            'cancellation_reason.max' => 'Cancellation reason cannot exceed 500 characters.',
        ];
    }

    /**
     * Get training type label.
     */
    public function getTrainingTypeLabel(): string
    {
        return TrainingType::from($this->training_type)->label();
    }

    /**
     * Get training status label.
     */
    public function getTrainingStatusLabel(): string
    {
        return TrainingStatus::from($this->status)->label();
    }

    /**
     * Get training method label.
     */
    public function getTrainingMethodLabel(): string
    {
        return TrainingMethod::from($this->training_method)->label();
    }

    /**
     * Check if training is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [TrainingStatus::NOT_STARTED->value, TrainingStatus::IN_PROGRESS->value]);
    }

    /**
     * Check if training is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === TrainingStatus::COMPLETED->value;
    }

    /**
     * Check if training is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === TrainingStatus::FAILED->value;
    }

    /**
     * Check if training is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === TrainingStatus::CANCELLED->value;
    }
}
