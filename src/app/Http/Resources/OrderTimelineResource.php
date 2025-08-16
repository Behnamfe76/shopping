<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;

class OrderTimelineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var OrderStatusHistory $this */
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'changed_by' => $this->changed_by,
            'changed_at' => $this->changed_at?->toISOString(),
            'note' => $this->note,
            'reason' => $this->reason,
            'is_system_change' => $this->is_system_change,
            'change_type' => $this->change_type,
            'change_category' => $this->change_category,

            // Timeline specific fields
            'timeline_position' => $this->timeline_position ?? null,
            'duration_from_previous' => $this->duration_from_previous ?? null,
            'duration_to_next' => $this->duration_to_next ?? null,
            'is_milestone' => $this->is_milestone ?? false,
            'milestone_type' => $this->milestone_type ?? null,

            // User information
            'changed_by_user' => $this->whenLoaded('changedByUser', function () {
                return [
                    'id' => $this->changedByUser->id,
                    'name' => $this->changedByUser->name,
                    'email' => $this->changedByUser->email,
                ];
            }),

            // Computed fields
            'status_transition_description' => $this->getStatusTransitionDescription(),
            'change_type_label' => $this->getChangeTypeLabel(),
            'change_category_label' => $this->getChangeCategoryLabel(),

            // Timeline indicators
            'is_first_change' => $this->is_first_change ?? false,
            'is_latest_change' => $this->is_latest_change ?? false,
            'is_current_status' => $this->is_current_status ?? false,

            // Links
            'links' => [
                'self' => route('shopping.order-status-history.show', $this->id),
                'order' => route('shopping.orders.show', $this->order_id),
            ],
        ];
    }
}
