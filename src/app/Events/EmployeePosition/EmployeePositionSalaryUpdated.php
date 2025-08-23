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

class EmployeePositionSalaryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePosition $position;
    public EmployeePositionDTO $positionDTO;
    public array $salaryChanges;
    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeePosition $position, array $salaryChanges = [], array $metadata = [])
    {
        $this->position = $position;
        $this->positionDTO = EmployeePositionDTO::fromModel($position);
        $this->salaryChanges = $salaryChanges;
        $this->metadata = array_merge([
            'updated_by' => auth()->id(),
            'updated_at' => now()->toISOString(),
            'department_name' => $position->department?->name,
            'level_label' => $position->level->label(),
            'old_salary_min' => $salaryChanges['old_salary_min'] ?? null,
            'old_salary_max' => $salaryChanges['old_salary_max'] ?? null,
            'new_salary_min' => $salaryChanges['new_salary_min'] ?? null,
            'new_salary_max' => $salaryChanges['new_salary_max'] ?? null,
            'old_hourly_rate_min' => $salaryChanges['old_hourly_rate_min'] ?? null,
            'old_hourly_rate_max' => $salaryChanges['old_hourly_rate_max'] ?? null,
            'new_hourly_rate_min' => $salaryChanges['new_hourly_rate_min'] ?? null,
            'new_hourly_rate_max' => $salaryChanges['new_hourly_rate_max'] ?? null,
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
            new PrivateChannel('salary-updates'),
        ];

        // Add role-based channels
        if ($this->position->level->isManagement()) {
            $channels[] = new PrivateChannel('management-positions');
        }

        // Notify all employees in the department about salary changes
        $channels[] = new PrivateChannel('department.' . $this->position->department_id . '.salary-updates');

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
            'salary_changes' => $this->salaryChanges,
            'new_salary_range' => $this->position->salary_range,
            'new_hourly_rate_range' => $this->position->hourly_rate_range,
            'updated_at' => $this->position->updated_at->toISOString(),
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'employee-position.salary-updated';
    }
}
