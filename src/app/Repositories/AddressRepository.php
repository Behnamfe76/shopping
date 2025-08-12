<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Fereydooni\Shopping\app\Models\Address;
use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface;

class AddressRepository implements AddressRepositoryInterface
{
    public function __construct(
        private Address $model
    ) {
    }

    // Basic CRUD Operations
    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Address
    {
        return $this->model->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function create(array $data): Address
    {
        return $this->model->create($data);
    }

    public function update(Address $address, array $data): bool
    {
        return $address->update($data);
    }

    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    // Pagination Methods
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // User-specific Pagination
    public function paginateByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)->paginate($perPage);
    }

    public function simplePaginateByUser(int $userId, int $perPage = 15): Paginator
    {
        return $this->model->where('user_id', $userId)->simplePaginate($perPage);
    }

    public function cursorPaginateByUser(int $userId, int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->where('user_id', $userId)->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // DTO Methods
    public function findDTO(int $id): ?AddressDTO
    {
        $address = $this->find($id);
        return $address ? AddressDTO::fromModel($address) : null;
    }

    public function findByUserDTO(int $userId): Collection
    {
        $addresses = $this->findByUser($userId);
        return $addresses->map(fn($address) => AddressDTO::fromModel($address));
    }

    public function createAndReturnDTO(array $data): AddressDTO
    {
        $address = $this->create($data);
        return AddressDTO::fromModel($address);
    }

    public function updateAndReturnDTO(Address $address, array $data): ?AddressDTO
    {
        $updated = $this->update($address, $data);
        return $updated ? AddressDTO::fromModel($address->fresh()) : null;
    }

    // Address Type Methods
    public function findByType(AddressType $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    public function findByUserAndType(int $userId, AddressType $type): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->get();
    }

    public function getDefaultByUser(int $userId, AddressType $type): ?Address
    {
        return $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }

    public function getDefaultDTOByUser(int $userId, AddressType $type): ?AddressDTO
    {
        $address = $this->getDefaultByUser($userId, $type);
        return $address ? AddressDTO::fromModel($address) : null;
    }

    // Default Address Methods
    public function setDefault(Address $address): bool
    {
        return DB::transaction(function () use ($address) {
            // Unset other default addresses of the same type for this user
            $this->model->where('user_id', $address->user_id)
                ->where('type', $address->type)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            // Set this address as default
            return $address->update(['is_default' => true]);
        });
    }

    public function unsetDefault(Address $address): bool
    {
        return $address->update(['is_default' => false]);
    }

    public function getDefaultAddresses(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('is_default', true)
            ->get();
    }

    public function getDefaultAddressesDTO(int $userId): Collection
    {
        $addresses = $this->getDefaultAddresses($userId);
        return $addresses->map(fn($address) => AddressDTO::fromModel($address));
    }

    // Search Methods
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('company_name', 'like', "%{$query}%")
                ->orWhere('address_line_1', 'like', "%{$query}%")
                ->orWhere('address_line_2', 'like', "%{$query}%")
                ->orWhere('city', 'like', "%{$query}%")
                ->orWhere('state', 'like', "%{$query}%")
                ->orWhere('postal_code', 'like', "%{$query}%")
                ->orWhere('country', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        })->get();
    }

    public function searchByUser(int $userId, string $query): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%")
                    ->orWhere('address_line_1', 'like', "%{$query}%")
                    ->orWhere('address_line_2', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%")
                    ->orWhere('state', 'like', "%{$query}%")
                    ->orWhere('postal_code', 'like', "%{$query}%")
                    ->orWhere('country', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })->get();
    }

    public function searchDTO(string $query): Collection
    {
        $addresses = $this->search($query);
        return $addresses->map(fn($address) => AddressDTO::fromModel($address));
    }

    public function searchByUserDTO(int $userId, string $query): Collection
    {
        $addresses = $this->searchByUser($userId, $query);
        return $addresses->map(fn($address) => AddressDTO::fromModel($address));
    }

    // Address Count Methods
    public function countByUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)->count();
    }

    public function countByType(AddressType $type): int
    {
        return $this->model->where('type', $type)->count();
    }

    public function countByUserAndType(int $userId, AddressType $type): int
    {
        return $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->count();
    }

    // Address Validation Methods
    public function hasDefaultAddress(int $userId, AddressType $type): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('type', $type)
            ->where('is_default', true)
            ->exists();
    }

    public function canSetDefault(Address $address): bool
    {
        return !$this->hasDefaultAddress($address->user_id, $address->type) || $address->is_default;
    }

    public function isDefault(Address $address): bool
    {
        return $address->is_default;
    }

    // Bulk Operations
    public function deleteByUser(int $userId): bool
    {
        return $this->model->where('user_id', $userId)->delete() > 0;
    }

    public function deleteByType(AddressType $type): bool
    {
        return $this->model->where('type', $type)->delete() > 0;
    }

    public function updateByUser(int $userId, array $data): bool
    {
        return $this->model->where('user_id', $userId)->update($data) > 0;
    }

    // Address Path Methods
    public function getAddressPath(Address $address): Collection
    {
        // For addresses, the path is just the address itself since there's no hierarchy
        return collect([$address]);
    }

    public function getAddressPathDTO(Address $address): Collection
    {
        return collect([AddressDTO::fromModel($address)]);
    }

    // Address Statistics
    public function getAddressStats(int $userId): array
    {
        $stats = $this->model->where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_addresses,
                COUNT(CASE WHEN is_default = 1 THEN 1 END) as default_addresses,
                COUNT(CASE WHEN type = ? THEN 1 END) as billing_addresses,
                COUNT(CASE WHEN type = ? THEN 1 END) as shipping_addresses
            ', [AddressType::BILLING->value, AddressType::SHIPPING->value])
            ->first();

        return [
            'total_addresses' => $stats->total_addresses ?? 0,
            'default_addresses' => $stats->default_addresses ?? 0,
            'billing_addresses' => $stats->billing_addresses ?? 0,
            'shipping_addresses' => $stats->shipping_addresses ?? 0,
        ];
    }

    public function getAddressStatsByType(int $userId): array
    {
        $stats = $this->model->where('user_id', $userId)
            ->selectRaw('type, COUNT(*) as count, COUNT(CASE WHEN is_default = 1 THEN 1 END) as default_count')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return [
            'billing' => [
                'total' => $stats->get(AddressType::BILLING->value)?->count ?? 0,
                'default' => $stats->get(AddressType::BILLING->value)?->default_count ?? 0,
            ],
            'shipping' => [
                'total' => $stats->get(AddressType::SHIPPING->value)?->count ?? 0,
                'default' => $stats->get(AddressType::SHIPPING->value)?->default_count ?? 0,
            ],
        ];
    }
}
