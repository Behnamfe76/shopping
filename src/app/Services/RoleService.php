<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    use HasCrudOperations;

    public function __construct()
    {
        $this->model = Role::class;
    }

    /**
     * @throws \Throwable
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $role = $this->model::create($data);

            $role->permissions()->sync($data['permissions']);
            DB::commit();

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Throwable
     */
    public function update(Role $role, array $data)
    {
        try {
            DB::beginTransaction();
            $role->update($data);

            $role->permissions()->sync($data['permissions']);
            DB::commit();

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
