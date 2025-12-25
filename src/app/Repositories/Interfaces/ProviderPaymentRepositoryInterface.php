<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderPaymentRepositoryInterface
{
    /**
     * Get all provider payments.
     */
    public function all(): Collection;

    /**
     * Get paginated provider payments.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated provider payments.
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated provider payments.
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find provider payment by ID.
     */
    public function find(int $id): ?ProviderPayment;

    /**
     * Find provider payment by ID and return DTO.
     */
    public function findDTO(int $id): ?ProviderPaymentDTO;

    /**
     * Find provider payments by provider ID.
     */
    public function findByProviderId(int $providerId): Collection;

    /**
     * Find provider payments by provider ID and return DTOs.
     */
    public function findByProviderIdDTO(int $providerId): Collection;

    /**
     * Find provider payments by invoice ID.
     */
    public function findByInvoiceId(int $invoiceId): Collection;

    /**
     * Find provider payments by invoice ID and return DTOs.
     */
    public function findByInvoiceIdDTO(int $invoiceId): Collection;

    /**
     * Find provider payment by payment number.
     */
    public function findByPaymentNumber(string $paymentNumber): ?ProviderPayment;

    /**
     * Find provider payment by payment number and return DTO.
     */
    public function findByPaymentNumberDTO(string $paymentNumber): ?ProviderPaymentDTO;

    /**
     * Find provider payments by status.
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find provider payments by status and return DTOs.
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find provider payments by payment method.
     */
    public function findByPaymentMethod(string $paymentMethod): Collection;

    /**
     * Find provider payments by payment method and return DTOs.
     */
    public function findByPaymentMethodDTO(string $paymentMethod): Collection;

    /**
     * Find provider payments by date range.
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find provider payments by date range and return DTOs.
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find provider payments by provider and date range.
     */
    public function findByProviderAndDateRange(int $providerId, string $startDate, string $endDate): Collection;

    /**
     * Find provider payments by provider and date range and return DTOs.
     */
    public function findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate): Collection;

    /**
     * Find provider payment by transaction ID.
     */
    public function findByTransactionId(string $transactionId): ?ProviderPayment;

    /**
     * Find provider payment by transaction ID and return DTO.
     */
    public function findByTransactionIdDTO(string $transactionId): ?ProviderPaymentDTO;

    /**
     * Find provider payments by reference number.
     */
    public function findByReferenceNumber(string $referenceNumber): Collection;

    /**
     * Find provider payments by reference number and return DTOs.
     */
    public function findByReferenceNumberDTO(string $referenceNumber): Collection;

    /**
     * Find pending provider payments.
     */
    public function findPending(): Collection;

    /**
     * Find pending provider payments and return DTOs.
     */
    public function findPendingDTO(): Collection;

    /**
     * Find processed provider payments.
     */
    public function findProcessed(): Collection;

    /**
     * Find processed provider payments and return DTOs.
     */
    public function findProcessedDTO(): Collection;

    /**
     * Find completed provider payments.
     */
    public function findCompleted(): Collection;

    /**
     * Find completed provider payments and return DTOs.
     */
    public function findCompletedDTO(): Collection;

    /**
     * Find failed provider payments.
     */
    public function findFailed(): Collection;

    /**
     * Find failed provider payments and return DTOs.
     */
    public function findFailedDTO(): Collection;

    /**
     * Find provider payments by amount range.
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection;

    /**
     * Find provider payments by amount range and return DTOs.
     */
    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection;

    /**
     * Find provider payments by currency.
     */
    public function findByCurrency(string $currency): Collection;

    /**
     * Find provider payments by currency and return DTOs.
     */
    public function findByCurrencyDTO(string $currency): Collection;

    /**
     * Find unreconciled provider payments.
     */
    public function findUnreconciled(): Collection;

    /**
     * Find unreconciled provider payments and return DTOs.
     */
    public function findUnreconciledDTO(): Collection;

    /**
     * Find reconciled provider payments.
     */
    public function findReconciled(): Collection;

    /**
     * Find reconciled provider payments and return DTOs.
     */
    public function findReconciledDTO(): Collection;

    /**
     * Create new provider payment.
     */
    public function create(array $data): ProviderPayment;

    /**
     * Create new provider payment and return DTO.
     */
    public function createAndReturnDTO(array $data): ProviderPaymentDTO;

    /**
     * Update provider payment.
     */
    public function update(ProviderPayment $payment, array $data): bool;

    /**
     * Update provider payment and return DTO.
     */
    public function updateAndReturnDTO(ProviderPayment $payment, array $data): ?ProviderPaymentDTO;

    /**
     * Delete provider payment.
     */
    public function delete(ProviderPayment $payment): bool;

    /**
     * Process provider payment.
     */
    public function process(ProviderPayment $payment, int $processedBy): bool;

    /**
     * Complete provider payment.
     */
    public function complete(ProviderPayment $payment): bool;

    /**
     * Fail provider payment.
     */
    public function fail(ProviderPayment $payment, ?string $reason = null): bool;

    /**
     * Cancel provider payment.
     */
    public function cancel(ProviderPayment $payment, ?string $reason = null): bool;

    /**
     * Refund provider payment.
     */
    public function refund(ProviderPayment $payment, float $refundAmount, ?string $reason = null): bool;

    /**
     * Reconcile provider payment.
     */
    public function reconcile(ProviderPayment $payment, ?string $reconciliationNotes = null): bool;

    /**
     * Update provider payment amount.
     */
    public function updateAmount(ProviderPayment $payment, float $newAmount): bool;

    /**
     * Get provider payment count.
     */
    public function getProviderPaymentCount(int $providerId): int;

    /**
     * Get provider payment count by status.
     */
    public function getProviderPaymentCountByStatus(int $providerId, string $status): int;

    /**
     * Get provider payment count by method.
     */
    public function getProviderPaymentCountByMethod(int $providerId, string $paymentMethod): int;

    /**
     * Get provider total paid amount.
     */
    public function getProviderTotalPaid(int $providerId, ?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get provider total pending amount.
     */
    public function getProviderTotalPending(int $providerId): float;

    /**
     * Get provider total failed amount.
     */
    public function getProviderTotalFailed(int $providerId): float;

    /**
     * Get provider average payment amount.
     */
    public function getProviderAveragePaymentAmount(int $providerId): float;

    /**
     * Get provider payment history.
     */
    public function getProviderPaymentHistory(int $providerId): Collection;

    /**
     * Get provider payment history and return DTOs.
     */
    public function getProviderPaymentHistoryDTO(int $providerId): Collection;

    /**
     * Get invoice payment history.
     */
    public function getInvoicePaymentHistory(int $invoiceId): Collection;

    /**
     * Get invoice payment history and return DTOs.
     */
    public function getInvoicePaymentHistoryDTO(int $invoiceId): Collection;

    /**
     * Get total payment count.
     */
    public function getTotalPaymentCount(): int;

    /**
     * Get total payment count by status.
     */
    public function getTotalPaymentCountByStatus(string $status): int;

    /**
     * Get total payment count by method.
     */
    public function getTotalPaymentCountByMethod(string $paymentMethod): int;

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAmount(?string $startDate = null, ?string $endDate = null): float;

    /**
     * Get total pending amount.
     */
    public function getTotalPendingAmount(): float;

    /**
     * Get total failed amount.
     */
    public function getTotalFailedAmount(): float;

    /**
     * Get average payment amount.
     */
    public function getAveragePaymentAmount(): float;

    /**
     * Get pending payment count.
     */
    public function getPendingPaymentCount(): int;

    /**
     * Get completed payment count.
     */
    public function getCompletedPaymentCount(): int;

    /**
     * Get failed payment count.
     */
    public function getFailedPaymentCount(): int;

    /**
     * Get unreconciled payment count.
     */
    public function getUnreconciledPaymentCount(): int;

    /**
     * Search payments.
     */
    public function searchPayments(string $query): Collection;

    /**
     * Search payments and return DTOs.
     */
    public function searchPaymentsDTO(string $query): Collection;

    /**
     * Search payments by provider.
     */
    public function searchPaymentsByProvider(int $providerId, string $query): Collection;

    /**
     * Search payments by provider and return DTOs.
     */
    public function searchPaymentsByProviderDTO(int $providerId, string $query): Collection;

    /**
     * Export payment data.
     */
    public function exportPaymentData(array $filters = []): string;

    /**
     * Import payment data.
     */
    public function importPaymentData(string $data): bool;

    /**
     * Get payment statistics.
     */
    public function getPaymentStatistics(): array;

    /**
     * Get provider payment statistics.
     */
    public function getProviderPaymentStatistics(int $providerId): array;

    /**
     * Get payment trends.
     */
    public function getPaymentTrends(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Generate payment number.
     */
    public function generatePaymentNumber(): string;

    /**
     * Check if payment number is unique.
     */
    public function isPaymentNumberUnique(string $paymentNumber): bool;

    /**
     * Calculate payment totals.
     */
    public function calculatePaymentTotals(int $paymentId): array;
}
