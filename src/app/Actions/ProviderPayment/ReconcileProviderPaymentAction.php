<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentReconciled;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;

class ReconcileProviderPaymentAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to reconcile a provider payment.
     */
    public function execute(ProviderPayment $payment, string $reconciliationNotes = null): ProviderPaymentDTO
    {
        try {
            DB::beginTransaction();

            // Validate reconciliation data
            $this->validateReconciliationData($payment);

            // Check if payment can be reconciled
            $this->checkPaymentReconcilable($payment);

            // Update payment status to reconciled
            $updated = $this->repository->reconcile($payment, $reconciliationNotes);

            if (!$updated) {
                throw new \Exception('Failed to reconcile provider payment');
            }

            // Refresh the payment model
            $payment->refresh();

            // Update financial records
            $this->updateFinancialRecords($payment);

            // Send reconciliation notifications
            $this->sendReconciliationNotifications($payment);

            // Dispatch event
            Event::dispatch(new ProviderPaymentReconciled($payment));

            DB::commit();

            Log::info('Provider payment reconciled successfully', [
                'payment_id' => $payment->id,
                'provider_id' => $payment->provider_id,
                'reconciled_at' => $payment->reconciled_at,
                'notes' => $reconciliationNotes
            ]);

            return ProviderPaymentDTO::fromModel($payment);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to reconcile provider payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id
            ]);

            throw $e;
        }
    }

    /**
     * Validate reconciliation data.
     */
    protected function validateReconciliationData(ProviderPayment $payment): void
    {
        // Check if payment has required reconciliation data
        if (empty($payment->transaction_id)) {
            throw new \InvalidArgumentException('Payment must have a transaction ID to be reconciled');
        }

        if (empty($payment->reference_number)) {
            throw new \InvalidArgumentException('Payment must have a reference number to be reconciled');
        }
    }

    /**
     * Check if the payment can be reconciled.
     */
    protected function checkPaymentReconcilable(ProviderPayment $payment): void
    {
        if ($payment->status !== ProviderPaymentStatus::COMPLETED) {
            throw new \InvalidArgumentException("Payment cannot be reconciled in current status: {$payment->status->value}");
        }

        // Additional checks could include:
        // - Bank statement matching
        // - Amount verification
        // - Date verification
        // - Account reconciliation
    }

    /**
     * Update financial records.
     */
    protected function updateFinancialRecords(ProviderPayment $payment): void
    {
        // This would typically update:
        // - General ledger
        // - Bank reconciliation
        // - Financial statements
        // - Audit trail

        Log::info('Updating financial records for reconciled payment', [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency
        ]);
    }

    /**
     * Send reconciliation notifications.
     */
    protected function sendReconciliationNotifications(ProviderPayment $payment): void
    {
        // This would typically send notifications to:
        // - Accounting team
        // - Finance team
        // - Management
        // - Auditors

        Log::info('Sending reconciliation notifications', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id
        ]);
    }
}
