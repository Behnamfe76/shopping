<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerPreference;

use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceCreated;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceUpdated;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateCustomerPreferenceAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $preference = $event->preference;
        $customerId = $preference->customer_id;
        $preferenceKey = $preference->preference_key;

        // Clear analytics cache
        $this->clearAnalyticsCache($customerId, $preferenceKey);

        // Update preference statistics
        $this->updatePreferenceStats($preference, $event);
    }

    /**
     * Clear analytics cache for the customer and preference.
     */
    private function clearAnalyticsCache(int $customerId, string $preferenceKey): void
    {
        $cacheKeys = [
            "customer_preference_stats_{$customerId}",
            "customer_preference_analytics_{$customerId}",
            "preference_usage_{$preferenceKey}",
            "customer_preferences_{$customerId}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update preference statistics.
     */
    private function updatePreferenceStats($preference, $event): void
    {
        $stats = Cache::get('customer_preference_stats', []);
        
        $eventType = match (get_class($event)) {
            CustomerPreferenceCreated::class => 'created',
            CustomerPreferenceUpdated::class => 'updated',
            CustomerPreferenceDeleted::class => 'deleted',
            default => 'modified'
        };

        $preferenceKey = $preference->preference_key;
        
        if (!isset($stats[$preferenceKey])) {
            $stats[$preferenceKey] = [
                'created' => 0,
                'updated' => 0,
                'deleted' => 0,
                'total_usage' => 0
            ];
        }

        $stats[$preferenceKey][$eventType]++;
        $stats[$preferenceKey]['total_usage']++;

        Cache::put('customer_preference_stats', $stats, now()->addHours(24));
    }
}
