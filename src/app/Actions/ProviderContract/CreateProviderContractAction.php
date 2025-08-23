<?php

namespace Fereydooni\Shopping\App\Actions\ProviderContract;

use Fereydooni\Shopping\App\Models\ProviderContract;
use Fereydooni\Shopping\App\DTOs\ProviderContractDTO;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderContractRepositoryInterface;
use Fereydooni\Shopping\App\Events\ProviderContract\ProviderContractCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Exception;

class CreateProviderContractAction
{
    public function __construct(
        private ProviderContractRepositoryInterface $repository
    ) {}

    public function execute(array $data): ProviderContractDTO
    {
        try {
            DB::beginTransaction();

            // Validate contract data
            $this->validateContractData($data);

            // Generate contract number if not provided
            if (empty($data['contract_number'])) {
                $data['contract_number'] = $this->repository->generateContractNumber();
            }

            // Set default status if not provided
            if (empty($data['status'])) {
                $data['status'] = 'draft';
            }

            // Handle attachments
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                $data['attachments'] = $this->processAttachments($data['attachments']);
            }

            // Create the contract
            $contract = $this->repository->create($data);
            $dto = $this->repository->findDTO($contract->id);

            // Send notifications
            $this->sendNotifications($contract);

            // Dispatch event
            Event::dispatch(new ProviderContractCreated($contract));

            DB::commit();

            Log::info('Provider contract created successfully', [
                'contract_id' => $contract->id,
                'provider_id' => $contract->provider_id,
                'contract_number' => $contract->contract_number
            ]);

            return $dto;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider contract: ' . $e->getMessage(), [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function validateContractData(array $data): void
    {
        $rules = ProviderContractDTO::rules();
        $messages = ProviderContractDTO::messages();

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new Exception('Validation failed: ' . $validator->errors()->first());
        }
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
                    'uploaded_at' => now()->toISOString()
                ];
            }
        }

        return $processedAttachments;
    }

    private function sendNotifications(ProviderContract $contract): void
    {
        // Send notification to provider
        // Send notification to contract manager
        // Send notification to relevant stakeholders

        // This would typically use Laravel's notification system
        // For now, we'll just log the notification intent
        Log::info('Notifications would be sent for new contract', [
            'contract_id' => $contract->id,
            'provider_id' => $contract->provider_id
        ]);
    }
}
