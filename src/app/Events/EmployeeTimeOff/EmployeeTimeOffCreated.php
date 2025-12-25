<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
