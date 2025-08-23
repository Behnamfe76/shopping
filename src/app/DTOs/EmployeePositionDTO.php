<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Decimal;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\PositionStatus;
use Fereydooni\Shopping\app\Enums\PositionLevel;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Models\Employee;

class EmployeePositionDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, StringType, Max(255)]
        public string $title,

        #[Required, StringType, Max(50), Unique('employee_positions', 'code')]
        public string $code,

        #[Nullable, StringType, Max(1000)]
        public ?string $description,

        #[Required, IntegerType]
        public int $department_id,

        #[Required]
        public PositionLevel $level,

        #[Nullable, Decimal(2), Min(0)]
        public ?float $salary_min,

        #[Nullable, Decimal(2), Min(0)]
        public ?float $salary_max,

        #[Nullable, Decimal(2), Min(0)]
        public ?float $hourly_rate_min,

        #[Nullable, Decimal(2), Min(0)]
        public ?float $hourly_rate_max,

        #[Required, BooleanType]
        public bool $is_active,

        #[Required]
        public PositionStatus $status,

        #[Nullable, ArrayType]
        public ?array $requirements,

        #[Nullable, ArrayType]
        public ?array $responsibilities,

        #[Nullable, ArrayType]
        public ?array $skills_required,

        #[Nullable, IntegerType, Min(0)]
        public ?int $experience_required,

        #[Nullable, ArrayType]
        public ?array $education_required,

        #[Required, BooleanType]
        public bool $is_remote,

        #[Required, BooleanType]
        public bool $is_travel_required,

        #[Nullable, Decimal(2), Min(0), Max(100)]
        public ?float $travel_percentage,

        #[Nullable, ArrayType]
        public ?array $metadata,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?EmployeeDepartmentDTO $department,

        #[Nullable]
        public ?array $employees,

        #[Nullable]
        public ?EmployeeDTO $manager
    ) {
    }

    public static function fromModel(EmployeePosition $position): self
    {
        return new self(
            id: $position->id,
            title: $position->title,
            code: $position->code,
            description: $position->description,
            department_id: $position->department_id,
            level: $position->level,
            salary_min: $position->salary_min,
            salary_max: $position->salary_max,
            hourly_rate_min: $position->hourly_rate_min,
            hourly_rate_max: $position->hourly_rate_max,
            is_active: $position->is_active,
            status: $position->status,
            requirements: $position->requirements,
            responsibilities: $position->responsibilities,
            skills_required: $position->skills_required,
            experience_required: $position->experience_required,
            education_required: $position->education_required,
            is_remote: $position->is_remote,
            is_travel_required: $position->is_travel_required,
            travel_percentage: $position->travel_percentage,
            metadata: $position->metadata,
            created_at: $position->created_at,
            updated_at: $position->updated_at,
            department: $position->department ? EmployeeDepartmentDTO::fromModel($position->department) : null,
            employees: $position->employees ? $position->employees->map(fn($employee) => EmployeeDTO::fromModel($employee))->toArray() : null,
            manager: $position->manager ? EmployeeDTO::fromModel($position->manager) : null
        );
    }

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:employee_positions,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'integer', 'exists:employee_departments,id'],
            'level' => ['required', 'string', 'in:' . implode(',', array_column(PositionLevel::cases(), 'value'))],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'hourly_rate_min' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_max' => ['nullable', 'numeric', 'min:0', 'gte:hourly_rate_min'],
            'is_active' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:' . implode(',', array_column(PositionStatus::cases(), 'value'))],
            'requirements' => ['nullable', 'array'],
            'responsibilities' => ['nullable', 'array'],
            'skills_required' => ['nullable', 'array'],
            'experience_required' => ['nullable', 'integer', 'min:0'],
            'education_required' => ['nullable', 'array'],
            'is_remote' => ['required', 'boolean'],
            'is_travel_required' => ['required', 'boolean'],
            'travel_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'title.required' => 'Position title is required.',
            'title.max' => 'Position title cannot exceed 255 characters.',
            'code.required' => 'Position code is required.',
            'code.unique' => 'Position code must be unique.',
            'code.max' => 'Position code cannot exceed 50 characters.',
            'description.max' => 'Position description cannot exceed 1000 characters.',
            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'level.required' => 'Position level is required.',
            'level.in' => 'Invalid position level selected.',
            'salary_min.numeric' => 'Minimum salary must be a number.',
            'salary_min.min' => 'Minimum salary cannot be negative.',
            'salary_max.numeric' => 'Maximum salary must be a number.',
            'salary_max.min' => 'Maximum salary cannot be negative.',
            'salary_max.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'hourly_rate_min.numeric' => 'Minimum hourly rate must be a number.',
            'hourly_rate_min.min' => 'Minimum hourly rate cannot be negative.',
            'hourly_rate_max.numeric' => 'Maximum hourly rate must be a number.',
            'hourly_rate_max.min' => 'Maximum hourly rate cannot be negative.',
            'hourly_rate_max.gte' => 'Maximum hourly rate must be greater than or equal to minimum hourly rate.',
            'is_active.required' => 'Active status is required.',
            'status.required' => 'Position status is required.',
            'status.in' => 'Invalid position status selected.',
            'experience_required.integer' => 'Experience required must be a whole number.',
            'experience_required.min' => 'Experience required cannot be negative.',
            'travel_percentage.numeric' => 'Travel percentage must be a number.',
            'travel_percentage.min' => 'Travel percentage cannot be negative.',
            'travel_percentage.max' => 'Travel percentage cannot exceed 100%.',
        ];
    }

    // Helper methods
    public function getFullTitle(): string
    {
        return "{$this->title} ({$this->level->label()})";
    }

    public function getSalaryRange(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return '$' . number_format($this->salary_min, 0) . ' - $' . number_format($this->salary_max, 0);
        }
        return 'Not specified';
    }

    public function getHourlyRateRange(): string
    {
        if ($this->hourly_rate_min && $this->hourly_rate_max) {
            return '$' . number_format($this->hourly_rate_min, 2) . ' - $' . number_format($this->hourly_rate_max, 2);
        }
        return 'Not specified';
    }

    public function getAverageSalary(): float
    {
        if ($this->salary_min && $this->salary_max) {
            return ($this->salary_min + $this->salary_max) / 2;
        }
        return 0;
    }

    public function getAverageHourlyRate(): float
    {
        if ($this->hourly_rate_min && $this->hourly_rate_max) {
            return ($this->hourly_rate_min + $this->hourly_rate_max) / 2;
        }
        return 0;
    }

    public function isOpen(): bool
    {
        return $this->status === PositionStatus::HIRING || $this->status === PositionStatus::ACTIVE;
    }

    public function requiresTravel(): bool
    {
        return $this->is_travel_required && $this->travel_percentage > 0;
    }

    public function isRemoteEligible(): bool
    {
        return $this->is_remote;
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

    public function hasSkill(string $skill): bool
    {
        return in_array($skill, $this->skills_required ?? []);
    }

    public function getSkillsRequired(): array
    {
        return $this->skills_required ?? [];
    }

    public function getRequirements(): array
    {
        return $this->requirements ?? [];
    }

    public function getResponsibilities(): array
    {
        return $this->responsibilities ?? [];
    }

    public function getEducationRequired(): array
    {
        return $this->education_required ?? [];
    }

    public function getExperienceRequired(): int
    {
        return $this->experience_required ?? 0;
    }

    public function getTravelPercentage(): float
    {
        return $this->travel_percentage ?? 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'description' => $this->description,
            'department_id' => $this->department_id,
            'level' => $this->level->value,
            'level_label' => $this->level->label(),
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'hourly_rate_min' => $this->hourly_rate_min,
            'hourly_rate_max' => $this->hourly_rate_max,
            'is_active' => $this->is_active,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'requirements' => $this->requirements,
            'responsibilities' => $this->responsibilities,
            'skills_required' => $this->skills_required,
            'experience_required' => $this->experience_required,
            'education_required' => $this->education_required,
            'is_remote' => $this->is_remote,
            'is_travel_required' => $this->is_travel_required,
            'travel_percentage' => $this->travel_percentage,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'full_title' => $this->getFullTitle(),
            'salary_range' => $this->getSalaryRange(),
            'hourly_rate_range' => $this->getHourlyRateRange(),
            'average_salary' => $this->getAverageSalary(),
            'average_hourly_rate' => $this->getAverageHourlyRate(),
            'is_open' => $this->isOpen(),
            'requires_travel' => $this->requiresTravel(),
            'level_hierarchy' => $this->getLevelHierarchy(),
            'requires_management' => $this->requiresManagement(),
            'department' => $this->department?->toArray(),
            'employees' => $this->employees,
            'manager' => $this->manager?->toArray(),
        ];
    }
}
