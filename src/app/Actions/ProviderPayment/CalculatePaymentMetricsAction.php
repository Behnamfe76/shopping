<?php

namespace Fereydooni\Shopping\App\Actions\ProviderPayment;

use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CalculatePaymentMetricsAction
{
    public function __construct(
        protected ProviderPaymentRepositoryInterface $repository
    ) {}

    /**
     * Execute the action to calculate payment metrics.
     */
    public function execute(?int $providerId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $metrics = [];

            if ($providerId) {
                // Calculate provider-specific metrics
                $metrics = $this->calculateProviderMetrics($providerId, $startDate, $endDate);
            } else {
                // Calculate global metrics
                $metrics = $this->calculateGlobalMetrics($startDate, $endDate);
            }

            Log::info('Payment metrics calculated successfully', [
                'provider_id' => $providerId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'metrics_count' => count($metrics),
            ]);

            return $metrics;

        } catch (\Exception $e) {
            Log::error('Failed to calculate payment metrics', [
                'error' => $e->getMessage(),
                'provider_id' => $providerId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Calculate provider-specific payment metrics.
     */
    protected function calculateProviderMetrics(int $providerId, ?string $startDate = null, ?string $endDate = null): array
    {
        $metrics = [];

        // Total payments
        $metrics['total_payments'] = $this->repository->getProviderPaymentCount($providerId);
        $metrics['total_paid_amount'] = $this->repository->getProviderTotalPaid($providerId, $startDate, $endDate);
        $metrics['total_pending_amount'] = $this->repository->getProviderTotalPending($providerId);
        $metrics['total_failed_amount'] = $this->repository->getProviderTotalFailed($providerId);
        $metrics['average_payment_amount'] = $this->repository->getProviderAveragePaymentAmount($providerId);

        // Payment counts by status
        $metrics['pending_count'] = $this->repository->getProviderPaymentCountByStatus($providerId, 'pending');
        $metrics['processed_count'] = $this->repository->getProviderPaymentCountByStatus($providerId, 'processed');
        $metrics['completed_count'] = $this->repository->getProviderPaymentCountByStatus($providerId, 'completed');
        $metrics['failed_count'] = $this->repository->getProviderPaymentCountByStatus($providerId, 'failed');

        // Payment counts by method
        $metrics['bank_transfer_count'] = $this->repository->getProviderPaymentCountByMethod($providerId, 'bank_transfer');
        $metrics['check_count'] = $this->repository->getProviderPaymentCountByMethod($providerId, 'check');
        $metrics['credit_card_count'] = $this->repository->getProviderPaymentCountByMethod($providerId, 'credit_card');
        $metrics['wire_transfer_count'] = $this->repository->getProviderPaymentCountByMethod($providerId, 'wire_transfer');

        // Reconciliation metrics
        $metrics['reconciled_count'] = $this->repository->findReconciledDTO()->where('provider_id', $providerId)->count();
        $metrics['unreconciled_count'] = $this->repository->findUnreconciledDTO()->where('provider_id', $providerId)->count();

        return $metrics;
    }

    /**
     * Calculate global payment metrics.
     */
    protected function calculateGlobalMetrics(?string $startDate = null, ?string $endDate = null): array
    {
        $metrics = [];

        // Total payments
        $metrics['total_payments'] = $this->repository->getTotalPaymentCount();
        $metrics['total_paid_amount'] = $this->repository->getTotalPaidAmount($startDate, $endDate);
        $metrics['total_pending_amount'] = $this->repository->getTotalPendingAmount();
        $metrics['total_failed_amount'] = $this->repository->getTotalFailedAmount();
        $metrics['average_payment_amount'] = $this->repository->getAveragePaymentAmount();

        // Payment counts by status
        $metrics['pending_count'] = $this->repository->getPendingPaymentCount();
        $metrics['processed_count'] = $this->repository->getTotalPaymentCountByStatus('processed');
        $metrics['completed_count'] = $this->repository->getCompletedPaymentCount();
        $metrics['failed_count'] = $this->repository->getFailedPaymentCount();

        // Payment counts by method
        $metrics['bank_transfer_count'] = $this->repository->getTotalPaymentCountByMethod('bank_transfer');
        $metrics['check_count'] = $this->repository->getTotalPaymentCountByMethod('check');
        $metrics['credit_card_count'] = $this->repository->getTotalPaymentCountByMethod('credit_card');
        $metrics['wire_transfer_count'] = $this->repository->getTotalPaymentCountByMethod('wire_transfer');

        // Reconciliation metrics
        $metrics['reconciled_count'] = $this->repository->getTotalPaymentCountByStatus('reconciled');
        $metrics['unreconciled_count'] = $this->repository->getUnreconciledPaymentCount();

        // Payment trends
        if ($startDate && $endDate) {
            $metrics['payment_trends'] = $this->repository->getPaymentTrends($startDate, $endDate);
        }

        return $metrics;
    }
}
