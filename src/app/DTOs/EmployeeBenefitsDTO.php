<?php

namespace Fereydooni\Shopping\app\DTOs;

use Carbon\Carbon;
use Fereydooni\Shopping\app\Enums\BenefitStatus;
use Fereydooni\Shopping\app\Enums\BenefitType;
use Fereydooni\Shopping\app\Enums\CoverageLevel;
use Fereydooni\Shopping\app\Enums\NetworkType;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
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
use Spatie\LaravelData\Data;

class EmployeeBenefitsDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $employee_id,

        #[Required, StringType, In(BenefitType::values())]
        public string $benefit_type,

        #[Required, StringType, Max(255)]
        public string $benefit_name,

        #[Required, StringType, Max(255)]
        public string $provider,

        #[Nullable, StringType, Max(100)]
        public ?string $plan_id,

        #[Nullable, Date]
        public ?string $enrollment_date,

        #[Required, Date]
        public string $effective_date,

        #[Nullable, Date]
        public ?string $end_date,

        #[Required, StringType, In(BenefitStatus::values())]
        public string $status,

        #[Required, StringType, In(CoverageLevel::values())]
        public string $coverage_level,

        #[Required, Numeric, Min(0)]
        public float $premium_amount,

        #[Required, Numeric, Min(0)]
        public float $employee_contribution,

        #[Required, Numeric, Min(0)]
        public float $employer_contribution,

        #[Required, Numeric, Min(0)]
        public float $total_cost,

        #[Nullable, Numeric, Min(0)]
        public ?float $deductible,

        #[Nullable, Numeric, Min(0)]
        public ?float $co_pay,

        #[Nullable, Numeric, Min(0)]
        public ?float $co_insurance,

        #[Nullable, Numeric, Min(0)]
        public ?float $max_out_of_pocket,

        #[Required, StringType, In(NetworkType::values())]
        public string $network_type,

        #[Required, Boolean]
        public bool $is_active,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes = null,

        #[Nullable, ArrayType]
        public ?array $documents = null,

        #[Nullable]
        public ?string $created_at = null,

        #[Nullable]
        public ?string $updated_at = null,
    ) {}

    /**
     * Create DTO from EmployeeBenefits model
     */
    public static function fromModel(EmployeeBenefits $benefit): self
    {
        return new self(
            id: $benefit->id,
            employee_id: $benefit->employee_id,
            benefit_type: $benefit->benefit_type->value,
            benefit_name: $benefit->benefit_name,
            provider: $benefit->provider,
            plan_id: $benefit->plan_id,
            enrollment_date: $benefit->enrollment_date?->format('Y-m-d'),
            effective_date: $benefit->effective_date?->format('Y-m-d'),
            end_date: $benefit->end_date?->format('Y-m-d'),
            status: $benefit->status->value,
            coverage_level: $benefit->coverage_level->value,
            premium_amount: $benefit->premium_amount,
            employee_contribution: $benefit->employee_contribution,
            employer_contribution: $benefit->employer_contribution,
            total_cost: $benefit->total_cost,
            deductible: $benefit->deductible,
            co_pay: $benefit->co_pay,
            co_insurance: $benefit->co_insurance,
            max_out_of_pocket: $benefit->max_out_of_pocket,
            network_type: $benefit->network_type->value,
            is_active: $benefit->is_active,
            notes: $benefit->notes,
            documents: $benefit->documents,
            created_at: $benefit->created_at?->format('Y-m-d H:i:s'),
            updated_at: $benefit->updated_at?->format('Y-m-d H:i:s'),
        );
    }

    /**
     * Get validation rules
     */
    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'benefit_type' => ['required', 'string', 'in:'.implode(',', BenefitType::values())],
            'benefit_name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'plan_id' => ['nullable', 'string', 'max:100'],
            'enrollment_date' => ['nullable', 'date'],
            'effective_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],
            'status' => ['required', 'string', 'in:'.implode(',', BenefitStatus::values())],
            'coverage_level' => ['required', 'string', 'in:'.implode(',', CoverageLevel::values())],
            'premium_amount' => ['required', 'numeric', 'min:0'],
            'employee_contribution' => ['required', 'numeric', 'min:0'],
            'employer_contribution' => ['required', 'numeric', 'min:0'],
            'total_cost' => ['required', 'numeric', 'min:0'],
            'deductible' => ['nullable', 'numeric', 'min:0'],
            'co_pay' => ['nullable', 'numeric', 'min:0'],
            'co_insurance' => ['nullable', 'numeric', 'min:0'],
            'max_out_of_pocket' => ['nullable', 'numeric', 'min:0'],
            'network_type' => ['required', 'string', 'in:'.implode(',', NetworkType::values())],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'documents' => ['nullable', 'array'],
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
            'benefit_type.required' => 'Benefit type is required.',
            'benefit_type.in' => 'The selected benefit type is invalid.',
            'benefit_name.required' => 'Benefit name is required.',
            'benefit_name.max' => 'Benefit name may not be greater than 255 characters.',
            'provider.required' => 'Provider is required.',
            'provider.max' => 'Provider may not be greater than 255 characters.',
            'effective_date.required' => 'Effective date is required.',
            'effective_date.after_or_equal' => 'Effective date must be today or a future date.',
            'end_date.after' => 'End date must be after the effective date.',
            'status.required' => 'Status is required.',
            'status.in' => 'The selected status is invalid.',
            'coverage_level.required' => 'Coverage level is required.',
            'coverage_level.in' => 'The selected coverage level is invalid.',
            'premium_amount.required' => 'Premium amount is required.',
            'premium_amount.numeric' => 'Premium amount must be a number.',
            'premium_amount.min' => 'Premium amount must be at least 0.',
            'employee_contribution.required' => 'Employee contribution is required.',
            'employee_contribution.numeric' => 'Employee contribution must be a number.',
            'employee_contribution.min' => 'Employee contribution must be at least 0.',
            'employer_contribution.required' => 'Employer contribution is required.',
            'employer_contribution.numeric' => 'Employer contribution must be a number.',
            'employer_contribution.min' => 'Employer contribution must be at least 0.',
            'total_cost.required' => 'Total cost is required.',
            'total_cost.numeric' => 'Total cost must be a number.',
            'total_cost.min' => 'Total cost must be at least 0.',
            'network_type.required' => 'Network type is required.',
            'network_type.in' => 'The selected network type is invalid.',
            'is_active.required' => 'Active status is required.',
            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }

    /**
     * Calculate total monthly cost
     */
    public function getMonthlyCost(): float
    {
        return $this->premium_amount;
    }

    /**
     * Calculate total annual cost
     */
    public function getAnnualCost(): float
    {
        return $this->getMonthlyCost() * 12;
    }

    /**
     * Calculate employee monthly contribution
     */
    public function getEmployeeMonthlyContribution(): float
    {
        return $this->employee_contribution;
    }

    /**
     * Calculate employer monthly contribution
     */
    public function getEmployerMonthlyContribution(): float
    {
        return $this->employer_contribution;
    }

    /**
     * Check if benefit is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === BenefitStatus::ENROLLED->value;
    }

    /**
     * Check if benefit can be modified
     */
    public function canBeModified(): bool
    {
        return in_array($this->status, [
            BenefitStatus::PENDING->value,
            BenefitStatus::ENROLLED->value,
        ]);
    }

    /**
     * Check if benefit can be terminated
     */
    public function canBeTerminated(): bool
    {
        return $this->status === BenefitStatus::ENROLLED->value;
    }

    /**
     * Check if benefit can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            BenefitStatus::PENDING->value,
            BenefitStatus::ENROLLED->value,
        ]);
    }

    /**
     * Get benefit summary
     */
    public function getBenefitSummary(): array
    {
        return [
            'id' => $this->id,
            'benefit_name' => $this->benefit_name,
            'benefit_type' => $this->benefit_type,
            'provider' => $this->provider,
            'status' => $this->status,
            'coverage_level' => $this->coverage_level,
            'monthly_cost' => $this->getMonthlyCost(),
            'employee_contribution' => $this->getEmployeeMonthlyContribution(),
            'employer_contribution' => $this->getEmployerMonthlyContribution(),
            'effective_date' => $this->effective_date,
            'end_date' => $this->end_date,
            'is_active' => $this->isActive(),
        ];
    }

    /**
     * Convert to array for database insertion
     */
    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'benefit_type' => $this->benefit_type,
            'benefit_name' => $this->benefit_name,
            'provider' => $this->provider,
            'plan_id' => $this->plan_id,
            'enrollment_date' => $this->enrollment_date ? Carbon::parse($this->enrollment_date) : null,
            'effective_date' => Carbon::parse($this->effective_date),
            'end_date' => $this->end_date ? Carbon::parse($this->end_date) : null,
            'status' => $this->status,
            'coverage_level' => $this->coverage_level,
            'premium_amount' => $this->premium_amount,
            'employee_contribution' => $this->employee_contribution,
            'employer_contribution' => $this->employer_contribution,
            'total_cost' => $this->total_cost,
            'deductible' => $this->deductible,
            'co_pay' => $this->co_pay,
            'co_insurance' => $this->co_insurance,
            'max_out_of_pocket' => $this->max_out_of_pocket,
            'network_type' => $this->network_type,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'documents' => $this->documents,
        ];
    }
}
