<?php

namespace App\Actions\ProviderContract;

use App\DTOs\ProviderContractDTO;
use App\Models\ProviderContract;
use App\Enums\ContractStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProviderContract\ContractTerminated;
use App\Events\Provider\ProviderContractTerminated;

class TerminateProviderContractAction
{
    /**
     * Execute the action to terminate a provider contract
     *
     * @param ProviderContract $contract
     * @param string|null $reason
     * @return ProviderContractDTO|null
     */
    public function execute(ProviderContract $contract, string $reason = null): ?ProviderContractDTO
    {
        try {
            DB::beginTransaction();

            // Validate that the contract can be terminated
            if (!$this->canTerminateContract($contract)) {
                throw new \Exception('Contract cannot be terminated in its current state');
            }

            // Update contract status and termination information
            $contract->update([
                'status' => ContractStatus::TERMINATED,
                'termination_date' => now(),
                'termination_reason' => $reason,
            ]);

            // Refresh the contract to get updated data
            $contract->refresh();

            // Convert to DTO
            $dto = ProviderContractDTO::fromModel($contract);

            // Send notifications
            $this->sendTerminationNotifications($contract, $reason);

            // Dispatch event
            event(new ProviderContractTerminated($contract, $reason));

            DB::commit();

            Log::info('Provider contract terminated successfully', [
                'contract_id' => $contract->id,
                'termination_reason' => $reason,
                'termination_date' => now()
            ]);

            return $dto;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to terminate provider contract', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Check if the contract can be terminated
     *
     * @param ProviderContract $contract
     * @return bool
     */
    protected function canTerminateContract(ProviderContract $contract): bool
    {
        // Contract must be active or suspended
        if (!in_array($contract->status, [ContractStatus::ACTIVE, ContractStatus::SUSPENDED])) {
            return false;
        }

        // Contract must not already be terminated
        if ($contract->status === ContractStatus::TERMINATED) {
            return false;
        }

        // Contract must not be expired
        if ($contract->status === ContractStatus::EXPIRED) {
            return false;
        }

        return true;
    }

    /**
     * Send notifications for contract termination
     *
     * @param ProviderContract $contract
     * @param string|null $reason
     * @return void
     */
    protected function sendTerminationNotifications(ProviderContract $contract, string $reason = null): void
    {
        try {
            // Notify provider
            if ($contract->provider) {
                Notification::send($contract->provider, new ContractTerminated($contract, $reason));
            }

            // Notify contract stakeholders (if any)
            $this->notifyStakeholders($contract, $reason);

        } catch (\Exception $e) {
            Log::warning('Failed to send contract termination notifications', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify contract stakeholders
     *
     * @param ProviderContract $contract
     * @param string|null $reason
     * @return void
     */
    protected function notifyStakeholders(ProviderContract $contract, string $reason = null): void
    {
        // This method can be extended to notify additional stakeholders
        // such as legal team, finance team, etc.

        // For now, we'll just log that stakeholders should be notified
        Log::info('Contract stakeholders should be notified of termination', [
            'contract_id' => $contract->id,
            'contract_type' => $contract->contract_type,
            'contract_value' => $contract->contract_value,
            'termination_reason' => $reason
        ]);
    }
}
