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

class LogProviderContractActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $this->logActivity($event);
        } catch (\Exception $e) {
            Log::error('Failed to log provider contract activity', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'contract_id' => $event->contract->id ?? null
            ]);
        }
    }

    /**
     * Log contract activity based on event type.
     */
    protected function logActivity($event): void
    {
        $contract = $event->contract;
        $provider = $contract->provider;

        switch (get_class($event)) {
            case ProviderContractCreated::class:
                $this->logContractCreated($contract, $provider);
                break;
            case ProviderContractUpdated::class:
                $this->logContractUpdated($contract, $provider, $event->changes ?? []);
                break;
            case ProviderContractSigned::class:
                $this->logContractSigned($contract, $provider);
                break;
            case ProviderContractRenewed::class:
                $this->logContractRenewed($contract, $provider);
                break;
            case ProviderContractTerminated::class:
                $this->logContractTerminated($contract, $provider);
                break;
            case ProviderContractExpiring::class:
                $this->logContractExpiring($contract, $provider, $event->daysUntilExpiry);
                break;
            case ProviderContractExtended::class:
                $this->logContractExtended($contract, $provider);
                break;
        }
    }

    /**
     * Log contract creation activity.
     */
    protected function logContractCreated($contract, $provider): void
    {
        Log::info('Provider contract created', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'contract_number' => $contract->contract_number,
            'contract_type' => $contract->contract_type,
            'status' => $contract->status,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract update activity.
     */
    protected function logContractUpdated($contract, $provider, array $changes): void
    {
        Log::info('Provider contract updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract signing activity.
     */
    protected function logContractSigned($contract, $provider): void
    {
        Log::info('Provider contract signed', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract renewal activity.
     */
    protected function logContractRenewed($contract, $provider): void
    {
        Log::info('Provider contract renewed', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'renewal_date' => now(),
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract termination activity.
     */
    protected function logContractTerminated($contract, $provider): void
    {
        Log::info('Provider contract terminated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'termination_date' => now(),
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract expiring activity.
     */
    protected function logContractExpiring($contract, $provider, int $daysUntilExpiry): void
    {
        Log::warning('Provider contract expiring soon', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'days_until_expiry' => $daysUntilExpiry,
            'end_date' => $contract->end_date,
            'timestamp' => now()
        ]);
    }

    /**
     * Log contract extension activity.
     */
    protected function logContractExtended($contract, $provider): void
    {
        Log::info('Provider contract extended', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'new_end_date' => $contract->end_date,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }
}
