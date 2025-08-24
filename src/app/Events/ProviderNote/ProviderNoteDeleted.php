<?php

namespace Fereydooni\Shopping\app\Events\ProviderNote;

use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderNoteDeleted
{
    use Dispatchable, SerializesModels;

    public int $providerNoteId;
    public int $providerId;
    public int $userId;
    public string $title;
    public string $noteType;
    public string $priority;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderNote $providerNote)
    {
        $this->providerNoteId = $providerNote->id;
        $this->providerId = $providerNote->provider_id;
        $this->userId = $providerNote->user_id;
        $this->title = $providerNote->title ?? $providerNote->note ?? '';
        $this->noteType = $providerNote->note_type ?? $providerNote->type ?? 'general';
        $this->priority = $providerNote->priority ?? 'medium';
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
            'id' => $this->providerNoteId,
            'provider_id' => $this->providerId,
            'user_id' => $this->userId,
            'title' => $this->title,
            'note_type' => $this->noteType,
            'priority' => $this->priority,
            'deleted_at' => now()->toISOString(),
        ];
    }
}
