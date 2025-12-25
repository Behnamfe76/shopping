<?php

namespace Fereydooni\Shopping\app\Events\CustomerNote;

use Fereydooni\Shopping\app\Models\CustomerNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerNoteDeleted
{
    use Dispatchable, SerializesModels;

    public CustomerNote $customerNote;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerNote $customerNote)
    {
        $this->customerNote = $customerNote;
    }
}
