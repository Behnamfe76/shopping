<?php

namespace Fereydooni\Shopping\App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Provider Facade
 *
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO onboardProvider(array $data)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null updateProviderProfile(int $providerId, array $data)
 * @method static bool activateProvider(int $providerId)
 * @method static bool deactivateProvider(int $providerId, string $reason = null)
 * @method static bool suspendProvider(int $providerId, string $reason = null)
 * @method static bool unsuspendProvider(int $providerId)
 * @method static bool updateProviderRating(int $providerId, float $rating)
 * @method static bool updateProviderQualityRating(int $providerId, float $rating)
 * @method static bool updateProviderDeliveryRating(int $providerId, float $rating)
 * @method static bool updateProviderCommunicationRating(int $providerId, float $rating)
 * @method static bool updateProviderCreditLimit(int $providerId, float $newLimit)
 * @method static bool updateProviderCommissionRate(int $providerId, float $newRate)
 * @method static bool updateProviderDiscountRate(int $providerId, float $newRate)
 * @method static bool extendProviderContract(int $providerId, string $newEndDate)
 * @method static bool terminateProviderContract(int $providerId, string $reason = null)
 * @method static array getProviderAnalytics(int $providerId)
 * @method static array evaluateProviderPerformance(int $providerId)
 * @method static bool addProviderNote(int $providerId, string $note, string $type = 'general')
 * @method static \Illuminate\Database\Eloquent\Collection getProviderNotes(int $providerId)
 * @method static array exportProviderData(int $providerId)
 * @method static bool updateProviderSpecializations(int $providerId, array $specializations)
 * @method static bool updateProviderCertifications(int $providerId, array $certifications)
 * @method static bool updateProviderInsurance(int $providerId, array $insurance)
 * @method static bool updateProviderLocation(int $providerId, array $locationData)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getProviderOrders(int $providerId, int $perPage = 15)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getProviderPayments(int $providerId, int $perPage = 15)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getProviderInvoices(int $providerId, int $perPage = 15)
 * @method static float calculateProviderScore(int $providerId)
 * @method static array getProviderPerformanceMetrics(int $providerId)
 * @method static \Fereydooni\Shopping\App\Models\Provider getProvider(int $id)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO getProviderDTO(int $id)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByUserId(int $userId)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByUserIdDTO(int $userId)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByEmail(string $email)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByEmailDTO(string $email)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByPhone(string $phone)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByPhoneDTO(string $phone)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByNumber(string $providerNumber)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByNumberDTO(string $providerNumber)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByCompanyName(string $companyName)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByCompanyNameDTO(string $companyName)
 * @method static \Fereydooni\Shopping\App\Models\Provider|null getProviderByTaxId(string $taxId)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderDTO|null getProviderByTaxIdDTO(string $taxId)
 * @method static \Illuminate\Database\Eloquent\Collection getAllProviders()
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPaginatedProviders(int $perPage = 15)
 * @method static \Illuminate\Database\Eloquent\Collection searchProviders(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchProvidersDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchProvidersByCompany(string $companyName)
 * @method static \Illuminate\Database\Eloquent\Collection searchProvidersByCompanyDTO(string $companyName)
 * @method static \Illuminate\Database\Eloquent\Collection searchProvidersBySpecialization(string $specialization)
 * @method static \Illuminate\Database\Eloquent\Collection searchProvidersBySpecializationDTO(string $specialization)
 * @method static bool validateProviderData(array $data)
 * @method static string generateProviderNumber()
 * @method static bool isProviderNumberUnique(string $providerNumber)
 * @method static int getProviderCount()
 * @method static int getProviderCountByStatus(string $status)
 * @method static int getProviderCountByType(string $type)
 * @method static int getActiveProviderCount()
 * @method static int getInactiveProviderCount()
 * @method static int getSuspendedProviderCount()
 * @method static float getTotalProviderSpending()
 * @method static float getAverageProviderSpending()
 * @method static float getAverageProviderRating()
 * @method static float getTotalCreditLimit()
 * @method static float getAverageCreditLimit()
 * @method static float getTotalCurrentBalance()
 * @method static float getAverageCurrentBalance()
 * @method static array getProviderStats()
 * @method static array getProviderStatsByStatus()
 * @method static array getProviderStatsByType()
 * @method static array getProviderGrowthStats(string $period = 'monthly')
 * @method static array getProviderPerformanceStats()
 * @method static array getProviderQualityStats()
 * @method static array getProviderFinancialStats()
 * @method static array getProviderContractStats()
 * @method static float getProviderLifetimeValue(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderOrderHistory(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderProducts(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderInvoices(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderPayments(int $providerId)
 * @method static array getProviderSpecializations(int $providerId)
 * @method static array getProviderCertifications(int $providerId)
 * @method static array getProviderInsurance(int $providerId)
 *
 * @see \Fereydooni\Shopping\App\Services\ProviderService
 */
class Provider extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shopping.provider';
    }
}
