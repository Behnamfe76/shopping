<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasDefaultItem;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Fereydooni\Shopping\app\Models\Address;
use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Log;

class AddressService
{
    use HasCrudOperations, HasDefaultItem, HasSearchOperations;

    public function __construct(
        private AddressRepositoryInterface $repository
    ) {
    }

    // Address-specific methods that extend the traits

    /**
     * Get addresses by type
     */
    public function findByType(AddressType $type): Collection
    {
        return $this->repository->findByType($type);
    }

    /**
     * Get addresses by user and type
     */
    public function findByUserAndType(int $userId, AddressType $type): Collection
    {
        return $this->repository->findByUserAndType($userId, $type);
    }

    /**
     * Get addresses by user and type as DTOs
     */
    public function findByUserAndTypeDTO(int $userId, AddressType $type): Collection
    {
        $addresses = $this->repository->findByUserAndType($userId, $type);
        return $addresses->map(fn($address) => AddressDTO::fromModel($address));
    }

    /**
     * Count addresses by type
     */
    public function countByType(AddressType $type): int
    {
        return $this->repository->countByType($type);
    }

    /**
     * Count addresses by user and type
     */
    public function countByUserAndType(int $userId, AddressType $type): int
    {
        return $this->repository->countByUserAndType($userId, $type);
    }

    /**
     * Get address statistics for user
     */
    public function getAddressStats(int $userId): array
    {
        return $this->repository->getAddressStats($userId);
    }

    /**
     * Get address statistics by type for user
     */
    public function getAddressStatsByType(int $userId): array
    {
        return $this->repository->getAddressStatsByType($userId);
    }

    /**
     * Delete addresses by type
     */
    public function deleteByType(AddressType $type): bool
    {
        return $this->repository->deleteByType($type);
    }

    /**
     * Get address path
     */
    public function getAddressPath(Address $address): Collection
    {
        return $this->repository->getAddressPath($address);
    }

    /**
     * Get address path as DTOs
     */
    public function getAddressPathDTO(Address $address): Collection
    {
        return $this->repository->getAddressPathDTO($address);
    }

    // Override trait methods for Address-specific logic

    /**
     * Override create method to handle address-specific logic
     */
    public function create(array $data): Address
    {
        // Handle default address logic
        $this->handleDefaultItemLogic($data);

        $this->validateData($data);
        return $this->repository->create($data);
    }

    /**
     * Override createDTO method to handle address-specific logic
     */
    public function createDTO(array $data): AddressDTO
    {
        // Handle default address logic
        $this->handleDefaultItemLogic($data);

        $this->validateData($data);
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Override update method to handle address-specific logic
     */
    public function update(Address $address, array $data): bool
    {
        // Handle default address logic
        $this->handleDefaultItemLogicUpdate($address, $data);

        $this->validateData($data, $address);
        return $this->repository->update($address, $data);
    }

    /**
     * Override updateDTO method to handle address-specific logic
     */
    public function updateDTO(Address $address, array $data): ?AddressDTO
    {
        // Handle default address logic
        $this->handleDefaultItemLogicUpdate($address, $data);

        $this->validateData($data, $address);
        return $this->repository->updateAndReturnDTO($address, $data);
    }

    /**
     * Override delete method to handle address-specific logic
     */
    public function delete(Address $address): bool
    {
        // Check if this is the last address of its type for the user
        $this->handleLastAddressDeletion($address);

        return $this->repository->delete($address);
    }

    /**
     * Handle last address deletion logic
     */
    protected function handleLastAddressDeletion(Address $address): void
    {
        $userId = $address->user_id;
        $addressType = $address->type;

        // Get all addresses of the same type for this user
        $addressesOfType = $this->repository->findByUserAndType($userId, $addressType);

        // If this is the only address of this type
        if ($addressesOfType->count() === 1) {
            // Check if there are addresses of the other type
            $otherType = $addressType === AddressType::BILLING ? AddressType::SHIPPING : AddressType::BILLING;
            $addressesOfOtherType = $this->repository->findByUserAndType($userId, $otherType);

            // If there are addresses of the other type, we can safely delete this one
            if ($addressesOfOtherType->isNotEmpty()) {
                return;
            }

            // If this is the last address overall, we can delete it
            $totalAddresses = $this->repository->countByUser($userId);
            if ($totalAddresses === 1) {
                return;
            }

            // If this is the last address of this type but there are other types,
            // we should prevent deletion or handle it differently
            // For now, we'll allow deletion but log a warning
            Log::warning("Deleting last address of type {$addressType->value} for user {$userId}");
        }
    }

    /**
     * Override isItemComplete method for Address-specific validation
     */
    protected function isItemComplete(object $item): bool
    {
        return !empty($item->first_name) &&
               !empty($item->last_name) &&
               !empty($item->address_line_1) &&
               !empty($item->city) &&
               !empty($item->state) &&
               !empty($item->postal_code) &&
               !empty($item->country);
    }

    /**
     * Override getSearchableFields method for Address-specific fields
     */
    protected function getSearchableFields(): array
    {
        return [
            'first_name',
            'last_name',
            'company_name',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'postal_code',
            'country',
            'phone',
            'email'
        ];
    }

    /**
     * Override getSuggestionText method for Address-specific text
     */
    protected function getSuggestionText(object $item): string
    {
        return $item->first_name . ' ' . $item->last_name . ' - ' . $item->full_address;
    }

    /**
     * Override convertToDTO method for Address-specific conversion
     */
    protected function convertToDTO(object $item): AddressDTO
    {
        return AddressDTO::fromModel($item);
    }

    /**
     * Override getDtoClass method for Address-specific DTO
     */
    protected function getDtoClass(): string
    {
        return AddressDTO::class;
    }
}
