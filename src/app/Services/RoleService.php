<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Models\Role;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;

class RoleService
{
    use HasCrudOperations;

    public function __construct() {
        $this->model =  \Spatie\Permission\Models\Role::class;
    }
}
