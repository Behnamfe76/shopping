<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;

    public string $oldStatus;

    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, string $oldStatus, string $newStatus)
    {
        $this->product = $product;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

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
