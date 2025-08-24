<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderCommunicationThreadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'thread_id' => $this->resource->first()?->thread_id,
            'provider_id' => $this->resource->first()?->provider_id,
            'total_messages' => $this->resource->count(),
            'first_message' => $this->resource->first() ? new ProviderCommunicationResource($this->resource->first()) : null,
            'last_message' => $this->resource->last() ? new ProviderCommunicationResource($this->resource->last()) : null,
            'messages' => ProviderCommunicationResource::collection($this->resource),
            'conversation_summary' => [
                'started_at' => $this->resource->first()?->created_at?->toISOString(),
                'last_activity' => $this->resource->last()?->updated_at?->toISOString(),
                'total_participants' => $this->resource->pluck('user_id')->unique()->count(),
                'communication_types' => $this->resource->pluck('communication_type')->unique()->values(),
                'average_response_time' => $this->calculateAverageResponseTime(),
                'satisfaction_rating' => $this->resource->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
            ],
            'thread_status' => $this->determineThreadStatus(),
        ];
    }

    private function calculateAverageResponseTime(): ?float
    {
        $responseTimes = $this->resource->whereNotNull('response_time')->pluck('response_time');
        return $responseTimes->isNotEmpty() ? $responseTimes->avg() : null;
    }

    private function determineThreadStatus(): string
    {
        $lastMessage = $this->resource->last();

        if (!$lastMessage) {
            return 'empty';
        }

        if ($lastMessage->status === 'closed') {
            return 'closed';
        }

        if ($lastMessage->status === 'archived') {
            return 'archived';
        }

        if ($lastMessage->status === 'replied') {
            return 'active';
        }

        if ($lastMessage->status === 'read') {
            return 'pending_reply';
        }

        return 'new';
    }
}
