<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

trait HasCrudOperations
{
    /**
     * Get all items
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Find item by ID
     */
    public function find(int $id): ?object
    {
        return $this->repository->find($id);
    }

    /**
     * Find item by ID and return as DTO
     */
    public function findDTO(int $id): ?Data
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find items by user ID
     */
    public function findByUser(int $userId): Collection
    {
        return $this->repository->findByUser($userId);
    }

    /**
     * Find items by user ID and return as DTOs
     */
    public function findByUserDTO(int $userId): Collection
    {
        return $this->repository->findByUserDTO($userId);
    }

    /**
     * Create new item
     */
    public function create(array $data): object
    {
        $this->validateData($data);
        return $this->repository->create($data);
    }

    /**
     * Create new item and return as DTO
     */
    public function createDTO(array $data): Data
    {
        $this->validateData($data);
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Update item
     */
    public function update(object $item, array $data): bool
    {
        $this->validateData($data, $item);
        return $this->repository->update($item, $data);
    }

    /**
     * Update item and return as DTO
     */
    public function updateDTO(object $item, array $data): ?Data
    {
        $this->validateData($data, $item);
        return $this->repository->updateAndReturnDTO($item, $data);
    }

    /**
     * Delete item
     */
    public function delete(object $item): bool
    {
        return $this->repository->delete($item);
    }

    /**
     * Paginate items (regular pagination)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Paginate items by user (regular pagination)
     */
    public function paginateByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByUser($userId, $perPage);
    }

    /**
     * Simple paginate items
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Simple paginate items by user
     */
    public function simplePaginateByUser(int $userId, int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginateByUser($userId, $perPage);
    }

    /**
     * Cursor paginate items
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Cursor paginate items by user
     */
    public function cursorPaginateByUser(int $userId, int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginateByUser($userId, $perPage, $cursor);
    }

    /**
     * Count items by user
     */
    public function countByUser(int $userId): int
    {
        return $this->repository->countByUser($userId);
    }

    /**
     * Delete all items by user
     */
    public function deleteByUser(int $userId): bool
    {
        return $this->repository->deleteByUser($userId);
    }

    /**
     * Update all items by user
     */
    public function updateByUser(int $userId, array $data): bool
    {
        return $this->repository->updateByUser($userId, $data);
    }

    /**
     * Validate data using DTO rules
     */
    protected function validateData(array $data, ?object $item = null): void
    {
        $dtoClass = $this->getDtoClass();

        if (!class_exists($dtoClass)) {
            throw new \InvalidArgumentException("DTO class {$dtoClass} not found");
        }

        $rules = $dtoClass::rules();
        $messages = $dtoClass::messages();

        // Remove user_id validation for updates
        if ($item && isset($rules['user_id'])) {
            unset($rules['user_id']);
        }

        // Set user_id if not provided (assuming authenticated user)
        if (!isset($data['user_id']) && !$item) {
            $data['user_id'] = auth()->id();
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Get the DTO class for this service
     */
    protected function getDtoClass(): string
    {
        // Extract model name from repository class
        $repositoryClass = get_class($this->repository);
        $modelName = class_basename(str_replace('Repository', '', $repositoryClass));

        return "Fereydooni\\Shopping\\app\\DTOs\\{$modelName}DTO";
    }

    /**
     * Check if user can perform action on item
     */
    protected function canUserPerformAction(object $item, string $action): bool
    {
        $userId = auth()->id();

        // Check if item belongs to user
        if (isset($item->user_id) && $item->user_id !== $userId) {
            return false;
        }

        // Check permissions
        $permission = $this->getPermissionName($action);
        return auth()->user()->can($permission);
    }

    /**
     * Get permission name for action
     */
    protected function getPermissionName(string $action): string
    {
        $modelName = strtolower(class_basename($this->repository));
        return "{$modelName}.{$action}";
    }
}
