<?php

namespace Fereydooni\Shopping\App\Events\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderRatingFlagged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderRating $rating;

    public int $moderatorId;

    public string $reason;

    public ?string $notes;

    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderRating $rating, int $moderatorId, string $reason, ?string $notes = null, array $metadata = [])
    {
        $this->rating = $rating;
        $this->moderatorId = $moderatorId;
        $this->reason = $reason;
        $this->notes = $notes;
        $this->metadata = $metadata;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.'.$this->rating->provider_id),
            new PrivateChannel('user.'.$this->rating->user_id),
            new PrivateChannel('moderator.'.$this->moderatorId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'user_id' => $this->rating->user_id,
            'moderator_id' => $this->moderatorId,
            'rating_value' => $this->rating->rating_value,
            'category' => $this->rating->category,
            'status' => $this->rating->status,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'flagged_at' => $this->rating->moderated_at,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'provider.rating.flagged';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return config('broadcasting.default') !== null;
    }
}
