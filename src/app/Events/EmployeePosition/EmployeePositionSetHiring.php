<?php

namespace App\Events\EmployeePosition;

use App\DTOs\EmployeePositionDTO;
use App\Models\EmployeePosition;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeePositionSetHiring implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePosition $position;

    public EmployeePositionDTO $positionDTO;

    public array $hiringDetails;

    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeePosition $position, array $hiringDetails = [], array $metadata = [])
    {
        $this->position = $position;
        $this->positionDTO = EmployeePositionDTO::fromModel($position);
        $this->hiringDetails = $hiringDetails;
        $this->metadata = array_merge([
            'set_by' => auth()->id(),
            'set_at' => now()->toISOString(),
            'department_name' => $position->department?->name,
            'level_label' => $position->level->label(),
            'urgency_level' => $hiringDetails['urgency_level'] ?? 'normal',
            'expected_fill_date' => $hiringDetails['expected_fill_date'] ?? null,
            'hiring_manager' => $hiringDetails['hiring_manager'] ?? null,
            'recruitment_budget' => $hiringDetails['recruitment_budget'] ?? null,
        ], $metadata);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('employee-positions'),
            new PrivateChannel('departments.'.$this->position->department_id),
            new PrivateChannel('hiring-positions'),
            new Channel('public-hiring-positions'),
        ];

        // Add role-based channels
        if ($this->position->level->isManagement()) {
            $channels[] = new PrivateChannel('management-positions');
        }

        // Notify HR team
        $channels[] = new PrivateChannel('hr-team');
        $channels[] = new PrivateChannel('recruitment-team');

        // Notify department managers
        $channels[] = new PrivateChannel('department-managers');

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
            'hiring_details' => $this->hiringDetails,
            'requirements' => $this->position->requirements,
            'skills_required' => $this->position->skills_required,
            'experience_required' => $this->position->experience_required,
            'education_required' => $this->position->education_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'set_at' => $this->metadata['set_at'],
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'employee-position.set-hiring';
    }
}
