<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderCommunicationSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => ProviderCommunicationResource::collection($this->resource),
            'meta' => [
                'query' => $request->get('query'),
                'total_results' => $this->resource->count(),
                'search_filters' => [
                    'provider_id' => $request->get('provider_id'),
                    'communication_type' => $request->get('communication_type'),
                    'direction' => $request->get('direction'),
                    'status' => $request->get('status'),
                    'priority' => $request->get('priority'),
                    'is_urgent' => $request->get('is_urgent'),
                    'is_archived' => $request->get('is_archived'),
                    'date_from' => $request->get('date_from'),
                    'date_to' => $request->get('date_to'),
                ],
                'sort' => [
                    'by' => $request->get('sort_by', 'created_at'),
                    'direction' => $request->get('sort_direction', 'desc'),
                ],
            ],
        ];
    }
}
