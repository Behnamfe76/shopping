<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\EmployeeNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeNoteDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public EmployeeNote $employeeNote
    ) {}
}
