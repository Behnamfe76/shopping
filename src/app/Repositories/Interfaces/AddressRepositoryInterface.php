<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface AddressRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;

    public function find(int $id): ?Address;

    public function findByUser(int $userId): Collection;

    public function create(array $data): Address;

    public function update(Address $address, array $data): bool;

    public function delete(Address $address): bool;

    // Pagination Methods
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // User-specific Pagination
    public function paginateByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function simplePaginateByUser(int $userId, int $perPage = 15): Paginator;

    public function cursorPaginateByUser(int $userId, int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // DTO Methods
    public function findDTO(int $id): ?AddressDTO;

    public function findByUserDTO(int $userId): Collection;

    public function createAndReturnDTO(array $data): AddressDTO;

    public function updateAndReturnDTO(Address $address, array $data): ?AddressDTO;

    // Address Type Methods
    public function findByType(AddressType $type): Collection;

    public function findByUserAndType(int $userId, AddressType $type): Collection;

    public function getDefaultByUser(int $userId, AddressType $type): ?Address;

    public function getDefaultDTOByUser(int $userId, AddressType $type): ?AddressDTO;

    // Default Address Methods
    public function setDefault(Address $address): bool;

    public function unsetDefault(Address $address): bool;

    public function getDefaultAddresses(int $userId): Collection;

    public function getDefaultAddressesDTO(int $userId): Collection;

    // Search Methods
    public function search(string $query): Collection;

    public function searchByUser(int $userId, string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByUserDTO(int $userId, string $query): Collection;

    // Address Count Methods
    public function countByUser(int $userId): int;

    public function countByType(AddressType $type): int;

    public function countByUserAndType(int $userId, AddressType $type): int;

    // Address Validation Methods
    public function hasDefaultAddress(int $userId, AddressType $type): bool;

    public function canSetDefault(Address $address): bool;

    public function isDefault(Address $address): bool;

    // Bulk Operations
    public function deleteByUser(int $userId): bool;

    public function deleteByType(AddressType $type): bool;

    public function updateByUser(int $userId, array $data): bool;

    // Address Path Methods
    public function getAddressPath(Address $address): Collection;

    public function getAddressPathDTO(Address $address): Collection;

    // Address Statistics
    public function getAddressStats(int $userId): array;

    public function getAddressStatsByType(int $userId): array;
}
