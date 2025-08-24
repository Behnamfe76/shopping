<?php

namespace Fereydooni\Shopping\App\Facades;

use Fereydooni\Shopping\App\Actions\ProviderInvoice\CreateProviderInvoiceAction;
use Fereydooni\Shopping\App\Actions\ProviderInvoice\UpdateProviderInvoiceAction;
use Fereydooni\Shopping\App\Actions\ProviderInvoice\SendProviderInvoiceAction;
use Fereydooni\Shopping\App\Actions\ProviderInvoice\MarkInvoiceAsPaidAction;
use Fereydooni\Shopping\App\Actions\ProviderInvoice\ProcessOverdueInvoicesAction;
use Fereydooni\Shopping\App\Actions\ProviderInvoice\CalculateInvoiceMetricsAction;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * ProviderInvoice Facade
 *
 * Provides easy access to provider invoice functionality including:
 * - CRUD operations
 * - Business logic actions
 * - Repository methods
 * - Statistics and metrics
 */
class ProviderInvoice extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'provider-invoice';
    }

    /**
     * Create a new provider invoice
     */
    public static function create(array $data): ProviderInvoiceDTO
    {
        return app(CreateProviderInvoiceAction::class)->execute($data);
    }

    /**
     * Update an existing provider invoice
     */
    public static function update(ProviderInvoice $invoice, array $data): ProviderInvoiceDTO
    {
        return app(UpdateProviderInvoiceAction::class)->execute($invoice, $data);
    }

    /**
     * Send a provider invoice
     */
    public static function send(ProviderInvoice $invoice): ProviderInvoiceDTO
    {
        return app(SendProviderInvoiceAction::class)->execute($invoice);
    }

    /**
     * Mark an invoice as paid
     */
    public static function markAsPaid(ProviderInvoice $invoice, string $paymentDate = null): ProviderInvoiceDTO
    {
        return app(MarkInvoiceAsPaidAction::class)->execute($invoice, $paymentDate);
    }

    /**
     * Process overdue invoices
     */
    public static function processOverdue(): array
    {
        return app(ProcessOverdueInvoicesAction::class)->execute();
    }

    /**
     * Calculate invoice metrics
     */
    public static function calculateMetrics(): array
    {
        return app(CalculateInvoiceMetricsAction::class)->execute();
    }

    /**
     * Get all invoices
     */
    public static function all(): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->all();
    }

    /**
     * Get paginated invoices
     */
    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return app(ProviderInvoiceRepositoryInterface::class)->paginate($perPage);
    }

    /**
     * Get simple paginated invoices
     */
    public static function simplePaginate(int $perPage = 15): Paginator
    {
        return app(ProviderInvoiceRepositoryInterface::class)->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated invoices
     */
    public static function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return app(ProviderInvoiceRepositoryInterface::class)->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find invoice by ID
     */
    public static function find(int $id): ?ProviderInvoice
    {
        return app(ProviderInvoiceRepositoryInterface::class)->find($id);
    }

    /**
     * Find invoice by ID and return DTO
     */
    public static function findDTO(int $id): ?ProviderInvoiceDTO
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findDTO($id);
    }

    /**
     * Find invoices by provider ID
     */
    public static function findByProvider(int $providerId): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByProviderId($providerId);
    }

    /**
     * Find invoices by provider ID and return DTOs
     */
    public static function findByProviderDTO(int $providerId): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByProviderIdDTO($providerId);
    }

    /**
     * Find invoice by invoice number
     */
    public static function findByNumber(string $invoiceNumber): ?ProviderInvoice
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByInvoiceNumber($invoiceNumber);
    }

    /**
     * Find invoice by invoice number and return DTO
     */
    public static function findByNumberDTO(string $invoiceNumber): ?ProviderInvoiceDTO
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByInvoiceNumberDTO($invoiceNumber);
    }

    /**
     * Find invoices by status
     */
    public static function findByStatus(string $status): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByStatus($status);
    }

    /**
     * Find invoices by status and return DTOs
     */
    public static function findByStatusDTO(string $status): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByStatusDTO($status);
    }

    /**
     * Find invoices by date range
     */
    public static function findByDateRange(string $startDate, string $endDate): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByDateRange($startDate, $endDate);
    }

    /**
     * Find invoices by due date range
     */
    public static function findByDueDateRange(string $startDate, string $endDate): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByDueDateRange($startDate, $endDate);
    }

    /**
     * Find overdue invoices
     */
    public static function findOverdue(): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findOverdue();
    }

    /**
     * Find paid invoices
     */
    public static function findPaid(): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findPaid();
    }

    /**
     * Find unpaid invoices
     */
    public static function findUnpaid(): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findUnpaid();
    }

    /**
     * Find draft invoices
     */
    public static function findDraft(): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findDraft();
    }

    /**
     * Find invoices by amount range
     */
    public static function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->findByAmountRange($minAmount, $maxAmount);
    }

    /**
     * Search invoices
     */
    public static function search(string $query): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->searchInvoices($query);
    }

    /**
     * Search invoices and return DTOs
     */
    public static function searchDTO(string $query): Collection
    {
        return app(ProviderInvoiceRepositoryInterface::class)->searchInvoicesDTO($query);
    }

    /**
     * Get invoice statistics
     */
    public static function getStatistics(): array
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getInvoiceStatistics();
    }

    /**
     * Get provider invoice statistics
     */
    public static function getProviderStatistics(int $providerId): array
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getProviderInvoiceStatistics($providerId);
    }

    /**
     * Get invoice trends
     */
    public static function getTrends(string $startDate = null, string $endDate = null): array
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getInvoiceTrends($startDate, $endDate);
    }

    /**
     * Generate unique invoice number
     */
    public static function generateNumber(): string
    {
        return app(ProviderInvoiceRepositoryInterface::class)->generateInvoiceNumber();
    }

    /**
     * Check if invoice number is unique
     */
    public static function isNumberUnique(string $invoiceNumber): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->isInvoiceNumberUnique($invoiceNumber);
    }

    /**
     * Calculate invoice totals
     */
    public static function calculateTotals(int $invoiceId): array
    {
        return app(ProviderInvoiceRepositoryInterface::class)->calculateInvoiceTotals($invoiceId);
    }

    /**
     * Get total invoice count
     */
    public static function getTotalCount(): int
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalInvoiceCount();
    }

    /**
     * Get total invoice count by status
     */
    public static function getTotalCountByStatus(string $status): int
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalInvoiceCountByStatus($status);
    }

    /**
     * Get total invoiced amount
     */
    public static function getTotalInvoicedAmount(string $startDate = null, string $endDate = null): float
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalInvoicedAmount($startDate, $endDate);
    }

    /**
     * Get total paid amount
     */
    public static function getTotalPaidAmount(string $startDate = null, string $endDate = null): float
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalPaidAmount($startDate, $endDate);
    }

    /**
     * Get total outstanding amount
     */
    public static function getTotalOutstandingAmount(): float
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalOutstandingAmount();
    }

    /**
     * Get total overdue amount
     */
    public static function getTotalOverdueAmount(): float
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getTotalOverdueAmount();
    }

    /**
     * Get average invoice amount
     */
    public static function getAverageAmount(): float
    {
        return app(ProviderInvoiceRepositoryInterface::class)->getAverageInvoiceAmount();
    }

    /**
     * Export invoice data
     */
    public static function export(array $filters = []): string
    {
        return app(ProviderInvoiceRepositoryInterface::class)->exportInvoiceData($filters);
    }

    /**
     * Import invoice data
     */
    public static function import(string $data): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->importInvoiceData($data);
    }

    /**
     * Delete invoice
     */
    public static function delete(ProviderInvoice $invoice): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->delete($invoice);
    }

    /**
     * Cancel invoice
     */
    public static function cancel(ProviderInvoice $invoice, string $reason = null): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->cancel($invoice, $reason);
    }

    /**
     * Dispute invoice
     */
    public static function dispute(ProviderInvoice $invoice, string $reason = null): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->dispute($invoice, $reason);
    }

    /**
     * Resend invoice
     */
    public static function resend(ProviderInvoice $invoice): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->resend($invoice);
    }

    /**
     * Update invoice amounts
     */
    public static function updateAmounts(ProviderInvoice $invoice, array $amounts): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->updateAmounts($invoice, $amounts);
    }

    /**
     * Extend invoice due date
     */
    public static function extendDueDate(ProviderInvoice $invoice, string $newDueDate): bool
    {
        return app(ProviderInvoiceRepositoryInterface::class)->extendDueDate($invoice, $newDueDate);
    }
}

