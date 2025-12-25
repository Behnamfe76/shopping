<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillUpdated
{
    use Dispatchable, SerializesModels;

    public EmployeeSkill $employeeSkill;

    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill, array $changes = [])
    {
        $this->employeeSkill = $employeeSkill;
        $this->changes = $changes;
    }
}
