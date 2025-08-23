<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCompleted;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;

class CompleteProviderPaymentAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to complete a provider payment.
     */
    public function execute(ProviderPayment $payment): ProviderPaymentDTO
    {
        try {
            DB::beginTransaction();

            // Validate completion data
            $this->validateCompletionData($payment);

            // Check if payment can be completed
            $this->checkPaymentCompletable($payment);

            // Update payment status to completed
            $updated = $this->repository->complete($payment);

            if (!$updated) {
                throw new \Exception('Failed to complete provider payment');
            }

            // Refresh the payment model
            $payment->refresh();

            // Update invoice records
            $this->updateInvoiceRecords($payment);

            // Send completion notifications
            $this->sendCompletionNotifications($payment);

            // Dispatch event
            Event::dispatch(new ProviderPaymentCompleted($payment));

            DB::commit();

            Log::info('Provider payment completed successfully', [
                'payment_id' => $payment->id,
                'provider_id' => $payment->provider_id,
                'amount' => $payment->amount,
                'completed_at' => now()
            ]);

            return ProviderPaymentDTO::fromModel($payment);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to complete provider payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id
            ]);

            throw $e;
        }
    }

    /**
     * Validate completion data.
     */
    protected function validateCompletionData(ProviderPayment $payment): void
    {
        // Check if payment has required completion data
        if (empty($payment->transaction_id)) {
            throw new \InvalidArgumentException('Payment must have a transaction ID to be completed');
        }

        if (empty($payment->reference_number)) {
            throw new \InvalidArgumentException('Payment must have a reference number to be completed');
        }
    }

    /**
     * Check if the payment can be completed.
     */
    protected function checkPaymentCompletable(ProviderPayment $payment): void
    {
        if ($payment->status !== ProviderPaymentStatus::PROCESSED) {
            throw new \InvalidArgumentException("Payment cannot be completed in current status: {$payment->status->value}");
        }

        // Additional checks could include:
        // - Transaction confirmation
        // - Bank settlement
        // - Payment verification
        // - Fraud checks
    }

    /**
     * Update invoice records.
     */
    protected function updateInvoiceRecords(ProviderPayment $payment): void
    {
        // This would typically update:
        // - Invoice payment status
        // - Outstanding balance
        // - Payment history
        // - Account receivables

        Log::info('Updating invoice records for completed payment', [
            'payment_id' => $payment->id,
            'invoice_id' => $payment->invoice_id
        ]);
    }

    /**
     * Send completion notifications.
     */
    protected function sendCompletionNotifications(ProviderPayment $payment): void
    {
        // This would typically send notifications to:
        // - Provider
        // - Accounting team
        // - Management
        // - Other stakeholders

        Log::info('Sending completion notifications', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id
        ]);
    }
}
