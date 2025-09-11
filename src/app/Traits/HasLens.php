<?php

namespace Fereydooni\Shopping\app\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Traits\AppliesQueryParameters;
use PhpParser\Node\Stmt\Echo_;

trait HasLens
{
    use AppliesQueryParameters;

    /**
     * Apply lens-specific query modifications and pagination.
     */
    public function lens(): Paginator
    {
        if (!property_exists($this, 'model') || !is_string($this->model)) {
            throw new \Exception('Class using HasLens trait must define a public string $model property.');
        }

        $request = request();

        $query = $this->model::query();
        $columns = Schema::getColumnListing((new $this->model)->getTable());
        $lensFields = array_intersect($request->get('lens_fields'), $columns);
        $lensRelations = array_diff($request->get('lens_fields'), $columns);

        // checking if relations exist in model
        array_map(function ($relation) {
            if (!method_exists($this->model, $relation)) {
                throw new Exception("Relation does not exist in $this->model model");
            }
        }, $lensRelations);

        $withCounts = collect($lensRelations)
            ->map(fn($r) => "$r as $r")
            ->values()
            ->all();

        $query->select($lensFields)
            ->withCount($withCounts);

        $query = $this->applySearch($query);
        $query = $this->applyFilters($query);
        $query = $this->applySorting($query);

        $perPage = $request->input('per_page', 15);
        $paginationType = $request->input('pagination', 'paginate');

        return match ($paginationType) {
            'simplePaginate' => $query->simplePaginate($perPage),
            'cursorPaginate' => $query->cursorPaginate($perPage, ['*'], 'id', request()->get('id')),
            default => $query->paginate($perPage),
        };
    }
}
