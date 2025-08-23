<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;

class EmployeeTimeOffUpdated
{
    use Dispatchable, SerializesModels;

    public $timeOff;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTimeOff $timeOff, array $changes = [])
    {
        $this->timeOff = $timeOff;
        $this->changes = $changes;
    }
}
