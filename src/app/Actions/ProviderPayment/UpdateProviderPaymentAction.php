<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentUpdated;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class UpdateProviderPaymentAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to update an existing provider payment.
     */
    public function execute(ProviderPayment $payment, array $data): ProviderPaymentDTO
    {
        try {
            DB::beginTransaction();

            // Check if payment can be modified
            $this->checkPaymentModifiable($payment);

            // Validate and prepare update data
            $updateData = $this->prepareUpdateData($payment, $data);

            // Update the payment
            $updated = $this->repository->update($payment, $updateData);

            if (! $updated) {
                throw new \Exception('Failed to update provider payment');
            }

            // Refresh the payment model
            $payment->refresh();

            // Handle attachments if provided
            if (! empty($data['attachments'])) {
                $this->handleAttachments($payment, $data['attachments']);
            }

            // Handle status changes
            if (isset($data['status']) && $data['status'] !== $payment->getOriginal('status')) {
                $this->handleStatusChange($payment, $data['status']);
            }

            // Send notifications
            $this->sendNotifications($payment, 'updated');

            // Dispatch event
            Event::dispatch(new ProviderPaymentUpdated($payment));

            DB::commit();

            Log::info('Provider payment updated successfully', [
                'payment_id' => $payment->id,
                'provider_id' => $payment->provider_id,
                'changes' => $updateData,
            ]);

            return ProviderPaymentDTO::fromModel($payment);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update provider payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Check if the payment can be modified.
     */
    protected function checkPaymentModifiable(ProviderPayment $payment): void
    {
        // Check if payment is in a state that allows modification
        $nonModifiableStatuses = [
            ProviderPaymentStatus::COMPLETED->value,
            ProviderPaymentStatus::RECONCILED->value,
        ];

        if (in_array($payment->status, $nonModifiableStatuses)) {
            throw new \InvalidArgumentException("Payment cannot be modified in current status: {$payment->status}");
        }
    }

    /**
     * Prepare update data.
     */
    protected function prepareUpdateData(ProviderPayment $payment, array $data): array
    {
        // Validate payment method if provided
        if (! empty($data['payment_method'])) {
            $this->validatePaymentMethod($data['payment_method']);
        }

        // Validate amount if provided
        if (isset($data['amount'])) {
            $this->validateAmount($data['amount']);
        }

        // Validate status if provided
        if (! empty($data['status'])) {
            $this->validateStatusTransition($payment->status, $data['status']);
        }

        // Validate provider if provided
        if (! empty($data['provider_id'])) {
            $this->validateProvider($data['provider_id']);
        }

        // Validate invoice if provided
        if (! empty($data['invoice_id'])) {
            $this->validateInvoice($data['invoice_id']);
        }

        return $data;
    }

    /**
     * Handle payment attachments.
     */
    protected function handleAttachments($payment, array $attachments): void
    {
        // Implementation for handling file uploads and storage
        Log::info('Handling attachments for payment update', [
            'payment_id' => $payment->id,
            'attachment_count' => count($attachments),
        ]);
    }

    /**
     * Handle status changes.
     */
    protected function handleStatusChange(ProviderPayment $payment, string $newStatus): void
    {
        Log::info('Payment status changed', [
            'payment_id' => $payment->id,
            'old_status' => $payment->getOriginal('status'),
            'new_status' => $newStatus,
        ]);

        // Additional logic for specific status changes could go here
    }

    /**
     * Send notifications for payment update.
     */
    protected function sendNotifications($payment, string $action): void
    {
        Log::info('Sending notifications for payment update', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id,
            'action' => $action,
        ]);
    }

    /**
     * Validate payment method.
     */
    protected function validatePaymentMethod(string $method): void
    {
        if (! in_array($method, array_column(ProviderPaymentMethod::cases(), 'value'))) {
            throw new \InvalidArgumentException("Invalid payment method: {$method}");
        }
    }

    /**
     * Validate payment amount.
     */
    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than 0');
        }
    }

    /**
     * Validate status transition.
     */
    protected function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            ProviderPaymentStatus::PENDING->value => [
                ProviderPaymentStatus::PROCESSED->value,
                ProviderPaymentStatus::CANCELLED->value,
            ],
            ProviderPaymentStatus::PROCESSED->value => [
                ProviderPaymentStatus::COMPLETED->value,
                ProviderPaymentStatus::FAILED->value,
            ],
            ProviderPaymentStatus::COMPLETED->value => [
                ProviderPaymentStatus::RECONCILED->value,
                ProviderPaymentStatus::REFUNDED->value,
            ],
        ];

        if (isset($allowedTransitions[$currentStatus]) &&
            ! in_array($newStatus, $allowedTransitions[$currentStatus])) {
            throw new \InvalidArgumentException("Invalid status transition from {$currentStatus} to {$newStatus}");
        }
    }

    /**
     * Validate provider exists.
     */
    protected function validateProvider(int $providerId): void
    {
        Log::info('Validating provider for update', ['provider_id' => $providerId]);
    }

    /**
     * Validate invoice if provided.
     */
    protected function validateInvoice(int $invoiceId): void
    {
        Log::info('Validating invoice for update', ['invoice_id' => $invoiceId]);
    }
}
