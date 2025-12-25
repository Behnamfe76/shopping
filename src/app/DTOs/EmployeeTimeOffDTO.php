<?php

namespace Fereydooni\Shopping\app\DTOs;

use Carbon\Carbon;
use Fereydooni\Shopping\app\Enums\TimeOffStatus;
use Fereydooni\Shopping\app\Enums\TimeOffType;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Time;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeTransformer;

class EmployeeTimeOffDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType, Min(1)]
        public int $employee_id,

        #[Required, IntegerType, Min(1)]
        public int $user_id,

        #[Required, StringType, In(TimeOffType::class)]
        public TimeOffType $time_off_type,

        #[Required, Date, After('today')]
        public string $start_date,

        #[Required, Date, After('start_date')]
        public string $end_date,

        #[Nullable, Time]
        public ?string $start_time,

        #[Nullable, Time]
        public ?string $end_time,

        #[Nullable, Numeric, Min(0)]
        public ?float $total_hours,

        #[Nullable, Numeric, Min(0)]
        public ?float $total_days,

        #[Required, StringType, Min(3), Max(500)]
        public string $reason,

        #[Nullable, StringType, Max(1000)]
        public ?string $description = null,

        #[Required, StringType, In(TimeOffStatus::class)]
        public TimeOffStatus $status = TimeOffStatus::PENDING,

        #[Nullable, IntegerType, Min(1)]
        public ?int $approved_by = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $approved_at = null,

        #[Nullable, IntegerType, Min(1)]
        public ?int $rejected_by = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $rejected_at = null,

        #[Nullable, StringType, Max(500)]
        public ?string $rejection_reason = null,

        #[Boolean]
        public bool $is_half_day = false,

        #[Boolean]
        public bool $is_urgent = false,

        #[Nullable, ArrayType]
        public ?array $attachments = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $created_at = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $updated_at = null,

        // Relationships
        #[Nullable]
        public ?EmployeeDTO $employee = null,

        #[Nullable]
        public ?UserDTO $user = null,

        #[Nullable]
        public ?UserDTO $approver = null,

        #[Nullable]
        public ?UserDTO $rejector = null,
    ) {}

    public static function fromModel(EmployeeTimeOff $timeOff): self
    {
        return new self(
            id: $timeOff->id,
            employee_id: $timeOff->employee_id,
            user_id: $timeOff->user_id,
            time_off_type: $timeOff->time_off_type,
            start_date: $timeOff->start_date->format('Y-m-d'),
            end_date: $timeOff->end_date->format('Y-m-d'),
            start_time: $timeOff->start_time?->format('H:i:s'),
            end_time: $timeOff->end_time?->format('H:i:s'),
            total_hours: $timeOff->total_hours,
            total_days: $timeOff->total_days,
            reason: $timeOff->reason,
            description: $timeOff->description,
            status: $timeOff->status,
            approved_by: $timeOff->approved_by,
            approved_at: $timeOff->approved_at?->format('Y-m-d H:i:s'),
            rejected_by: $timeOff->rejected_by,
            rejected_at: $timeOff->rejected_at?->format('Y-m-d H:i:s'),
            rejection_reason: $timeOff->rejection_reason,
            is_half_day: $timeOff->is_half_day,
            is_urgent: $timeOff->is_urgent,
            attachments: $timeOff->attachments,
            created_at: $timeOff->created_at?->format('Y-m-d H:i:s'),
            updated_at: $timeOff->updated_at?->format('Y-m-d H:i:s'),
            employee: $timeOff->employee ? EmployeeDTO::fromModel($timeOff->employee) : null,
            user: $timeOff->user ? UserDTO::fromModel($timeOff->user) : null,
            approver: $timeOff->approver ? UserDTO::fromModel($timeOff->approver) : null,
            rejector: $timeOff->rejector ? UserDTO::fromModel($timeOff->rejector) : null,
        );
    }

    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'min:1', 'exists:employees,id'],
            'user_id' => ['required', 'integer', 'min:1', 'exists:users,id'],
            'time_off_type' => ['required', 'string', 'in:'.implode(',', array_column(TimeOffType::cases(), 'value'))],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'start_time' => ['nullable', 'date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s', 'after:start_time'],
            'total_hours' => ['nullable', 'numeric', 'min:0'],
            'total_days' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(TimeOffStatus::cases(), 'value'))],
            'approved_by' => ['nullable', 'integer', 'min:1', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
            'rejected_by' => ['nullable', 'integer', 'min:1', 'exists:users,id'],
            'rejected_at' => ['nullable', 'date'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
            'is_half_day' => ['boolean'],
            'is_urgent' => ['boolean'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'Selected employee does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'time_off_type.required' => 'Time-off type is required.',
            'time_off_type.in' => 'Invalid time-off type selected.',
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'start_time.date_format' => 'Start time must be in HH:MM:SS format.',
            'end_time.date_format' => 'End time must be in HH:MM:SS format.',
            'end_time.after' => 'End time must be after start time.',
            'total_hours.min' => 'Total hours cannot be negative.',
            'total_days.min' => 'Total days cannot be negative.',
            'reason.required' => 'Reason is required.',
            'reason.min' => 'Reason must be at least 3 characters.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'approved_by.exists' => 'Selected approver does not exist.',
            'rejected_by.exists' => 'Selected rejector does not exist.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 500 characters.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.string' => 'Each attachment must be a string.',
            'attachments.*.max' => 'Each attachment path cannot exceed 255 characters.',
        ];
    }

    public function calculateTotalDays(): float
    {
        if ($this->total_days !== null) {
            return $this->total_days;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        $totalDays = $startDate->diffInDays($endDate) + 1;

        if ($this->is_half_day) {
            $totalDays = $totalDays * 0.5;
        }

        return $totalDays;
    }

    public function calculateTotalHours(): float
    {
        if ($this->total_hours !== null) {
            return $this->total_hours;
        }

        $totalDays = $this->calculateTotalDays();

        // Assuming 8-hour workday
        return $totalDays * 8;
    }

    public function isOverlapping(string $startDate, string $endDate): bool
    {
        $requestStart = Carbon::parse($this->start_date);
        $requestEnd = Carbon::parse($this->end_date);
        $checkStart = Carbon::parse($startDate);
        $checkEnd = Carbon::parse($endDate);

        return $requestStart->lte($checkEnd) && $requestEnd->gte($checkStart);
    }

    public function isPending(): bool
    {
        return $this->status === TimeOffStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === TimeOffStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === TimeOffStatus::REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TimeOffStatus::CANCELLED;
    }

    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }

    public function requiresApproval(): bool
    {
        return $this->time_off_type->requiresApproval();
    }

    public function isPaid(): bool
    {
        return $this->time_off_type->isPaid();
    }

    public function getDurationInDays(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $startDate->diffInDays($endDate) + 1;
    }

    public function getDurationInHours(): int
    {
        return $this->getDurationInDays() * 8; // Assuming 8-hour workday
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'user_id' => $this->user_id,
            'time_off_type' => $this->time_off_type->value,
            'time_off_type_label' => $this->time_off_type->label(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_hours' => $this->total_hours,
            'total_days' => $this->total_days,
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'rejected_by' => $this->rejected_by,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'is_half_day' => $this->is_half_day,
            'is_urgent' => $this->is_urgent,
            'attachments' => $this->attachments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'duration_days' => $this->getDurationInDays(),
            'duration_hours' => $this->getDurationInHours(),
            'can_be_modified' => $this->canBeModified(),
            'requires_approval' => $this->requiresApproval(),
            'is_paid' => $this->isPaid(),
            'employee' => $this->employee?->toArray(),
            'user' => $this->user?->toArray(),
            'approver' => $this->approver?->toArray(),
            'rejector' => $this->rejector?->toArray(),
        ];
    }
}
