<?php

namespace Fereydooni\Shopping\app\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

interface QueryDriverInterface
{
    /**
     * Get paginated results
     */
    public function paginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated results
     */
    public function simplePaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): Paginator;

    /**
     * Get cursor paginated results
     */
    public function cursorPaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Search records
     */
    public function search(string $model, string $query, array $fields = [], array $filters = []): Collection;

    /**
     * Get all records
     */
    public function all(string $model, array $filters = []): Collection;

    /**
     * Apply filters to the query
     */
    public function applyFilters($query, array $filters = []);

    /**
     * Apply search to the query
     */
    public function applySearch($query, string $searchTerm = '', array $searchOptions = [], array $searchableFields = []);

    /**
     * Apply sorting to the query
     */
    public function applySorting($query, array $sortOptions = [], string $model);

    /**
     * Check if this driver supports the given model
     */
    public function supports(string $model): bool;

    /**
     * Get the driver name
     */
    public function getDriverName(): string;
}
