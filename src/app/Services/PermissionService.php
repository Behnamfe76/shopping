<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\PermissionDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;

class PermissionService
{
    use HasCrudOperations;

    public function __construct() {
        $this->model =  \Spatie\Permission\Models\Permission::class;
        $this->dtoClass = PermissionDTO::class;
    }
}
