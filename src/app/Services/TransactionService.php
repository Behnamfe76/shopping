<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\TransactionDTO;
use Fereydooni\Shopping\app\Models\Transaction;
use Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasTransactionOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class TransactionService
{
    use HasCrudOperations, HasSearchOperations, HasTransactionOperations;

    protected TransactionRepositoryInterface $repository;

    public function __construct(TransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = Transaction::class;
        $this->dtoClass = TransactionDTO::class;
    }

    /**
     * Get all transactions
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get all transactions as DTOs
     */
    public function allDTO(): Collection
    {
        return $this->all()->map(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }

    /**
     * Get paginated transactions
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get simple paginated transactions
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated transactions
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find transaction by ID
     */
    public function find(int $id): ?Transaction
    {
        return $this->repository->find($id);
    }

    /**
     * Find transaction by ID and return DTO
     */
    public function findDTO(int $id): ?TransactionDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find transactions by order ID
     */
    public function findByOrderId(int $orderId): Collection
    {
        return $this->repository->findByOrderId($orderId);
    }

    /**
     * Find transactions by order ID and return DTOs
     */
    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->repository->findByOrderIdDTO($orderId);
    }

    /**
     * Find transactions by user ID
     */
    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Find transactions by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    /**
     * Find transactions by gateway
     */
    public function findByGateway(string $gateway): Collection
    {
        return $this->repository->findByGateway($gateway);
    }

    /**
     * Find transactions by gateway and return DTOs
     */
    public function findByGatewayDTO(string $gateway): Collection
    {
        return $this->repository->findByGatewayDTO($gateway);
    }

    /**
     * Find transactions by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Find transactions by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    /**
     * Find transaction by transaction ID
     */
    public function findByTransactionId(string $transactionId): ?Transaction
    {
        return $this->repository->findByTransactionId($transactionId);
    }

    /**
     * Find transaction by transaction ID and return DTO
     */
    public function findByTransactionIdDTO(string $transactionId): ?TransactionDTO
    {
        return $this->repository->findByTransactionIdDTO($transactionId);
    }

    /**
     * Find transactions by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    /**
     * Find transactions by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Find transactions by amount range
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->repository->findByAmountRange($minAmount, $maxAmount);
    }

    /**
     * Find transactions by amount range and return DTOs
     */
    public function findByAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return $this->repository->findByAmountRangeDTO($minAmount, $maxAmount);
    }

    /**
     * Find transactions by currency
     */
    public function findByCurrency(string $currency): Collection
    {
        return $this->repository->findByCurrency($currency);
    }

    /**
     * Find transactions by currency and return DTOs
     */
    public function findByCurrencyDTO(string $currency): Collection
    {
        return $this->repository->findByCurrencyDTO($currency);
    }

    /**
     * Create a new transaction
     */
    public function create(array $data): Transaction
    {
        return $this->repository->create($data);
    }

    /**
     * Create a new transaction and return DTO
     */
    public function createAndReturnDTO(array $data): TransactionDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Update transaction
     */
    public function update(Transaction $transaction, array $data): bool
    {
        return $this->repository->update($transaction, $data);
    }

    /**
     * Update transaction and return DTO
     */
    public function updateAndReturnDTO(Transaction $transaction, array $data): ?TransactionDTO
    {
        return $this->repository->updateAndReturnDTO($transaction, $data);
    }

    /**
     * Delete transaction
     */
    public function delete(Transaction $transaction): bool
    {
        return $this->repository->delete($transaction);
    }

    /**
     * Mark transaction as success
     */
    public function markAsSuccess(Transaction $transaction, array $responseData = []): bool
    {
        return $this->repository->markAsSuccess($transaction, $responseData);
    }

    /**
     * Mark transaction as success and return DTO
     */
    public function markAsSuccessAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        return $this->repository->markAsSuccessAndReturnDTO($transaction, $responseData);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(Transaction $transaction, array $responseData = []): bool
    {
        return $this->repository->markAsFailed($transaction, $responseData);
    }

    /**
     * Mark transaction as failed and return DTO
     */
    public function markAsFailedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        return $this->repository->markAsFailedAndReturnDTO($transaction, $responseData);
    }

    /**
     * Mark transaction as refunded
     */
    public function markAsRefunded(Transaction $transaction, array $responseData = []): bool
    {
        return $this->repository->markAsRefunded($transaction, $responseData);
    }

    /**
     * Mark transaction as refunded and return DTO
     */
    public function markAsRefundedAndReturnDTO(Transaction $transaction, array $responseData = []): ?TransactionDTO
    {
        return $this->repository->markAsRefundedAndReturnDTO($transaction, $responseData);
    }

    /**
     * Get transaction count
     */
    public function getTransactionCount(): int
    {
        return $this->repository->getTransactionCount();
    }

    /**
     * Get transaction count by status
     */
    public function getTransactionCountByStatus(string $status): int
    {
        return $this->repository->getTransactionCountByStatus($status);
    }

    /**
     * Get transaction count by user ID
     */
    public function getTransactionCountByUserId(int $userId): int
    {
        return $this->repository->getTransactionCountByUserId($userId);
    }

    /**
     * Get transaction count by gateway
     */
    public function getTransactionCountByGateway(string $gateway): int
    {
        return $this->repository->getTransactionCountByGateway($gateway);
    }

    /**
     * Get total amount
     */
    public function getTotalAmount(): float
    {
        return $this->repository->getTotalAmount();
    }

    /**
     * Get total amount by status
     */
    public function getTotalAmountByStatus(string $status): float
    {
        return $this->repository->getTotalAmountByStatus($status);
    }

    /**
     * Get total amount by user ID
     */
    public function getTotalAmountByUserId(int $userId): float
    {
        return $this->repository->getTotalAmountByUserId($userId);
    }

    /**
     * Get total amount by gateway
     */
    public function getTotalAmountByGateway(string $gateway): float
    {
        return $this->repository->getTotalAmountByGateway($gateway);
    }

    /**
     * Get total amount by date range
     */
    public function getTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getTotalAmountByDateRange($startDate, $endDate);
    }

    /**
     * Search transactions
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search transactions and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    /**
     * Get successful transactions
     */
    public function getSuccessfulTransactions(): Collection
    {
        return $this->repository->getSuccessfulTransactions();
    }

    /**
     * Get successful transactions as DTOs
     */
    public function getSuccessfulTransactionsDTO(): Collection
    {
        return $this->repository->getSuccessfulTransactionsDTO();
    }

    /**
     * Get failed transactions
     */
    public function getFailedTransactions(): Collection
    {
        return $this->repository->getFailedTransactions();
    }

    /**
     * Get failed transactions as DTOs
     */
    public function getFailedTransactionsDTO(): Collection
    {
        return $this->repository->getFailedTransactionsDTO();
    }

    /**
     * Get refunded transactions
     */
    public function getRefundedTransactions(): Collection
    {
        return $this->repository->getRefundedTransactions();
    }

    /**
     * Get refunded transactions as DTOs
     */
    public function getRefundedTransactionsDTO(): Collection
    {
        return $this->repository->getRefundedTransactionsDTO();
    }

    /**
     * Get pending transactions
     */
    public function getPendingTransactions(): Collection
    {
        return $this->repository->getPendingTransactions();
    }

    /**
     * Get pending transactions as DTOs
     */
    public function getPendingTransactionsDTO(): Collection
    {
        return $this->repository->getPendingTransactionsDTO();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10): Collection
    {
        return $this->repository->getRecentTransactions($limit);
    }

    /**
     * Get recent transactions as DTOs
     */
    public function getRecentTransactionsDTO(int $limit = 10): Collection
    {
        return $this->repository->getRecentTransactionsDTO($limit);
    }

    /**
     * Get transactions by gateway
     */
    public function getTransactionsByGateway(string $gateway): Collection
    {
        return $this->repository->getTransactionsByGateway($gateway);
    }

    /**
     * Get transactions by gateway as DTOs
     */
    public function getTransactionsByGatewayDTO(string $gateway): Collection
    {
        return $this->repository->getTransactionsByGatewayDTO($gateway);
    }

    /**
     * Validate transaction
     */
    public function validateTransaction(array $data): bool
    {
        return $this->repository->validateTransaction($data);
    }

    /**
     * Check if transaction ID exists
     */
    public function checkTransactionIdExists(string $transactionId): bool
    {
        return $this->repository->checkTransactionIdExists($transactionId);
    }

    /**
     * Get transaction statistics
     */
    public function getTransactionStatistics(): array
    {
        return $this->repository->getTransactionStatistics();
    }

    /**
     * Get transaction statistics by date range
     */
    public function getTransactionStatisticsByDateRange(string $startDate, string $endDate): array
    {
        return $this->repository->getTransactionStatisticsByDateRange($startDate, $endDate);
    }

    /**
     * Get transaction statistics by gateway
     */
    public function getTransactionStatisticsByGateway(string $gateway): array
    {
        return $this->repository->getTransactionStatisticsByGateway($gateway);
    }

    /**
     * Get transaction statistics by status
     */
    public function getTransactionStatisticsByStatus(string $status): array
    {
        return $this->repository->getTransactionStatisticsByStatus($status);
    }

    /**
     * Process gateway response
     */
    public function processGatewayResponse(Transaction $transaction, array $responseData): bool
    {
        return $this->processGatewayResponse($transaction, $responseData);
    }

    /**
     * Generate transaction ID
     */
    public function generateTransactionId(string $gateway, string $prefix = 'TXN'): string
    {
        return $this->generateTransactionId($gateway, $prefix);
    }

    /**
     * Calculate transaction fees
     */
    public function calculateTransactionFees(float $amount, string $gateway): array
    {
        return $this->calculateTransactionFees($amount, $gateway);
    }

    /**
     * Get supported gateways
     */
    public function getSupportedGateways(): array
    {
        return $this->getSupportedGateways();
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return $this->getSupportedCurrencies();
    }

    /**
     * Get transaction summary
     */
    public function getTransactionSummary(Transaction $transaction): array
    {
        return $this->getTransactionSummary($transaction);
    }
}
