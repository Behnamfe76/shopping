<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\Services\CustomerPreferenceService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection getAllPreferences()
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPaginatedPreferences(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator getSimplePaginatedPreferences(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator getCursorPaginatedPreferences(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\CustomerPreference|null getPreference(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO|null getPreferenceDTO(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO createPreference(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO|null updatePreference(\Fereydooni\Shopping\app\Models\CustomerPreference $preference, array $data)
 * @method static bool deletePreference(\Fereydooni\Shopping\app\Models\CustomerPreference $preference)
 *
 * // Customer Preference Operations
 * @method static bool setCustomerPreference(int $customerId, string $key, $value, string $type = 'string', string $description = null)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO|null setCustomerPreferenceDTO(int $customerId, string $key, $value, string $type = 'string', string $description = null)
 * @method static mixed getCustomerPreference(int $customerId, string $key, $default = null)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO|null getCustomerPreferenceDTO(int $customerId, string $key)
 * @method static mixed getCustomerPreferenceWithDefault(int $customerId, string $key, $default = null)
 * @method static bool removeCustomerPreference(int $customerId, string $key)
 * @method static bool hasCustomerPreference(int $customerId, string $key)
 * @method static array getAllCustomerPreferences(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection getAllCustomerPreferencesDTO(int $customerId)
 * @method static array getCustomerPreferencesByType(int $customerId, string $type)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerPreferencesByTypeDTO(int $customerId, string $type)
 * @method static array getCustomerPreferencesByTypeWithValidation(int $customerId, string $type)
 * @method static array getCustomerPreferencesByCategory(int $customerId, string $category)
 * @method static array getCustomerPreferencesByCategoryWithDefaults(int $customerId, string $category)
 * @method static bool setMultipleCustomerPreferences(int $customerId, array $preferences)
 * @method static bool updateCustomerPreference(int $customerId, string $key, $value, string $type = 'string', string $description = null)
 * @method static bool bulkUpdateCustomerPreferences(int $customerId, array $preferences)
 *
 * // Status Management
 * @method static bool activateCustomerPreference(\Fereydooni\Shopping\app\Models\CustomerPreference $preference, string $reason = null)
 * @method static bool deactivateCustomerPreference(\Fereydooni\Shopping\app\Models\CustomerPreference $preference, string $reason = null)
 * @method static bool toggleCustomerPreferenceStatus(\Fereydooni\Shopping\app\Models\CustomerPreference $preference, string $reason = null)
 * @method static bool activateCustomerPreferenceById(int $preferenceId, string $reason = null)
 * @method static bool deactivateCustomerPreferenceById(int $preferenceId, string $reason = null)
 * @method static bool activateCustomerPreferenceByKey(int $customerId, string $key, string $reason = null)
 * @method static bool deactivateCustomerPreferenceByKey(int $customerId, string $key, string $reason = null)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerPreferencesByStatus(int $customerId, bool $isActive)
 * @method static \Illuminate\Database\Eloquent\Collection getCustomerPreferencesByStatusDTO(int $customerId, bool $isActive)
 * @method static \Illuminate\Database\Eloquent\Collection getActiveCustomerPreferences(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection getActiveCustomerPreferencesDTO(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection getInactiveCustomerPreferences(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Collection getInactiveCustomerPreferencesDTO(int $customerId)
 * @method static bool activateAllCustomerPreferences(int $customerId, string $reason = null)
 * @method static bool deactivateAllCustomerPreferences(int $customerId, string $reason = null)
 * @method static bool activateCustomerPreferencesByType(int $customerId, string $type, string $reason = null)
 * @method static bool deactivateCustomerPreferencesByType(int $customerId, string $type, string $reason = null)
 * @method static bool activateCustomerPreferencesByCategory(int $customerId, string $category, string $reason = null)
 * @method static bool deactivateCustomerPreferencesByCategory(int $customerId, string $category, string $reason = null)
 * @method static array getCustomerPreferenceStatusStats(int $customerId)
 *
 * // Import/Export Operations
 * @method static bool importCustomerPreferences(int $customerId, array $preferences)
 * @method static array exportCustomerPreferences(int $customerId)
 * @method static bool syncCustomerPreferences(int $customerId, array $preferences)
 * @method static bool resetCustomerPreferences(int $customerId)
 * @method static bool resetCustomerPreferencesByType(int $customerId, string $type)
 * @method static array backupCustomerPreferences(int $customerId)
 * @method static bool restoreCustomerPreferences(int $customerId, array $backup)
 * @method static bool cloneCustomerPreferences(int $sourceCustomerId, int $targetCustomerId, array $categories = [])
 *
 * // Templates and Migration
 * @method static array getPreferenceTemplates()
 * @method static bool applyPreferenceTemplate(int $customerId, string $templateName)
 * @method static bool migrateCustomerPreferences(int $customerId, array $migrationRules)
 *
 * // Analytics and Reporting
 * @method static array getCustomerPreferenceStats(int $customerId)
 * @method static array getCustomerPreferenceSummary(int $customerId)
 * @method static array getRecentPreferences(int $customerId, int $limit = 10)
 * @method static array getPopularCategories(int $customerId)
 * @method static array getPreferenceAnalytics(int $customerId = null)
 * @method static array getCustomerPreferenceAnalytics(int $customerId)
 * @method static array getGlobalPreferenceAnalytics()
 * @method static float calculatePreferenceCompleteness(int $customerId)
 * @method static float calculateAveragePreferencesPerCustomer()
 * @method static array compareCustomerPreferences(int $customerId1, int $customerId2)
 *
 * // Search Operations
 * @method static \Illuminate\Database\Eloquent\Collection searchCustomerPreferences(int $customerId, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPreferences(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPreferencesDTO(string $query)
 *
 * // Initialization
 * @method static bool initializeCustomerPreferences(int $customerId)
 *
 * // Audit and History
 * @method static \Illuminate\Database\Eloquent\Collection getPreferenceAuditTrail(int $customerId, string $key = null)
 * @method static \Illuminate\Database\Eloquent\Collection getPreferenceVersionHistory(int $customerId, string $key)
 * @method static \Illuminate\Database\Eloquent\Collection getPreferenceHistory(int $customerId, string $key)
 * @method static \Illuminate\Database\Eloquent\Collection getPreferenceHistoryDTO(int $customerId, string $key)
 *
 * // Statistics and Counts
 * @method static int getPreferenceCount()
 * @method static int getPreferenceCountByCustomer(int $customerId)
 * @method static int getPreferenceCountByType(string $type)
 * @method static int getActivePreferenceCount()
 * @method static int getInactivePreferenceCount()
 * @method static array getPopularPreferences(int $limit = 10)
 * @method static array getPopularPreferencesByType(string $type, int $limit = 10)
 * @method static array getPreferenceStats()
 * @method static array getPreferenceStatsByType()
 *
 * // Validation
 * @method static bool validatePreference(array $data)
 * @method static array getDefaultPreferences()
 */
class CustomerPreference extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CustomerPreferenceService::class;
    }
}
