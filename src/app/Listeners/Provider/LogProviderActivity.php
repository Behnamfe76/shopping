<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeleted;
use Fereydooni\Shopping\app\Events\Provider\ProviderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogProviderActivity implements ShouldQueue
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
        // Log provider creation activity
        \Log::info('Provider created: ' . $event->provider->id);
    }

    /**
     * Handle provider updated event.
     */
    public function handleProviderUpdated(ProviderUpdated $event): void
    {
        // Log provider update activity
        \Log::info('Provider updated: ' . $event->provider->id);
    }

    /**
     * Handle provider deleted event.
     */
    public function handleProviderDeleted(ProviderDeleted $event): void
    {
        // Log provider deletion activity
        \Log::info('Provider deleted: ' . $event->provider->id);
    }

    /**
     * Handle provider status changed event.
     */
    public function handleProviderStatusChanged(ProviderStatusChanged $event): void
    {
        // Log provider status change activity
        \Log::info('Provider status changed: ' . $event->provider->id . ' to ' . $event->provider->status);
    }
}
