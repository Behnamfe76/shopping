<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeNote;

class EmployeeNoteMadePrivate
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public EmployeeNote $employeeNote
    ) {}
}
