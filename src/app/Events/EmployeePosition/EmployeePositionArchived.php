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

class EmployeePositionArchived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePosition $position;
    public EmployeePositionDTO $positionDTO;
    public array $archiveDetails;
    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeePosition $position, array $archiveDetails = [], array $metadata = [])
    {
        $this->position = $position;
        $this->positionDTO = EmployeePositionDTO::fromModel($position);
        $this->archiveDetails = $archiveDetails;
        $this->metadata = array_merge([
            'archived_by' => auth()->id(),
            'archived_at' => now()->toISOString(),
            'department_name' => $position->department?->name,
            'level_label' => $position->level->label(),
            'archive_reason' => $archiveDetails['reason'] ?? 'No reason specified',
            'replacement_position_id' => $archiveDetails['replacement_position_id'] ?? null,
            'affected_employees_count' => $archiveDetails['affected_employees_count'] ?? 0,
            'transition_plan' => $archiveDetails['transition_plan'] ?? null,
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
            new PrivateChannel('archived-positions'),
        ];

        // Add role-based channels
        if ($this->position->level->isManagement()) {
            $channels[] = new PrivateChannel('management-positions');
        }

        // Notify HR team about position archive
        $channels[] = new PrivateChannel('hr-team');
        $channels[] = new PrivateChannel('department-managers');

        // Notify affected employees if any
        if ($this->archiveDetails['affected_employees_count'] > 0) {
            $channels[] = new PrivateChannel('affected-employees');
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
            'archive_details' => $this->archiveDetails,
            'archive_reason' => $this->metadata['archive_reason'],
            'replacement_position_id' => $this->metadata['replacement_position_id'],
            'affected_employees_count' => $this->metadata['affected_employees_count'],
            'transition_plan' => $this->metadata['transition_plan'],
            'archived_at' => $this->metadata['archived_at'],
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'employee-position.archived';
    }
}
