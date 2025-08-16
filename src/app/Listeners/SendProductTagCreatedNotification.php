<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendProductTagCreatedNotification implements ShouldQueue
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
    public function handle(ProductTagCreated $event): void
    {
        $tag = $event->tag;

        // Log the tag creation
        Log::info('Product tag created', [
            'tag_id' => $tag->id,
            'tag_name' => $tag->name,
            'tag_slug' => $tag->slug,
            'created_by' => $tag->created_by,
        ]);

        // Send notification to administrators if needed
        // This can be customized based on your notification system
        // Notification::send($admins, new ProductTagCreatedNotification($tag));
    }
}
