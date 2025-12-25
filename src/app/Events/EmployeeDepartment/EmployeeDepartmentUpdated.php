<?php

namespace App\Events\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;

    public $updatedBy;

    public $changes;

    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeDepartment $department, array $changes = [], $updatedBy = null)
    {
        $this->department = $department;
        $this->changes = $changes;
        $this->updatedBy = $updatedBy ?? auth()->id();
        $this->timestamp = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('departments'),
            new Channel('department-updates'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'changes' => $this->changes,
            'updated_by' => $this->updatedBy,
            'updated_at' => $this->timestamp->toISOString(),
            'event_type' => 'department_updated',
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee.department.updated';
    }
}
