<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var TeamDTO $team */
        $team = $this->resource;

        $data = [
            'id' => $team->id,
            'name' => $team->name,
            'code' => $team->code,
            'description' => $team->description,
            'department_id' => $team->department_id,
            'location' => $team->location,
            'member_limit' => $team->member_limit,
            'is_active' => $team->is_active,
            'status' => __('teams.statuses.'.$team->status->value),
            'status_value' => $team->status->value,
            'status_label' => $team->status->label(),
            'status_color' => $team->status->color(),
            'metadata' => $team->metadata,
            'created_at' => $team->created_at?->toISOString(),
            'updated_at' => $team->updated_at?->toISOString(),
        ];

        // Include department information if available
        if ($team->department) {
            $data['department'] = [
                'id' => $team->department->id,
                'name' => $team->department->name,
                'code' => $team->department->code,
            ];
        } else {
            $data['department'] = null;
        }

        // Include members information if available
        if ($team->members !== null) {
            $data['members'] = $team->members;
        }

        // Include managers information if available
        if ($team->managers !== null) {
            $data['managers'] = $team->managers;
        }

        // Include member count if available
        if ($team->member_count !== null) {
            $data['member_count'] = $team->member_count;
        }

        // Include manager count if available
        if ($team->manager_count !== null) {
            $data['manager_count'] = $team->manager_count;
        }

        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'team',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
