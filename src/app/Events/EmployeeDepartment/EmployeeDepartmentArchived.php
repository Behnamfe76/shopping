<?php

namespace App\Events\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentArchived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;
    public $archivedBy;
    public $reason;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeDepartment $department, string $reason = '', $archivedBy = null)
    {
        $this->department = $department;
        $this->reason = $reason;
        $this->archivedBy = $archivedBy ?? auth()->id();
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
            new Channel('department-updates')
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
            'reason' => $this->reason,
            'archived_by' => $this->archivedBy,
            'archived_at' => $this->timestamp->toISOString(),
            'event_type' => 'department_archived'
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee.department.archived';
    }
}
