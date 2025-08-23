<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderActivated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeactivated;
use Fereydooni\Shopping\app\Events\Provider\ProviderSuspended;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyProviderStatusChange implements ShouldQueue
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
     * Handle provider activated event.
     */
    public function handleProviderActivated(ProviderActivated $event): void
    {
        // Notify provider and relevant staff about activation
        \Log::info('Provider activated notification sent for: ' . $event->provider->id);
    }

    /**
     * Handle provider deactivated event.
     */
    public function handleProviderDeactivated(ProviderDeactivated $event): void
    {
        // Notify provider and relevant staff about deactivation
        \Log::info('Provider deactivated notification sent for: ' . $event->provider->id);
    }

    /**
     * Handle provider suspended event.
     */
    public function handleProviderSuspended(ProviderSuspended $event): void
    {
        // Notify provider and relevant staff about suspension
        \Log::info('Provider suspended notification sent for: ' . $event->provider->id . ' Reason: ' . $event->reason);
    }
}
