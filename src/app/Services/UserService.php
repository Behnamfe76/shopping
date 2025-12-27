<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;

class UserService
{
    use HasCrudOperations;

    public function __construct(
        private UserRepositoryInterface $repository
    ) {
        $this->model = User::class;
    }
}
