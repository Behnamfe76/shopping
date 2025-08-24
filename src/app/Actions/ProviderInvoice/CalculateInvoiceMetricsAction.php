<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class CalculateInvoiceMetricsAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(?int $providerId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $metrics = [];

            if ($providerId) {
                $metrics = $this->calculateProviderMetrics($providerId, $startDate, $endDate);
            } else {
                $metrics = $this->calculateGlobalMetrics($startDate, $endDate);
            }

            Log::info('Invoice metrics calculated successfully', [
                'provider_id' => $providerId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'metrics_count' => count($metrics)
            ]);

            return $metrics;

        } catch (Exception $e) {
            Log::error('Failed to calculate invoice metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function calculateProviderMetrics(int $providerId, ?string $startDate, ?string $endDate): array
    {
        $totalInvoiced = $this->repository->getProviderTotalInvoiced($providerId, $startDate, $endDate);
        $totalPaid = $this->repository->getProviderTotalPaid($providerId, $startDate, $endDate);
        $totalOutstanding = $this->repository->getProviderTotalOutstanding($providerId);
        $overdueAmount = $this->repository->getProviderOverdueAmount($providerId);
        $averageAmount = $this->repository->getProviderAverageInvoiceAmount($providerId);

        $totalCount = $this->repository->getProviderInvoiceCount($providerId);
        $draftCount = $this->repository->getProviderInvoiceCountByStatus($providerId, 'draft');
        $sentCount = $this->repository->getProviderInvoiceCountByStatus($providerId, 'sent');
        $paidCount = $this->repository->getProviderInvoiceCountByStatus($providerId, 'paid');
        $overdueCount = $this->repository->getProviderInvoiceCountByStatus($providerId, 'overdue');

        return [
            'provider_id' => $providerId,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'overdue_amount' => $overdueAmount,
            'average_invoice_amount' => $averageAmount,
            'total_count' => $totalCount,
            'draft_count' => $draftCount,
            'sent_count' => $sentCount,
            'paid_count' => $paidCount,
            'overdue_count' => $overdueCount,
            'payment_rate' => $totalInvoiced > 0 ? ($totalPaid / $totalInvoiced) * 100 : 0,
            'outstanding_rate' => $totalInvoiced > 0 ? ($totalOutstanding / $totalInvoiced) * 100 : 0,
            'overdue_rate' => $totalOutstanding > 0 ? ($overdueAmount / $totalOutstanding) * 100 : 0,
        ];
    }

    protected function calculateGlobalMetrics(?string $startDate, ?string $endDate): array
    {
        $totalInvoiced = $this->repository->getTotalInvoicedAmount($startDate, $endDate);
        $totalPaid = $this->repository->getTotalPaidAmount($startDate, $endDate);
        $totalOutstanding = $this->repository->getTotalOutstandingAmount();
        $overdueAmount = $this->repository->getTotalOverdueAmount();
        $averageAmount = $this->repository->getAverageInvoiceAmount();

        $totalCount = $this->repository->getTotalInvoiceCount();
        $draftCount = $this->repository->getTotalInvoiceCountByStatus('draft');
        $sentCount = $this->repository->getTotalInvoiceCountByStatus('sent');
        $paidCount = $this->repository->getTotalInvoiceCountByStatus('paid');
        $overdueCount = $this->repository->getTotalInvoiceCountByStatus('overdue');

        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'overdue_amount' => $overdueAmount,
            'average_invoice_amount' => $averageAmount,
            'total_count' => $totalCount,
            'draft_count' => $draftCount,
            'sent_count' => $sentCount,
            'paid_count' => $paidCount,
            'overdue_count' => $overdueCount,
            'payment_rate' => $totalInvoiced > 0 ? ($totalPaid / $totalInvoiced) * 100 : 0,
            'outstanding_rate' => $totalInvoiced > 0 ? ($totalOutstanding / $totalInvoiced) * 100 : 0,
            'overdue_rate' => $totalOutstanding > 0 ? ($overdueAmount / $totalOutstanding) * 100 : 0,
        ];
    }

    public function calculatePaymentTrends(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $trends = $this->repository->getInvoiceTrends($startDate, $endDate);

            Log::info('Payment trends calculated successfully', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'trends_count' => count($trends)
            ]);

            return $trends;

        } catch (Exception $e) {
            Log::error('Failed to calculate payment trends', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calculateProviderComparison(array $providerIds, ?string $startDate = null, ?string $endDate = null): array
    {
        $comparison = [];

        foreach ($providerIds as $providerId) {
            try {
                $metrics = $this->calculateProviderMetrics($providerId, $startDate, $endDate);
                $comparison[$providerId] = $metrics;
            } catch (Exception $e) {
                Log::error('Failed to calculate metrics for provider', [
                    'provider_id' => $providerId,
                    'error' => $e->getMessage()
                ]);
                $comparison[$providerId] = null;
            }
        }

        return $comparison;
    }
}

