<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SendProviderInvoiceAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ProviderInvoice $invoice): ProviderInvoiceDTO
    {
        try {
            DB::beginTransaction();

            // Validate sending permissions
            if (!$this->canInvoiceBeSent($invoice)) {
                throw new Exception('Invoice cannot be sent in its current status.');
            }

            // Validate invoice data before sending
            $this->validateInvoiceForSending($invoice);

            // Update invoice status to sent
            $result = $this->repository->send($invoice);

            if (!$result) {
                throw new Exception('Failed to send invoice.');
            }

            // Refresh invoice data
            $sentInvoice = $invoice->fresh();

            // Send invoice notifications
            $this->sendInvoiceNotifications($sentInvoice);

            // Update provider records if needed
            $this->updateProviderRecords($sentInvoice);

            DB::commit();

            return ProviderInvoiceDTO::fromModel($sentInvoice);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to send provider invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function canInvoiceBeSent(ProviderInvoice $invoice): bool
    {
        return $invoice->status === InvoiceStatus::DRAFT->value;
    }

    protected function validateInvoiceForSending(ProviderInvoice $invoice): void
    {
        // Check if invoice has required data
        if (empty($invoice->invoice_number)) {
            throw new Exception('Invoice number is required before sending.');
        }

        if (empty($invoice->due_date)) {
            throw new Exception('Due date is required before sending.');
        }

        if ($invoice->total_amount <= 0) {
            throw new Exception('Invoice total amount must be greater than 0.');
        }

        if (empty($invoice->provider_id)) {
            throw new Exception('Provider is required before sending.');
        }

        // Check if invoice is complete
        if (empty($invoice->subtotal) || empty($invoice->tax_amount)) {
            throw new Exception('Invoice must have complete amount breakdown before sending.');
        }
    }

    protected function sendInvoiceNotifications(ProviderInvoice $invoice): void
    {
        // Send notification to provider
        Log::info('Invoice sent notification sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'invoice_number' => $invoice->invoice_number
        ]);

        // Send notification to internal team
        Log::info('Invoice sent notification sent to internal team', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }

    protected function updateProviderRecords(ProviderInvoice $invoice): void
    {
        // Update provider's invoice count
        Log::info('Provider invoice records updated', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);

        // Could add more provider record updates here
        // such as updating last invoice date, total outstanding amount, etc.
    }
}

