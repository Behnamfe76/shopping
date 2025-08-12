<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

trait HasSearchOperations
{
    /**
     * Search items
     */
    public function search(string $query, ?int $userId = null, ?string $type = null): Collection
    {
        $this->validateSearchQuery($query);

        // Perform search based on parameters
        if ($userId && $type) {
            $items = $this->repository->findByUserAndType($userId, $type);
            $items = $this->filterItemsByQuery($items, $query);
        } elseif ($userId) {
            $items = $this->repository->searchByUser($userId, $query);
        } elseif ($type) {
            $items = $this->repository->findByType($type);
            $items = $this->filterItemsByQuery($items, $query);
        } else {
            $items = $this->repository->search($query);
        }

        // Convert to DTOs
        return $items->map(fn($item) => $this->convertToDTO($item));
    }

    /**
     * Search items with pagination
     */
    public function searchWithPagination(
        string $query,
        int $perPage = 15,
        ?int $userId = null,
        ?string $type = null,
        string $paginationType = 'regular'
    ): mixed {
        $this->validateSearchQuery($query);

        // Perform search with pagination
        switch ($paginationType) {
            case 'simple':
                return $this->searchWithSimplePagination($query, $perPage, $userId, $type);
            case 'cursor':
                return $this->searchWithCursorPagination($query, $perPage, $userId, $type);
            default:
                return $this->searchWithRegularPagination($query, $perPage, $userId, $type);
        }
    }

    /**
     * Get search suggestions
     */
    public function getSearchSuggestions(string $query, ?int $userId = null): array
    {
        $this->validateSearchQuery($query);

        $items = $this->search($query, $userId);

        $suggestions = [];

        foreach ($items as $item) {
            $suggestions[] = [
                'id' => $item->id,
                'text' => $this->getSuggestionText($item),
                'item' => $item,
            ];
        }

        return $suggestions;
    }

    /**
     * Search by specific field
     */
    public function searchByField(string $field, string $value, ?int $userId = null): Collection
    {
        $this->validateSearchField($field);

        $items = $this->repository->searchByField($field, $value, $userId);

        return $items->map(fn($item) => $this->convertToDTO($item));
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(array $criteria, ?int $userId = null): Collection
    {
        $this->validateSearchCriteria($criteria);

        $items = $this->repository->advancedSearch($criteria, $userId);

        return $items->map(fn($item) => $this->convertToDTO($item));
    }

    /**
     * Validate search query
     */
    protected function validateSearchQuery(string $query): void
    {
        $validator = Validator::make(['query' => $query], [
            'query' => 'required|string|min:2|max:255',
        ], [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate search field
     */
    protected function validateSearchField(string $field): void
    {
        $allowedFields = $this->getSearchableFields();

        if (!in_array($field, $allowedFields)) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('field', "Field '{$field}' is not searchable.")
            );
        }
    }

    /**
     * Validate search criteria
     */
    protected function validateSearchCriteria(array $criteria): void
    {
        $allowedFields = $this->getSearchableFields();

        foreach ($criteria as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                throw new ValidationException(
                    Validator::make([], [])->errors()->add('criteria', "Field '{$field}' is not searchable.")
                );
            }
        }
    }

    /**
     * Filter items by query
     */
    protected function filterItemsByQuery(Collection $items, string $query): Collection
    {
        $query = strtolower($query);
        $searchableFields = $this->getSearchableFields();

        return $items->filter(function ($item) use ($query, $searchableFields) {
            foreach ($searchableFields as $field) {
                if (isset($item->$field) && str_contains(strtolower($item->$field), $query)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Search with regular pagination
     */
    protected function searchWithRegularPagination(
        string $query,
        int $perPage,
        ?int $userId,
        ?string $type
    ): LengthAwarePaginator {
        $items = $this->search($query, $userId, $type);

        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedItems = $items->slice($offset, $perPage);

        return new LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Search with simple pagination
     */
    protected function searchWithSimplePagination(
        string $query,
        int $perPage,
        ?int $userId,
        ?string $type
    ): Paginator {
        $items = $this->search($query, $userId, $type);

        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedItems = $items->slice($offset, $perPage);

        return new Paginator(
            $paginatedItems,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Search with cursor pagination
     */
    protected function searchWithCursorPagination(
        string $query,
        int $perPage,
        ?int $userId,
        ?string $type
    ): CursorPaginator {
        $items = $this->search($query, $userId, $type);

        $cursor = request()->get('cursor');
        $cursorIndex = 0;

        if ($cursor) {
            $cursorIndex = $items->search(function ($item) use ($cursor) {
                return $item->id == $cursor;
            });

            if ($cursorIndex === false) {
                $cursorIndex = 0;
            } else {
                $cursorIndex++;
            }
        }

        $paginatedItems = $items->slice($cursorIndex, $perPage);

        return new CursorPaginator(
            $paginatedItems,
            $perPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Get searchable fields for this model
     */
    protected function getSearchableFields(): array
    {
        // This method should be overridden in specific services
        // to return the searchable fields for the model
        return ['name', 'description'];
    }

    /**
     * Get suggestion text for item
     */
    protected function getSuggestionText(object $item): string
    {
        // This method should be overridden in specific services
        // to return a meaningful suggestion text for the item
        return $item->name ?? $item->id;
    }

    /**
     * Convert item to DTO
     */
    protected function convertToDTO(object $item): Data
    {
        $dtoClass = $this->getDtoClass();
        return $dtoClass::fromModel($item);
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
}
