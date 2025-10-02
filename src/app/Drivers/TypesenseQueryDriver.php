<?php

namespace Fereydooni\Shopping\app\Drivers;

use Laravel\Scout\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Contracts\QueryDriverInterface;

class TypesenseQueryDriver implements QueryDriverInterface
{
    protected array $searchableFields = [];
    protected $model;

    public function paginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): LengthAwarePaginator
    {
            $builder = $this->buildScoutQuery($model, $filters, $searchOptions);

            return $builder->paginate($perPage);
    }

    public function simplePaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15): Paginator
    {
        $builder = $this->buildScoutQuery($model, $filters, $searchOptions);
        return $builder->simplePaginate($perPage);
    }

    public function cursorPaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $builder = $this->buildScoutQuery($model, $filters, $searchOptions);
        return $builder->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function search(string $model, string $query, array $fields = [], array $filters = []): Collection
    {
        $searchFields = $fields ?: $model::searchableFields();

        $builder = $model::search($query);

        $builder = $this->applyFilters($builder, $filters);

        return $builder->get();
    }

    public function all(string $model, array $filters = []): Collection
    {
        $builder = $model::search('*');
        $builder = $this->applyFilters($builder, $filters);
        return $builder->get();
    }

    public function applyFilters($query, array $filters = [])
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                if (is_bool($value) || in_array($value, ['true', 'false', '1', '0'])) {
                    $booleanValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where($key, $booleanValue);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Apply comprehensive Typesense search with all available capabilities
     *
     * @param mixed $query Scout builder query
     * @param string $searchTerm Search query
     * @param array $searchOptions Configuration options
     * @param array $searchableFields Fields to search in
     * @return mixed
     */
    public function applySearch($query, string $searchTerm = '', array $searchOptions = [], array $searchableFields = [])
    {
        if (empty($searchTerm)) {
            return $query;
        }

        $searchableFields = $searchableFields ?: $this->model::searchableFields();

        // Build Typesense options based on search configuration
        $typesenseOptions = $this->buildTypesenseOptions($searchOptions, $searchableFields);
        
        // Apply the search with options
        return $query->options($typesenseOptions);
    }

    /**
     * Build Typesense options array based on search configuration
     */
    protected function buildTypesenseOptions(array $config, array $searchableFields): array
    {
        $options = [];

        // ==================== SEARCH FIELDS ====================
        $options['query_by'] = implode(',', $searchableFields);

        // Field weights (if provided)
        if (!empty($config['field_weights'])) {
            $options['query_by_weights'] = implode(',', $config['field_weights']);
        }

        // ==================== MATCH TYPE ====================
        $matchType = $config['match_type'] ?? 'partial';

        switch ($matchType) {
            case 'exact':
                // Exact match - disable fuzzy features
                $options['num_typos'] = 0;
                $options['prefix'] = false;
                $options['infix'] = 'off';
                $options['prioritize_exact_match'] = true;
                break;

            case 'prefix':
            case 'starts_with':
                // Prefix matching
                $options['prefix'] = true;
                $options['infix'] = 'off';
                $options['num_typos'] = $config['typo_tolerance'] ?? 1;
                break;

            case 'infix':
            case 'contains':
                // Match anywhere in words
                $options['infix'] = 'always';
                $options['prefix'] = true;
                $options['num_typos'] = $config['typo_tolerance'] ?? 1;
                break;

            case 'fuzzy':
                // Maximum fuzzy matching
                $options['num_typos'] = $config['typo_tolerance'] ?? 2;
                $options['prefix'] = true;
                $options['infix'] = 'fallback';
                $options['split_join_tokens'] = 'fallback';
                $options['max_candidates'] = 10000;
                break;

            case 'semantic':
                // Semantic/vector search
                if (!empty($config['embedding_field'])) {
                    $embeddingField = $config['embedding_field'];
                    $k = $config['k'] ?? 100;
                    $alpha = $config['alpha'] ?? 0.5;

                    $options['vector_query'] = "{$embeddingField}:([], k:{$k}, alpha:{$alpha})";
                    $options['exclude_fields'] = $embeddingField; // Don't return large embeddings
                }
                break;

            case 'hybrid':
                // Combine keyword + semantic search
                if (!empty($config['embedding_field'])) {
                    $embeddingField = $config['embedding_field'];
                    $k = $config['k'] ?? 100;
                    $alpha = $config['alpha'] ?? 0.3; // 30% weight to vector, 70% to keyword

                    $options['vector_query'] = "{$embeddingField}:([], k:{$k}, alpha:{$alpha})";
                    $options['exclude_fields'] = $embeddingField;
                    $options['num_typos'] = $config['typo_tolerance'] ?? 1;
                    $options['prefix'] = true;
                }
                break;

            case 'partial':
            default:
                // Default balanced search
                $options['num_typos'] = $config['typo_tolerance'] ?? 2;
                $options['prefix'] = true;
                $options['infix'] = 'fallback';
                break;
        }
        
        // ==================== TYPO TOLERANCE ====================
        if (isset($config['typo_tolerance'])) {
            $options['num_typos'] = min(2, max(0, $config['typo_tolerance']));
        }

        if (isset($config['min_len_1typo'])) {
            $options['min_len_1typo'] = $config['min_len_1typo'];
        }

        if (isset($config['min_len_2typo'])) {
            $options['min_len_2typo'] = $config['min_len_2typo'];
        }

        // ==================== TOKEN HANDLING ====================
        if (isset($config['drop_tokens_threshold'])) {
            $options['drop_tokens_threshold'] = $config['drop_tokens_threshold'];
        }

        if (isset($config['split_join_tokens'])) {
            $options['split_join_tokens'] = $config['split_join_tokens'];
        }

        // ==================== STOPWORDS ====================
        if (!empty($config['stopwords'])) {
            $options['stopwords'] = is_array($config['stopwords'])
                ? implode(',', $config['stopwords'])
                : $config['stopwords'];
        }

        // ==================== SORTING ====================
        if (!empty($config['sort_by'])) {
            $options['sort_by'] = $config['sort_by'];
        }

        // ==================== FILTERING ====================
        if (!empty($config['filter_by'])) {
            $options['filter_by'] = $config['filter_by'];
        }

        // ==================== FACETING ====================
        if (!empty($config['facet_by'])) {
            $options['facet_by'] = is_array($config['facet_by'])
                ? implode(',', $config['facet_by'])
                : $config['facet_by'];
        }

        if (isset($config['max_facet_values'])) {
            $options['max_facet_values'] = $config['max_facet_values'];
        }

        // ==================== GROUPING ====================
        if (!empty($config['group_by'])) {
            $options['group_by'] = $config['group_by'];
            $options['group_limit'] = $config['group_limit'] ?? 3;
        }

        // ==================== HIGHLIGHTING ====================
        if (!empty($config['highlight_fields'])) {
            $options['highlight_fields'] = is_array($config['highlight_fields'])
                ? implode(',', $config['highlight_fields'])
                : $config['highlight_fields'];
        }

        if (!empty($config['highlight_full_fields'])) {
            $options['highlight_full_fields'] = is_array($config['highlight_full_fields'])
                ? implode(',', $config['highlight_full_fields'])
                : $config['highlight_full_fields'];
        }

        // ==================== PAGINATION ====================
        if (isset($config['per_page'])) {
            $options['per_page'] = $config['per_page'];
        }

        if (isset($config['page'])) {
            $options['page'] = $config['page'];
        }

        // ==================== RESULT FIELDS ====================
        if (!empty($config['include_fields'])) {
            $options['include_fields'] = is_array($config['include_fields'])
                ? implode(',', $config['include_fields'])
                : $config['include_fields'];
        }

        if (!empty($config['exclude_fields'])) {
            $existingExclude = $options['exclude_fields'] ?? '';
            $newExclude = is_array($config['exclude_fields'])
                ? implode(',', $config['exclude_fields'])
                : $config['exclude_fields'];

            $options['exclude_fields'] = $existingExclude
                ? "{$existingExclude},{$newExclude}"
                : $newExclude;
        }

        // ==================== RANKING PRIORITIES ====================
        if (isset($config['prioritize_exact_match'])) {
            $options['prioritize_exact_match'] = $config['prioritize_exact_match'];
        }

        if (isset($config['prioritize_token_position'])) {
            $options['prioritize_token_position'] = $config['prioritize_token_position'];
        }

        if (isset($config['prioritize_num_matching_fields'])) {
            $options['prioritize_num_matching_fields'] = $config['prioritize_num_matching_fields'];
        }

        // ==================== PERFORMANCE ====================
        if (isset($config['exhaustive_search'])) {
            $options['exhaustive_search'] = $config['exhaustive_search'];
        }

        if (isset($config['use_cache'])) {
            $options['use_cache'] = $config['use_cache'];
        }

        if (isset($config['cache_ttl'])) {
            $options['cache_ttl'] = $config['cache_ttl'];
        }

        // ==================== PINNING & HIDING ====================
        if (!empty($config['pinned_hits'])) {
            $options['pinned_hits'] = $config['pinned_hits'];
        }

        if (!empty($config['hidden_hits'])) {
            $options['hidden_hits'] = $config['hidden_hits'];
        }

        return $options;
    }

    /**
     * Preset search configurations for common use cases
     */
    public function getSearchPreset(string $preset): array
    {
        $presets = [
            // E-commerce product search - forgiving, user-friendly
            'ecommerce' => [
                'match_type' => 'fuzzy',
                'typo_tolerance' => 2,
                'prefix' => true,
                'infix' => 'fallback',
                'split_join_tokens' => 'fallback',
                'prioritize_exact_match' => true,
                'drop_tokens_threshold' => 1,
            ],

            // Strict technical documentation search
            'technical' => [
                'match_type' => 'exact',
                'typo_tolerance' => 0,
                'prefix' => false,
                'prioritize_exact_match' => true,
            ],

            // Autocomplete/typeahead search
            'autocomplete' => [
                'match_type' => 'prefix',
                'typo_tolerance' => 0,
                'prefix' => true,
                'infix' => 'off',
                'per_page' => 10,
            ],

            // Semantic search with embeddings
            'semantic' => [
                'match_type' => 'semantic',
                'embedding_field' => 'embedding',
                'k' => 100,
                'alpha' => 0.8,
                'exclude_fields' => 'embedding',
            ],

            // Hybrid keyword + semantic search
            'hybrid' => [
                'match_type' => 'hybrid',
                'embedding_field' => 'embedding',
                'k' => 200,
                'alpha' => 0.3, // 30% vector, 70% keyword
                'typo_tolerance' => 1,
                'prefix' => true,
                'drop_tokens_threshold' => 0,
                'exclude_fields' => 'embedding',
            ],

            // Conversational search (for chatbots, RAG)
            'conversational' => [
                'match_type' => 'hybrid',
                'embedding_field' => 'embedding',
                'k' => 50,
                'alpha' => 0.7, // Favor semantic meaning
                'drop_tokens_threshold' => 0,
                'exclude_fields' => 'embedding',
            ],

            // Fast autocorrect search
            'autocorrect' => [
                'match_type' => 'fuzzy',
                'typo_tolerance' => 2,
                'max_candidates' => 4,
                'prefix' => true,
            ],

            // Multilingual search
            'multilingual' => [
                'match_type' => 'partial',
                'typo_tolerance' => 1,
                'prefix' => true,
                'infix' => 'fallback',
            ],
        ];

        return $presets[$preset] ?? $presets['ecommerce'];
    }

    public function applySorting($query, array $sortOptions = [])
    {
        if (empty($sortOptions)) {
            return $query;
        }

        $sortField = $sortOptions['sort_field'] ?? 'id_numeric';
        $sortField = $sortOptions['sort_field'] === 'id' ? 'id_numeric' : $sortField;
        $sortDirection = $sortOptions['sort_direction'] ?? 'asc';

        $stringTypeFields = array_filter($this->model::getTypesenseCollectionSchema()['fields'], fn($field) => $field['type'] === 'string');

        if (in_array($sortField, array_column($stringTypeFields, 'name'))) {
            throw new \Exception("Typesense does not support sorting by string field: {$sortField}", 400);
        }

        return $query->orderBy($sortField, $sortDirection);
    }

    public function supports(string $model): bool
    {
        if (!class_exists($model) || !is_subclass_of($model, Model::class)) {
            return false;
        }

        // Check if the model uses the Searchable trait
        $traits = class_uses_recursive($model);
        return in_array('Laravel\Scout\Searchable', $traits);
    }

    public function getDriverName(): string
    {
        return 'typesense';
    }

    protected function buildScoutQuery(string $model, array $filters = [], array $searchOptions = []): Builder
    {
        $this->model = $model;
        $searchTerm = $searchOptions['search'] ?? '*';

        // If there's a search term, create the builder with it, otherwise use wildcard
        if (!empty($searchTerm) && $searchTerm !== '*') {
            $builder = $model::search($searchTerm);

            // Apply search options to modify the search term if needed
            $builder = $this->applySearch($builder, $searchTerm, $searchOptions, $searchOptions['search_fields']);
        } else {
            $builder = $model::search('*');
        }

        $builder = $this->applyFilters($builder, $filters);
        $builder = $this->applySorting($builder, $searchOptions);

        return $builder;
    }
}
