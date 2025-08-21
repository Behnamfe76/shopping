<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\CustomerService;

/**
 * @method static \Illuminate\Database\Eloquent\Collection getAllCustomers()
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPaginatedCustomers(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator getSimplePaginatedCustomers(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator getCursorPaginatedCustomers(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\Customer|null getCustomer(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO|null getCustomerDTO(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO createCustomer(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO|null updateCustomer(\Fereydooni\Shopping\app\Models\Customer $customer, array $data)
 * @method static bool deleteCustomer(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO registerCustomer(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO|null completeOnboarding(\Fereydooni\Shopping\app\Models\Customer $customer, array $additionalData)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO|null updateProfile(\Fereydooni\Shopping\app\Models\Customer $customer, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO|null updateContactInfo(\Fereydooni\Shopping\app\Models\Customer $customer, array $contactData)
 * @method static bool activateCustomer(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static bool deactivateCustomer(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static bool suspendCustomer(\Fereydooni\Shopping\app\Models\Customer $customer, string $reason = null)
 * @method static bool unsuspendCustomer(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static bool addLoyaltyPoints(\Fereydooni\Shopping\app\Models\Customer $customer, int $points, string $reason = null)
 * @method static bool deductLoyaltyPoints(\Fereydooni\Shopping\app\Models\Customer $customer, int $points, string $reason = null)
 * @method static bool resetLoyaltyPoints(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static int getLoyaltyBalance(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static array getCustomerStats()
 * @method static array getCustomerStatsByStatus()
 * @method static array getCustomerStatsByType()
 * @method static array getCustomerGrowthStats(string $period = 'monthly')
 * @method static array getCustomerRetentionStats()
 * @method static float getCustomerLifetimeValue(int $customerId)
 * @method static bool updateMarketingConsent(\Fereydooni\Shopping\app\Models\Customer $customer, bool $consent)
 * @method static bool updateNewsletterSubscription(\Fereydooni\Shopping\app\Models\Customer $customer, bool $subscribed)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByMarketingConsent(bool $consent)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByNewsletterSubscription(bool $subscribed)
 * @method static bool updatePreferences(\Fereydooni\Shopping\app\Models\Customer $customer, array $preferences)
 * @method static array getPreferences(int $customerId)
 * @method static array exportCustomerData(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerDTO importCustomerData(array $data)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByType(\Fereydooni\Shopping\app\Enums\CustomerType $type)
 * @method static \Illuminate\Database\Eloquent\Collection getActiveCustomers()
 * @method static \Illuminate\Database\Eloquent\Collection getInactiveCustomers()
 * @method static \Illuminate\Database\Eloquent\Collection getTopSpenders(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getMostLoyal(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getNewestCustomers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getOldestCustomers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersWithBirthdayThisMonth()
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByDateRange(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByLoyaltyPointsRange(int $minPoints, int $maxPoints)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomersByTotalSpentRange(float $minSpent, float $maxSpent)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerOrderHistory(int $customerId)
 * @method static bool updateOrderStats(\Fereydooni\Shopping\app\Models\Customer $customer, float $orderValue)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerAddresses(int $customerId)
 * @method static bool updateAddressCount(\Fereydooni\Shopping\app\Models\Customer $customer, int $count)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerReviews(int $customerId)
 * @method static bool updateReviewCount(\Fereydooni\Shopping\app\Models\Customer $customer, int $count)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerWishlist(int $customerId)
 * @method static bool updateWishlistCount(\Fereydooni\Shopping\app\Models\Customer $customer, int $count)
 * @method static \Illuminate\Database\Eloquent\Collection searchCustomers(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchCustomersByCompany(string $companyName)
 * @method static bool addCustomerNote(\Fereydooni\Shopping\app\Models\Customer $customer, string $note, string $type = 'general')
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerNotes(\Fereydooni\Shopping\app\Models\Customer $customer)
 * @method static string generateCustomerNumber()
 * @method static bool validateCustomerData(array $data)
 * @method static bool isCustomerNumberUnique(string $customerNumber)
 * @method static \Fereydooni\Shopping\app\Models\Customer|null findByUserId(int $userId)
 * @method static \Fereydooni\Shopping\app\Models\Customer|null findByEmail(string $email)
 * @method static \Fereydooni\Shopping\app\Models\Customer|null findByPhone(string $phone)
 * @method static \Fereydooni\Shopping\app\Models\Customer|null findByCustomerNumber(string $customerNumber)
 */
class Customer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CustomerService::class;
    }
}
