<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'note' => $this->note,
            'type' => $this->type,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Note metadata
            'type_label' => $this->getTypeLabel(),
            'type_color' => $this->getTypeColor(),
            'is_system_note' => $this->type === 'system',
            'is_customer_note' => $this->type === 'customer',
            'is_internal_note' => $this->type === 'internal',
            'is_general_note' => $this->type === 'general',

            // User information (if available)
            'user' => $this->when($this->user, function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }

    /**
     * Get human-readable type label
     */
    private function getTypeLabel(): string
    {
        return match ($this->type) {
            'system' => 'System Note',
            'customer' => 'Customer Note',
            'internal' => 'Internal Note',
            'general' => 'General Note',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get type color for UI
     */
    private function getTypeColor(): string
    {
        return match ($this->type) {
            'system' => 'red',
            'customer' => 'blue',
            'internal' => 'orange',
            'general' => 'gray',
            default => 'gray',
        };
    }
}
