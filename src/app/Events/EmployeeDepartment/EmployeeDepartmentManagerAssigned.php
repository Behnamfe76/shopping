<?php

namespace App\Events\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentManagerAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;

    public $managerId;

    public $previousManagerId;

    public $assignedBy;

    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeDepartment $department, int $managerId, ?int $previousManagerId = null, $assignedBy = null)
    {
        $this->department = $department;
        $this->managerId = $managerId;
        $this->previousManagerId = $previousManagerId;
        $this->assignedBy = $assignedBy ?? auth()->id();
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
            new PrivateChannel('user.'.$this->managerId),
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
            'manager_id' => $this->managerId,
            'previous_manager_id' => $this->previousManagerId,
            'assigned_by' => $this->assignedBy,
            'assigned_at' => $this->timestamp->toISOString(),
            'event_type' => 'manager_assigned',
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee.department.manager.assigned';
    }
}
