<?php

namespace Fereydooni\Shopping\app\Services;

use App\Models\User;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use HasCrudOperations;

    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
        $this->model = User::class;
    }

    public function create(array $data): User
    {
        try {
            DB::beginTransaction();
            $data['password'] = Hash::make($data['password']);
            $user = $this->repository->create($data);

            $user->assignRole(array_map(fn ($item) => (int) $item, $data['roles']));
            DB::commit();

            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function update(User $user, array $data): User
    {
        try {
            DB::beginTransaction();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user->update($data);

            $user->syncRoles(array_map(fn ($item) => (int) $item, $data['roles']));
            DB::commit();

            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
