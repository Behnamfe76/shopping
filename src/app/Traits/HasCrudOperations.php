<?php

namespace Fereydooni\Shopping\app\Traits;

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

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model::query();

        $query = $this->applySorting($query);
        $query = $this->applyFilters($query);
        $query = $this->applySearch($query);
        
        return $query->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        $query = $this->model::query();

        $query = $this->applySorting($query);
        $query = $this->applyFilters($query);
        $query = $this->applySearch($query);

        return $query->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor): CursorPaginator
    {
        $query = $this->model::query();

        $query = $this->applySorting($query);
        $query = $this->applyFilters($query);
        $query = $this->applySearch($query);

        return $query->cursorPaginate($perPage, ['*'], 'id', $cursor);
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
    public function search(string $query, array $fields = []): Collection
    {
        $searchFields = $fields ?: $this->getSearchableFields();

        return $this->model::where(function ($q) use ($query, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        })->get();
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


    protected function getSearchableFields(): array
    {
        return ['title', 'name', 'description', 'slug'];
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
}
