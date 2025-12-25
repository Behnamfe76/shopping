<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderCommunicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'user_id' => $this->user_id,
            'communication_type' => $this->communication_type,
            'subject' => $this->subject,
            'message' => $this->message,
            'direction' => $this->direction,
            'status' => $this->status,
            'sent_at' => $this->sent_at?->toISOString(),
            'read_at' => $this->read_at?->toISOString(),
            'replied_at' => $this->replied_at?->toISOString(),
            'priority' => $this->priority,
            'is_urgent' => $this->is_urgent,
            'is_archived' => $this->is_archived,
            'attachments' => $this->attachments,
            'tags' => $this->tags,
            'thread_id' => $this->thread_id,
            'parent_id' => $this->parent_id,
            'response_time' => $this->response_time,
            'satisfaction_rating' => $this->satisfaction_rating,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'name' => $this->provider->name,
                    'email' => $this->provider->email,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'parent' => $this->whenLoaded('parent', function () {
                return new ProviderCommunicationResource($this->parent);
            }),
            'replies' => $this->whenLoaded('replies', function () {
                return ProviderCommunicationResource::collection($this->replies);
            }),

            // Computed fields
            'is_unread' => $this->status === 'delivered' || $this->status === 'sent',
            'is_replied' => $this->status === 'replied',
            'is_closed' => $this->status === 'closed',
            'is_archived' => $this->is_archived,
            'is_urgent' => $this->is_urgent,
            'has_attachments' => ! empty($this->attachments),
            'has_tags' => ! empty($this->tags),
            'response_time_formatted' => $this->response_time ? $this->formatResponseTime($this->response_time) : null,
        ];
    }

    private function formatResponseTime(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} minutes";
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);

            return "{$hours} hours";
        } else {
            $days = floor($minutes / 1440);

            return "{$days} days";
        }
    }
}
