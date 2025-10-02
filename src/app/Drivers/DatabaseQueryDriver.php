<?php

namespace Fereydooni\Shopping\app\Drivers;

use Fereydooni\Shopping\app\Contracts\QueryDriverInterface;
use Fereydooni\Shopping\app\Traits\AppliesQueryParameters;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class DatabaseQueryDriver implements QueryDriverInterface
{
    use AppliesQueryParameters;

    protected array $searchableFields = [];

    public function paginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->buildQuery($model, $filters, $searchOptions);
        return $query->paginate($perPage);
    }

    public function simplePaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): Paginator
    {
        $query = $this->buildQuery($model, $filters, $searchOptions);
        return $query->simplePaginate($perPage);
    }

    public function cursorPaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $query = $this->buildQuery($model, $filters, $searchOptions);
        return $query->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function search(string $model, string $query, array $fields = [], array $filters = []): Collection
    {
        $searchFields = $fields ?: $model::searchableFields();
        $modelQuery = $model::where(function ($q) use ($query, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        });

        $modelQuery = $this->applyFilters($modelQuery, $filters);

        return $modelQuery->get();
    }

    public function all(string $model, array $filters = []): Collection
    {
        $query = $model::query();
        $query = $this->applyFilters($query, $filters);
        return $query->get();
    }

    public function applyFilters($query, array $filters = [])
    {
        if (empty($filters)) {
            return $query;
        }

        $booleanFields = ['is_active', 'is_published', 'is_featured'];

        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                if (in_array($key, $booleanFields)) {
                    $booleanValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $booleanLiteral = $booleanValue ? 'TRUE' : 'FALSE';
                    $quotedColumn = $query->getGrammar()->wrap($key);
                    $query->whereRaw("{$quotedColumn} IS {$booleanLiteral}");
                } else {
                    $query->where($key, $value);
                }
            }
        }

        return $query;
    }

    public function applySearch($query, string $searchTerm = '', array $searchOptions = [], array $searchableFields = [])
    {
        if (empty($searchTerm)) {
            return $query;
        }

        $matchType = $searchOptions['match_type'] ?? 'partial';
        $caseSensitive = $searchOptions['case_sensitive'] ?? false;
        $wordMatching = $searchOptions['word_matching'] ?? false;
        $multipleTermsLogic = $searchOptions['multiple_terms_logic'] ?? 'or';

        $dbDriver = config('database.connections.' . config('database.default') . '.driver');
        $searchTerms = preg_split('/\\s+\//', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

        $query->where(function ($q) use ($searchTerms, $searchableFields, $matchType, $dbDriver, $caseSensitive, $wordMatching, $multipleTermsLogic) {
            foreach ($searchableFields as $field) {
                foreach ($searchTerms as $term) {
                    $currentOperator = 'like';
                    $currentTerm = $term;

                    if ($wordMatching && $matchType === 'partial') {
                        $currentTerm = "{$term}";
                    }

                    switch ($matchType) {
                        case 'exact':
                            $currentOperator = '=';
                            break;
                        case 'starts_with':
                            $currentTerm = "{$term}%";
                            break;
                        case 'ends_with':
                            $currentTerm = "%{$term}";
                            break;
                        case 'partial':
                        default:
                            $currentTerm = "%{$term}%";
                            break;
                    }

                    $whereMethod = $multipleTermsLogic === 'and' ? 'where' : 'orWhere';

                    if ($dbDriver === 'sqlite' && !$caseSensitive) {
                        $q->{$whereMethod}->Raw("LOWER({$field}) {$currentOperator} LOWER(?)", [$currentTerm]);
                    } else {
                        $q->{$whereMethod}($field, $currentOperator, $currentTerm);
                    }
                }
            }
        });

        return $query;
    }

    public function applySorting($query, array $sortOptions = [])
    {
        if (empty($sortOptions)) {
            return $query;
        }

        $sortField = $sortOptions['sort_field'] ?? 'id';
        $sortDirection = $sortOptions['sort_direction'] ?? 'asc';

        return $query->orderBy($sortField, $sortDirection);
    }

    public function supports(string $model): bool
    {
        return class_exists($model) && is_subclass_of($model, Model::class);
    }

    public function getDriverName(): string
    {
        return 'database';
    }

    protected function buildQuery(string $model, array $filters = [], array $searchOptions = []): Builder
    {
        $query = $model::query();

        $query = $this->applyFilters($query, $filters);

        if (!empty($searchOptions['search'])) {
            $searchableFields = $searchOptions['search_fields'] ?: $model::searchableFields();
            $query = $this->applySearch($query, $searchOptions['search'], $searchOptions, $searchableFields);
        }

        $query = $this->applySorting($query, $searchOptions);

        return $query;
    }
}
