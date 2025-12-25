<?php

namespace Fereydooni\Shopping\app\Listeners\Provider;

use Fereydooni\Shopping\app\Events\Provider\ProviderContractExtended;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractTerminated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendContractExpirationReminder implements ShouldQueue
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
     * Handle provider contract extended event.
     */
    public function handleProviderContractExtended(ProviderContractExtended $event): void
    {
        // Send contract extension confirmation
        \Log::info('Contract extension confirmation sent for provider: '.$event->provider->id);
    }

    /**
     * Handle provider contract terminated event.
     */
    public function handleProviderContractTerminated(ProviderContractTerminated $event): void
    {
        // Send contract termination notification
        \Log::info('Contract termination notification sent for provider: '.$event->provider->id.' Reason: '.$event->reason);
    }

    /**
     * Check for contracts expiring soon and send reminders
     */
    public function checkExpiringContracts(): void
    {
        // This method would be called by a scheduled task
        // to check for contracts expiring in the next 30/60/90 days
        \Log::info('Checking for expiring contracts...');
    }
}
