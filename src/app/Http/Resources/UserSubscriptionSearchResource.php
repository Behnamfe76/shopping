<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserSubscriptionSearchResource extends ResourceCollection
{
    protected string $query;
    protected ?int $userId;

    public function __construct($resource, string $query = '', ?int $userId = null)
    {
        parent::__construct($resource);
        $this->query = $query;
        $this->userId = $userId;
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
                'user_id' => $this->userId,
                'total_results' => $this->total(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
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
            'message' => 'User subscriptions search completed successfully.',
            'status' => 'success',
        ];
    }
}
