<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderNote;

use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteCreated;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteUpdated;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteArchived;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogProviderNoteActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof ProviderNoteCreated) {
                $this->logProviderNoteCreated($event);
            } elseif ($event instanceof ProviderNoteUpdated) {
                $this->logProviderNoteUpdated($event);
            } elseif ($event instanceof ProviderNoteArchived) {
                $this->logProviderNoteArchived($event);
            } elseif ($event instanceof ProviderNoteDeleted) {
                $this->logProviderNoteDeleted($event);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log provider note activity', [
                'error' => $e->getMessage(),
                'event' => get_class($event),
            ]);
        }
    }

    /**
     * Log provider note created activity
     */
    private function logProviderNoteCreated(ProviderNoteCreated $event): void
    {
        Log::info('Provider note activity: Created', [
            'action' => 'created',
            'note_id' => $event->providerNote->id ?? 'unknown',
            'provider_id' => $event->providerNote->provider_id ?? 'unknown',
            'user_id' => $event->providerNote->user_id ?? 'unknown',
            'note_type' => $event->providerNote->note_type ?? $event->providerNote->type ?? 'unknown',
            'priority' => $event->providerNote->priority ?? 'unknown',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log provider note updated activity
     */
    private function logProviderNoteUpdated(ProviderNoteUpdated $event): void
    {
        Log::info('Provider note activity: Updated', [
            'action' => 'updated',
            'note_id' => $event->providerNote->id ?? 'unknown',
            'provider_id' => $event->providerNote->provider_id ?? 'unknown',
            'user_id' => $event->providerNote->user_id ?? 'unknown',
            'changes' => $event->changes,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log provider note archived activity
     */
    private function logProviderNoteArchived(ProviderNoteArchived $event): void
    {
        Log::info('Provider note activity: Archived', [
            'action' => 'archived',
            'note_id' => $event->providerNote->id ?? 'unknown',
            'provider_id' => $event->providerNote->provider_id ?? 'unknown',
            'user_id' => $event->providerNote->user_id ?? 'unknown',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log provider note deleted activity
     */
    private function logProviderNoteDeleted(ProviderNoteDeleted $event): void
    {
        Log::info('Provider note activity: Deleted', [
            'action' => 'deleted',
            'note_id' => $event->providerNoteId,
            'provider_id' => $event->providerId,
            'user_id' => $event->userId,
            'title' => $event->title,
            'note_type' => $event->noteType,
            'priority' => $event->priority,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
