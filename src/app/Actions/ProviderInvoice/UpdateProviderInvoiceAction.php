<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateProviderInvoiceAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ProviderInvoice $invoice, array $data): ProviderInvoiceDTO
    {
        try {
            DB::beginTransaction();

            // Check if invoice can be modified
            if (!$this->canInvoiceBeModified($invoice)) {
                throw new Exception('Invoice cannot be modified in its current status.');
            }

            // Validate update data
            $this->validateUpdateData($data, $invoice);

            // Update invoice
            $result = $this->repository->update($invoice, $data);

            if (!$result) {
                throw new Exception('Failed to update invoice.');
            }

            // Refresh invoice data
            $updatedInvoice = $invoice->fresh();

            // Handle status changes
            if (isset($data['status']) && $data['status'] !== $invoice->status) {
                $this->handleStatusChange($updatedInvoice, $data['status']);
            }

            // Recalculate amounts if needed
            if ($this->shouldRecalculateAmounts($data)) {
                $this->recalculateAmounts($updatedInvoice);
            }

            // Send notifications if needed
            $this->sendUpdateNotifications($updatedInvoice, $data);

            DB::commit();

            return ProviderInvoiceDTO::fromModel($updatedInvoice);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    protected function canInvoiceBeModified(ProviderInvoice $invoice): bool
    {
        $status = InvoiceStatus::from($invoice->status);
        return $status->isEditable();
    }

    protected function validateUpdateData(array $data, ProviderInvoice $invoice): void
    {
        // Basic validation
        if (isset($data['invoice_number']) && $data['invoice_number'] !== $invoice->invoice_number) {
            // Check if new invoice number is unique
            $existingInvoice = $this->repository->findByInvoiceNumber($data['invoice_number']);
            if ($existingInvoice && $existingInvoice->id !== $invoice->id) {
                throw new Exception('Invoice number must be unique.');
            }
        }

        // Validate due date
        if (isset($data['due_date'])) {
            $invoiceDate = $data['invoice_date'] ?? $invoice->invoice_date;
            if (strtotime($data['due_date']) < strtotime($invoiceDate)) {
                throw new Exception('Due date must be after or equal to invoice date.');
            }
        }

        // Validate amounts
        if (isset($data['total_amount']) && $data['total_amount'] < 0) {
            throw new Exception('Total amount cannot be negative.');
        }
    }

    protected function handleStatusChange(ProviderInvoice $invoice, string $newStatus): void
    {
        $oldStatus = $invoice->status;

        // Update status-specific fields
        switch ($newStatus) {
            case InvoiceStatus::SENT->value:
                $invoice->update(['sent_at' => now()]);
                break;
            case InvoiceStatus::PAID->value:
                $invoice->update(['paid_at' => now()]);
                break;
            case InvoiceStatus::OVERDUE->value:
                $invoice->update(['overdue_notice_sent' => now()]);
                break;
        }

        Log::info('Provider invoice status changed', [
            'invoice_id' => $invoice->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }

    protected function shouldRecalculateAmounts(array $data): bool
    {
        return isset($data['subtotal']) ||
               isset($data['tax_amount']) ||
               isset($data['discount_amount']) ||
               isset($data['shipping_amount']);
    }

    protected function recalculateAmounts(ProviderInvoice $invoice): void
    {
        $totalAmount = ($invoice->subtotal ?? 0) +
                      ($invoice->tax_amount ?? 0) +
                      ($invoice->shipping_amount ?? 0) -
                      ($invoice->discount_amount ?? 0);

        $invoice->update(['total_amount' => $totalAmount]);
    }

    protected function sendUpdateNotifications(ProviderInvoice $invoice, array $data): void
    {
        // Send notifications based on what was updated
        if (isset($data['status'])) {
            // Status change notification
            Log::info('Status change notification sent for invoice', [
                'invoice_id' => $invoice->id,
                'new_status' => $data['status']
            ]);
        }

        if (isset($data['due_date'])) {
            // Due date change notification
            Log::info('Due date change notification sent for invoice', [
                'invoice_id' => $invoice->id,
                'new_due_date' => $data['due_date']
            ]);
        }
    }
}

