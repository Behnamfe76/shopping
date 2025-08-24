<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;

interface ProviderInvoiceRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?ProviderInvoice;
    public function findDTO(int $id): ?ProviderInvoiceDTO;
    public function create(array $data): ProviderInvoice;
    public function createAndReturnDTO(array $data): ProviderInvoiceDTO;
    public function update(ProviderInvoice $invoice, array $data): bool;
    public function updateAndReturnDTO(ProviderInvoice $invoice, array $data): ?ProviderInvoiceDTO;
    public function delete(ProviderInvoice $invoice): bool;

    // Find by specific criteria
    public function findByProviderId(int $providerId): Collection;
    public function findByProviderIdDTO(int $providerId): Collection;
    public function findByInvoiceNumber(string $invoiceNumber): ?ProviderInvoice;
    public function findByInvoiceNumberDTO(string $invoiceNumber): ?ProviderInvoiceDTO;
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByProviderAndDateRange(int $providerId, string $startDate, string $endDate): Collection;
    public function findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate): Collection;
    public function findByDueDateRange(string $startDate, string $endDate): Collection;
    public function findByDueDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection;
    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection;
    public function findByPaymentTerms(string $paymentTerms): Collection;
    public function findByPaymentTermsDTO(string $paymentTerms): Collection;
    public function findByCurrency(string $currency): Collection;
    public function findByCurrencyDTO(string $currency): Collection;

    // Status-based queries
    public function findOverdue(): Collection;
    public function findOverdueDTO(): Collection;
    public function findPaid(): Collection;
    public function findPaidDTO(): Collection;
    public function findUnpaid(): Collection;
    public function findUnpaidDTO(): Collection;
    public function findDraft(): Collection;
    public function findDraftDTO(): Collection;

    // Business logic operations
    public function send(ProviderInvoice $invoice): bool;
    public function markAsPaid(ProviderInvoice $invoice, string $paymentDate = null): bool;
    public function markAsOverdue(ProviderInvoice $invoice): bool;
    public function cancel(ProviderInvoice $invoice, string $reason = null): bool;
    public function dispute(ProviderInvoice $invoice, string $reason = null): bool;
    public function resend(ProviderInvoice $invoice): bool;
    public function updateAmounts(ProviderInvoice $invoice, array $amounts): bool;
    public function extendDueDate(ProviderInvoice $invoice, string $newDueDate): bool;

    // Provider-specific statistics
    public function getProviderInvoiceCount(int $providerId): int;
    public function getProviderInvoiceCountByStatus(int $providerId, string $status): int;
    public function getProviderTotalInvoiced(int $providerId, string $startDate = null, string $endDate = null): float;
    public function getProviderTotalPaid(int $providerId, string $startDate = null, string $endDate = null): float;
    public function getProviderTotalOutstanding(int $providerId): float;
    public function getProviderOverdueAmount(int $providerId): float;
    public function getProviderAverageInvoiceAmount(int $providerId): float;

    // Global statistics
    public function getTotalInvoiceCount(): int;
    public function getTotalInvoiceCountByStatus(string $status): int;
    public function getTotalInvoicedAmount(string $startDate = null, string $endDate = null): float;
    public function getTotalPaidAmount(string $startDate = null, string $endDate = null): float;
    public function getTotalOutstandingAmount(): float;
    public function getTotalOverdueAmount(): float;
    public function getAverageInvoiceAmount(): float;
    public function getOverdueInvoiceCount(): int;
    public function getPaidInvoiceCount(): int;
    public function getUnpaidInvoiceCount(): int;

    // Search functionality
    public function searchInvoices(string $query): Collection;
    public function searchInvoicesDTO(string $query): Collection;
    public function searchInvoicesByProvider(int $providerId, string $query): Collection;
    public function searchInvoicesByProviderDTO(int $providerId, string $query): Collection;

    // Import/Export functionality
    public function exportInvoiceData(array $filters = []): string;
    public function importInvoiceData(string $data): bool;

    // Analytics and reporting
    public function getInvoiceStatistics(): array;
    public function getProviderInvoiceStatistics(int $providerId): array;
    public function getInvoiceTrends(string $startDate = null, string $endDate = null): array;

    // Utility methods
    public function generateInvoiceNumber(): string;
    public function isInvoiceNumberUnique(string $invoiceNumber): bool;
    public function calculateInvoiceTotals(int $invoiceId): array;
}

