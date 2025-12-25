<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\EmployeeNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeNoteAttachmentAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public EmployeeNote $employeeNote,
        public string $attachmentPath
    ) {}
}
