<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasSearchOperations
{
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

    public function searchWithPagination(string $query, int $perPage = 15, array $fields = []): LengthAwarePaginator
    {
        $searchFields = $fields ?: $this->getSearchableFields();

        return $this->model::where(function ($q) use ($query, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        })->paginate($perPage);
    }

    public function getSearchSuggestions(string $query, int $limit = 10): Collection
    {
        $searchFields = $this->getSearchableFields();

        return $this->model::where(function ($q) use ($query, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        })->limit($limit)->get();
    }

    protected function getSearchableFields(): array
    {
        return ['title', 'name', 'description', 'slug'];
    }
}
