<?php

namespace App\Actions\ProviderContract;

use App\DTOs\ProviderContractDTO;
use App\Models\ProviderContract;
use App\Enums\ContractStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProviderContract\ContractRenewed;
use App\Events\Provider\ProviderContractRenewed;

class RenewProviderContractAction
{
    /**
     * Execute the action to renew a provider contract
     *
     * @param ProviderContract $contract
     * @param string|null $newEndDate
     * @return ProviderContractDTO|null
     */
    public function execute(ProviderContract $contract, string $newEndDate = null): ?ProviderContractDTO
    {
        try {
            DB::beginTransaction();

            // Validate that the contract can be renewed
            if (!$this->canRenewContract($contract)) {
                throw new \Exception('Contract cannot be renewed in its current state');
            }

            // Calculate new dates
            $renewalData = $this->calculateRenewalDates($contract, $newEndDate);

            // Update contract with renewal information
            $contract->update([
                'status' => ContractStatus::ACTIVE,
                'start_date' => $renewalData['start_date'],
                'end_date' => $renewalData['end_date'],
                'renewal_date' => $renewalData['renewal_date'],
                'auto_renewal' => $contract->auto_renewal ?? false,
            ]);

            // Refresh the contract to get updated data
            $contract->refresh();

            // Convert to DTO
            $dto = ProviderContractDTO::fromModel($contract);

            // Send notifications
            $this->sendRenewalNotifications($contract);

            // Dispatch event
            event(new ProviderContractRenewed($contract));

            DB::commit();

            Log::info('Provider contract renewed successfully', [
                'contract_id' => $contract->id,
                'old_end_date' => $renewalData['old_end_date'],
                'new_end_date' => $renewalData['end_date'],
                'renewal_date' => $renewalData['renewal_date']
            ]);

            return $dto;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to renew provider contract', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Check if the contract can be renewed
     *
     * @param ProviderContract $contract
     * @return bool
     */
    protected function canRenewContract(ProviderContract $contract): bool
    {
        // Contract must be active or expiring soon
        if (!in_array($contract->status, [ContractStatus::ACTIVE, ContractStatus::PENDING_RENEWAL])) {
            return false;
        }

        // Contract must have an end date
        if (!$contract->end_date) {
            return false;
        }

        // Contract must not be terminated
        if ($contract->status === ContractStatus::TERMINATED) {
            return false;
        }

        // Contract must not be suspended
        if ($contract->status === ContractStatus::SUSPENDED) {
            return false;
        }

        return true;
    }

    /**
     * Calculate renewal dates
     *
     * @param ProviderContract $contract
     * @param string|null $newEndDate
     * @return array
     */
    protected function calculateRenewalDates(ProviderContract $contract, string $newEndDate = null): array
    {
        $oldEndDate = $contract->end_date;
        $currentDate = now();

        // If no new end date provided, calculate based on renewal terms
        if (!$newEndDate) {
            $newEndDate = $this->calculateDefaultRenewalDate($contract);
        }

        // Ensure new end date is in the future
        if (strtotime($newEndDate) <= $currentDate->timestamp) {
            $newEndDate = $currentDate->addYear()->format('Y-m-d');
        }

        return [
            'start_date' => $currentDate->format('Y-m-d'),
            'end_date' => $newEndDate,
            'renewal_date' => $currentDate->format('Y-m-d'),
            'old_end_date' => $oldEndDate
        ];
    }

    /**
     * Calculate default renewal date based on contract terms
     *
     * @param ProviderContract $contract
     * @return string
     */
    protected function calculateDefaultRenewalDate(ProviderContract $contract): string
    {
        $currentDate = now();

        // Default to 1 year renewal if no specific terms
        $renewalPeriod = $contract->renewal_terms['period'] ?? 12; // months
        $renewalUnit = $contract->renewal_terms['unit'] ?? 'months';

        switch ($renewalUnit) {
            case 'years':
                return $currentDate->addYears($renewalPeriod)->format('Y-m-d');
            case 'months':
                return $currentDate->addMonths($renewalPeriod)->format('Y-m-d');
            case 'weeks':
                return $currentDate->addWeeks($renewalPeriod)->format('Y-m-d');
            case 'days':
                return $currentDate->addDays($renewalPeriod)->format('Y-m-d');
            default:
                return $currentDate->addYear()->format('Y-m-d');
        }
    }

    /**
     * Send notifications for contract renewal
     *
     * @param ProviderContract $contract
     * @return void
     */
    protected function sendRenewalNotifications(ProviderContract $contract): void
    {
        try {
            // Notify provider
            if ($contract->provider) {
                Notification::send($contract->provider, new ContractRenewed($contract));
            }

            // Notify contract stakeholders (if any)
            $this->notifyStakeholders($contract);

        } catch (\Exception $e) {
            Log::warning('Failed to send contract renewal notifications', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify contract stakeholders
     *
     * @param ProviderContract $contract
     * @return void
     */
    protected function notifyStakeholders(ProviderContract $contract): void
    {
        // This method can be extended to notify additional stakeholders
        // such as legal team, finance team, etc.

        // For now, we'll just log that stakeholders should be notified
        Log::info('Contract stakeholders should be notified of renewal', [
            'contract_id' => $contract->id,
            'contract_type' => $contract->contract_type,
            'contract_value' => $contract->contract_value,
            'new_end_date' => $contract->end_date
        ]);
    }
}
