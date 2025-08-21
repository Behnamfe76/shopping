<?php

namespace Fereydooni\Shopping\app\Events\CustomerWishlist;

use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerWishlistDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CustomerWishlist $wishlist
    ) {}
}
