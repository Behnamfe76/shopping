<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeSkill $employeeSkill;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill)
    {
        $this->employeeSkill = $employeeSkill;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-skill.' . $this->employeeSkill->employee_id),
        ];
    }

    /**
     * Get the event data for broadcasting.
     */
    public function broadcastWith(): array
    {
        return [
            'skill_id' => $this->employeeSkill->id,
            'employee_id' => $this->employeeSkill->employee_id,
            'skill_name' => $this->employeeSkill->skill_name,
            'skill_category' => $this->employeeSkill->skill_category->value,
            'proficiency_level' => $this->employeeSkill->proficiency_level->value,
            'created_at' => $this->employeeSkill->created_at->toISOString(),
        ];
    }
}
