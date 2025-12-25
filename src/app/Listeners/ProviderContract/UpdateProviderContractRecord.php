<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderContract;

use Fereydooni\Shopping\app\Events\Provider\ProviderContractExtended;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractRenewed;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractSigned;
use Fereydooni\Shopping\app\Events\Provider\ProviderContractTerminated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProviderContractRecord implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $this->updateRecord($event);
        } catch (\Exception $e) {
            Log::error('Failed to update provider contract record', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'contract_id' => $event->contract->id ?? null,
            ]);
        }
    }

    /**
     * Update contract record based on event type.
     */
    protected function updateRecord($event): void
    {
        $contract = $event->contract;

        switch (get_class($event)) {
            case ProviderContractSigned::class:
                $this->updateSignedRecord($contract);
                break;
            case ProviderContractRenewed::class:
                $this->updateRenewedRecord($contract);
                break;
            case ProviderContractTerminated::class:
                $this->updateTerminatedRecord($contract);
                break;
            case ProviderContractExtended::class:
                $this->updateExtendedRecord($contract);
                break;
        }
    }

    /**
     * Update record when contract is signed.
     */
    protected function updateSignedRecord($contract): void
    {
        // Update contract status and signing information
        $contract->update([
            'status' => 'active',
            'signed_at' => now(),
            'signed_by' => auth()->id(),
        ]);

        Log::info('Provider contract record updated for signing', [
            'contract_id' => $contract->id,
        ]);
    }

    /**
     * Update record when contract is renewed.
     */
    protected function updateRenewedRecord($contract): void
    {
        // Update renewal information
        $contract->update([
            'renewal_date' => now(),
            'status' => 'active',
        ]);

        Log::info('Provider contract record updated for renewal', [
            'contract_id' => $contract->id,
        ]);
    }

    /**
     * Update record when contract is terminated.
     */
    protected function updateTerminatedRecord($contract): void
    {
        // Update termination information
        $contract->update([
            'status' => 'terminated',
            'termination_date' => now(),
        ]);

        Log::info('Provider contract record updated for termination', [
            'contract_id' => $contract->id,
        ]);
    }

    /**
     * Update record when contract is extended.
     */
    protected function updateExtendedRecord($contract): void
    {
        // Update extension information
        $contract->update([
            'status' => 'active',
            'end_date' => $contract->end_date->addDays(30), // Example: extend by 30 days
        ]);

        Log::info('Provider contract record updated for extension', [
            'contract_id' => $contract->id,
        ]);
    }
}
