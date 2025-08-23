<?php

namespace App\Events\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSkillCertified
{
    use Dispatchable, SerializesModels;

    public EmployeeSkill $employeeSkill;
    public array $certificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeSkill $employeeSkill, array $certificationData = [])
    {
        $this->employeeSkill = $employeeSkill;
        $this->certificationData = $certificationData;
    }
}
