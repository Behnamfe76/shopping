<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\PermissionDTO;
use Fereydooni\Shopping\app\Models\Permission;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class PermissionService
{
    use HasCrudOperations;

    public function __construct()
    {
        $this->model = Permission::class;
        $this->dtoClass = PermissionDTO::class;
    }

    /**
     * @throws Throwable
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $locale = app()->getLocale();
            $data['meta'] = [
                "{$locale}" => [
                    "description" => $data['description'],
                ]
            ];
            $permission = $this->model::create($data);
            DB::commit();

            return $permission;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function update(Permission $permission, array $data)
    {
        try {
            DB::beginTransaction();
            $locale = app()->getLocale();
            $data['meta'] = [
                "{$locale}" => [
                    "description" => $data['description'],
                ]
            ];
            $permission->update($data);
            DB::commit();

            return $permission;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
