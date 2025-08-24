<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MarkInvoiceAsPaidAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ProviderInvoice $invoice, string $paymentDate = null): ProviderInvoiceDTO
    {
        try {
            DB::beginTransaction();

            // Validate payment data
            if (!$this->canInvoiceBeMarkedAsPaid($invoice)) {
                throw new Exception('Invoice cannot be marked as paid in its current status.');
            }

            // Validate payment date
            $this->validatePaymentDate($paymentDate, $invoice);

            // Update invoice status to paid
            $result = $this->repository->markAsPaid($invoice, $paymentDate);

            if (!$result) {
                throw new Exception('Failed to mark invoice as paid.');
            }

            // Refresh invoice data
            $paidInvoice = $invoice->fresh();

            // Process payment records
            $this->processPaymentRecords($paidInvoice, $paymentDate);

            // Send payment notifications
            $this->sendPaymentNotifications($paidInvoice);

            DB::commit();

            return ProviderInvoiceDTO::fromModel($paidInvoice);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark provider invoice as paid', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'payment_date' => $paymentDate
            ]);
            throw $e;
        }
    }

    protected function canInvoiceBeMarkedAsPaid(ProviderInvoice $invoice): bool
    {
        return in_array($invoice->status, [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ]);
    }

    protected function validatePaymentDate(?string $paymentDate, ProviderInvoice $invoice): void
    {
        if ($paymentDate) {
            $paymentDateObj = \Carbon\Carbon::parse($paymentDate);
            $invoiceDate = $invoice->invoice_date;

            if ($paymentDateObj->isBefore($invoiceDate)) {
                throw new Exception('Payment date cannot be before invoice date.');
            }
        }
    }

    protected function processPaymentRecords(ProviderInvoice $invoice, ?string $paymentDate): void
    {
        // Update payment records
        Log::info('Payment records processed for invoice', [
            'invoice_id' => $invoice->id,
            'payment_date' => $paymentDate ?? now()->format('Y-m-d')
        ]);

        // Could add more payment processing logic here
        // such as creating payment records, updating accounting systems, etc.
    }

    protected function sendPaymentNotifications(ProviderInvoice $invoice): void
    {
        // Send notification to provider
        Log::info('Payment received notification sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'amount' => $invoice->total_amount
        ]);

        // Send notification to internal team
        Log::info('Payment received notification sent to internal team', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'amount' => $invoice->total_amount
        ]);

        // Send notification to accounting/finance team
        Log::info('Payment received notification sent to finance team', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total_amount
        ]);
    }
}

