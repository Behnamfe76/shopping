<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentProcessed;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProcessProviderPaymentAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to process a provider payment.
     */
    public function execute(ProviderPayment $payment, int $processedBy): ProviderPaymentDTO
    {
        try {
            DB::beginTransaction();

            // Validate processing permissions
            $this->validateProcessingPermissions($payment, $processedBy);

            // Check if payment can be processed
            $this->checkPaymentProcessable($payment);

            // Update payment status to processed
            $updated = $this->repository->process($payment, $processedBy);

            if (! $updated) {
                throw new \Exception('Failed to process provider payment');
            }

            // Refresh the payment model
            $payment->refresh();

            // Update provider records
            $this->updateProviderRecords($payment);

            // Send processing notifications
            $this->sendProcessingNotifications($payment);

            // Dispatch event
            Event::dispatch(new ProviderPaymentProcessed($payment));

            DB::commit();

            Log::info('Provider payment processed successfully', [
                'payment_id' => $payment->id,
                'provider_id' => $payment->provider_id,
                'processed_by' => $processedBy,
                'processed_at' => $payment->processed_at,
            ]);

            return ProviderPaymentDTO::fromModel($payment);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process provider payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'processed_by' => $processedBy,
            ]);

            throw $e;
        }
    }

    /**
     * Validate processing permissions.
     */
    protected function validateProcessingPermissions(ProviderPayment $payment, int $processedBy): void
    {
        // This would typically check if the user has permission to process payments
        // For now, we'll just log the validation
        Log::info('Validating processing permissions', [
            'payment_id' => $payment->id,
            'processed_by' => $processedBy,
        ]);
    }

    /**
     * Check if the payment can be processed.
     */
    protected function checkPaymentProcessable(ProviderPayment $payment): void
    {
        if ($payment->status !== ProviderPaymentStatus::PENDING) {
            throw new \InvalidArgumentException("Payment cannot be processed in current status: {$payment->status->value}");
        }

        // Additional checks could include:
        // - Payment amount validation
        // - Provider account status
        // - Invoice validation
        // - Payment method validation
    }

    /**
     * Update provider records.
     */
    protected function updateProviderRecords(ProviderPayment $payment): void
    {
        // This would typically update:
        // - Provider's payment history
        // - Outstanding balance
        // - Payment statistics
        // - Account status

        Log::info('Updating provider records for processed payment', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id,
        ]);
    }

    /**
     * Send processing notifications.
     */
    protected function sendProcessingNotifications(ProviderPayment $payment): void
    {
        // This would typically send notifications to:
        // - Provider
        // - Accounting team
        // - Management
        // - Other stakeholders

        Log::info('Sending processing notifications', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id,
        ]);
    }
}
