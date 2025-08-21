<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\LoyaltyTransaction|null find(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO|null findDTO(int $id)
 * @method static \Fereydooni\Shopping\app\Models\LoyaltyTransaction create(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO createAndReturnDTO(array $data)
 * @method static bool update(\Fereydooni\Shopping\app\Models\LoyaltyTransaction $transaction, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO|null updateAndReturnDTO(\Fereydooni\Shopping\app\Models\LoyaltyTransaction $transaction, array $data)
 * @method static bool delete(\Fereydooni\Shopping\app\Models\LoyaltyTransaction $transaction)
 * 
 * @method static \Illuminate\Database\Eloquent\Collection findByCustomerId(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByCustomerIdDTO(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByUserId(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection findByUserIdDTO(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection findByType(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type)
 * @method static \Illuminate\Database\Eloquent\Collection findByTypeDTO(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatus(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatusDTO(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceType(\Fereydooni\Shopping\app\Enums\LoyaltyReferenceType $referenceType)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceTypeDTO(\Fereydooni\Shopping\app\Enums\LoyaltyReferenceType $referenceType)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceId(int $referenceId)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceIdDTO(int $referenceId)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRange(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByPointsRange(int $minPoints, int $maxPoints)
 * @method static \Illuminate\Database\Eloquent\Collection findByPointsRangeDTO(int $minPoints, int $maxPoints)
 * 
 * @method static \Illuminate\Database\Eloquent\Collection findExpiringTransactions(string $date)
 * @method static \Illuminate\Database\Eloquent\Collection findExpiringTransactionsDTO(string $date)
 * @method static \Illuminate\Database\Eloquent\Collection findReversedTransactions()
 * @method static \Illuminate\Database\Eloquent\Collection findReversedTransactionsDTO()
 * 
 * @method static bool reverse(\Fereydooni\Shopping\app\Models\LoyaltyTransaction $transaction, string $reason = null)
 * @method static \Fereydooni\Shopping\app\Models\LoyaltyTransaction addPoints(int $customerId, int $points, string $reason = null, array $metadata = [])
 * @method static \Fereydooni\Shopping\app\Models\LoyaltyTransaction deductPoints(int $customerId, int $points, string $reason = null, array $metadata = [])
 * 
 * @method static int calculateBalance(int $customerId)
 * @method static float calculateBalanceValue(int $customerId)
 * @method static int checkExpiration(int $customerId)
 * @method static string calculateTier(int $customerId)
 * @method static bool validateTransaction(array $data)
 * 
 * @method static int getTransactionCount()
 * @method static int getTransactionCountByCustomer(int $customerId)
 * @method static int getTransactionCountByType(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type)
 * @method static int getTransactionCountByStatus(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus $status)
 * @method static int getTransactionCountByReferenceType(\Fereydooni\Shopping\app\Enums\LoyaltyReferenceType $referenceType)
 * @method static int getTotalPointsIssued()
 * @method static int getTotalPointsRedeemed()
 * @method static int getTotalPointsExpired()
 * @method static int getTotalPointsReversed()
 * @method static float getAveragePointsPerTransaction()
 * @method static float getAveragePointsPerCustomer()
 * 
 * @method static \Illuminate\Database\Eloquent\Collection search(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchByCustomer(int $customerId, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchByCustomerDTO(int $customerId, string $query)
 * 
 * @method static \Illuminate\Database\Eloquent\Collection getRecentTransactions(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getRecentTransactionsDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getRecentTransactionsByCustomer(int $customerId, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getRecentTransactionsByCustomerDTO(int $customerId, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTransactionsByType(int $customerId, \Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTransactionsByTypeDTO(int $customerId, \Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTransactionsByStatus(int $customerId, \Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus $status, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTransactionsByStatusDTO(int $customerId, \Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus $status, int $limit = 10)
 * 
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerTransactionHistory(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerTransactionHistoryDTO(int $customerId)
 * @method static array getCustomerTransactionSummary(int $customerId)
 * @method static array getCustomerTransactionSummaryDTO(int $customerId)
 * 
 * @method static array exportCustomerHistory(int $customerId)
 * @method static bool importCustomerHistory(int $customerId, array $transactions)
 * 
 * @method static array getTransactionAnalytics(int $customerId)
 * @method static array getTransactionAnalyticsByType(\Fereydooni\Shopping\app\Enums\LoyaltyTransactionType $type)
 * @method static array getTransactionAnalyticsByDateRange(string $startDate, string $endDate)
 * @method static array getTransactionRecommendations(int $customerId)
 * @method static array getTransactionInsights(int $customerId)
 * @method static array getTransactionTrends(int $customerId, string $period = 'monthly')
 * @method static array getTransactionComparison(int $customerId1, int $customerId2)
 * @method static array getTransactionForecast(int $customerId)
 * 
 * @method static float calculatePointsValue(int $points)
 * @method static array generateRecommendations()
 * @method static array calculateInsights()
 * @method static array forecastTrends(string $period = 'monthly')
 * 
 * @see \Fereydooni\Shopping\app\Services\LoyaltyTransactionService
 */
class LoyaltyTransaction extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'loyalty-transaction';
    }
}
