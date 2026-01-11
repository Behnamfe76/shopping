<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\PermissionDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Illuminate\Pagination\CursorPaginator;

class PermissionService
{
    use HasCrudOperations;

    public function __construct()
    {
        $this->model = \Spatie\Permission\Models\Permission::class;
        $this->dtoClass = PermissionDTO::class;
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
