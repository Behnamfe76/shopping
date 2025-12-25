<?php

namespace Fereydooni\Shopping\App\Actions\ProviderContract;

use Exception;
use Fereydooni\Shopping\App\DTOs\ProviderContractDTO;
use Fereydooni\Shopping\App\Events\ProviderContract\ProviderContractUpdated;
use Fereydooni\Shopping\App\Models\ProviderContract;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderContractRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UpdateProviderContractAction
{
    public function __construct(
        private ProviderContractRepositoryInterface $repository
    ) {}

    public function execute(int $id, array $data): ?ProviderContractDTO
    {
        try {
            DB::beginTransaction();

            $contract = $this->repository->find($id);
            if (! $contract) {
                throw new Exception('Provider contract not found');
            }

            // Check if contract can be modified
            if (! $contract->canBeModified()) {
                throw new Exception('Contract cannot be modified in its current status');
            }

            // Validate update data
            $this->validateUpdateData($data, $contract);

            // Handle status changes
            if (isset($data['status']) && $data['status'] !== $contract->status) {
                $this->handleStatusChange($contract, $data['status']);
            }

            // Update contract terms if provided
            if (isset($data['terms'])) {
                $data['terms'] = $this->processTerms($data['terms']);
            }

            // Handle attachments if provided
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                $data['attachments'] = $this->processAttachments($data['attachments']);
            }

            // Update the contract
            $updated = $this->repository->update($contract, $data);
            if (! $updated) {
                throw new Exception('Failed to update provider contract');
            }

            $dto = $this->repository->findDTO($id);

            // Send notifications
            $this->sendNotifications($contract, $data);

            // Dispatch event
            Event::dispatch(new ProviderContractUpdated($contract));

            DB::commit();

            Log::info('Provider contract updated successfully', [
                'contract_id' => $contract->id,
                'provider_id' => $contract->provider_id,
            ]);

            return $dto;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider contract: '.$e->getMessage(), [
                'contract_id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function validateUpdateData(array $data, ProviderContract $contract): void
    {
        // Create validation rules for update (some fields might not be required)
        $rules = [];
        $messages = ProviderContractDTO::messages();

        if (isset($data['title'])) {
            $rules['title'] = 'string|min:3|max:255';
        }

        if (isset($data['description'])) {
            $rules['description'] = 'nullable|string|min:10|max:1000';
        }

        if (isset($data['start_date'])) {
            $rules['start_date'] = 'date|after_or_equal:today';
        }

        if (isset($data['end_date'])) {
            $rules['end_date'] = 'date|after:start_date';
        }

        if (isset($data['commission_rate'])) {
            $rules['commission_rate'] = 'numeric|min:0|max:100';
        }

        if (isset($data['contract_value'])) {
            $rules['contract_value'] = 'numeric|min:0';
        }

        if (isset($data['currency'])) {
            $rules['currency'] = 'string|size:3';
        }

        if (! empty($rules)) {
            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                throw new Exception('Validation failed: '.$validator->errors()->first());
            }
        }
    }

    private function handleStatusChange(ProviderContract $contract, string $newStatus): void
    {
        // Validate status transition
        $allowedTransitions = $this->getAllowedStatusTransitions($contract->status);

        if (! in_array($newStatus, $allowedTransitions)) {
            throw new Exception("Invalid status transition from {$contract->status} to {$newStatus}");
        }
    }

    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'draft' => ['active', 'cancelled'],
            'active' => ['expired', 'terminated', 'suspended', 'pending_renewal'],
            'suspended' => ['active', 'terminated'],
            'pending_renewal' => ['active', 'expired', 'terminated'],
            'expired' => ['active', 'terminated'],
            default => []
        };
    }

    private function processTerms(array $terms): array
    {
        // Process and validate contract terms
        $processedTerms = [];

        foreach ($terms as $term) {
            if (isset($term['title']) && isset($term['description'])) {
                $processedTerms[] = [
                    'title' => $term['title'],
                    'description' => $term['description'],
                    'effective_date' => $term['effective_date'] ?? now()->toISOString(),
                ];
            }
        }

        return $processedTerms;
    }

    private function processAttachments(array $attachments): array
    {
        // Process and validate attachments
        $processedAttachments = [];

        foreach ($attachments as $attachment) {
            if (isset($attachment['name']) && isset($attachment['path'])) {
                $processedAttachments[] = [
                    'name' => $attachment['name'],
                    'path' => $attachment['path'],
                    'size' => $attachment['size'] ?? 0,
                    'type' => $attachment['type'] ?? 'unknown',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        return $processedAttachments;
    }

    private function sendNotifications(ProviderContract $contract, array $updatedData): void
    {
        // Send notification about contract update
        // Send notification to relevant stakeholders if significant changes

        Log::info('Update notifications would be sent for contract', [
            'contract_id' => $contract->id,
            'provider_id' => $contract->provider_id,
            'updated_fields' => array_keys($updatedData),
        ]);
    }
}
