<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\TransactionDTO;
use Fereydooni\Shopping\app\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface TransactionRepositoryInterface
{
    /**
     * Get all transactions
     */
    public function all(): Collection;

    /**
     * Get paginated transactions (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated transactions
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated transactions
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find transaction by ID
     */
    public function find(int $id): ?Transaction;

    /**
     * Find transaction by ID and return DTO
     */
    public function findDTO(int $id): ?TransactionDTO;

    /**
     * Find transactions by order ID
     */
    public function findByOrderId(int $orderId): Collection;

    /**
     * Find transactions by order ID and return DTOs
     */
    public function findByOrderIdDTO(int $orderId): Collection;

    /**
     * Find transactions by user ID
     */
    public function findByUserId(int $userId): Collection;

    /**
     * Find transactions by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection;

    /**
     * Find transactions by gateway
     */
    public function findByGateway(string $gateway): Collection;

    /**
     * Find transactions by gateway and return DTOs
     */
    public function findByGatewayDTO(string $gateway): Collection;

    /**
     * Find transactions by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find transactions by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find transaction by transaction ID
     */
    public function findByTransactionId(string $transactionId): ?Transaction;

    /**
     * Find transaction by transaction ID and return DTO
     */
    public function findByTransactionIdDTO(string $transactionId): ?TransactionDTO;

    /**
     * Find transactions by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find transactions by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find transactions by amount range
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection;

    /**
     * Find transactions by amount range and return DTOs
     */
    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection;

    /**
     * Find transactions by currency
     */
    public function findByCurrency(string $currency): Collection;

    /**
     * Find transactions by currency and return DTOs
     */
    public function findByCurrencyDTO(string $currency): Collection;

    /**
     * Create a new transaction
     */
    public function create(array $data): Transaction;

    /**
     * Create a new transaction and return DTO
     */
    public function createAndReturnDTO(array $data): TransactionDTO;

    /**
     * Update transaction
     */
    public function update(Transaction $transaction, array $data): bool;

    /**
     * Update transaction and return DTO
     */
    public function updateAndReturnDTO(Transaction $transaction, array $data): ?TransactionDTO;

    /**
     * Delete transaction
     */
    public function delete(Transaction $transaction): bool;

    /**
     * Mark transaction as success
     */
    public function markAsSuccess(Transaction $transaction, array $responseData = []): bool;

    /**
     * Mark transaction as success and return DTO
     */
    public function markAsSuccessAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO;

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(Transaction $transaction, array $responseData = []): bool;

    /**
     * Mark transaction as failed and return DTO
     */
    public function markAsFailedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO;

    /**
     * Mark transaction as refunded
     */
    public function markAsRefunded(Transaction $transaction, array $responseData = []): bool;

    /**
     * Mark transaction as refunded and return DTO
     */
    public function markAsRefundedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO;

    /**
     * Get total transaction count
     */
    public function getTransactionCount(): int;

    /**
     * Get transaction count by status
     */
    public function getTransactionCountByStatus(string $status): int;

    /**
     * Get transaction count by user ID
     */
    public function getTransactionCountByUserId(int $userId): int;

    /**
     * Get transaction count by gateway
     */
    public function getTransactionCountByGateway(string $gateway): int;

    /**
     * Get total amount of all transactions
     */
    public function getTotalAmount(): float;

    /**
     * Get total amount by status
     */
    public function getTotalAmountByStatus(string $status): float;

    /**
     * Get total amount by user ID
     */
    public function getTotalAmountByUserId(int $userId): float;

    /**
     * Get total amount by gateway
     */
    public function getTotalAmountByGateway(string $gateway): float;

    /**
     * Get total amount by date range
     */
    public function getTotalAmountByDateRange(string $startDate, string $endDate): float;

    /**
     * Search transactions
     */
    public function search(string $query): Collection;

    /**
     * Search transactions and return DTOs
     */
    public function searchDTO(string $query): Collection;

    /**
     * Get successful transactions
     */
    public function getSuccessfulTransactions(): Collection;

    /**
     * Get successful transactions as DTOs
     */
    public function getSuccessfulTransactionsDTO(): Collection;

    /**
     * Get failed transactions
     */
    public function getFailedTransactions(): Collection;

    /**
     * Get failed transactions as DTOs
     */
    public function getFailedTransactionsDTO(): Collection;

    /**
     * Get refunded transactions
     */
    public function getRefundedTransactions(): Collection;

    /**
     * Get refunded transactions as DTOs
     */
    public function getRefundedTransactionsDTO(): Collection;

    /**
     * Get pending transactions
     */
    public function getPendingTransactions(): Collection;

    /**
     * Get pending transactions as DTOs
     */
    public function getPendingTransactionsDTO(): Collection;

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10): Collection;

    /**
     * Get recent transactions as DTOs
     */
    public function getRecentTransactionsDTO(int $limit = 10): Collection;

    /**
     * Get transactions by gateway
     */
    public function getTransactionsByGateway(string $gateway): Collection;

    /**
     * Get transactions by gateway as DTOs
     */
    public function getTransactionsByGatewayDTO(string $gateway): Collection;

    /**
     * Validate transaction data
     */
    public function validateTransaction(array $data): bool;

    /**
     * Check if transaction ID exists
     */
    public function checkTransactionIdExists(string $transactionId): bool;

    /**
     * Get transaction statistics
     */
    public function getTransactionStatistics(): array;

    /**
     * Get transaction statistics by date range
     */
    public function getTransactionStatisticsByDateRange(string $startDate, string $endDate): array;

    /**
     * Get transaction statistics by gateway
     */
    public function getTransactionStatisticsByGateway(string $gateway): array;

    /**
     * Get transaction statistics by status
     */
    public function getTransactionStatisticsByStatus(string $status): array;
}
