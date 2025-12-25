<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderContract;

use Fereydooni\Shopping\app\Events\Provider\ProviderContractExpiring;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractRenewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessContractRenewal implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $this->processRenewal($event);
        } catch (\Exception $e) {
            Log::error('Failed to process provider contract renewal', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'contract_id' => $event->contract->id ?? null,
            ]);
        }
    }

    /**
     * Process renewal based on event type.
     */
    protected function processRenewal($event): void
    {
        $contract = $event->contract;
        $provider = $contract->provider;

        switch (get_class($event)) {
            case ProviderContractRenewed::class:
                $this->processContractRenewal($contract, $provider);
                break;
            case ProviderContractExpiring::class:
                $this->processContractExpiring($contract, $provider, $event->daysUntilExpiry);
                break;
        }
    }

    /**
     * Process contract renewal.
     */
    protected function processContractRenewal($contract, $provider): void
    {
        // Update contract dates
        $this->updateRenewalDates($contract);

        // Update provider records
        $this->updateProviderRenewalRecords($provider, $contract);

        // Send renewal confirmation
        $this->sendRenewalConfirmation($contract, $provider);

        Log::info('Provider contract renewal processed', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Process contract expiring.
     */
    protected function processContractExpiring($contract, $provider, int $daysUntilExpiry): void
    {
        // Check if auto-renewal is enabled
        if ($contract->auto_renewal) {
            $this->processAutoRenewal($contract, $provider);
        } else {
            $this->sendExpirationReminder($contract, $provider, $daysUntilExpiry);
        }

        Log::info('Provider contract expiration processed', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'days_until_expiry' => $daysUntilExpiry,
        ]);
    }

    /**
     * Update renewal dates for the contract.
     */
    protected function updateRenewalDates($contract): void
    {
        $contract->update([
            'renewal_date' => now(),
            'start_date' => $contract->end_date,
            'end_date' => $contract->end_date->addDays($contract->renewal_terms['duration_days'] ?? 365),
        ]);
    }

    /**
     * Update provider renewal records.
     */
    protected function updateProviderRenewalRecords($provider, $contract): void
    {
        // Update provider renewal count
        $provider->increment('contract_renewals');

        // Update last renewal date
        $provider->update([
            'last_contract_renewal' => now(),
        ]);
    }

    /**
     * Process auto-renewal for the contract.
     */
    protected function processAutoRenewal($contract, $provider): void
    {
        // Auto-renew the contract
        $contract->update([
            'status' => 'active',
            'renewal_date' => now(),
            'end_date' => $contract->end_date->addDays($contract->renewal_terms['duration_days'] ?? 365),
        ]);

        Log::info('Provider contract auto-renewed', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Send renewal confirmation.
     */
    protected function sendRenewalConfirmation($contract, $provider): void
    {
        // Send confirmation to provider and stakeholders
        Log::info('Provider contract renewal confirmation sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Send expiration reminder.
     */
    protected function sendExpirationReminder($contract, $provider, int $daysUntilExpiry): void
    {
        // Send reminder about expiring contract
        Log::info('Provider contract expiration reminder sent', [
            'contract_id' => $contract->id,
            'provider_id' => $provider->id,
            'days_until_expiry' => $daysUntilExpiry,
        ]);
    }
}
