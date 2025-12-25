<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillExpiring
{
    use Dispatchable, SerializesModels;

    public EmployeeSkill $employeeSkill;

    public int $daysUntilExpiry;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill, int $daysUntilExpiry)
    {
        $this->employeeSkill = $employeeSkill;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }
}
