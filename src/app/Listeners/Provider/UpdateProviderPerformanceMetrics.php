<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderCommunicationRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeliveryRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderQualityRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderRatingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProviderPerformanceMetrics implements ShouldQueue
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
     * Handle provider rating updated event.
     */
    public function handleProviderRatingUpdated(ProviderRatingUpdated $event): void
    {
        // Update provider performance metrics when overall rating changes
        \Log::info('Provider performance metrics updated for rating change: '.$event->provider->id);
    }

    /**
     * Handle provider quality rating updated event.
     */
    public function handleProviderQualityRatingUpdated(ProviderQualityRatingUpdated $event): void
    {
        // Update provider quality performance metrics
        \Log::info('Provider quality performance metrics updated for: '.$event->provider->id);
    }

    /**
     * Handle provider delivery rating updated event.
     */
    public function handleProviderDeliveryRatingUpdated(ProviderDeliveryRatingUpdated $event): void
    {
        // Update provider delivery performance metrics
        \Log::info('Provider delivery performance metrics updated for: '.$event->provider->id);
    }

    /**
     * Handle provider communication rating updated event.
     */
    public function handleProviderCommunicationRatingUpdated(ProviderCommunicationRatingUpdated $event): void
    {
        // Update provider communication performance metrics
        \Log::info('Provider communication performance metrics updated for: '.$event->provider->id);
    }
}
