<?php

namespace App\Actions\ProviderContract;

use App\DTOs\ProviderContractDTO;
use App\Models\ProviderContract;
use App\Models\User;
use App\Enums\ContractStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProviderContract\ContractSigned;
use App\Events\Provider\ProviderContractSigned;

class SignProviderContractAction
{
    /**
     * Execute the action to sign a provider contract
     *
     * @param ProviderContract $contract
     * @param int $signedBy
     * @return ProviderContractDTO|null
     */
    public function execute(ProviderContract $contract, int $signedBy): ?ProviderContractDTO
    {
        try {
            DB::beginTransaction();

            // Validate that the contract can be signed
            if (!$this->canSignContract($contract)) {
                throw new \Exception('Contract cannot be signed in its current state');
            }

            // Get the user who is signing
            $signer = User::find($signedBy);
            if (!$signer) {
                throw new \Exception('Signer not found');
            }

            // Update contract status and signing information
            $contract->update([
                'status' => ContractStatus::ACTIVE,
                'signed_by' => $signedBy,
                'signed_at' => now(),
                'start_date' => $contract->start_date ?? now(),
            ]);

            // Refresh the contract to get updated data
            $contract->refresh();

            // Convert to DTO
            $dto = ProviderContractDTO::fromModel($contract);

            // Send notifications
            $this->sendSigningNotifications($contract, $signer);

            // Dispatch event
            event(new ProviderContractSigned($contract, $signer));

            DB::commit();

            Log::info('Provider contract signed successfully', [
                'contract_id' => $contract->id,
                'signed_by' => $signedBy,
                'signed_at' => now()
            ]);

            return $dto;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to sign provider contract', [
                'contract_id' => $contract->id,
                'signed_by' => $signedBy,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Check if the contract can be signed
     *
     * @param ProviderContract $contract
     * @return bool
     */
    protected function canSignContract(ProviderContract $contract): bool
    {
        // Contract must be in draft status
        if ($contract->status !== ContractStatus::DRAFT) {
            return false;
        }

        // Contract must have required fields
        if (empty($contract->title) || empty($contract->provider_id)) {
            return false;
        }

        // Contract must have valid dates
        if ($contract->end_date && $contract->start_date >= $contract->end_date) {
            return false;
        }

        // Contract must not be expired
        if ($contract->end_date && $contract->end_date < now()) {
            return false;
        }

        return true;
    }

    /**
     * Send notifications for contract signing
     *
     * @param ProviderContract $contract
     * @param User $signer
     * @return void
     */
    protected function sendSigningNotifications(ProviderContract $contract, User $signer): void
    {
        try {
            // Notify provider
            if ($contract->provider) {
                Notification::send($contract->provider, new ContractSigned($contract, $signer));
            }

            // Notify contract stakeholders (if any)
            $this->notifyStakeholders($contract, $signer);

        } catch (\Exception $e) {
            Log::warning('Failed to send contract signing notifications', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify contract stakeholders
     *
     * @param ProviderContract $contract
     * @param User $signer
     * @return void
     */
    protected function notifyStakeholders(ProviderContract $contract, User $signer): void
    {
        // This method can be extended to notify additional stakeholders
        // such as legal team, finance team, etc.

        // For now, we'll just log that stakeholders should be notified
        Log::info('Contract stakeholders should be notified', [
            'contract_id' => $contract->id,
            'contract_type' => $contract->contract_type,
            'contract_value' => $contract->contract_value
        ]);
    }

    /**
     * Validate signing permissions
     *
     * @param ProviderContract $contract
     * @param User $signer
     * @return bool
     */
    protected function validateSigningPermissions(ProviderContract $contract, User $signer): bool
    {
        // Check if user has permission to sign contracts
        if (!$signer->can('provider-contract.sign')) {
            return false;
        }

        // Check if user can sign this specific contract
        if (!$signer->can('provider-contract.sign-own') &&
            $signer->id !== $contract->created_by) {
            return false;
        }

        return true;
    }
}
