<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
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
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'metadata' => $this->metadata,
            'is_system_change' => $this->is_system_change,
            'change_type' => $this->change_type,
            'change_category' => $this->change_category,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional relationships
            'order' => $this->whenLoaded('order', function () {
                return [
                    'id' => $this->order->id,
                    'status' => $this->order->status,
                    'total_amount' => $this->order->total_amount,
                    'currency' => $this->order->currency,
                ];
            }),

            'changed_by_user' => $this->whenLoaded('changedByUser', function () {
                return [
                    'id' => $this->changedByUser->id,
                    'name' => $this->changedByUser->name,
                    'email' => $this->changedByUser->email,
                ];
            }),

            // Computed fields
            'status_transition_description' => $this->getStatusTransitionDescription(),
            'is_user_change' => ! $this->is_system_change,
            'change_type_label' => $this->getChangeTypeLabel(),
            'change_category_label' => $this->getChangeCategoryLabel(),

            // Timeline indicators
            'is_latest_change' => $this->is_latest_change ?? false,
            'is_first_change' => $this->is_first_change ?? false,
            'change_duration' => $this->change_duration ?? null,

            // Links
            'links' => [
                'self' => route('shopping.order-status-history.show', $this->id),
                'order' => $this->order_id ? route('shopping.orders.show', $this->order_id) : null,
                'timeline' => route('shopping.order-status-history.timeline', $this->order_id),
            ],
        ];
    }
}
