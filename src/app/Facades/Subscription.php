<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\SubscriptionService;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\DTOs\SubscriptionDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static Subscription|null find(int $id)
 * @method static SubscriptionDTO|null findDTO(int $id)
 * @method static Subscription create(array $data)
 * @method static SubscriptionDTO createAndReturnDTO(array $data)
 * @method static bool update(Subscription $subscription, array $data)
 * @method static SubscriptionDTO|null updateAndReturnDTO(Subscription $subscription, array $data)
 * @method static bool delete(Subscription $subscription)
 * @method static Collection findByProductId(int $productId)
 * @method static Collection findByProductIdDTO(int $productId)
 * @method static Collection findByBillingCycle(string $billingCycle)
 * @method static Collection findByBillingCycleDTO(string $billingCycle)
 * @method static Collection findByPriceRange(float $minPrice, float $maxPrice)
 * @method static Collection findByPriceRangeDTO(float $minPrice, float $maxPrice)
 * @method static Collection findByTrialPeriod(int $trialDays)
 * @method static Collection findByTrialPeriodDTO(int $trialDays)
 * @method static int getSubscriptionCount()
 * @method static int getSubscriptionCountByProduct(int $productId)
 * @method static int getSubscriptionCountByBillingCycle(string $billingCycle)
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection getActiveSubscriptions()
 * @method static Collection getActiveSubscriptionsDTO()
 * @method static Collection getTrialSubscriptions()
 * @method static Collection getTrialSubscriptionsDTO()
 * @method static bool validateSubscription(array $data)
 * @method static string calculateNextBillingDate(Subscription $subscription, string $startDate = null)
 * @method static float getSubscriptionRevenue()
 * @method static float getSubscriptionRevenueByDateRange(string $startDate, string $endDate)
 * @method static Collection getPopularSubscriptions(int $limit = 10)
 * @method static Collection getPopularSubscriptionsDTO(int $limit = 10)
 * @method static SubscriptionDTO createSubscription(array $data)
 * @method static SubscriptionDTO|null updateSubscription(int $id, array $data)
 * @method static bool deleteSubscription(int $id)
 * @method static Collection getSubscriptionsByProduct(int $productId)
 * @method static Collection getSubscriptionsByBillingCycle(string $billingCycle)
 * @method static Collection getSubscriptionsByPriceRange(float $minPrice, float $maxPrice)
 * @method static Collection getSubscriptionsWithTrial()
 * @method static Collection searchSubscriptions(string $query)
 * @method static array getSubscriptionStatistics()
 */
class SubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.subscription.facade';
    }
}
