<?php

namespace Fereydooni\Shopping\app\Models;


use Illuminate\Support\Facades\Log;

class Permission extends \Spatie\Permission\Models\Permission
{
    public function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
