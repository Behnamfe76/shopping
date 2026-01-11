<?php

namespace Fereydooni\Shopping\app\DTOs;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class PermissionDTO extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $guard_name,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
    ) {}

    public static function fromModel($permission): static
    {
        return new static(
            id: $permission->id,
            name: $permission->name,
            guard_name: $permission->guard_name,
            created_at: $permission->created_at,
            updated_at: $permission->updated_at,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|unique:permissions,name|max:64',
            'guard_name' => 'required|string|max:255',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Permission name is required',
            'name.unique' => 'Permission name has been taken',
            'guard_name.required' => 'Permission guard name is required',
        ];
    }
}
