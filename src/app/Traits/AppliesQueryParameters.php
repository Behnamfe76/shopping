<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Builder;

// Add this line

trait AppliesQueryParameters
{
    protected function applyFilters(Builder $query): Builder
    {
        if (request()->has('filters')) {
            $booleanFields = ['is_active', 'is_published', 'is_featured'];

            foreach (request()->get('filters') as $key => $value) {
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
        }

        return $query;
    }

    protected function applySearch(Builder $query): Builder
    {
        $searchableFields = $this->searchableFields ?? [];
        $searchOptions = request()->get('search_options', []);
        $search_fields = $searchOptions['search_fields'] ?? [];

        if (is_array($search_fields) && count($search_fields)) {
            $searchableFields = $search_fields;
        }

        if (request()->has('search')) {
            $searchTerm = request()->get('search');
            $matchType = $searchOptions['match_type'] ?? 'partial'; // Default to partial
            $caseSensitive = $searchOptions['case_sensitive'] ?? false;
            $wordMatching = $searchOptions['word_matching'] ?? false;
            $multipleTermsLogic = $searchOptions['multiple_terms_logic'] ?? 'or'; // Default to OR

            $dbDriver = config('database.connections.'.config('database.default').'.driver');

            $searchTerms = preg_split('/\\s+\//', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

            $query->where(function ($q) use ($searchTerms, $searchableFields, $matchType, $dbDriver, $caseSensitive, $wordMatching, $multipleTermsLogic) {
                $whereMethod = ($multipleTermsLogic === 'and') ? 'where' : 'orWhere';

                foreach ($searchableFields as $field) {
                    foreach ($searchTerms as $term) {
                        // Initialize operator and term for the current iteration
                        $currentOperator = 'like';
                        $currentTerm = $term;

                        // Apply whole word matching if enabled and not exact match (exact handles it implicitly)
                        if ($wordMatching && $matchType === 'partial') {
                            $currentTerm = "{$term}"; // No wildcards for whole word partial match
                        }

                        switch ($matchType) {
                            case 'exact':
                                $currentOperator = '=';
                                // For exact, whole word matching is implicit, no wildcards needed
                                $this->applyCaseSensitiveWhere($q, $whereMethod, $field, $currentOperator, $currentTerm, $caseSensitive, $dbDriver);
                                break;
                            case 'fuzzy':
                                if ($dbDriver === 'pgsql') {
                                    $q->{$whereMethod}->Raw("{$field} % ?", [$term]);
                                } else {
                                    // Fallback for non-PostgreSQL fuzzy: partial match
                                    $this->applyCaseSensitiveWhere($q, $whereMethod, $field, 'like', "%{$term}%", $caseSensitive, $dbDriver);
                                }
                                break;
                            case 'phonetic':
                                if ($dbDriver === 'pgsql') {
                                    $q->{$whereMethod}->Raw("SOUNDEX({$field}) = SOUNDEX(?)", [$term]);
                                } else {
                                    // Fallback for non-PostgreSQL phonetic: partial match
                                    $this->applyCaseSensitiveWhere($q, $whereMethod, $field, 'like', "%{$term}%", $caseSensitive, $dbDriver);
                                }
                                break;
                            case 'partial':
                            default:
                                // For partial match, adjust term for wildcards if not whole word matching
                                if (! $wordMatching) {
                                    $currentTerm = "%{$term}%";
                                }
                                $this->applyCaseSensitiveWhere($q, $whereMethod, $field, $currentOperator, $currentTerm, $caseSensitive, $dbDriver);
                                break;
                        }
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Helper to apply where conditions with case sensitivity logic.
     */
    protected function applyCaseSensitiveWhere(Builder $q, string $whereMethod, string $field, string $operator, string $term, bool $caseSensitive, string $dbDriver): void
    {
        if ($dbDriver === 'pgsql') {
            if ($caseSensitive) {
                $q->{$whereMethod}($field, $operator, $term); // PostgreSQL LIKE is case-sensitive by default (unless C locale)
            } elseif (! $caseSensitive && $operator !== 'like') {
                $q->{$whereMethod}($field, $operator, $term);
            } else {
                $q->{$whereMethod}($field, 'ilike', $term); // PostgreSQL ILIKE is case-insensitive
            }
        } else {
            if ($caseSensitive) {
                $q->{$whereMethod}($field, $operator, $term);
            } else {
                // For other databases, convert to lower for case-insensitivity
                $q->{$whereMethod}->Raw("LOWER({$field}) {$operator} LOWER(?)", [$term]);
            }
        }
    }

    protected function applySorting(Builder $query, string $defaultSortBy = 'id', string $defaultSortDirection = 'asc'): Builder
    {
        $sortBy = request()->get('sort_by', $defaultSortBy);
        $sortDirection = request()->get('sort_direction', $defaultSortDirection);

        return $query->orderBy($sortBy, $sortDirection);
    }
}
