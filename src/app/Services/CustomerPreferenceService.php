<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerPreferenceOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerPreferenceStatusManagement;
use Fereydooni\Shopping\app\Traits\HasNotesManagement;
use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Log;

class CustomerPreferenceService
{
    use HasCrudOperations,
        HasSearchOperations,
        HasCustomerPreferenceOperations,
        HasCustomerPreferenceStatusManagement,
        HasNotesManagement;

    public function __construct(
        private CustomerPreferenceRepositoryInterface $repository
    ) {
        $this->model = CustomerPreference::class;
        $this->dtoClass = CustomerPreferenceDTO::class;
    }

    // CustomerPreference-specific methods that extend the traits

    /**
     * Initialize customer with default preferences
     */
    public function initializeCustomerPreferences(int $customerId): bool
    {
        $defaultPreferences = $this->repository->getDefaultPreferences();
        return $this->setMultipleCustomerPreferences($customerId, $defaultPreferences);
    }

    /**
     * Get customer preference with fallback to default
     */
    public function getCustomerPreferenceWithDefault(int $customerId, string $key, $default = null): mixed
    {
        $preference = $this->getCustomerPreference($customerId, $key);

        if ($preference !== null) {
            return $preference;
        }

        // Check if there's a default preference
        $defaultPreferences = $this->repository->getDefaultPreferences();
        if (isset($defaultPreferences[$key])) {
            return $defaultPreferences[$key]['value'];
        }

        return $default;
    }

    /**
     * Get customer preferences by category with defaults
     */
    public function getCustomerPreferencesByCategoryWithDefaults(int $customerId, string $category): array
    {
        $preferences = $this->getCustomerPreferencesByCategory($customerId, $category);
        $defaultPreferences = $this->repository->getDefaultPreferences();

        // Add default preferences for the category that don't exist
        foreach ($defaultPreferences as $key => $default) {
            if ($this->getPreferenceCategory($key) === $category && !isset($preferences[$key])) {
                $preferences[$key] = $default['value'];
            }
        }

        return $preferences;
    }

    /**
     * Update customer preference with validation
     */
    public function updateCustomerPreference(int $customerId, string $key, $value, string $type = 'string', string $description = null): bool
    {
        // Validate the preference value based on type
        if (!$this->validatePreferenceValue($value, $type)) {
            throw new \InvalidArgumentException("Invalid value for preference type: {$type}");
        }

        return $this->setCustomerPreference($customerId, $key, $value, $type, $description);
    }

    /**
     * Bulk update customer preferences
     */
    public function bulkUpdateCustomerPreferences(int $customerId, array $preferences): bool
    {
        foreach ($preferences as $key => $preference) {
            $value = is_array($preference) ? $preference['value'] : $preference;
            $type = is_array($preference) ? ($preference['type'] ?? 'string') : 'string';
            $description = is_array($preference) ? ($preference['description'] ?? null) : null;

            if (!$this->validatePreferenceValue($value, $type)) {
                throw new \InvalidArgumentException("Invalid value for preference key: {$key}");
            }
        }

        return $this->setMultipleCustomerPreferences($customerId, $preferences);
    }

    /**
     * Get customer preference summary
     */
    public function getCustomerPreferenceSummary(int $customerId): array
    {
        $preferences = $this->getAllCustomerPreferences($customerId);
        $stats = $this->getCustomerPreferenceStats($customerId);
        $statusStats = $this->getCustomerPreferenceStatusStats($customerId);

        return [
            'customer_id' => $customerId,
            'total_preferences' => $stats['total_preferences'],
            'active_preferences' => $statusStats['active_preferences'],
            'inactive_preferences' => $statusStats['inactive_preferences'],
            'preferences_by_category' => $stats['preferences_by_category'],
            'preferences_by_type' => $stats['preferences_by_type'],
            'recent_preferences' => $this->getRecentPreferences($customerId, 5),
            'popular_categories' => $this->getPopularCategories($customerId),
        ];
    }

    /**
     * Get recent preferences
     */
    public function getRecentPreferences(int $customerId, int $limit = 10): array
    {
        $preferences = $this->repository->findByCustomerId($customerId)
            ->sortByDesc('updated_at')
            ->take($limit);

        $result = [];
        foreach ($preferences as $preference) {
            $result[] = [
                'key' => $preference->preference_key,
                'value' => $this->convertStringToValue($preference->preference_value, $preference->preference_type),
                'type' => $preference->preference_type,
                'category' => $this->getPreferenceCategory($preference->preference_key),
                'updated_at' => $preference->updated_at,
            ];
        }

        return $result;
    }

    /**
     * Get popular categories for customer
     */
    public function getPopularCategories(int $customerId): array
    {
        $preferences = $this->repository->findByCustomerId($customerId);
        $categories = [];

        foreach ($preferences as $preference) {
            $category = $this->getPreferenceCategory($preference->preference_key);
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }

        arsort($categories);
        return $categories;
    }

    /**
     * Search customer preferences
     */
    public function searchCustomerPreferences(int $customerId, string $query): Collection
    {
        return $this->repository->searchByCustomerDTO($customerId, $query);
    }

    /**
     * Get customer preferences by type with validation
     */
    public function getCustomerPreferencesByTypeWithValidation(int $customerId, string $type): array
    {
        $preferences = $this->getCustomerPreferencesByType($customerId, $type);

        // Validate that all preferences are of the correct type
        foreach ($preferences as $key => $value) {
            if (!$this->validatePreferenceValue($value, $type)) {
                Log::warning("Invalid preference value found", [
                    'customer_id' => $customerId,
                    'key' => $key,
                    'value' => $value,
                    'expected_type' => $type,
                ]);
            }
        }

        return $preferences;
    }

    /**
     * Clone preferences from one customer to another
     */
    public function cloneCustomerPreferences(int $sourceCustomerId, int $targetCustomerId, array $categories = []): bool
    {
        $sourcePreferences = $this->repository->findByCustomerId($sourceCustomerId);
        $preferencesToClone = [];

        foreach ($sourcePreferences as $preference) {
            if (empty($categories) || in_array($this->getPreferenceCategory($preference->preference_key), $categories)) {
                $preferencesToClone[$preference->preference_key] = [
                    'value' => $preference->preference_value,
                    'type' => $preference->preference_type,
                    'description' => $preference->description,
                ];
            }
        }

        return $this->setMultipleCustomerPreferences($targetCustomerId, $preferencesToClone);
    }

    /**
     * Compare preferences between two customers
     */
    public function compareCustomerPreferences(int $customerId1, int $customerId2): array
    {
        $preferences1 = $this->getAllCustomerPreferences($customerId1);
        $preferences2 = $this->getAllCustomerPreferences($customerId2);

        $common = array_intersect_key($preferences1, $preferences2);
        $onlyIn1 = array_diff_key($preferences1, $preferences2);
        $onlyIn2 = array_diff_key($preferences2, $preferences1);

        $differences = [];
        foreach ($common as $key => $value1) {
            $value2 = $preferences2[$key];
            if ($value1 !== $value2) {
                $differences[$key] = [
                    'customer1_value' => $value1,
                    'customer2_value' => $value2,
                ];
            }
        }

        return [
            'common_preferences' => array_keys($common),
            'only_in_customer1' => array_keys($onlyIn1),
            'only_in_customer2' => array_keys($onlyIn2),
            'different_values' => $differences,
            'similarity_percentage' => count($common) > 0 ?
                round((count($common) - count($differences)) / count($common) * 100, 2) : 0,
        ];
    }

    /**
     * Get preference analytics
     */
    public function getPreferenceAnalytics(int $customerId = null): array
    {
        if ($customerId) {
            return $this->getCustomerPreferenceAnalytics($customerId);
        }

        return $this->getGlobalPreferenceAnalytics();
    }

    /**
     * Get customer-specific preference analytics
     */
    public function getCustomerPreferenceAnalytics(int $customerId): array
    {
        $preferences = $this->repository->findByCustomerId($customerId);
        $stats = $this->getCustomerPreferenceStats($customerId);

        return [
            'customer_id' => $customerId,
            'total_preferences' => $stats['total_preferences'],
            'preferences_by_type' => $stats['preferences_by_type'],
            'preferences_by_category' => $stats['preferences_by_category'],
            'most_used_categories' => $this->getPopularCategories($customerId),
            'recent_activity' => $this->getRecentPreferences($customerId, 10),
            'preference_completeness' => $this->calculatePreferenceCompleteness($customerId),
        ];
    }

    /**
     * Get global preference analytics
     */
    public function getGlobalPreferenceAnalytics(): array
    {
        $globalStats = $this->repository->getPreferenceStats();
        $popularPreferences = $this->repository->getPopularPreferences(10);

        return [
            'total_preferences' => $globalStats['total_preferences'],
            'active_preferences' => $globalStats['active_preferences'],
            'inactive_preferences' => $globalStats['inactive_preferences'],
            'preferences_by_type' => $globalStats['preferences_by_type'],
            'preferences_by_category' => $globalStats['preferences_by_category'],
            'popular_preferences' => $popularPreferences,
            'average_preferences_per_customer' => $this->calculateAveragePreferencesPerCustomer(),
        ];
    }

    /**
     * Calculate preference completeness for a customer
     */
    public function calculatePreferenceCompleteness(int $customerId): float
    {
        $customerPreferences = $this->repository->findByCustomerId($customerId);
        $defaultPreferences = $this->repository->getDefaultPreferences();

        $customerKeys = $customerPreferences->pluck('preference_key')->toArray();
        $defaultKeys = array_keys($defaultPreferences);

        $commonKeys = array_intersect($customerKeys, $defaultKeys);

        return count($defaultKeys) > 0 ? round(count($commonKeys) / count($defaultKeys) * 100, 2) : 0;
    }

    /**
     * Calculate average preferences per customer
     */
    public function calculateAveragePreferencesPerCustomer(): float
    {
        $totalPreferences = $this->repository->getPreferenceCount();
        $totalCustomers = $this->repository->getPreferenceCountByCustomer(1); // This would need a different approach

        // For now, return a placeholder value
        return $totalPreferences > 0 ? round($totalPreferences / max($totalCustomers, 1), 2) : 0;
    }

    /**
     * Validate preference value based on type
     */
    protected function validatePreferenceValue($value, string $type): bool
    {
        return match($type) {
            'string' => is_string($value),
            'integer' => is_numeric($value) && floor($value) == $value,
            'float' => is_numeric($value),
            'boolean' => is_bool($value) || in_array($value, ['true', 'false', '1', '0', 1, 0]),
            'json', 'array', 'object' => is_array($value) || is_object($value) || (is_string($value) && json_decode($value) !== null),
            default => true,
        };
    }

    /**
     * Get preference category from key
     */
    protected function getPreferenceCategory(string $key): string
    {
        $parts = explode('.', $key);
        return $parts[0] ?? 'general';
    }

    /**
     * Convert string value to typed value
     */
    protected function convertStringToValue(string $value, string $type): mixed
    {
        return match($type) {
            'string' => $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array', 'object' => json_decode($value, true),
            default => $value,
        };
    }
}

