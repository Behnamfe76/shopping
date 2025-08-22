<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeSearchResource extends ResourceCollection
{
    protected string $query;
    protected ?string $department;
    protected ?string $position;
    protected ?string $status;
    protected ?string $employmentType;

    public function __construct($resource, string $query = '', ?string $department = null, ?string $position = null, ?string $status = null, ?string $employmentType = null)
    {
        parent::__construct($resource);
        $this->query = $query;
        $this->department = $department;
        $this->position = $position;
        $this->status = $status;
        $this->employmentType = $employmentType;
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'search_meta' => [
                'query' => $this->query,
                'department' => $this->department,
                'position' => $this->position,
                'status' => $this->status,
                'employment_type' => $this->employmentType,
                'total_results' => $this->total(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
                'search_filters_applied' => [
                    'has_query' => !empty($this->query),
                    'has_department_filter' => !empty($this->department),
                    'has_position_filter' => !empty($this->position),
                    'has_status_filter' => !empty($this->status),
                    'has_employment_type_filter' => !empty($this->employmentType),
                ],
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
            'message' => 'Employee search completed successfully.',
            'status' => 'success',
            'search_summary' => $this->getSearchSummary(),
        ];
    }

    /**
     * Get a summary of the search results.
     */
    protected function getSearchSummary(): array
    {
        $summary = [
            'total_found' => $this->total(),
            'search_criteria' => [],
        ];

        if (!empty($this->query)) {
            $summary['search_criteria'][] = "Query: '{$this->query}'";
        }

        if (!empty($this->department)) {
            $summary['search_criteria'][] = "Department: {$this->department}";
        }

        if (!empty($this->position)) {
            $summary['search_criteria'][] = "Position: {$this->position}";
        }

        if (!empty($this->status)) {
            $summary['search_criteria'][] = "Status: {$this->status}";
        }

        if (!empty($this->employmentType)) {
            $summary['search_criteria'][] = "Employment Type: {$this->employmentType}";
        }

        return $summary;
    }
}

