<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;

class EmployeeTimeOffApproved
{
    use Dispatchable, SerializesModels;

    public $timeOff;
    public $approvedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTimeOff $timeOff, int $approvedBy)
    {
        $this->timeOff = $timeOff;
        $this->approvedBy = $approvedBy;
    }
}
