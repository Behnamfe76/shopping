<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\Services\UserService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\User|null getUser(int $id)
 * @method static bool deleteUser(\Fereydooni\Shopping\app\Models\User $User)
 * @method static bool activateUser(\Fereydooni\Shopping\app\Models\User $User)
 * @method static bool deactivateUser(\Fereydooni\Shopping\app\Models\User $User)
 * @method static bool suspendUser(\Fereydooni\Shopping\app\Models\User $User, string $reason = null)
 * @method static bool unsuspendUser(\Fereydooni\Shopping\app\Models\User $User)
 * @method static bool addLoyaltyPoints(\Fereydooni\Shopping\app\Models\User $User, int $points, string $reason = null)
 * @method static bool deductLoyaltyPoints(\Fereydooni\Shopping\app\Models\User $User, int $points, string $reason = null)
 * @method static bool resetLoyaltyPoints(\Fereydooni\Shopping\app\Models\User $User)
 * @method static int getLoyaltyBalance(\Fereydooni\Shopping\app\Models\User $User)
 * @method static array getUserStats()
 * @method static array getUserStatsByStatus()
 * @method static array getUserStatsByType()
 * @method static array getUserGrowthStats(string $period = 'monthly')
 * @method static array getUserRetentionStats()
 * @method static float getUserLifetimeValue(int $UserId)
 * @method static bool updateMarketingConsent(\Fereydooni\Shopping\app\Models\User $User, bool $consent)
 * @method static bool updateNewsletterSubscription(\Fereydooni\Shopping\app\Models\User $User, bool $subscribed)
 * @method static bool updatePreferences(\Fereydooni\Shopping\app\Models\User $User, array $preferences)
 * @method static array getPreferences(int $UserId)
 * @method static array exportUserData(\Fereydooni\Shopping\app\Models\User $User)
 * @method static bool updateOrderStats(\Fereydooni\Shopping\app\Models\User $User, float $orderValue)
 * @method static bool updateAddressCount(\Fereydooni\Shopping\app\Models\User $User, int $count)
 * @method static bool updateReviewCount(\Fereydooni\Shopping\app\Models\User $User, int $count)
 * @method static bool updateWishlistCount(\Fereydooni\Shopping\app\Models\User $User, int $count)
 * @method static bool addUserNote(\Fereydooni\Shopping\app\Models\User $User, string $note, string $type = 'general')
 * @method static string generateUserNumber()
 * @method static bool validateUserData(array $data)
 * @method static bool isUserNumberUnique(string $UserNumber)
 * @method static \Fereydooni\Shopping\app\Models\User|null findByUserId(int $userId)
 * @method static \Fereydooni\Shopping\app\Models\User|null findByEmail(string $email)
 * @method static \Fereydooni\Shopping\app\Models\User|null findByPhone(string $phone)
 * @method static \Fereydooni\Shopping\app\Models\User|null findByUserNumber(string $userNumber)
 */
class User extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return UserService::class;
    }
}
