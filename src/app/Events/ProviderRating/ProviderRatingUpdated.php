<?php

namespace Fereydooni\Shopping\App\Events\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderRatingUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderRating $rating;

    public array $oldData;

    public array $newData;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderRating $rating, array $oldData = [], array $newData = [])
    {
        $this->rating = $rating;
        $this->oldData = $oldData;
        $this->newData = $newData;
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
            'rating_value' => $this->rating->rating_value,
            'category' => $this->rating->category,
            'status' => $this->rating->status,
            'old_data' => $this->oldData,
            'new_data' => $this->newData,
            'updated_at' => $this->rating->updated_at,
        ];
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'provider.rating.updated';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return config('broadcasting.default') !== null;
    }
}
