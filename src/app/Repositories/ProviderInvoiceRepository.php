<?php

namespace Fereydooni\Shopping\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Fereydooni\Shopping\App\Enums\PaymentTerms;
use Carbon\Carbon;

class ProviderInvoiceRepository implements ProviderInvoiceRepositoryInterface
{
    protected ProviderInvoice $model;

    public function __construct(ProviderInvoice $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember('provider_invoices_all', 3600, function () {
            return $this->model->with(['provider', 'payments'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'payments'])
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderInvoice
    {
        return Cache::remember("provider_invoice_{$id}", 3600, function () use ($id) {
            return $this->model->with(['provider', 'payments'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderInvoiceDTO
    {
        $invoice = $this->find($id);
        return $invoice ? ProviderInvoiceDTO::fromModel($invoice) : null;
    }

    public function create(array $data): ProviderInvoice
    {
        try {
            DB::beginTransaction();

            $invoice = $this->model->create($data);

            // Clear cache
            Cache::forget('provider_invoices_all');

            DB::commit();
            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider invoice: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderInvoiceDTO
    {
        $invoice = $this->create($data);
        return ProviderInvoiceDTO::fromModel($invoice);
    }

    public function update(ProviderInvoice $invoice, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $invoice->update($data);

            // Clear cache
            Cache::forget("provider_invoice_{$invoice->id}");
            Cache::forget('provider_invoices_all');

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider invoice: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderInvoice $invoice, array $data): ?ProviderInvoiceDTO
    {
        $result = $this->update($invoice, $data);
        return $result ? ProviderInvoiceDTO::fromModel($invoice->fresh()) : null;
    }

    public function delete(ProviderInvoice $invoice): bool
    {
        try {
            DB::beginTransaction();

            $result = $invoice->delete();

            // Clear cache
            Cache::forget("provider_invoice_{$invoice->id}");
            Cache::forget('provider_invoices_all');

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider invoice: ' . $e->getMessage());
            throw $e;
        }
    }

    // Find by specific criteria
    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember("provider_invoices_provider_{$providerId}", 3600, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)
                ->with(['provider', 'payments'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $invoices = $this->findByProviderId($providerId);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?ProviderInvoice
    {
        return $this->model->where('invoice_number', $invoiceNumber)
            ->with(['provider', 'payments'])
            ->first();
    }

    public function findByInvoiceNumberDTO(string $invoiceNumber): ?ProviderInvoiceDTO
    {
        $invoice = $this->findByInvoiceNumber($invoiceNumber);
        return $invoice ? ProviderInvoiceDTO::fromModel($invoice) : null;
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $invoices = $this->findByStatus($status);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('invoice_date', [$startDate, $endDate])
            ->with(['provider', 'payments'])
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $invoices = $this->findByDateRange($startDate, $endDate);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByProviderAndDateRange(int $providerId, string $startDate, string $endDate): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with(['provider', 'payments'])
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    public function findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate): Collection
    {
        $invoices = $this->findByProviderAndDateRange($providerId, $startDate, $endDate);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByDueDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('due_date', [$startDate, $endDate])
            ->with(['provider', 'payments'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function findByDueDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $invoices = $this->findByDueDateRange($startDate, $endDate);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->model->whereBetween('total_amount', [$minAmount, $maxAmount])
            ->with(['provider', 'payments'])
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        $invoices = $this->findByAmountRange($minAmount, $maxAmount);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByPaymentTerms(string $paymentTerms): Collection
    {
        return $this->model->where('payment_terms', $paymentTerms)
            ->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByPaymentTermsDTO(string $paymentTerms): Collection
    {
        $invoices = $this->findByPaymentTerms($paymentTerms);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findByCurrency(string $currency): Collection
    {
        return $this->model->where('currency', $currency)
            ->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCurrencyDTO(string $currency): Collection
    {
        $invoices = $this->findByCurrency($currency);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    // Status-based queries
    public function findOverdue(): Collection
    {
        return $this->model->where('due_date', '<', now())
            ->where('status', '!=', InvoiceStatus::PAID->value)
            ->where('status', '!=', InvoiceStatus::CANCELLED->value)
            ->with(['provider', 'payments'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function findOverdueDTO(): Collection
    {
        $invoices = $this->findOverdue();
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findPaid(): Collection
    {
        return $this->findByStatus(InvoiceStatus::PAID->value);
    }

    public function findPaidDTO(): Collection
    {
        return $this->findByStatusDTO(InvoiceStatus::PAID->value);
    }

    public function findUnpaid(): Collection
    {
        return $this->model->whereIn('status', [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ])->with(['provider', 'payments'])
        ->orderBy('due_date', 'asc')
        ->get();
    }

    public function findUnpaidDTO(): Collection
    {
        $invoices = $this->findUnpaid();
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function findDraft(): Collection
    {
        return $this->findByStatus(InvoiceStatus::DRAFT->value);
    }

    public function findDraftDTO(): Collection
    {
        return $this->findByStatusDTO(InvoiceStatus::DRAFT->value);
    }

    // Business logic operations
    public function send(ProviderInvoice $invoice): bool
    {
        if ($invoice->status !== InvoiceStatus::DRAFT->value) {
            return false;
        }

        return $this->update($invoice, [
            'status' => InvoiceStatus::SENT->value,
            'sent_at' => now()
        ]);
    }

    public function markAsPaid(ProviderInvoice $invoice, string $paymentDate = null): bool
    {
        if (!in_array($invoice->status, [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ])) {
            return false;
        }

        return $this->update($invoice, [
            'status' => InvoiceStatus::PAID->value,
            'paid_at' => $paymentDate ?: now()
        ]);
    }

    public function markAsOverdue(ProviderInvoice $invoice): bool
    {
        if ($invoice->status !== InvoiceStatus::SENT->value) {
            return false;
        }

        return $this->update($invoice, [
            'status' => InvoiceStatus::OVERDUE->value
        ]);
    }

    public function cancel(ProviderInvoice $invoice, string $reason = null): bool
    {
        if (!in_array($invoice->status, [
            InvoiceStatus::DRAFT->value,
            InvoiceStatus::SENT->value
        ])) {
            return false;
        }

        $data = ['status' => InvoiceStatus::CANCELLED->value];
        if ($reason) {
            $data['notes'] = ($invoice->notes ? $invoice->notes . "\n" : '') . "Cancelled: {$reason}";
        }

        return $this->update($invoice, $data);
    }

    public function dispute(ProviderInvoice $invoice, string $reason = null): bool
    {
        $data = ['status' => InvoiceStatus::DISPUTED->value];
        if ($reason) {
            $data['notes'] = ($invoice->notes ? $invoice->notes . "\n" : '') . "Disputed: {$reason}";
        }

        return $this->update($invoice, $data);
    }

    public function resend(ProviderInvoice $invoice): bool
    {
        if ($invoice->status !== InvoiceStatus::SENT->value) {
            return false;
        }

        return $this->update($invoice, [
            'sent_at' => now()
        ]);
    }

    public function updateAmounts(ProviderInvoice $invoice, array $amounts): bool
    {
        $data = [];

        if (isset($amounts['subtotal'])) $data['subtotal'] = $amounts['subtotal'];
        if (isset($amounts['tax_amount'])) $data['tax_amount'] = $amounts['tax_amount'];
        if (isset($amounts['discount_amount'])) $data['discount_amount'] = $amounts['discount_amount'];
        if (isset($amounts['shipping_amount'])) $data['shipping_amount'] = $amounts['shipping_amount'];

        if (!empty($data)) {
            $data['total_amount'] = $this->calculateTotalAmount($data);
        }

        return $this->update($invoice, $data);
    }

    public function extendDueDate(ProviderInvoice $invoice, string $newDueDate): bool
    {
        if ($invoice->status === InvoiceStatus::PAID->value ||
            $invoice->status === InvoiceStatus::CANCELLED->value) {
            return false;
        }

        return $this->update($invoice, [
            'due_date' => $newDueDate
        ]);
    }

    // Provider-specific statistics
    public function getProviderInvoiceCount(int $providerId): int
    {
        return Cache::remember("provider_invoice_count_{$providerId}", 3600, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)->count();
        });
    }

    public function getProviderInvoiceCountByStatus(int $providerId, string $status): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', $status)
            ->count();
    }

    public function getProviderTotalInvoiced(int $providerId, string $startDate = null, string $endDate = null): float
    {
        $query = $this->model->where('provider_id', $providerId);

        if ($startDate && $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate]);
        }

        return $query->sum('total_amount');
    }

    public function getProviderTotalPaid(int $providerId, string $startDate = null, string $endDate = null): float
    {
        $query = $this->model->where('provider_id', $providerId)
            ->where('status', InvoiceStatus::PAID->value);

        if ($startDate && $endDate) {
            $query->whereBetween('paid_at', [$startDate, $endDate]);
        }

        return $query->sum('total_amount');
    }

    public function getProviderTotalOutstanding(int $providerId): float
    {
        return $this->model->where('provider_id', $providerId)
            ->whereIn('status', [
                InvoiceStatus::SENT->value,
                InvoiceStatus::OVERDUE->value,
                InvoiceStatus::PARTIALLY_PAID->value
            ])
            ->sum('total_amount');
    }

    public function getProviderOverdueAmount(int $providerId): float
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', InvoiceStatus::OVERDUE->value)
            ->sum('total_amount');
    }

    public function getProviderAverageInvoiceAmount(int $providerId): float
    {
        return $this->model->where('provider_id', $providerId)
            ->avg('total_amount') ?? 0;
    }

    // Global statistics
    public function getTotalInvoiceCount(): int
    {
        return Cache::remember('total_invoice_count', 3600, function () {
            return $this->model->count();
        });
    }

    public function getTotalInvoiceCountByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getTotalInvoicedAmount(string $startDate = null, string $endDate = null): float
    {
        $query = $this->model;

        if ($startDate && $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate]);
        }

        return $query->sum('total_amount');
    }

    public function getTotalPaidAmount(string $startDate = null, string $endDate = null): float
    {
        $query = $this->model->where('status', InvoiceStatus::PAID->value);

        if ($startDate && $endDate) {
            $query->whereBetween('paid_at', [$startDate, $endDate]);
        }

        return $query->sum('total_amount');
    }

    public function getTotalOutstandingAmount(): float
    {
        return $this->model->whereIn('status', [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ])->sum('total_amount');
    }

    public function getTotalOverdueAmount(): float
    {
        return $this->model->where('status', InvoiceStatus::OVERDUE->value)
            ->sum('total_amount');
    }

    public function getAverageInvoiceAmount(): float
    {
        return $this->model->avg('total_amount') ?? 0;
    }

    public function getOverdueInvoiceCount(): int
    {
        return $this->model->where('status', InvoiceStatus::OVERDUE->value)->count();
    }

    public function getPaidInvoiceCount(): int
    {
        return $this->model->where('status', InvoiceStatus::PAID->value)->count();
    }

    public function getUnpaidInvoiceCount(): int
    {
        return $this->model->whereIn('status', [
            InvoiceStatus::SENT->value,
            InvoiceStatus::OVERDUE->value,
            InvoiceStatus::PARTIALLY_PAID->value
        ])->count();
    }

    // Search functionality
    public function searchInvoices(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('invoice_number', 'like', "%{$query}%")
              ->orWhere('reference_number', 'like', "%{$query}%")
              ->orWhere('notes', 'like', "%{$query}%")
              ->orWhereHas('provider', function ($providerQuery) use ($query) {
                  $providerQuery->where('name', 'like', "%{$query}%");
              });
        })->with(['provider', 'payments'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function searchInvoicesDTO(string $query): Collection
    {
        $invoices = $this->searchInvoices($query);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    public function searchInvoicesByProvider(int $providerId, string $query): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('invoice_number', 'like', "%{$query}%")
                  ->orWhere('reference_number', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            })->with(['provider', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchInvoicesByProviderDTO(int $providerId, string $query): Collection
    {
        $invoices = $this->searchInvoicesByProvider($providerId, $query);
        return $invoices->map(fn($invoice) => ProviderInvoiceDTO::fromModel($invoice));
    }

    // Import/Export functionality
    public function exportInvoiceData(array $filters = []): string
    {
        // Implementation for CSV/Excel export
        // This is a placeholder - actual implementation would depend on export library
        return json_encode($this->getInvoiceStatistics());
    }

    public function importInvoiceData(string $data): bool
    {
        // Implementation for CSV/Excel import
        // This is a placeholder - actual implementation would depend on import library
        try {
            $data = json_decode($data, true);
            // Process imported data
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to import invoice data: ' . $e->getMessage());
            return false;
        }
    }

    // Analytics and reporting
    public function getInvoiceStatistics(): array
    {
        return Cache::remember('invoice_statistics', 3600, function () {
            return [
                'total_count' => $this->getTotalInvoiceCount(),
                'total_amount' => $this->getTotalInvoicedAmount(),
                'paid_count' => $this->getPaidInvoiceCount(),
                'paid_amount' => $this->getTotalPaidAmount(),
                'outstanding_count' => $this->getUnpaidInvoiceCount(),
                'outstanding_amount' => $this->getTotalOutstandingAmount(),
                'overdue_count' => $this->getOverdueInvoiceCount(),
                'overdue_amount' => $this->getTotalOverdueAmount(),
                'average_amount' => $this->getAverageInvoiceAmount(),
                'status_distribution' => $this->getStatusDistribution(),
                'monthly_trends' => $this->getMonthlyTrends(),
            ];
        });
    }

    public function getProviderInvoiceStatistics(int $providerId): array
    {
        return [
            'total_count' => $this->getProviderInvoiceCount($providerId),
            'total_invoiced' => $this->getProviderTotalInvoiced($providerId),
            'total_paid' => $this->getProviderTotalPaid($providerId),
            'total_outstanding' => $this->getProviderTotalOutstanding($providerId),
            'overdue_amount' => $this->getProviderOverdueAmount($providerId),
            'average_amount' => $this->getProviderAverageInvoiceAmount($providerId),
            'status_distribution' => $this->getProviderStatusDistribution($providerId),
        ];
    }

    public function getInvoiceTrends(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?: now()->subMonths(12)->format('Y-m-d');
        $endDate = $endDate ?: now()->format('Y-m-d');

        return $this->model->whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    // Utility methods
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastInvoice = $this->model->where('invoice_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    public function isInvoiceNumberUnique(string $invoiceNumber): bool
    {
        return !$this->model->where('invoice_number', $invoiceNumber)->exists();
    }

    public function calculateInvoiceTotals(int $invoiceId): array
    {
        $invoice = $this->find($invoiceId);
        if (!$invoice) {
            return [];
        }

        $subtotal = $invoice->subtotal ?? 0;
        $taxAmount = $invoice->tax_amount ?? 0;
        $discountAmount = $invoice->discount_amount ?? 0;
        $shippingAmount = $invoice->shipping_amount ?? 0;

        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingAmount,
            'total_amount' => $totalAmount,
        ];
    }

    // Helper methods
    private function calculateTotalAmount(array $data): float
    {
        $subtotal = $data['subtotal'] ?? 0;
        $taxAmount = $data['tax_amount'] ?? 0;
        $discountAmount = $data['discount_amount'] ?? 0;
        $shippingAmount = $data['shipping_amount'] ?? 0;

        return $subtotal + $taxAmount + $shippingAmount - $discountAmount;
    }

    private function getStatusDistribution(): array
    {
        return $this->model->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getProviderStatusDistribution(int $providerId): array
    {
        return $this->model->where('provider_id', $providerId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getMonthlyTrends(): array
    {
        return $this->model->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->pluck('count', 'month')
            ->toArray();
    }
}
