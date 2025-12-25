<?php

namespace App\Events\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentMoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;

    public $previousParentId;

    public $newParentId;

    public $movedBy;

    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeDepartment $department, int $previousParentId, int $newParentId, $movedBy = null)
    {
        $this->department = $department;
        $this->previousParentId = $previousParentId;
        $this->newParentId = $newParentId;
        $this->movedBy = $movedBy ?? auth()->id();
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
            'previous_parent_id' => $this->previousParentId,
            'new_parent_id' => $this->newParentId,
            'moved_by' => $this->movedBy,
            'moved_at' => $this->timestamp->toISOString(),
            'event_type' => 'department_moved',
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee.department.moved';
    }
}
