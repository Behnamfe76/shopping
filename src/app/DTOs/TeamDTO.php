<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Models\Team;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class TeamDTO extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $code,
        public ?string $description,
        public int $department_id,
        public ?string $location,
        public ?int $member_limit,
        public bool $is_active,
        public TeamStatus $status,
        public ?array $metadata,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?object $department = null,
        public ?array $members = null,
        public ?array $managers = null,
        public ?int $member_count = null,
        public ?int $manager_count = null,
    ) {}

    public static function fromModel(Team $team): static
    {
        return new static(
            id: $team->id,
            name: $team->name,
            code: $team->code,
            description: $team->description,
            department_id: $team->department_id,
            location: $team->location,
            member_limit: $team->member_limit,
            is_active: $team->is_active ?? true,
            status: $team->status ?? TeamStatus::ACTIVE,
            metadata: $team->metadata,
            created_at: $team->created_at,
            updated_at: $team->updated_at,
            department: $team->department ? (object) [
                'id' => $team->department->id,
                'name' => $team->department->name,
                'code' => $team->department->code,
            ] : null,
            members: $team->members ? $team->members->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->user->name ?? null,
                'is_manager' => $member->pivot->is_manager ?? false,
                'joined_at' => $member->pivot->joined_at ?? null,
                'left_at' => $member->pivot->left_at ?? null,
            ])->toArray() : null,
            managers: $team->managers ? $team->managers->map(fn ($manager) => [
                'id' => $manager->id,
                'name' => $manager->user->name ?? null,
                'joined_at' => $manager->pivot->joined_at ?? null,
            ])->toArray() : null,
            member_count: $team->member_count ?? null,
            manager_count: $team->manager_count ?? null,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:teams,code',
            'description' => 'nullable|string',
            'department_id' => 'required|integer|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'member_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'status' => 'required|in:'.implode(',', array_column(TeamStatus::cases(), 'value')),
            'metadata' => 'nullable|array',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Team name is required',
            'name.max' => 'Team name cannot exceed 255 characters',
            'code.required' => 'Team code is required',
            'code.max' => 'Team code cannot exceed 50 characters',
            'code.unique' => 'This team code is already taken',
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Selected department does not exist',
            'member_limit.integer' => 'Member limit must be a number',
            'member_limit.min' => 'Member limit must be at least 1',
            'status.required' => 'Team status is required',
            'status.in' => 'Invalid team status selected',
        ];
    }
}
