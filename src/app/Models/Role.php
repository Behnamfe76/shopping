<?php

namespace Fereydooni\Shopping\app\Models;

class Role extends \Spatie\Permission\Models\Role {

    public function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
