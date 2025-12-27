<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\App\Models\User;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface UserRepositoryInterface
{
    public function paginate(): LengthAwarePaginator;

    public function simplePaginate(): Paginator;

    public function cursorPaginate(): CursorPaginator;

    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function delete(User $user): bool;
}
