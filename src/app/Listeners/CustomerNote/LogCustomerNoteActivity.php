<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerNote;

use Fereydooni\Shopping\app\Events\CustomerNote\CustomerNoteCreated;
use Fereydooni\Shopping\app\Events\CustomerNote\CustomerNoteDeleted;
use Fereydooni\Shopping\app\Events\CustomerNote\CustomerNoteUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCustomerNoteActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CustomerNoteCreated|CustomerNoteUpdated|CustomerNoteDeleted $event): void
    {
        $action = match (true) {
            $event instanceof CustomerNoteCreated => 'created',
            $event instanceof CustomerNoteUpdated => 'updated',
            $event instanceof CustomerNoteDeleted => 'deleted',
        };

        Log::info("Customer note {$action}", [
            'note_id' => $event->customerNote->id,
            'customer_id' => $event->customerNote->customer_id,
            'user_id' => $event->customerNote->user_id,
            'title' => $event->customerNote->title,
            'action' => $action,
            'timestamp' => now(),
        ]);
    }
}
