<?php

namespace Fereydooni\Shopping\app\Events\CustomerNote;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\CustomerNote;

class CustomerNoteCreated
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
