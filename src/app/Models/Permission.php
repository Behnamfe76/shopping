<?php

namespace Fereydooni\Shopping\app\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
