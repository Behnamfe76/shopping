<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\UserSubscriptionService;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\DTOs\UserSubscriptionDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static UserSubscription|null find(int $id)
 * @method static UserSubscriptionDTO|null findDTO(int $id)
 * @method static UserSubscription create(array $data)
 * @method static UserSubscriptionDTO createAndReturnDTO(array $data)
 * @method static bool update(UserSubscription $userSubscription, array $data)
 * @method static UserSubscriptionDTO|null updateAndReturnDTO(UserSubscription $userSubscription, array $data)
 * @method static bool delete(UserSubscription $userSubscription)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByUserIdDTO(int $userId)
 * @method static Collection findBySubscriptionId(int $subscriptionId)
 * @method static Collection findBySubscriptionIdDTO(int $subscriptionId)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static UserSubscription|null findByUserAndSubscription(int $userId, int $subscriptionId)
 * @method static UserSubscriptionDTO|null findByUserAndSubscriptionDTO(int $userId, int $subscriptionId)
 * @method static Collection findActiveByUserId(int $userId)
 * @method static Collection findActiveByUserIdDTO(int $userId)
 * @method static Collection findExpiredByUserId(int $userId)
 * @method static Collection findExpiredByUserIdDTO(int $userId)
 * @method static Collection findTrialByUserId(int $userId)
 * @method static Collection findTrialByUserIdDTO(int $userId)
 * @method static Collection findCancelledByUserId(int $userId)
 * @method static Collection findCancelledByUserIdDTO(int $userId)
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection findByNextBillingDate(string $date)
 * @method static Collection findByNextBillingDateDTO(string $date)
 * @method static bool activate(UserSubscription $userSubscription)
 * @method static UserSubscriptionDTO|null activateAndReturnDTO(UserSubscription $userSubscription)
 * @method static bool cancel(UserSubscription $userSubscription, string $reason = null)
 * @method static UserSubscriptionDTO|null cancelAndReturnDTO(UserSubscription $userSubscription, string $reason = null)
 * @method static bool expire(UserSubscription $userSubscription)
 * @method static UserSubscriptionDTO|null expireAndReturnDTO(UserSubscription $userSubscription)
 * @method static bool renew(UserSubscription $userSubscription)
 * @method static UserSubscriptionDTO|null renewAndReturnDTO(UserSubscription $userSubscription)
 * @method static bool pause(UserSubscription $userSubscription, string $reason = null)
 * @method static UserSubscriptionDTO|null pauseAndReturnDTO(UserSubscription $userSubscription, string $reason = null)
 * @method static bool resume(UserSubscription $userSubscription)
 * @method static UserSubscriptionDTO|null resumeAndReturnDTO(UserSubscription $userSubscription)
 * @method static int getUserSubscriptionCount(int $userId)
 * @method static int getUserSubscriptionCountByStatus(int $userId, string $status)
 * @method static int getTotalActiveSubscriptions()
 * @method static int getTotalTrialSubscriptions()
 * @method static int getTotalExpiredSubscriptions()
 * @method static int getTotalCancelledSubscriptions()
 * @method static int getTotalPausedSubscriptions()
 * @method static Collection search(int $userId, string $query)
 * @method static Collection searchDTO(int $userId, string $query)
 * @method static Collection getUpcomingRenewals(int $days = 7)
 * @method static Collection getUpcomingRenewalsDTO(int $days = 7)
 * @method static Collection getExpiringTrials(int $days = 3)
 * @method static Collection getExpiringTrialsDTO(int $days = 3)
 * @method static Collection getExpiringSubscriptions(int $days = 30)
 * @method static Collection getExpiringSubscriptionsDTO(int $days = 30)
 * @method static bool validateUserSubscription(array $data)
 * @method static string calculateNextBillingDate(UserSubscription $userSubscription)
 * @method static bool checkSubscriptionAvailability(int $subscriptionId)
 * @method static float getUserSubscriptionRevenue(int $userId)
 * @method static float getUserSubscriptionRevenueByDateRange(int $userId, string $startDate, string $endDate)
 * @method static float getTotalRevenue()
 * @method static float getTotalRevenueByDateRange(string $startDate, string $endDate)
 * @method static array getUserSubscriptionStatistics(int $userId)
 * @method static array getGlobalSubscriptionStatistics()
 * @method static array getSubscriptionAnalytics()
 * @method static float getChurnRate()
 * @method static float getRetentionRate()
 * @method static UserSubscriptionDTO createUserSubscription(array $data)
 * @method static UserSubscriptionDTO|null updateUserSubscription(int $id, array $data)
 * @method static bool deleteUserSubscription(int $id)
 * @method static Collection getUserSubscriptions(int $userId)
 * @method static Collection getUserActiveSubscriptions(int $userId)
 * @method static Collection getUserTrialSubscriptions(int $userId)
 * @method static Collection getUserExpiredSubscriptions(int $userId)
 * @method static Collection getUserCancelledSubscriptions(int $userId)
 * @method static Collection searchUserSubscriptions(int $userId, string $query)
 * @method static UserSubscriptionDTO|null activateUserSubscription(int $id)
 * @method static UserSubscriptionDTO|null cancelUserSubscription(int $id, string $reason = null)
 * @method static UserSubscriptionDTO|null renewUserSubscription(int $id)
 * @method static UserSubscriptionDTO|null pauseUserSubscription(int $id, string $reason = null)
 * @method static UserSubscriptionDTO|null resumeUserSubscription(int $id)
 * @method static array getUserSubscriptionDashboard(int $userId)
 * @method static array getAdminDashboard()
 */
class UserSubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.user-subscription.facade';
    }
}
