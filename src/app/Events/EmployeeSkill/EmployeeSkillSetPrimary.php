<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillSetPrimary
{
    use Dispatchable, SerializesModels;

    public EmployeeSkill $employeeSkill;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill)
    {
        $this->employeeSkill = $employeeSkill;
    }
}
