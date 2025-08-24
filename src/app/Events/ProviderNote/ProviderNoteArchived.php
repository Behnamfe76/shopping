<?php

namespace Fereydooni\Shopping\app\Events\ProviderNote;

use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderNoteArchived
{
    use Dispatchable, SerializesModels;

    public ProviderNote $providerNote;
    public ProviderNoteDTO $providerNoteDTO;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderNote $providerNote, ProviderNoteDTO $providerNoteDTO)
    {
        $this->providerNote = $providerNote;
        $this->providerNoteDTO = $providerNoteDTO;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerNote->id,
            'provider_id' => $this->providerNote->provider_id,
            'user_id' => $this->providerNote->user_id,
            'title' => $this->providerNote->title ?? $this->providerNote->note,
            'note_type' => $this->providerNote->note_type ?? $this->providerNote->type,
            'priority' => $this->providerNote->priority ?? 'medium',
            'archived_at' => $this->providerNote->updated_at?->toISOString(),
        ];
    }
}
