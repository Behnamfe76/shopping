<?php

namespace Fereydooni\Shopping\app\Events\CustomerWishlist;

use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WishlistPriorityChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CustomerWishlist $wishlist,
        public string $oldPriority,
        public string $newPriority
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
