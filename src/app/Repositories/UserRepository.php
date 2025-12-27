<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Enums\UserStatus;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return User::simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return User::cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByUserId(int $userId): ?User
    {
        return User::where('user_id', $userId)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findByUserNumber(string $userNumber): ?User
    {
        return User::where('user_number', $userNumber)->first();
    }

    public function findByStatus(string $status): Collection
    {
        return User::where('status', $status)->get();
    }

    public function findActive(): Collection
    {
        return User::where('status', UserStatus::ACTIVE)->get();
    }

    public function findInactive(): Collection
    {
        return User::where('status', UserStatus::INACTIVE)->get();
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return User::whereBetween('created_at', [$startDate, $endDate])->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function activate(User $user): bool
    {
        return $user->activate();
    }

    public function deactivate(User $user): bool
    {
        return $user->deactivate();
    }

    public function suspend(User $user, ?string $reason = null): bool
    {
        $data = ['status' => UserStatus::SUSPENDED];
        if ($reason) {
            $data['notes'] = $user->notes."\nSuspended: ".$reason;
        }

        return $this->update($user, $data);
    }

    public function unsuspend(User $user): bool
    {
        return $this->update($user, ['status' => UserStatus::ACTIVE]);
    }

    public function getUserCount(): int
    {
        return User::count();
    }

    public function getUserCountByStatus(string $status): int
    {
        return User::where('status', $status)->count();
    }

    public function getActiveUserCount(): int
    {
        return User::where('status', UserStatus::ACTIVE)->count();
    }

    public function getInactiveUserCount(): int
    {
        return User::where('status', UserStatus::INACTIVE)->count();
    }

    public function generateUserNumber(): string
    {
        do {
            $number = 'USR'.strtoupper(Str::random(8));
        } while (! $this->isUserNumberUnique($number));

        return $number;
    }

    public function isUserNumberUnique(string $userNumber): bool
    {
        return ! User::where('user_number', $userNumber)->exists();
    }
}
