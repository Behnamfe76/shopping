<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderQualityRatingUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderDeliveryRatingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyQualityIssues implements ShouldQueue
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
     * Handle provider quality rating updated event.
     */
    public function handleProviderQualityRatingUpdated(ProviderQualityRatingUpdated $event): void
    {
        // Check if quality rating dropped below threshold and notify relevant parties
        if ($event->newRating < 3.0) {
            \Log::warning('Provider quality rating dropped below threshold: ' . $event->provider->id . ' Rating: ' . $event->newRating);
            // Send notification to quality management team
        }
    }

    /**
     * Handle provider delivery rating updated event.
     */
    public function handleProviderDeliveryRatingUpdated(ProviderDeliveryRatingUpdated $event): void
    {
        // Check if delivery rating dropped below threshold and notify relevant parties
        if ($event->newRating < 3.0) {
            \Log::warning('Provider delivery rating dropped below threshold: ' . $event->provider->id . ' Rating: ' . $event->newRating);
            // Send notification to logistics team
        }
    }

    /**
     * Check for providers with consistently low ratings
     */
    public function checkLowRatedProviders(): void
    {
        // This method would be called by a scheduled task
        // to identify providers with consistently low ratings
        \Log::info('Checking for providers with consistently low ratings...');
    }
}
