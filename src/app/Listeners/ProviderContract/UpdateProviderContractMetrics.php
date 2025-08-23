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

class UpdateProviderContractMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $this->updateMetrics($event);
        } catch (\Exception $e) {
            Log::error('Failed to update provider contract metrics', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'contract_id' => $event->contract->id ?? null
            ]);
        }
    }

    /**
     * Update metrics based on event type.
     */
    protected function updateMetrics($event): void
    {
        $contract = $event->contract;
        $provider = $contract->provider;

        switch (get_class($event)) {
            case ProviderContractCreated::class:
                $this->updateCreationMetrics($contract, $provider);
                break;
            case ProviderContractSigned::class:
                $this->updateSigningMetrics($contract, $provider);
                break;
            case ProviderContractRenewed::class:
                $this->updateRenewalMetrics($contract, $provider);
                break;
            case ProviderContractTerminated::class:
                $this->updateTerminationMetrics($contract, $provider);
                break;
            case ProviderContractExpiring::class:
                $this->updateExpirationMetrics($contract, $provider);
                break;
        }
    }

    /**
     * Update metrics when contract is created.
     */
    protected function updateCreationMetrics($contract, $provider): void
    {
        // Update provider contract count
        $provider->increment('total_contracts');

        // Update contract type metrics
        $this->updateContractTypeMetrics($provider, $contract->contract_type, 1);

        Log::info('Provider contract creation metrics updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update metrics when contract is signed.
     */
    protected function updateSigningMetrics($contract, $provider): void
    {
        // Update active contracts count
        $provider->increment('active_contracts');

        // Update contract value metrics
        if ($contract->contract_value) {
            $provider->increment('total_contract_value', $contract->contract_value);
        }

        Log::info('Provider contract signing metrics updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update metrics when contract is renewed.
     */
    protected function updateRenewalMetrics($contract, $provider): void
    {
        // Update renewal count
        $provider->increment('contract_renewals');

        Log::info('Provider contract renewal metrics updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update metrics when contract is terminated.
     */
    protected function updateTerminationMetrics($contract, $provider): void
    {
        // Decrease active contracts count
        $provider->decrement('active_contracts');

        // Update termination count
        $provider->increment('terminated_contracts');

        Log::info('Provider contract termination metrics updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update metrics when contract is expiring.
     */
    protected function updateExpirationMetrics($contract, $provider): void
    {
        // Update expiring contracts count
        $provider->increment('expiring_contracts');

        Log::info('Provider contract expiration metrics updated', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update contract type specific metrics.
     */
    protected function updateContractTypeMetrics($provider, string $contractType, int $increment): void
    {
        switch ($contractType) {
            case 'service':
                $provider->increment('service_contracts', $increment);
                break;
            case 'supply':
                $provider->increment('supply_contracts', $increment);
                break;
            case 'distribution':
                $provider->increment('distribution_contracts', $increment);
                break;
            case 'partnership':
                $provider->increment('partnership_contracts', $increment);
                break;
        }
    }
}
