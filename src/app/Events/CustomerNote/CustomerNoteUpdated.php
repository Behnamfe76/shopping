<?php

namespace Fereydooni\Shopping\app\Events\CustomerNote;

use Fereydooni\Shopping\app\Models\CustomerNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerNoteUpdated
{
    use Dispatchable, SerializesModels;

    public CustomerNote $customerNote;

    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerNote $customerNote, array $changes = [])
    {
        $this->customerNote = $customerNote;
        $this->changes = $changes;
    }
}
