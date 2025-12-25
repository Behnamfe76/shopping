<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillVerified
{
    use Dispatchable, SerializesModels;

    public EmployeeSkill $employeeSkill;

    public int $verifiedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill, int $verifiedBy)
    {
        $this->employeeSkill = $employeeSkill;
        $this->verifiedBy = $verifiedBy;
    }
}
