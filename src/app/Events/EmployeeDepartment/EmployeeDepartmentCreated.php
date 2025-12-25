<?php

namespace App\Events\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;

    public $createdBy;

    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeDepartment $department, $createdBy = null)
    {
        $this->department = $department;
        $this->createdBy = $createdBy ?? auth()->id();
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
            'department_code' => $this->department->code,
            'parent_id' => $this->department->parent_id,
            'manager_id' => $this->department->manager_id,
            'created_by' => $this->createdBy,
            'created_at' => $this->timestamp->toISOString(),
            'event_type' => 'department_created',
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee.department.created';
    }
}
