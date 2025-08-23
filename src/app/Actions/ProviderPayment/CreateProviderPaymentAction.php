<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCreated;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;

class CreateProviderPaymentAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to create a new provider payment.
     */
    public function execute(array $data): ProviderPaymentDTO
    {
        try {
            DB::beginTransaction();

            // Validate and prepare data
            $paymentData = $this->preparePaymentData($data);

            // Create the payment
            $payment = $this->repository->create($paymentData);

            // Handle attachments if provided
            if (!empty($data['attachments'])) {
                $this->handleAttachments($payment, $data['attachments']);
            }

            // Send notifications
            $this->sendNotifications($payment);

            // Dispatch event
            Event::dispatch(new ProviderPaymentCreated($payment));

            DB::commit();

            Log::info('Provider payment created successfully', [
                'payment_id' => $payment->id,
                'provider_id' => $payment->provider_id,
                'amount' => $payment->amount,
                'currency' => $payment->currency
            ]);

            return ProviderPaymentDTO::fromModel($payment);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create provider payment', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw $e;
        }
    }

    /**
     * Prepare payment data for creation.
     */
    protected function preparePaymentData(array $data): array
    {
        // Generate payment number if not provided
        if (empty($data['payment_number'])) {
            $data['payment_number'] = $this->repository->generatePaymentNumber();
        }

        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = ProviderPaymentStatus::PENDING->value;
        }

        // Set default currency if not provided
        if (empty($data['currency'])) {
            $data['currency'] = 'USD';
        }

        // Validate payment method
        if (!empty($data['payment_method'])) {
            $this->validatePaymentMethod($data['payment_method']);
        }

        // Validate amount
        if (isset($data['amount'])) {
            $this->validateAmount($data['amount']);
        }

        // Validate provider exists
        $this->validateProvider($data['provider_id']);

        // Validate invoice if provided
        if (!empty($data['invoice_id'])) {
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
        // This would typically involve:
        // 1. Validating file types and sizes
        // 2. Storing files in appropriate storage
        // 3. Updating the payment record with file paths

        Log::info('Handling attachments for payment', [
            'payment_id' => $payment->id,
            'attachment_count' => count($attachments)
        ]);
    }

    /**
     * Send notifications for new payment.
     */
    protected function sendNotifications($payment): void
    {
        // Implementation for sending notifications
        // This would typically involve:
        // 1. Notifying the provider
        // 2. Notifying relevant staff members
        // 3. Sending email/SMS notifications

        Log::info('Sending notifications for new payment', [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id
        ]);
    }

    /**
     * Validate payment method.
     */
    protected function validatePaymentMethod(string $method): void
    {
        if (!in_array($method, array_column(ProviderPaymentMethod::cases(), 'value'))) {
            throw new \InvalidArgumentException("Invalid payment method: {$method}");
        }
    }

    /**
     * Validate payment amount.
     */
    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Payment amount must be greater than 0");
        }
    }

    /**
     * Validate provider exists.
     */
    protected function validateProvider(int $providerId): void
    {
        // This would typically check if the provider exists in the database
        // For now, we'll just log the validation
        Log::info('Validating provider', ['provider_id' => $providerId]);
    }

    /**
     * Validate invoice if provided.
     */
    protected function validateInvoice(int $invoiceId): void
    {
        // This would typically check if the invoice exists and belongs to the provider
        // For now, we'll just log the validation
        Log::info('Validating invoice', ['invoice_id' => $invoiceId]);
    }
}
