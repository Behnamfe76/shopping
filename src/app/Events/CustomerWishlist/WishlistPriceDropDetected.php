<?php

namespace Fereydooni\Shopping\app\Events\CustomerWishlist;

use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WishlistPriceDropDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CustomerWishlist $wishlist,
        public float $oldPrice,
        public float $newPrice,
        public float $priceDrop
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
