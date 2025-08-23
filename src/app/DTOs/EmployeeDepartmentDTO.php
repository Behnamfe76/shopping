<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Models\Employee;

class EmployeeDepartmentDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, StringType, Max(255)]
        public string $name,

        #[Required, StringType, Max(50), Unique('employee_departments', 'code')]
        public string $code,

        #[Nullable, StringType, Max(1000)]
        public ?string $description,

        #[Nullable, IntegerType]
        public ?int $parent_id,

        #[Nullable, IntegerType]
        public ?int $manager_id,

        #[Nullable, StringType, Max(255)]
        public ?string $location,

        #[Nullable, Numeric, Min(0)]
        public ?float $budget,

        #[Nullable, IntegerType, Min(0)]
        public ?int $headcount_limit,

        #[Required, BooleanType]
        public bool $is_active,

        #[Required]
        public DepartmentStatus $status,

        #[Nullable]
        public ?array $metadata,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Computed fields
        #[Nullable]
        public ?string $full_name,

        #[Nullable]
        public ?int $employee_count,

        #[Nullable]
        public ?float $budget_utilization,

        #[Nullable]
        public ?float $headcount_utilization,

        #[Nullable]
        public ?int $depth,

        #[Nullable]
        public ?int $level,

        // Relationships
        #[Nullable]
        public ?EmployeeDepartmentDTO $parent,

        #[Nullable]
        public ?EmployeeDTO $manager,

        #[Nullable]
        public ?array $children,

        #[Nullable]
        public ?array $employees,

        #[Nullable]
        public ?array $ancestors,

        #[Nullable]
        public ?array $descendants,
    ) {
    }

    public static function fromModel(EmployeeDepartment $department): self
    {
        return new self(
            id: $department->id,
            name: $department->name,
            code: $department->code,
            description: $department->description,
            parent_id: $department->parent_id,
            manager_id: $department->manager_id,
            location: $department->location,
            budget: $department->budget,
            headcount_limit: $department->headcount_limit,
            is_active: $department->is_active,
            status: $department->status,
            metadata: $department->metadata,
            created_at: $department->created_at,
            updated_at: $department->updated_at,
            full_name: $department->full_name,
            employee_count: $department->employee_count,
            budget_utilization: $department->budget_utilization,
            headcount_utilization: $department->headcount_utilization,
            depth: $department->depth,
            level: $department->level,
            parent: $department->parent ? self::fromModel($department->parent) : null,
            manager: $department->manager ? EmployeeDTO::fromModel($department->manager) : null,
            children: $department->children ? $department->children->map(fn($child) => self::fromModel($child))->toArray() : null,
            employees: $department->employees ? $department->employees->map(fn($employee) => EmployeeDTO::fromModel($employee))->toArray() : null,
            ancestors: $department->ancestors ? array_map(fn($ancestor) => self::fromModel($ancestor), $department->ancestors) : null,
            descendants: $department->descendants ? array_map(fn($descendant) => self::fromModel($descendant), $department->descendants) : null,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:employee_departments,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer', 'exists:employee_departments,id'],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'headcount_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'status' => ['required', 'in:' . implode(',', array_column(DepartmentStatus::cases(), 'value'))],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'name.max' => 'Department name cannot exceed 255 characters.',
            'code.required' => 'Department code is required.',
            'code.max' => 'Department code cannot exceed 50 characters.',
            'code.unique' => 'Department code must be unique.',
            'description.max' => 'Department description cannot exceed 1000 characters.',
            'parent_id.exists' => 'Parent department does not exist.',
            'manager_id.exists' => 'Manager does not exist.',
            'budget.min' => 'Budget cannot be negative.',
            'headcount_limit.min' => 'Headcount limit cannot be negative.',
            'status.in' => 'Invalid department status.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'manager_id' => $this->manager_id,
            'location' => $this->location,
            'budget' => $this->budget,
            'headcount_limit' => $this->headcount_limit,
            'is_active' => $this->is_active,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'full_name' => $this->full_name,
            'employee_count' => $this->employee_count,
            'budget_utilization' => $this->budget_utilization,
            'headcount_utilization' => $this->headcount_utilization,
            'depth' => $this->depth,
            'level' => $this->level,
            'is_root' => is_null($this->parent_id),
            'is_leaf' => empty($this->children),
            'has_children' => !empty($this->children),
            'can_operate' => $this->status->canOperate(),
            'is_visible' => $this->status->isVisible(),
        ];
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isLeaf(): bool
    {
        return empty($this->children);
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isInactive(): bool
    {
        return $this->status->isInactive();
    }

    public function canOperate(): bool
    {
        return $this->status->canOperate();
    }

    public function isVisible(): bool
    {
        return $this->status->isVisible();
    }
}
