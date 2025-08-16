<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderStatusHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'per_page' => $this->perPage ?? null,
                'current_page' => $this->currentPage ?? null,
                'last_page' => $this->lastPage ?? null,
                'from' => $this->firstItem ?? null,
                'to' => $this->lastItem ?? null,
                'has_more_pages' => $this->hasMorePages() ?? false,
            ],
            'links' => [
                'first' => $this->url(1) ?? null,
                'last' => $this->url($this->lastPage) ?? null,
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_history_count' => $this->collection->count(),
                'system_changes_count' => $this->collection->where('is_system_change', true)->count(),
                'user_changes_count' => $this->collection->where('is_system_change', false)->count(),
                'unique_orders_count' => $this->collection->unique('order_id')->count(),
                'unique_users_count' => $this->collection->unique('changed_by')->count(),
                'status_distribution' => $this->collection->groupBy('new_status')->map->count(),
                'change_type_distribution' => $this->collection->groupBy('change_type')->map->count(),
                'change_category_distribution' => $this->collection->groupBy('change_category')->map->count(),
            ],
        ];
    }
}
