<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;

class EmployeeTimeOffCreated
{
    use Dispatchable, SerializesModels;

    public $timeOff;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTimeOff $timeOff)
    {
        $this->timeOff = $timeOff;
    }
}
