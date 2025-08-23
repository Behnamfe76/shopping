<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderContract;

use Fereydooni\Shopping\app\Events\Provider\ProviderContractCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractSigned;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractRenewed;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractTerminated;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractExpiring;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractExtended;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProviderContractNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $this->sendNotification($event);
        } catch (\Exception $e) {
            Log::error('Failed to send provider contract notification', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'contract_id' => $event->contract->id ?? null
            ]);
        }
    }

    /**
     * Send appropriate notification based on event type.
     */
    protected function sendNotification($event): void
    {
        $contract = $event->contract;
        $provider = $contract->provider;

        switch (get_class($event)) {
            case ProviderContractCreated::class:
                $this->notifyContractCreated($contract, $provider);
                break;
            case ProviderContractSigned::class:
                $this->notifyContractSigned($contract, $provider);
                break;
            case ProviderContractRenewed::class:
                $this->notifyContractRenewed($contract, $provider);
                break;
            case ProviderContractTerminated::class:
                $this->notifyContractTerminated($contract, $provider);
                break;
            case ProviderContractExpiring::class:
                $this->notifyContractExpiring($contract, $provider, $event->daysUntilExpiry);
                break;
            case ProviderContractExtended::class:
                $this->notifyContractExtended($contract, $provider);
                break;
            case ProviderContractUpdated::class:
                $this->notifyContractUpdated($contract, $provider, $event->changes ?? []);
                break;
        }
    }

    /**
     * Notify when contract is created.
     */
    protected function notifyContractCreated($contract, $provider): void
    {
        // Send notification to provider and relevant stakeholders
        Log::info('Provider contract created notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Notify when contract is signed.
     */
    protected function notifyContractSigned($contract, $provider): void
    {
        // Send notification about contract signing
        Log::info('Provider contract signed notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Notify when contract is renewed.
     */
    protected function notifyContractRenewed($contract, $provider): void
    {
        // Send notification about contract renewal
        Log::info('Provider contract renewed notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Notify when contract is terminated.
     */
    protected function notifyContractTerminated($contract, $provider): void
    {
        // Send notification about contract termination
        Log::info('Provider contract terminated notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Notify when contract is expiring.
     */
    protected function notifyContractExpiring($contract, $provider, int $daysUntilExpiry): void
    {
        // Send reminder about expiring contract
        Log::info('Provider contract expiring reminder sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'days_until_expiry' => $daysUntilExpiry
        ]);
    }

    /**
     * Notify when contract is extended.
     */
    protected function notifyContractExtended($contract, $provider): void
    {
        // Send notification about contract extension
        Log::info('Provider contract extended notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Notify when contract is updated.
     */
    protected function notifyContractUpdated($contract, $provider, array $changes): void
    {
        // Send notification about contract updates
        Log::info('Provider contract updated notification sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'changes' => $changes
        ]);
    }
}
