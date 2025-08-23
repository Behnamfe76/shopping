<?php

namespace App\Events\EmployeePosition;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\EmployeePosition;
use App\DTOs\EmployeePositionDTO;

class EmployeePositionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePosition $position;
    public EmployeePositionDTO $positionDTO;
    public array $changes;
    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeePosition $position, array $changes = [], array $metadata = [])
    {
        $this->position = $position;
        $this->positionDTO = EmployeePositionDTO::fromModel($position);
        $this->changes = $changes;
        $this->metadata = array_merge([
            'updated_by' => auth()->id(),
            'updated_at' => now()->toISOString(),
            'department_name' => $position->department?->name,
            'level_label' => $position->level->label(),
            'status_label' => $position->status->label(),
            'is_remote' => $position->is_remote,
            'is_travel_required' => $position->is_travel_required,
            'salary_range' => $position->salary_range,
            'hourly_rate_range' => $position->hourly_rate_range,
        ], $metadata);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('employee-positions'),
            new PrivateChannel('departments.' . $this->position->department_id),
        ];

        // Add role-based channels
        if ($this->position->level->isManagement()) {
            $channels[] = new PrivateChannel('management-positions');
        }

        if ($this->position->is_remote) {
            $channels[] = new PrivateChannel('remote-positions');
        }

        if ($this->position->status->isHiring()) {
            $channels[] = new PrivateChannel('hiring-positions');
            $channels[] = new Channel('public-hiring-positions');
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->position->id,
            'title' => $this->position->title,
            'code' => $this->position->code,
            'department_id' => $this->position->department_id,
            'department_name' => $this->position->department?->name,
            'level' => $this->position->level->value,
            'level_label' => $this->position->level->label(),
            'status' => $this->position->status->value,
            'status_label' => $this->position->status->label(),
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'experience_required' => $this->position->experience_required,
            'changes' => $this->changes,
            'updated_at' => $this->position->updated_at->toISOString(),
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'employee-position.updated';
    }
}
