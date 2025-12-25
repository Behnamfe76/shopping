<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
