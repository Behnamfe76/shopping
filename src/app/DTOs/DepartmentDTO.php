<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\Department;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class DepartmentDTO extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $code,
        public ?string $description,
        public ?int $parent_id,
        public ?int $manager_id,
        public ?string $location,
        public ?float $budget,
        public ?int $headcount_limit,
        public bool $is_active,
        public DepartmentStatus $status,
        public ?array $metadata,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?DepartmentDTO $parent = null,
        public ?array $children = null,
        public ?int $employee_count = null,
        public ?int $depth = null,
        public ?array $path = null,
        public ?object $manager = null,
    ) {}

    public static function fromModel(Department $department): static
    {
        return new static(
            id: $department->id,
            name: $department->name,
            code: $department->code,
            description: $department->description,
            parent_id: $department->parent_id,
            manager_id: $department->manager_id,
            location: $department->location,
            budget: $department->budget,
            headcount_limit: $department->headcount_limit,
            is_active: $department->is_active ?? true,
            status: $department->status ?? DepartmentStatus::ACTIVE,
            metadata: $department->metadata,
            created_at: $department->created_at,
            updated_at: $department->updated_at,
            parent: $department->parent ? static::fromModel($department->parent) : null,
            children: $department->children ? $department->children->map(fn ($child) => static::fromModel($child))->toArray() : null,
            employee_count: $department->employee_count ?? null,
            depth: null,
            path: null,
            manager: $department->manager ? (object) [
                'id' => $department->manager->id,
                'name' => $department->manager->user->name ?? null,
            ] : null,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:departments,id',
            'manager_id' => 'nullable|integer|exists:employees,id',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'headcount_limit' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'status' => 'required|in:'.implode(',', array_column(DepartmentStatus::cases(), 'value')),
            'metadata' => 'nullable|array',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Department name is required',
            'name.max' => 'Department name cannot exceed 255 characters',
            'code.required' => 'Department code is required',
            'code.max' => 'Department code cannot exceed 50 characters',
            'code.unique' => 'This department code is already taken',
            'parent_id.exists' => 'Selected parent department does not exist',
            'manager_id.exists' => 'Selected manager does not exist',
            'budget.numeric' => 'Budget must be a number',
            'budget.min' => 'Budget cannot be negative',
            'headcount_limit.integer' => 'Headcount limit must be a number',
            'headcount_limit.min' => 'Headcount limit cannot be negative',
            'status.required' => 'Department status is required',
            'status.in' => 'Invalid department status selected',
        ];
    }
}
