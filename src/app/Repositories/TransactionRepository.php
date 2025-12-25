<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\TransactionDTO;
use Fereydooni\Shopping\app\Enums\TransactionStatus;
use Fereydooni\Shopping\app\Models\Transaction;
use Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function all(): Collection
    {
        return Transaction::with(['order', 'user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Transaction::with(['order', 'user'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return Transaction::with(['order', 'user'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return Transaction::with(['order', 'user'])->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Transaction
    {
        return Transaction::with(['order', 'user'])->find($id);
    }

    public function findDTO(int $id): ?TransactionDTO
    {
        $transaction = $this->find($id);

        return $transaction ? TransactionDTO::fromModel($transaction) : null;
    }

    public function findByOrderId(int $orderId): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->findByOrderId($orderId)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByUserId(int $userId): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByGateway(string $gateway): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('gateway', $gateway)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByGatewayDTO(string $gateway): Collection
    {
        return $this->findByGateway($gateway)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByStatus(string $status): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByTransactionId(string $transactionId): ?Transaction
    {
        return Transaction::with(['order', 'user'])
            ->where('transaction_id', $transactionId)
            ->first();
    }

    public function findByTransactionIdDTO(string $transactionId): ?TransactionDTO
    {
        $transaction = $this->findByTransactionId($transactionId);

        return $transaction ? TransactionDTO::fromModel($transaction) : null;
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return Transaction::with(['order', 'user'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return Transaction::with(['order', 'user'])
            ->whereBetween('amount', [$minAmount, $maxAmount])
            ->orderBy('amount', 'desc')
            ->get();
    }

    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return $this->findByAmountRange($minAmount, $maxAmount)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function findByCurrency(string $currency): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('currency', strtoupper($currency))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCurrencyDTO(string $currency): Collection
    {
        return $this->findByCurrency($currency)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function create(array $data): Transaction
    {
        $validated = $this->validateTransactionData($data);

        return Transaction::create($validated);
    }

    public function createAndReturnDTO(array $data): TransactionDTO
    {
        $transaction = $this->create($data);

        return TransactionDTO::fromModel($transaction);
    }

    public function update(Transaction $transaction, array $data): bool
    {
        $validated = $this->validateTransactionData($data, $transaction->id);

        return $transaction->update($validated);
    }

    public function updateAndReturnDTO(Transaction $transaction, array $data): ?TransactionDTO
    {
        $updated = $this->update($transaction, $data);

        return $updated ? TransactionDTO::fromModel($transaction->fresh()) : null;
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function markAsSuccess(Transaction $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::SUCCESS,
            'payment_date' => now(),
            'response_data' => array_merge($transaction->response_data ?? [], $responseData),
        ]);
    }

    public function markAsSuccessAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        $updated = $this->markAsSuccess($transaction, $responseData);

        return $updated ? TransactionDTO::fromModel($transaction->fresh()) : null;
    }

    public function markAsFailed(Transaction $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::FAILED,
            'response_data' => array_merge($transaction->response_data ?? [], $responseData),
        ]);
    }

    public function markAsFailedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        $updated = $this->markAsFailed($transaction, $responseData);

        return $updated ? TransactionDTO::fromModel($transaction->fresh()) : null;
    }

    public function markAsRefunded(Transaction $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::REFUNDED,
            'response_data' => array_merge($transaction->response_data ?? [], $responseData),
        ]);
    }

    public function markAsRefundedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        $updated = $this->markAsRefunded($transaction, $responseData);

        return $updated ? TransactionDTO::fromModel($transaction->fresh()) : null;
    }

    public function getTransactionCount(): int
    {
        return Transaction::count();
    }

    public function getTransactionCountByStatus(string $status): int
    {
        return Transaction::where('status', $status)->count();
    }

    public function getTransactionCountByUserId(int $userId): int
    {
        return Transaction::where('user_id', $userId)->count();
    }

    public function getTransactionCountByGateway(string $gateway): int
    {
        return Transaction::where('gateway', $gateway)->count();
    }

    public function getTotalAmount(): float
    {
        return Transaction::where('status', TransactionStatus::SUCCESS)->sum('amount');
    }

    public function getTotalAmountByStatus(string $status): float
    {
        return Transaction::where('status', $status)->sum('amount');
    }

    public function getTotalAmountByUserId(int $userId): float
    {
        return Transaction::where('user_id', $userId)
            ->where('status', TransactionStatus::SUCCESS)
            ->sum('amount');
    }

    public function getTotalAmountByGateway(string $gateway): float
    {
        return Transaction::where('gateway', $gateway)
            ->where('status', TransactionStatus::SUCCESS)
            ->sum('amount');
    }

    public function getTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        return Transaction::where('status', TransactionStatus::SUCCESS)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function search(string $query): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('transaction_id', 'LIKE', "%{$query}%")
                    ->orWhere('gateway', 'LIKE', "%{$query}%")
                    ->orWhere('currency', 'LIKE', "%{$query}%")
                    ->orWhereHas('order', function ($orderQuery) use ($query) {
                        $orderQuery->where('id', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('email', 'LIKE', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getSuccessfulTransactions(): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('status', TransactionStatus::SUCCESS)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function getSuccessfulTransactionsDTO(): Collection
    {
        return $this->getSuccessfulTransactions()->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getFailedTransactions(): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('status', TransactionStatus::FAILED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFailedTransactionsDTO(): Collection
    {
        return $this->getFailedTransactions()->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getRefundedTransactions(): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('status', TransactionStatus::REFUNDED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRefundedTransactionsDTO(): Collection
    {
        return $this->getRefundedTransactions()->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getPendingTransactions(): Collection
    {
        return Transaction::with(['order', 'user'])
            ->where('status', TransactionStatus::INITIATED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingTransactionsDTO(): Collection
    {
        return $this->getPendingTransactions()->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getRecentTransactions(int $limit = 10): Collection
    {
        return Transaction::with(['order', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentTransactionsDTO(int $limit = 10): Collection
    {
        return $this->getRecentTransactions($limit)->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getTransactionsByGateway(string $gateway): Collection
    {
        return $this->findByGateway($gateway);
    }

    public function getTransactionsByGatewayDTO(string $gateway): Collection
    {
        return $this->findByGatewayDTO($gateway);
    }

    public function validateTransaction(array $data): bool
    {
        $validator = Validator::make($data, TransactionDTO::rules(), TransactionDTO::messages());

        return ! $validator->fails();
    }

    public function checkTransactionIdExists(string $transactionId): bool
    {
        return Transaction::where('transaction_id', $transactionId)->exists();
    }

    public function getTransactionStatistics(): array
    {
        $totalTransactions = $this->getTransactionCount();
        $successfulTransactions = $this->getTransactionCountByStatus(TransactionStatus::SUCCESS->value);
        $failedTransactions = $this->getTransactionCountByStatus(TransactionStatus::FAILED->value);
        $refundedTransactions = $this->getTransactionCountByStatus(TransactionStatus::REFUNDED->value);
        $pendingTransactions = $this->getTransactionCountByStatus(TransactionStatus::INITIATED->value);
        $totalAmount = $this->getTotalAmount();

        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'failed_transactions' => $failedTransactions,
            'refunded_transactions' => $refundedTransactions,
            'pending_transactions' => $pendingTransactions,
            'success_rate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 2) : 0,
            'failure_rate' => $totalTransactions > 0 ? round(($failedTransactions / $totalTransactions) * 100, 2) : 0,
            'refund_rate' => $totalTransactions > 0 ? round(($refundedTransactions / $totalTransactions) * 100, 2) : 0,
            'total_amount' => $totalAmount,
            'average_transaction_amount' => $successfulTransactions > 0 ? round($totalAmount / $successfulTransactions, 2) : 0,
        ];
    }

    public function getTransactionStatisticsByDateRange(string $startDate, string $endDate): array
    {
        $transactions = $this->findByDateRange($startDate, $endDate);
        $totalTransactions = $transactions->count();
        $successfulTransactions = $transactions->where('status', TransactionStatus::SUCCESS)->count();
        $totalAmount = $transactions->where('status', TransactionStatus::SUCCESS)->sum('amount');

        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'success_rate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 2) : 0,
            'total_amount' => $totalAmount,
            'average_transaction_amount' => $successfulTransactions > 0 ? round($totalAmount / $successfulTransactions, 2) : 0,
            'date_range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }

    public function getTransactionStatisticsByGateway(string $gateway): array
    {
        $transactions = $this->findByGateway($gateway);
        $totalTransactions = $transactions->count();
        $successfulTransactions = $transactions->where('status', TransactionStatus::SUCCESS)->count();
        $totalAmount = $transactions->where('status', TransactionStatus::SUCCESS)->sum('amount');

        return [
            'gateway' => $gateway,
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'success_rate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 2) : 0,
            'total_amount' => $totalAmount,
            'average_transaction_amount' => $successfulTransactions > 0 ? round($totalAmount / $successfulTransactions, 2) : 0,
        ];
    }

    public function getTransactionStatisticsByStatus(string $status): array
    {
        $transactions = $this->findByStatus($status);
        $totalTransactions = $transactions->count();
        $totalAmount = $transactions->sum('amount');

        return [
            'status' => $status,
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'average_transaction_amount' => $totalTransactions > 0 ? round($totalAmount / $totalTransactions, 2) : 0,
        ];
    }

    private function validateTransactionData(array $data, ?int $excludeId = null): array
    {
        $rules = TransactionDTO::rules();

        if ($excludeId) {
            $rules['transaction_id'] = 'required|string|max:255|unique:transactions,transaction_id,'.$excludeId;
        }

        $validator = Validator::make($data, $rules, TransactionDTO::messages());

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }
}
