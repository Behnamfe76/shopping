<?php

namespace App\DTOs;

use App\Models\EmployeeSalaryHistory;
use App\Models\Employee;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Integer;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Carbon\Carbon;

class EmployeeSalaryHistoryDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, Integer, Min(1)]
        public int $employee_id,

        #[Required, Numeric, Min(0)]
        public float $old_salary,

        #[Required, Numeric, Min(0)]
        public float $new_salary,

        #[Nullable, Numeric]
        public ?float $change_amount,

        #[Nullable, Numeric, Min(-100), Max(1000)]
        public ?float $change_percentage,

        #[Required, StringType, In('promotion', 'merit', 'cost_of_living', 'market_adjustment', 'demotion', 'other')]
        public string $change_type,

        #[Required, Date]
        public string $effective_date,

        #[Nullable, StringType, Max(500)]
        public ?string $reason,

        #[Nullable, Integer, Min(1)]
        public ?int $approved_by,

        #[Nullable, Date]
        public ?string $approved_at,

        #[Boolean]
        public bool $is_retroactive = false,

        #[Nullable, Date]
        public ?string $retroactive_start_date,

        #[Nullable, Date]
        public ?string $retroactive_end_date,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes,

        #[Nullable]
        public ?array $attachments,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $created_at = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $updated_at = null,

        // Relationships
        public ?Employee $employee = null,
        public ?User $approver = null
    ) {
        // Auto-calculate change amount if not provided
        if ($this->change_amount === null) {
            $this->change_amount = $this->new_salary - $this->old_salary;
        }

        // Auto-calculate change percentage if not provided
        if ($this->change_percentage === null && $this->old_salary > 0) {
            $this->change_percentage = (($this->new_salary - $this->old_salary) / $this->old_salary) * 100;
        }
    }

    /**
     * Create DTO from EmployeeSalaryHistory model
     */
    public static function fromModel(EmployeeSalaryHistory $salaryHistory): self
    {
        return new self(
            id: $salaryHistory->id,
            employee_id: $salaryHistory->employee_id,
            old_salary: $salaryHistory->old_salary,
            new_salary: $salaryHistory->new_salary,
            change_amount: $salaryHistory->change_amount,
            change_percentage: $salaryHistory->change_percentage,
            change_type: $salaryHistory->change_type,
            effective_date: $salaryHistory->effective_date,
            reason: $salaryHistory->reason,
            approved_by: $salaryHistory->approved_by,
            approved_at: $salaryHistory->approved_at,
            is_retroactive: $salaryHistory->is_retroactive,
            retroactive_start_date: $salaryHistory->retroactive_start_date,
            retroactive_end_date: $salaryHistory->retroactive_end_date,
            notes: $salaryHistory->notes,
            attachments: $salaryHistory->attachments,
            created_at: $salaryHistory->created_at,
            updated_at: $salaryHistory->updated_at,
            employee: $salaryHistory->employee,
            approver: $salaryHistory->approver
        );
    }

    /**
     * Get validation rules
     */
    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'min:1', 'exists:employees,id'],
            'old_salary' => ['required', 'numeric', 'min:0'],
            'new_salary' => ['required', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric'],
            'change_percentage' => ['nullable', 'numeric', 'min:-100', 'max:1000'],
            'change_type' => ['required', 'string', 'in:promotion,merit,cost_of_living,market_adjustment,demotion,other'],
            'effective_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:500'],
            'approved_by' => ['nullable', 'integer', 'min:1', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
            'is_retroactive' => ['boolean'],
            'retroactive_start_date' => ['nullable', 'date', 'required_if:is_retroactive,true'],
            'retroactive_end_date' => ['nullable', 'date', 'required_if:is_retroactive,true', 'after:retroactive_start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],
        ];
    }

    /**
     * Get validation messages
     */
    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'The selected employee does not exist.',
            'old_salary.required' => 'Old salary is required.',
            'old_salary.min' => 'Old salary must be at least 0.',
            'new_salary.required' => 'New salary is required.',
            'new_salary.min' => 'New salary must be at least 0.',
            'change_type.required' => 'Change type is required.',
            'change_type.in' => 'Change type must be one of: promotion, merit, cost_of_living, market_adjustment, demotion, other.',
            'effective_date.required' => 'Effective date is required.',
            'effective_date.date' => 'Effective date must be a valid date.',
            'retroactive_start_date.required_if' => 'Retroactive start date is required when retroactive is enabled.',
            'retroactive_end_date.after' => 'Retroactive end date must be after start date.',
        ];
    }

    /**
     * Check if salary change is an increase
     */
    public function isIncrease(): bool
    {
        return $this->change_amount > 0;
    }

    /**
     * Check if salary change is a decrease
     */
    public function isDecrease(): bool
    {
        return $this->change_amount < 0;
    }

    /**
     * Check if salary change is approved
     */
    public function isApproved(): bool
    {
        return $this->approved_by !== null && $this->approved_at !== null;
    }

    /**
     * Check if salary change is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approved_by === null;
    }

    /**
     * Get formatted change amount
     */
    public function getFormattedChangeAmount(): string
    {
        $prefix = $this->isIncrease() ? '+' : '';
        return $prefix . number_format($this->change_amount, 2);
    }

    /**
     * Get formatted change percentage
     */
    public function getFormattedChangePercentage(): string
    {
        if ($this->change_percentage === null) {
            return 'N/A';
        }

        $prefix = $this->isIncrease() ? '+' : '';
        return $prefix . number_format($this->change_percentage, 2) . '%';
    }

    /**
     * Get effective date as Carbon instance
     */
    public function getEffectiveDate(): Carbon
    {
        return Carbon::parse($this->effective_date);
    }

    /**
     * Check if effective date is in the future
     */
    public function isFutureEffective(): bool
    {
        return $this->getEffectiveDate()->isFuture();
    }

    /**
     * Check if effective date is in the past
     */
    public function isPastEffective(): bool
    {
        return $this->getEffectiveDate()->isPast();
    }

    /**
     * Get retroactive period in days
     */
    public function getRetroactivePeriodDays(): ?int
    {
        if (!$this->is_retroactive || !$this->retroactive_start_date || !$this->retroactive_end_date) {
            return null;
        }

        return Carbon::parse($this->retroactive_start_date)
            ->diffInDays(Carbon::parse($this->retroactive_end_date)) + 1;
    }

    /**
     * Calculate retroactive adjustment amount
     */
    public function getRetroactiveAdjustmentAmount(): ?float
    {
        if (!$this->is_retroactive) {
            return null;
        }

        $days = $this->getRetroactivePeriodDays();
        if ($days === null) {
            return null;
        }

        // Calculate daily rate difference
        $dailyRateDifference = $this->change_amount / 365;

        return $dailyRateDifference * $days;
    }
}
