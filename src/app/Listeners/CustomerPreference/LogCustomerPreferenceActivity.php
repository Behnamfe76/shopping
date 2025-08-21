<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerPreference;

use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceCreated;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceUpdated;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceDeleted;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceActivated;
use Fereydooni\Shopping\app\Events\CustomerPreference\CustomerPreferenceDeactivated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCustomerPreferenceActivity implements ShouldQueue
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

        $message = match (get_class($event)) {
            CustomerPreferenceCreated::class => "Customer preference created: {$preferenceKey} for customer {$customerId}",
            CustomerPreferenceUpdated::class => "Customer preference updated: {$preferenceKey} for customer {$customerId}",
            CustomerPreferenceDeleted::class => "Customer preference deleted: {$preferenceKey} for customer {$customerId}",
            CustomerPreferenceActivated::class => "Customer preference activated: {$preferenceKey} for customer {$customerId}",
            CustomerPreferenceDeactivated::class => "Customer preference deactivated: {$preferenceKey} for customer {$customerId}",
            default => "Customer preference activity: {$preferenceKey} for customer {$customerId}"
        };

        Log::info($message, [
            'customer_id' => $customerId,
            'preference_key' => $preferenceKey,
            'preference_type' => $preference->preference_type,
            'event' => get_class($event),
            'timestamp' => now()->toISOString()
        ]);
    }
}
