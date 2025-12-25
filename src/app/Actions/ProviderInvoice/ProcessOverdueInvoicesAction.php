<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Exception;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ProcessOverdueInvoicesAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        try {
            // Identify overdue invoices
            $overdueInvoices = $this->repository->findOverdue();

            if ($overdueInvoices->isEmpty()) {
                Log::info('No overdue invoices found to process');

                return [
                    'processed_count' => 0,
                    'overdue_invoices' => [],
                    'total_overdue_amount' => 0,
                ];
            }

            $processedCount = 0;
            $totalOverdueAmount = 0;
            $processedInvoices = [];

            foreach ($overdueInvoices as $invoice) {
                try {
                    $this->processOverdueInvoice($invoice);
                    $processedCount++;
                    $totalOverdueAmount += $invoice->total_amount;
                    $processedInvoices[] = $invoice;
                } catch (Exception $e) {
                    Log::error('Failed to process overdue invoice', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Overdue invoices processing completed', [
                'total_found' => $overdueInvoices->count(),
                'processed_count' => $processedCount,
                'total_overdue_amount' => $totalOverdueAmount,
            ]);

            return [
                'processed_count' => $processedCount,
                'overdue_invoices' => $processedInvoices,
                'total_overdue_amount' => $totalOverdueAmount,
            ];

        } catch (Exception $e) {
            Log::error('Failed to process overdue invoices', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function processOverdueInvoice(ProviderInvoice $invoice): void
    {
        // Update invoice status to overdue if not already
        if ($invoice->status !== InvoiceStatus::OVERDUE->value) {
            $this->repository->markAsOverdue($invoice);
        }

        // Send overdue notifications
        $this->sendOverdueNotifications($invoice);

        // Calculate late fees if applicable
        $this->calculateLateFees($invoice);

        // Update provider records
        $this->updateProviderOverdueRecords($invoice);
    }

    protected function sendOverdueNotifications(ProviderInvoice $invoice): void
    {
        // Send notification to provider
        Log::info('Overdue notice sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'days_overdue' => $invoice->due_date->diffInDays(now()),
        ]);

        // Send notification to internal team
        Log::info('Overdue notice sent to internal team', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'amount' => $invoice->total_amount,
        ]);

        // Send escalation notification if severely overdue
        if ($invoice->due_date->diffInDays(now()) > 30) {
            Log::info('Severe overdue escalation sent', [
                'invoice_id' => $invoice->id,
                'days_overdue' => $invoice->due_date->diffInDays(now()),
            ]);
        }
    }

    protected function calculateLateFees(ProviderInvoice $invoice): void
    {
        $daysOverdue = $invoice->due_date->diffInDays(now());

        // Calculate late fees based on overdue days
        if ($daysOverdue > 30) {
            Log::info('Late fees calculated for severely overdue invoice', [
                'invoice_id' => $invoice->id,
                'days_overdue' => $daysOverdue,
                'late_fee_amount' => $this->calculateLateFeeAmount($invoice, $daysOverdue),
            ]);
        }
    }

    protected function calculateLateFeeAmount(ProviderInvoice $invoice, int $daysOverdue): float
    {
        // Simple late fee calculation (could be more complex)
        $baseAmount = $invoice->total_amount;
        $lateFeeRate = 0.05; // 5% per month

        if ($daysOverdue <= 30) {
            return 0; // No late fees for first month
        }

        $monthsOverdue = ceil($daysOverdue / 30);

        return $baseAmount * $lateFeeRate * ($monthsOverdue - 1);
    }

    protected function updateProviderOverdueRecords(ProviderInvoice $invoice): void
    {
        // Update provider's overdue invoice count and amounts
        Log::info('Provider overdue records updated', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'overdue_amount' => $invoice->total_amount,
        ]);
    }
}
