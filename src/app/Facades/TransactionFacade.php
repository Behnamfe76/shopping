<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\TransactionDTO;
use Fereydooni\Shopping\app\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static Collection allDTO()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static Transaction|null find(int $id)
 * @method static TransactionDTO|null findDTO(int $id)
 * @method static Collection findByOrderId(int $orderId)
 * @method static Collection findByOrderIdDTO(int $orderId)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByUserIdDTO(int $userId)
 * @method static Collection findByGateway(string $gateway)
 * @method static Collection findByGatewayDTO(string $gateway)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Transaction|null findByTransactionId(string $transactionId)
 * @method static TransactionDTO|null findByTransactionIdDTO(string $transactionId)
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection findByAmountRange(float $minAmount, float $maxAmount)
 * @method static Collection findByAmountRangeDTO(float $minAmount, float $maxAmount)
 * @method static Collection findByCurrency(string $currency)
 * @method static Collection findByCurrencyDTO(string $currency)
 * @method static Transaction create(array $data)
 * @method static TransactionDTO createAndReturnDTO(array $data)
 * @method static bool update(Transaction $transaction, array $data)
 * @method static TransactionDTO|null updateAndReturnDTO(Transaction $transaction, array $data)
 * @method static bool delete(Transaction $transaction)
 * @method static bool markAsSuccess(Transaction $transaction, array $responseData = [])
 * @method static TransactionDTO|null markAsSuccessAndReturnDTO(Transaction $transaction, array $responseData = [])
 * @method static bool markAsFailed(Transaction $transaction, array $responseData = [])
 * @method static TransactionDTO|null markAsFailedAndReturnDTO(Transaction $transaction, array $responseData = [])
 * @method static bool markAsRefunded(Transaction $transaction, array $responseData = [])
 * @method static TransactionDTO|null markAsRefundedAndReturnDTO(Transaction $transaction, array $responseData = [])
 * @method static int getTransactionCount()
 * @method static int getTransactionCountByStatus(string $status)
 * @method static int getTransactionCountByUserId(int $userId)
 * @method static int getTransactionCountByGateway(string $gateway)
 * @method static float getTotalAmount()
 * @method static float getTotalAmountByStatus(string $status)
 * @method static float getTotalAmountByUserId(int $userId)
 * @method static float getTotalAmountByGateway(string $gateway)
 * @method static float getTotalAmountByDateRange(string $startDate, string $endDate)
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection getSuccessfulTransactions()
 * @method static Collection getSuccessfulTransactionsDTO()
 * @method static Collection getFailedTransactions()
 * @method static Collection getFailedTransactionsDTO()
 * @method static Collection getRefundedTransactions()
 * @method static Collection getRefundedTransactionsDTO()
 * @method static Collection getPendingTransactions()
 * @method static Collection getPendingTransactionsDTO()
 * @method static Collection getRecentTransactions(int $limit = 10)
 * @method static Collection getRecentTransactionsDTO(int $limit = 10)
 * @method static Collection getTransactionsByGateway(string $gateway)
 * @method static Collection getTransactionsByGatewayDTO(string $gateway)
 * @method static bool validateTransaction(array $data)
 * @method static bool checkTransactionIdExists(string $transactionId)
 * @method static array getTransactionStatistics()
 * @method static array getTransactionStatisticsByDateRange(string $startDate, string $endDate)
 * @method static array getTransactionStatisticsByGateway(string $gateway)
 * @method static array getTransactionStatisticsByStatus(string $status)
 *
 * @see \Fereydooni\Shopping\app\Services\TransactionService
 */
class TransactionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.transaction';
    }
}
