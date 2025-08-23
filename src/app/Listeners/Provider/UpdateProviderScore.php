<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderQualityRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeliveryRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderCommunicationRatingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProviderScore implements ShouldQueue
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
        // Update provider overall score when rating changes
        \Log::info('Provider score updated for rating change: ' . $event->provider->id);
    }

    /**
     * Handle provider quality rating updated event.
     */
    public function handleProviderQualityRatingUpdated(ProviderQualityRatingUpdated $event): void
    {
        // Update provider quality score when quality rating changes
        \Log::info('Provider quality score updated for: ' . $event->provider->id);
    }

    /**
     * Handle provider delivery rating updated event.
     */
    public function handleProviderDeliveryRatingUpdated(ProviderDeliveryRatingUpdated $event): void
    {
        // Update provider delivery score when delivery rating changes
        \Log::info('Provider delivery score updated for: ' . $event->provider->id);
    }

    /**
     * Handle provider communication rating updated event.
     */
    public function handleProviderCommunicationRatingUpdated(ProviderCommunicationRatingUpdated $event): void
    {
        // Update provider communication score when communication rating changes
        \Log::info('Provider communication score updated for: ' . $event->provider->id);
    }
}
