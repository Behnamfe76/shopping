<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeleted;
use Fereydooni\Shopping\app\Events\Provider\ProviderUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProviderAnalytics implements ShouldQueue
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
     * Handle provider created event.
     */
    public function handleProviderCreated(ProviderCreated $event): void
    {
        // Update provider analytics when a new provider is created
        \Log::info('Provider analytics updated for new provider: '.$event->provider->id);
    }

    /**
     * Handle provider updated event.
     */
    public function handleProviderUpdated(ProviderUpdated $event): void
    {
        // Update provider analytics when provider is updated
        \Log::info('Provider analytics updated for provider: '.$event->provider->id);
    }

    /**
     * Handle provider deleted event.
     */
    public function handleProviderDeleted(ProviderDeleted $event): void
    {
        // Clean up provider analytics when provider is deleted
        \Log::info('Provider analytics cleaned up for provider: '.$event->provider->id);
    }
}
