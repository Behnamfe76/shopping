<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use HasCrudOperations;

    public function __construct(
        private readonly UserRepositoryInterface $repository
    )
    {
        $this->model = User::class;
    }

    public function create(array $data): User
    {
        try {
            DB::beginTransaction();
            $data['password'] = Hash::make($data['password']);
            $user = $this->repository->create($data);

            $user->syncRoles($data['roles']);
            DB::commit();

            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
