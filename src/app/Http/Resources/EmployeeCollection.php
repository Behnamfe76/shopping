<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
                'total_employees' => $this->total(),
                'active_employees' => $this->collection->where('status', 'active')->count(),
                'inactive_employees' => $this->collection->where('status', 'inactive')->count(),
                'terminated_employees' => $this->collection->where('status', 'terminated')->count(),
                'pending_employees' => $this->collection->where('status', 'pending')->count(),
                'on_leave_employees' => $this->collection->where('status', 'on_leave')->count(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'message' => 'Employees retrieved successfully.',
            'status' => 'success',
            'filters' => $request->only(['status', 'department', 'employment_type', 'search']),
            'sort' => $request->only(['sort_by', 'sort_direction']),
        ];
    }
}

