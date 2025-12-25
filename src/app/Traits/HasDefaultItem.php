<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

trait HasDefaultItem
{
    /**
     * Set item as default
     */
    public function setDefault(object $item): bool
    {
        $this->validateDefaultItem($item);

        return $this->repository->setDefault($item);
    }

    /**
     * Set item as default and return as DTO
     */
    public function setDefaultDTO(object $item): ?Data
    {
        $this->validateDefaultItem($item);
        $success = $this->repository->setDefault($item);

        if (! $success) {
            return null;
        }

        return $this->repository->findDTO($item->id);
    }

    /**
     * Unset default status from item
     */
    public function unsetDefault(object $item): bool
    {
        $this->validateUnsetDefault($item);

        return $this->repository->unsetDefault($item);
    }

    /**
     * Get default item by user and type
     */
    public function getDefaultByUser(int $userId, string $type): ?object
    {
        return $this->repository->getDefaultByUser($userId, $type);
    }

    /**
     * Get default item by user and type as DTO
     */
    public function getDefaultDTOByUser(int $userId, string $type): ?Data
    {
        return $this->repository->getDefaultDTOByUser($userId, $type);
    }

    /**
     * Get all default items for user
     */
    public function getDefaultItems(int $userId): Collection
    {
        return $this->repository->getDefaultAddresses($userId);
    }

    /**
     * Get all default items for user as DTOs
     */
    public function getDefaultItemsDTO(int $userId): Collection
    {
        return $this->repository->getDefaultAddressesDTO($userId);
    }

    /**
     * Check if user has default item of type
     */
    public function hasDefaultItem(int $userId, string $type): bool
    {
        return $this->repository->hasDefaultAddress($userId, $type);
    }

    /**
     * Check if item can be set as default
     */
    public function canSetDefault(object $item): bool
    {
        return $this->repository->canSetDefault($item);
    }

    /**
     * Check if item is default
     */
    public function isDefault(object $item): bool
    {
        return $this->repository->isDefault($item);
    }

    /**
     * Handle default item logic during creation
     */
    protected function handleDefaultItemLogic(array &$data): void
    {
        // If this item is being set as default
        if (isset($data['is_default']) && $data['is_default']) {
            $type = $data['type'] ?? null;
            $userId = $data['user_id'];

            if ($type && $this->repository->hasDefaultAddress($userId, $type)) {
                // Unset the existing default item
                $existingDefault = $this->repository->getDefaultByUser($userId, $type);
                if ($existingDefault) {
                    $this->repository->unsetDefault($existingDefault);
                }
            }
        } else {
            // If no default item exists for this type, make this one default
            $type = $data['type'] ?? null;
            $userId = $data['user_id'];

            if ($type && ! $this->repository->hasDefaultAddress($userId, $type)) {
                $data['is_default'] = true;
            }
        }
    }

    /**
     * Handle default item logic during update
     */
    protected function handleDefaultItemLogicUpdate(object $item, array &$data): void
    {
        // If this item is being set as default
        if (isset($data['is_default']) && $data['is_default']) {
            $type = $data['type'] ?? $item->type;
            $userId = $item->user_id;

            // Check if user already has a default item of this type (excluding current item)
            $existingDefault = $this->repository->getDefaultByUser($userId, $type);

            if ($existingDefault && $existingDefault->id !== $item->id) {
                // Unset the existing default item
                $this->repository->unsetDefault($existingDefault);
            }
        } else {
            // If item type is being changed and this was the default
            if (isset($data['type']) && $data['type'] !== $item->type && $item->is_default) {
                // Check if there are other items of the new type
                $otherItems = $this->repository->findByUserAndType($item->user_id, $data['type']);

                if ($otherItems->isEmpty()) {
                    // No other items of this type, keep as default
                    $data['is_default'] = true;
                }
            }
        }
    }

    /**
     * Validate default item
     */
    protected function validateDefaultItem(object $item): void
    {
        $validator = Validator::make([], []);

        // Check if user owns this item
        if (isset($item->user_id) && $item->user_id !== auth()->id()) {
            $validator->errors()->add('item', 'You can only set your own items as default.');
        }

        // Check if item is already default
        if (isset($item->is_default) && $item->is_default) {
            $validator->errors()->add('item', 'This item is already set as default.');
        }

        // Check if item is valid (has required fields)
        if (! $this->isItemComplete($item)) {
            $validator->errors()->add('item', 'Item must have complete information to be set as default.');
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate unset default
     */
    protected function validateUnsetDefault(object $item): void
    {
        $validator = Validator::make([], []);

        // Check if user owns this item
        if (isset($item->user_id) && $item->user_id !== auth()->id()) {
            $validator->errors()->add('item', 'You can only modify your own items.');
        }

        // Check if this is the only item of this type
        if (isset($item->type)) {
            $itemsOfType = $this->repository->findByUserAndType($item->user_id, $item->type);

            if ($itemsOfType->count() === 1 && $item->is_default) {
                $validator->errors()->add('item', 'Cannot unset default status from the only item of this type.');
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Check if item is complete (has required fields)
     */
    protected function isItemComplete(object $item): bool
    {
        // This method should be overridden in specific services
        // to check if the item has all required fields
        return true;
    }
}
