<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Managers\QueryManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait HasCrudOperations
{
    protected string $model;
    protected string $dtoClass;
    protected ?QueryManager $queryManager = null;

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model::all();
    }

    public function paginate(int $perPage = 15, ?string $driver = null): LengthAwarePaginator
    {
        $queryManager = $this->getQueryManager();
        $filters = $this->getFiltersFromRequest();
        $searchOptions = $this->getSearchOptionsFromRequest();

        return $queryManager->paginate($this->model, $filters, $searchOptions, $perPage, $driver);
    }

    public function simplePaginate(int $perPage = 15, ?string $driver = null): Paginator
    {
        $queryManager = $this->getQueryManager();
        $filters = $this->getFiltersFromRequest();
        $searchOptions = $this->getSearchOptionsFromRequest();

        return $queryManager->simplePaginate($this->model, $filters, $searchOptions, $perPage, $driver);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null, ?string $driver = null): CursorPaginator
    {
        $queryManager = $this->getQueryManager();
        $filters = $this->getFiltersFromRequest();
        $searchOptions = $this->getSearchOptionsFromRequest();

        return $queryManager->cursorPaginate($this->model, $filters, $searchOptions, $perPage, $cursor, $driver);
    }

    public function find(int $id): ?Model
    {
        return $this->model::find($id);
    }

    public function findDTO(int $id): mixed
    {
        $model = $this->find($id);
        return $model ? $this->dtoClass::fromModel($model) : null;
    }

    public function create(array $data): Model
    {
        $validated = $this->validateData($data);
        return $this->model::create($validated);
    }

    public function createAndReturnDTO(array $data): mixed
    {
        $model = $this->create($data);
        return $this->dtoClass::fromModel($model);
    }

    public function update(Model $model, array $data): bool
    {
        $validated = $this->validateData($data, $model->id);
        return $model->update($validated);
    }

    public function updateAndReturnDTO(Model $model, array $data): mixed
    {
        $updated = $this->update($model, $data);
        return $updated ? $this->dtoClass::fromModel($model->fresh()) : null;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    // Bulk operations
    public function bulkCreate(array $items): Collection
    {
        $validatedItems = [];
        foreach ($items as $item) {
            $validatedItems[] = $this->validateData($item);
        }

        return $this->model::insert($validatedItems);
    }

    public function bulkUpdate(array $updates): bool
    {
        return DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                if (!isset($update['id'])) {
                    continue;
                }
                $model = $this->find($update['id']);
                if ($model) {
                    $this->update($model, $update);
                }
            }
            return true;
        });
    }

    public function deleteSome(array $ids): bool
    {
        return $this->model::whereIn('id', $ids)->delete() > 0;
    }

    public function deleteAll(): bool
    {
        $deletedCount = 0;
        $this->model::cursor()->each(function ($model) use (&$deletedCount) {
            if ($model->delete()) {
                $deletedCount++;
            }
        });

        return $deletedCount > 0;
    }

    // Search functionality
    public function search(string $query, array $fields = [], ?string $driver = null): Collection
    {
        $queryManager = $this->getQueryManager();
        $filters = $this->getFiltersFromRequest();

        return $queryManager->search($this->model, $query, $fields, $filters, $driver);
    }

    public function searchDTO(string $query, array $fields = []): Collection
    {
        $models = $this->search($query, $fields);
        return $models->map(fn($model) => $this->dtoClass::fromModel($model));
    }

    // Validation
    protected function validateData(array $data, ?int $excludeId = null): array
    {
        $rules = $this->dtoClass::rules();

        if ($excludeId) {
            $rules = $this->updateUniqueRules($rules, $excludeId);
        }

        $validator = Validator::make($data, $rules, $this->dtoClass::messages());
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        return $validator->validated();
    }

    protected function updateUniqueRules(array $rules, int $excludeId): array
    {
        foreach ($rules as $field => $fieldRules) {
            if (!is_array($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }
            $rules[$field] = array_map(function ($rule) use ($excludeId, $field) {
                if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                    // Parse the unique rule properly
                    // Format: unique:table,column,except,idColumn
                    $ruleParts = explode(':', $rule, 2);
                    if (count($ruleParts) === 2) {
                        $parameters = explode(',', $ruleParts[1]);
                        $table = $parameters[0]; // First parameter is the table
                        $column = $parameters[1] ?? $field; // Second parameter is the column, default to field name
                        $idColumn = $parameters[3] ?? 'id'; // Fourth parameter is the ID column, default to 'id'

                        return "unique:{$table},{$column},{$excludeId},{$idColumn}";
                    }
                }
                return $rule;
            }, $fieldRules);

        }

        return $rules;
    }

    // Soft delete support
    public function withTrashed(): self
    {
        $this->model = $this->model::withTrashed();
        return $this;
    }

    public function onlyTrashed(): Collection
    {
        return $this->model::onlyTrashed()->get();
    }

    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    // Helper methods for query management
    protected function getQueryManager(): QueryManager
    {
        if ($this->queryManager === null) {
            $this->queryManager = app(QueryManager::class);
        }

        return $this->queryManager;
    }

    protected function getFiltersFromRequest(): array
    {
        return request()->get('filters', []);
    }

    protected function getSearchOptionsFromRequest(): array
    {

        return [
            'search' => request()->input('search', ''),
            'search_fields' => request()->input('search_options.search_fields', []),
            'match_type' => request()->input('search_options.match_type', 'partial'),
            'case_sensitive' => request()->input('search_options.case_sensitive', false),
            'word_matching' => request()->input('search_options.word_matching', false),
            'multiple_terms_logic' => request()->input('search_options.multiple_terms_logic', 'or'),
            'sort_field' => request()->input('sort_by', 'id'),
            'sort_direction' => request()->input('sort_direction', 'asc'),
        ];
    }
}
