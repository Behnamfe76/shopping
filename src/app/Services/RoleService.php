<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Models\Role;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

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
            $locale = app()->getLocale();
            $data['meta'] = [
                "{$locale}" => [
                    "description" => $data['description'],
                ]
            ];
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
            $locale = app()->getLocale();
            $data['meta'] = [
                "{$locale}" => [
                    "description" => $data['description'],
                ]
            ];
            $role->update($data);
            if(isset($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }
            DB::commit();

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $select = '*';
        $columns = request()->get('columns', []);
        if (! empty($columns)) {
            $select = $columns;
        }

        return (new $this->model)
            ->query()->when(request()->input('search'), function ($query, $input) {
                return $query->whereLike('name', "%$input%");
            })
            ->select($select)
            ->cursorPaginate($perPage, [$columns], 'id', $cursor);
    }
}
