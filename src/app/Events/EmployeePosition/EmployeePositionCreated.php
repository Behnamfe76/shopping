<?php

namespace Fereydooni\Shopping\app\Events\EmployeePosition;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\DTOs\EmployeePositionDTO;

class EmployeePositionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePosition $position;
    public EmployeePositionDTO $positionDTO;
    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeePosition $position, array $metadata = [])
    {
        $this->position = $position;
        $this->positionDTO = EmployeePositionDTO::fromModel($position);
        $this->metadata = array_merge([
            'created_by' => auth()->id(),
            'created_at' => now()->toISOString(),
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
            'created_at' => $this->position->created_at->toISOString(),
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'employee-position.created';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Only broadcast if the position is active or hiring
        return $this->position->is_active &&
               in_array($this->position->status->value, ['active', 'hiring']);
    }

    /**
     * Get the tags for the event.
     */
    public function tags(): array
    {
        return [
            'employee-position',
            'position-created',
            'department-' . $this->position->department_id,
            'level-' . $this->position->level->value,
            'status-' . $this->position->status->value,
        ];
    }

    /**
     * Get the event description.
     */
    public function getDescription(): string
    {
        return "New employee position '{$this->position->title}' created in {$this->position->department?->name} department";
    }

    /**
     * Get the event summary.
     */
    public function getSummary(): array
    {
        return [
            'action' => 'position_created',
            'position_id' => $this->position->id,
            'position_title' => $this->position->title,
            'department' => $this->position->department?->name,
            'level' => $this->position->level->label(),
            'status' => $this->position->status->label(),
            'created_by' => $this->metadata['created_by'] ?? null,
            'timestamp' => $this->metadata['created_at'] ?? now()->toISOString(),
        ];
    }

    /**
     * Get the event priority.
     */
    public function getPriority(): string
    {
        // High priority for management positions, medium for others
        if ($this->position->level->isManagement()) {
            return 'high';
        }

        if ($this->position->status->isHiring()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event category.
     */
    public function getCategory(): string
    {
        return 'employee_management';
    }

    /**
     * Get the event subcategory.
     */
    public function getSubcategory(): string
    {
        return 'position_management';
    }

    /**
     * Get the event impact level.
     */
    public function getImpactLevel(): string
    {
        if ($this->position->level->isManagement()) {
            return 'high';
        }

        if ($this->position->level->isSeniorLevel()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event notification settings.
     */
    public function getNotificationSettings(): array
    {
        return [
            'email' => true,
            'slack' => $this->position->level->isManagement(),
            'sms' => false,
            'in_app' => true,
            'dashboard' => true,
        ];
    }

    /**
     * Get the event recipients.
     */
    public function getRecipients(): array
    {
        $recipients = [
            'hr_team' => true,
            'department_managers' => true,
            'executives' => $this->position->level->isManagement(),
            'recruiters' => $this->position->status->isHiring(),
        ];

        return $recipients;
    }

    /**
     * Get the event escalation rules.
     */
    public function getEscalationRules(): array
    {
        $rules = [];

        // Escalate management positions to executives
        if ($this->position->level->isManagement()) {
            $rules[] = [
                'condition' => 'management_position_created',
                'escalate_to' => 'executives',
                'timeout' => '1_hour',
                'priority' => 'high',
            ];
        }

        // Escalate hiring positions to recruiters
        if ($this->position->status->isHiring()) {
            $rules[] = [
                'condition' => 'hiring_position_created',
                'escalate_to' => 'recruiters',
                'timeout' => '4_hours',
                'priority' => 'medium',
            ];
        }

        return $rules;
    }
}
