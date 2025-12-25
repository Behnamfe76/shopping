<?php

namespace Fereydooni\Shopping\App\Repositories;

use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderPaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderPaymentRepository implements ProviderPaymentRepositoryInterface
{
    public function __construct(
        protected ProviderPayment $model
    ) {}

    public function all(): Collection
    {
        return Cache::remember('provider_payments_all', 3600, function () {
            return $this->model->with(['provider', 'invoice', 'processor'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->orderBy('id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderPayment
    {
        return Cache::remember("provider_payment_{$id}", 3600, function () use ($id) {
            return $this->model->with(['provider', 'invoice', 'processor'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderPaymentDTO
    {
        $payment = $this->find($id);

        return $payment ? ProviderPaymentDTO::fromModel($payment) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember("provider_payments_provider_{$providerId}", 3600, function () use ($providerId) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('provider_id', $providerId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        return $this->findByProviderId($providerId)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByInvoiceId(int $invoiceId): Collection
    {
        return Cache::remember("provider_payments_invoice_{$invoiceId}", 3600, function () use ($invoiceId) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('invoice_id', $invoiceId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByInvoiceIdDTO(int $invoiceId): Collection
    {
        return $this->findByInvoiceId($invoiceId)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByPaymentNumber(string $paymentNumber): ?ProviderPayment
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where('payment_number', $paymentNumber)
            ->first();
    }

    public function findByPaymentNumberDTO(string $paymentNumber): ?ProviderPaymentDTO
    {
        $payment = $this->findByPaymentNumber($paymentNumber);

        return $payment ? ProviderPaymentDTO::fromModel($payment) : null;
    }

    public function findByStatus(string $status): Collection
    {
        return Cache::remember("provider_payments_status_{$status}", 1800, function () use ($status) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByPaymentMethod(string $paymentMethod): Collection
    {
        return Cache::remember("provider_payments_method_{$paymentMethod}", 1800, function () use ($paymentMethod) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('payment_method', $paymentMethod)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByPaymentMethodDTO(string $paymentMethod): Collection
    {
        return $this->findByPaymentMethod($paymentMethod)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByProviderAndDateRange(int $providerId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where('provider_id', $providerId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate): Collection
    {
        return $this->findByProviderAndDateRange($providerId, $startDate, $endDate)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByTransactionId(string $transactionId): ?ProviderPayment
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where('transaction_id', $transactionId)
            ->first();
    }

    public function findByTransactionIdDTO(string $transactionId): ?ProviderPaymentDTO
    {
        $payment = $this->findByTransactionId($transactionId);

        return $payment ? ProviderPaymentDTO::fromModel($payment) : null;
    }

    public function findByReferenceNumber(string $referenceNumber): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where('reference_number', $referenceNumber)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByReferenceNumberDTO(string $referenceNumber): Collection
    {
        return $this->findByReferenceNumber($referenceNumber)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findPending(): Collection
    {
        return $this->model->pending()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findPendingDTO(): Collection
    {
        return $this->findPending()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findProcessed(): Collection
    {
        return $this->model->processed()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findProcessedDTO(): Collection
    {
        return $this->findProcessed()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findCompleted(): Collection
    {
        return $this->model->completed()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findCompletedDTO(): Collection
    {
        return $this->findCompleted()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findFailed(): Collection
    {
        return $this->model->failed()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findFailedDTO(): Collection
    {
        return $this->findFailed()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->byAmountRange($minAmount, $maxAmount)
            ->orderBy('amount', 'desc')
            ->get();
    }

    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return $this->findByAmountRange($minAmount, $maxAmount)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findByCurrency(string $currency): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->byCurrency($currency)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCurrencyDTO(string $currency): Collection
    {
        return $this->findByCurrency($currency)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findUnreconciled(): Collection
    {
        return $this->model->unreconciled()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findUnreconciledDTO(): Collection
    {
        return $this->findUnreconciled()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function findReconciled(): Collection
    {
        return $this->model->reconciled()->with(['provider', 'invoice', 'processor'])->get();
    }

    public function findReconciledDTO(): Collection
    {
        return $this->findReconciled()->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function create(array $data): ProviderPayment
    {
        try {
            DB::beginTransaction();

            $payment = $this->model->create($data);

            // Clear related caches
            $this->clearProviderPaymentCaches($payment->provider_id);

            DB::commit();

            Log::info('Provider payment created', ['payment_id' => $payment->id, 'provider_id' => $payment->provider_id]);

            return $payment->load(['provider', 'invoice', 'processor']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider payment', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderPaymentDTO
    {
        $payment = $this->create($data);

        return ProviderPaymentDTO::fromModel($payment);
    }

    public function update(ProviderPayment $payment, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $payment->update($data);

            if ($updated) {
                // Clear related caches
                $this->clearProviderPaymentCaches($payment->provider_id);

                Log::info('Provider payment updated', ['payment_id' => $payment->id, 'provider_id' => $payment->provider_id]);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider payment', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderPayment $payment, array $data): ?ProviderPaymentDTO
    {
        $updated = $this->update($payment, $data);

        return $updated ? ProviderPaymentDTO::fromModel($payment->fresh()) : null;
    }

    public function delete(ProviderPayment $payment): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $payment->delete();

            if ($deleted) {
                // Clear related caches
                $this->clearProviderPaymentCaches($payment->provider_id);

                Log::info('Provider payment deleted', ['payment_id' => $payment->id, 'provider_id' => $payment->provider_id]);
            }

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider payment', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);
            throw $e;
        }
    }

    public function process(ProviderPayment $payment, int $processedBy): bool
    {
        if (! $payment->canBeProcessed()) {
            Log::warning('Cannot process payment', ['payment_id' => $payment->id, 'status' => $payment->status]);

            return false;
        }

        $data = [
            'status' => ProviderPaymentStatus::PROCESSED,
            'processed_by' => $processedBy,
            'processed_at' => now(),
        ];

        return $this->update($payment, $data);
    }

    public function complete(ProviderPayment $payment): bool
    {
        if (! $payment->canBeCompleted()) {
            Log::warning('Cannot complete payment', ['payment_id' => $payment->id, 'status' => $payment->status]);

            return false;
        }

        $data = [
            'status' => ProviderPaymentStatus::COMPLETED,
        ];

        return $this->update($payment, $data);
    }

    public function fail(ProviderPayment $payment, ?string $reason = null): bool
    {
        $data = [
            'status' => ProviderPaymentStatus::FAILED,
            'notes' => $reason ? ($payment->notes ? $payment->notes."\n".$reason : $reason) : $payment->notes,
        ];

        return $this->update($payment, $data);
    }

    public function cancel(ProviderPayment $payment, ?string $reason = null): bool
    {
        $data = [
            'status' => ProviderPaymentStatus::CANCELLED,
            'notes' => $reason ? ($payment->notes ? $payment->notes."\n".$reason : $reason) : $payment->notes,
        ];

        return $this->update($payment, $data);
    }

    public function refund(ProviderPayment $payment, float $refundAmount, ?string $reason = null): bool
    {
        $data = [
            'status' => ProviderPaymentStatus::REFUNDED,
            'amount' => $refundAmount,
            'notes' => $reason ? ($payment->notes ? $payment->notes."\n".$reason : $reason) : $payment->notes,
        ];

        return $this->update($payment, $data);
    }

    public function reconcile(ProviderPayment $payment, ?string $reconciliationNotes = null): bool
    {
        if (! $payment->canBeReconciled()) {
            Log::warning('Cannot reconcile payment', ['payment_id' => $payment->id, 'status' => $payment->status]);

            return false;
        }

        $data = [
            'reconciled_at' => now(),
            'reconciliation_notes' => $reconciliationNotes,
        ];

        return $this->update($payment, $data);
    }

    public function updateAmount(ProviderPayment $payment, float $newAmount): bool
    {
        if ($newAmount <= 0) {
            Log::warning('Invalid amount for payment update', ['payment_id' => $payment->id, 'amount' => $newAmount]);

            return false;
        }

        $data = ['amount' => $newAmount];

        return $this->update($payment, $data);
    }

    public function getProviderPaymentCount(int $providerId): int
    {
        return Cache::remember("provider_payment_count_{$providerId}", 1800, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)->count();
        });
    }

    public function getProviderPaymentCountByStatus(int $providerId, string $status): int
    {
        return Cache::remember("provider_payment_count_status_{$providerId}_{$status}", 1800, function () use ($providerId, $status) {
            return $this->model->where('provider_id', $providerId)->where('status', $status)->count();
        });
    }

    public function getProviderPaymentCountByMethod(int $providerId, string $paymentMethod): int
    {
        return Cache::remember("provider_payment_count_method_{$providerId}_{$paymentMethod}", 1800, function () use ($providerId, $paymentMethod) {
            return $this->model->where('provider_id', $providerId)->where('payment_method', $paymentMethod)->count();
        });
    }

    public function getProviderTotalPaid(int $providerId, ?string $startDate = null, ?string $endDate = null): float
    {
        $cacheKey = "provider_total_paid_{$providerId}_".($startDate ?? 'all').'_'.($endDate ?? 'all');

        return Cache::remember($cacheKey, 1800, function () use ($providerId, $startDate, $endDate) {
            $query = $this->model->where('provider_id', $providerId)->where('status', ProviderPaymentStatus::COMPLETED);

            if ($startDate && $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate]);
            }

            return $query->sum('amount');
        });
    }

    public function getProviderTotalPending(int $providerId): float
    {
        return Cache::remember("provider_total_pending_{$providerId}", 1800, function () use ($providerId) {
            return $this->model
                ->where('provider_id', $providerId)
                ->where('status', ProviderPaymentStatus::PENDING)
                ->sum('amount');
        });
    }

    public function getProviderTotalFailed(int $providerId): float
    {
        return Cache::remember("provider_total_failed_{$providerId}", 1800, function () use ($providerId) {
            return $this->model
                ->where('provider_id', $providerId)
                ->where('status', ProviderPaymentStatus::FAILED)
                ->sum('amount');
        });
    }

    public function getProviderAveragePaymentAmount(int $providerId): float
    {
        return Cache::remember("provider_avg_payment_{$providerId}", 1800, function () use ($providerId) {
            return $this->model
                ->where('provider_id', $providerId)
                ->where('status', ProviderPaymentStatus::COMPLETED)
                ->avg('amount') ?? 0.0;
        });
    }

    public function getProviderPaymentHistory(int $providerId): Collection
    {
        return Cache::remember("provider_payment_history_{$providerId}", 1800, function () use ($providerId) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('provider_id', $providerId)
                ->orderBy('payment_date', 'desc')
                ->get();
        });
    }

    public function getProviderPaymentHistoryDTO(int $providerId): Collection
    {
        return $this->getProviderPaymentHistory($providerId)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function getInvoicePaymentHistory(int $invoiceId): Collection
    {
        return Cache::remember("invoice_payment_history_{$invoiceId}", 1800, function () use ($invoiceId) {
            return $this->model
                ->with(['provider', 'invoice', 'processor'])
                ->where('invoice_id', $invoiceId)
                ->orderBy('payment_date', 'desc')
                ->get();
        });
    }

    public function getInvoicePaymentHistoryDTO(int $invoiceId): Collection
    {
        return $this->getInvoicePaymentHistory($invoiceId)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function getTotalPaymentCount(): int
    {
        return Cache::remember('total_payment_count', 1800, function () {
            return $this->model->count();
        });
    }

    public function getTotalPaymentCountByStatus(string $status): int
    {
        return Cache::remember("total_payment_count_status_{$status}", 1800, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    public function getTotalPaymentCountByMethod(string $paymentMethod): int
    {
        return Cache::remember("total_payment_count_method_{$paymentMethod}", 1800, function () use ($paymentMethod) {
            return $this->model->where('payment_method', $paymentMethod)->count();
        });
    }

    public function getTotalPaidAmount(?string $startDate = null, ?string $endDate = null): float
    {
        $cacheKey = 'total_paid_amount_'.($startDate ?? 'all').'_'.($endDate ?? 'all');

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            $query = $this->model->where('status', ProviderPaymentStatus::COMPLETED);

            if ($startDate && $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate]);
            }

            return $query->sum('amount');
        });
    }

    public function getTotalPendingAmount(): float
    {
        return Cache::remember('total_pending_amount', 1800, function () {
            return $this->model->where('status', ProviderPaymentStatus::PENDING)->sum('amount');
        });
    }

    public function getTotalFailedAmount(): float
    {
        return Cache::remember('total_failed_amount', 1800, function () {
            return $this->model->where('status', ProviderPaymentStatus::FAILED)->sum('amount');
        });
    }

    public function getAveragePaymentAmount(): float
    {
        return Cache::remember('average_payment_amount', 1800, function () {
            return $this->model->where('status', ProviderPaymentStatus::COMPLETED)->avg('amount') ?? 0.0;
        });
    }

    public function getPendingPaymentCount(): int
    {
        return $this->getTotalPaymentCountByStatus(ProviderPaymentStatus::PENDING->value);
    }

    public function getCompletedPaymentCount(): int
    {
        return $this->getTotalPaymentCountByStatus(ProviderPaymentStatus::COMPLETED->value);
    }

    public function getFailedPaymentCount(): int
    {
        return $this->getTotalPaymentCountByStatus(ProviderPaymentStatus::FAILED->value);
    }

    public function getUnreconciledPaymentCount(): int
    {
        return Cache::remember('unreconciled_payment_count', 1800, function () {
            return $this->model->whereNull('reconciled_at')->count();
        });
    }

    public function searchPayments(string $query): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where(function ($q) use ($query) {
                $q->where('payment_number', 'like', "%{$query}%")
                    ->orWhere('reference_number', 'like', "%{$query}%")
                    ->orWhere('transaction_id', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%")
                    ->orWhereHas('provider', function ($providerQuery) use ($query) {
                        $providerQuery->where('company_name', 'like', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchPaymentsDTO(string $query): Collection
    {
        return $this->searchPayments($query)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function searchPaymentsByProvider(int $providerId, string $query): Collection
    {
        return $this->model
            ->with(['provider', 'invoice', 'processor'])
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('payment_number', 'like', "%{$query}%")
                    ->orWhere('reference_number', 'like', "%{$query}%")
                    ->orWhere('transaction_id', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchPaymentsByProviderDTO(int $providerId, string $query): Collection
    {
        return $this->searchPaymentsByProvider($providerId, $query)->map(function ($payment) {
            return ProviderPaymentDTO::fromModel($payment);
        });
    }

    public function exportPaymentData(array $filters = []): string
    {
        // Implementation for exporting payment data
        // This would typically generate CSV, Excel, or JSON format
        return json_encode($this->getPaymentStatistics());
    }

    public function importPaymentData(string $data): bool
    {
        // Implementation for importing payment data
        // This would typically parse CSV, Excel, or JSON format
        try {
            $importData = json_decode($data, true);

            // Process import data and create payments
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to import payment data', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function getPaymentStatistics(): array
    {
        return Cache::remember('payment_statistics', 3600, function () {
            return [
                'total_count' => $this->getTotalPaymentCount(),
                'total_amount' => $this->getTotalPaidAmount(),
                'pending_count' => $this->getPendingPaymentCount(),
                'pending_amount' => $this->getTotalPendingAmount(),
                'completed_count' => $this->getCompletedPaymentCount(),
                'failed_count' => $this->getFailedPaymentCount(),
                'failed_amount' => $this->getTotalFailedAmount(),
                'unreconciled_count' => $this->getUnreconciledPaymentCount(),
                'average_amount' => $this->getAveragePaymentAmount(),
                'by_status' => $this->getPaymentCountsByStatus(),
                'by_method' => $this->getPaymentCountsByMethod(),
            ];
        });
    }

    public function getProviderPaymentStatistics(int $providerId): array
    {
        return Cache::remember("provider_payment_statistics_{$providerId}", 3600, function () use ($providerId) {
            return [
                'total_count' => $this->getProviderPaymentCount($providerId),
                'total_amount' => $this->getProviderTotalPaid($providerId),
                'pending_amount' => $this->getProviderTotalPending($providerId),
                'failed_amount' => $this->getProviderTotalFailed($providerId),
                'average_amount' => $this->getProviderAveragePaymentAmount($providerId),
                'by_status' => $this->getProviderPaymentCountsByStatus($providerId),
                'by_method' => $this->getProviderPaymentCountsByMethod($providerId),
            ];
        });
    }

    public function getPaymentTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = 'payment_trends_'.($startDate ?? 'all').'_'.($endDate ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($startDate, $endDate) {
            $query = $this->model->selectRaw('DATE(payment_date) as date, COUNT(*) as count, SUM(amount) as total_amount')
                ->where('status', ProviderPaymentStatus::COMPLETED)
                ->groupBy('date')
                ->orderBy('date');

            if ($startDate && $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate]);
            }

            return $query->get()->toArray();
        });
    }

    public function generatePaymentNumber(): string
    {
        do {
            $paymentNumber = ProviderPayment::generatePaymentNumber();
        } while (! $this->isPaymentNumberUnique($paymentNumber));

        return $paymentNumber;
    }

    public function isPaymentNumberUnique(string $paymentNumber): bool
    {
        return ProviderPayment::isPaymentNumberUnique($paymentNumber);
    }

    public function calculatePaymentTotals(int $paymentId): array
    {
        $payment = $this->find($paymentId);
        if (! $payment) {
            return [];
        }

        return [
            'payment_id' => $paymentId,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'formatted_amount' => $payment->formatted_amount,
            'status' => $payment->status->value,
            'status_label' => $payment->status_label,
            'can_be_edited' => $payment->canBeEdited(),
            'can_be_processed' => $payment->canBeProcessed(),
            'can_be_completed' => $payment->canBeCompleted(),
            'can_be_reconciled' => $payment->canBeReconciled(),
        ];
    }

    /**
     * Get payment counts by status for all payments.
     */
    protected function getPaymentCountsByStatus(): array
    {
        return Cache::remember('payment_counts_by_status', 3600, function () {
            $counts = [];
            foreach (ProviderPaymentStatus::cases() as $status) {
                $counts[$status->value] = $this->getTotalPaymentCountByStatus($status->value);
            }

            return $counts;
        });
    }

    /**
     * Get payment counts by method for all payments.
     */
    protected function getPaymentCountsByMethod(): array
    {
        return Cache::remember('payment_counts_by_method', 3600, function () {
            $counts = [];
            foreach (ProviderPaymentMethod::cases() as $method) {
                $counts[$method->value] = $this->getTotalPaymentCountByMethod($method->value);
            }

            return $counts;
        });
    }

    /**
     * Get provider payment counts by status.
     */
    protected function getProviderPaymentCountsByStatus(int $providerId): array
    {
        $counts = [];
        foreach (ProviderPaymentStatus::cases() as $status) {
            $counts[$status->value] = $this->getProviderPaymentCountByStatus($providerId, $status->value);
        }

        return $counts;
    }

    /**
     * Get provider payment counts by method.
     */
    protected function getProviderPaymentCountsByMethod(int $providerId): array
    {
        $counts = [];
        foreach (ProviderPaymentMethod::cases() as $method) {
            $counts[$method->value] = $this->getProviderPaymentCountByMethod($providerId, $method->value);
        }

        return $counts;
    }

    /**
     * Clear provider payment related caches.
     */
    protected function clearProviderPaymentCaches(int $providerId): void
    {
        Cache::forget('provider_payments_all');
        Cache::forget("provider_payments_provider_{$providerId}");
        Cache::forget("provider_payment_count_{$providerId}");
        Cache::forget("provider_total_paid_{$providerId}_all_all");
        Cache::forget("provider_total_pending_{$providerId}");
        Cache::forget("provider_total_failed_{$providerId}");
        Cache::forget("provider_avg_payment_{$providerId}");
        Cache::forget("provider_payment_history_{$providerId}");
        Cache::forget("provider_payment_statistics_{$providerId}");
        Cache::forget('payment_statistics');
        Cache::forget('total_payment_count');
        Cache::forget('total_paid_amount_all_all');
        Cache::forget('total_pending_amount');
        Cache::forget('total_failed_amount');
        Cache::forget('average_payment_amount');
        Cache::forget('unreconciled_payment_count');
        Cache::forget('payment_counts_by_status');
        Cache::forget('payment_counts_by_method');
        Cache::forget('payment_trends_all_all');
    }
}
