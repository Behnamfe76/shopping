<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasCustomerPreferenceOperations
{
    /**
     * Set a customer preference
     */
    public function setCustomerPreference(int $customerId, string $key, $value, string $type = 'string', ?string $description = null): bool
    {
        $this->validatePreferenceData($customerId, $key, $value, $type);

        return $this->repository->setPreference($customerId, $key, $value, $type);
    }

    /**
     * Set a customer preference and return DTO
     */
    public function setCustomerPreferenceDTO(int $customerId, string $key, $value, string $type = 'string', ?string $description = null): ?CustomerPreferenceDTO
    {
        $this->validatePreferenceData($customerId, $key, $value, $type);

        return $this->repository->setPreferenceDTO($customerId, $key, $value, $type);
    }

    /**
     * Get a customer preference
     */
    public function getCustomerPreference(int $customerId, string $key, $default = null): mixed
    {
        return $this->repository->getPreference($customerId, $key, $default);
    }

    /**
     * Get a customer preference as DTO
     */
    public function getCustomerPreferenceDTO(int $customerId, string $key): ?CustomerPreferenceDTO
    {
        return $this->repository->getPreferenceDTO($customerId, $key);
    }

    /**
     * Remove a customer preference
     */
    public function removeCustomerPreference(int $customerId, string $key): bool
    {
        return $this->repository->removePreference($customerId, $key);
    }

    /**
     * Check if customer has a preference
     */
    public function hasCustomerPreference(int $customerId, string $key): bool
    {
        return $this->repository->hasPreference($customerId, $key);
    }

    /**
     * Get all customer preferences
     */
    public function getAllCustomerPreferences(int $customerId): array
    {
        return $this->repository->getAllPreferences($customerId);
    }

    /**
     * Get all customer preferences as DTOs
     */
    public function getAllCustomerPreferencesDTO(int $customerId): Collection
    {
        return $this->repository->getAllPreferencesDTO($customerId);
    }

    /**
     * Get customer preferences by type
     */
    public function getCustomerPreferencesByType(int $customerId, string $type): array
    {
        return $this->repository->getPreferencesByType($customerId, $type);
    }

    /**
     * Get customer preferences by type as DTOs
     */
    public function getCustomerPreferencesByTypeDTO(int $customerId, string $type): Collection
    {
        return $this->repository->getPreferencesByTypeDTO($customerId, $type);
    }

    /**
     * Get customer preferences by category
     */
    public function getCustomerPreferencesByCategory(int $customerId, string $category): array
    {
        $preferences = $this->repository->findByCustomerId($customerId);
        $result = [];

        foreach ($preferences as $preference) {
            if ($preference->is_active && $this->getPreferenceCategory($preference->preference_key) === $category) {
                $result[$preference->preference_key] = $this->convertStringToValue(
                    $preference->preference_value,
                    $preference->preference_type
                );
            }
        }

        return $result;
    }

    /**
     * Set multiple customer preferences
     */
    public function setMultipleCustomerPreferences(int $customerId, array $preferences): bool
    {
        DB::beginTransaction();

        try {
            foreach ($preferences as $key => $preference) {
                $value = is_array($preference) ? $preference['value'] : $preference;
                $type = is_array($preference) ? ($preference['type'] ?? 'string') : 'string';
                $description = is_array($preference) ? ($preference['description'] ?? null) : null;

                $this->setCustomerPreference($customerId, $key, $value, $type, $description);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Import customer preferences
     */
    public function importCustomerPreferences(int $customerId, array $preferences): bool
    {
        return $this->repository->importPreferences($customerId, $preferences);
    }

    /**
     * Export customer preferences
     */
    public function exportCustomerPreferences(int $customerId): array
    {
        return $this->repository->exportPreferences($customerId);
    }

    /**
     * Sync customer preferences
     */
    public function syncCustomerPreferences(int $customerId, array $preferences): bool
    {
        return $this->repository->syncPreferences($customerId, $preferences);
    }

    /**
     * Reset all customer preferences
     */
    public function resetCustomerPreferences(int $customerId): bool
    {
        return $this->repository->resetCustomerPreferences($customerId);
    }

    /**
     * Reset customer preferences by type
     */
    public function resetCustomerPreferencesByType(int $customerId, string $type): bool
    {
        return $this->repository->resetCustomerPreferencesByType($customerId, $type);
    }

    /**
     * Get customer preference statistics
     */
    public function getCustomerPreferenceStats(int $customerId): array
    {
        return $this->repository->getCustomerPreferenceStats($customerId);
    }

    /**
     * Get preference templates
     */
    public function getPreferenceTemplates(): array
    {
        return [
            'ui' => [
                'theme' => ['value' => 'light', 'type' => 'string', 'description' => 'User interface theme'],
                'language' => ['value' => 'en', 'type' => 'string', 'description' => 'Interface language'],
                'font_size' => ['value' => 'medium', 'type' => 'string', 'description' => 'Font size preference'],
                'color_scheme' => ['value' => 'default', 'type' => 'string', 'description' => 'Color scheme preference'],
            ],
            'notifications' => [
                'email' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Email notifications'],
                'sms' => ['value' => 'false', 'type' => 'boolean', 'description' => 'SMS notifications'],
                'push' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Push notifications'],
                'marketing' => ['value' => 'false', 'type' => 'boolean', 'description' => 'Marketing communications'],
            ],
            'privacy' => [
                'data_sharing' => ['value' => 'false', 'type' => 'boolean', 'description' => 'Data sharing consent'],
                'analytics' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Analytics tracking'],
                'cookies' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Cookie consent'],
            ],
            'shopping' => [
                'currency' => ['value' => 'USD', 'type' => 'string', 'description' => 'Preferred currency'],
                'items_per_page' => ['value' => '20', 'type' => 'integer', 'description' => 'Items per page'],
                'sort_order' => ['value' => 'newest', 'type' => 'string', 'description' => 'Default sort order'],
                'show_prices' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Show prices'],
            ],
        ];
    }

    /**
     * Apply preference template to customer
     */
    public function applyPreferenceTemplate(int $customerId, string $templateName): bool
    {
        $templates = $this->getPreferenceTemplates();

        if (! isset($templates[$templateName])) {
            return false;
        }

        return $this->setMultipleCustomerPreferences($customerId, $templates[$templateName]);
    }

    /**
     * Migrate customer preferences
     */
    public function migrateCustomerPreferences(int $customerId, array $migrationRules): bool
    {
        DB::beginTransaction();

        try {
            foreach ($migrationRules as $oldKey => $newKey) {
                $preference = $this->repository->findByCustomerAndKey($customerId, $oldKey);

                if ($preference) {
                    // Create new preference with new key
                    $this->repository->setPreference(
                        $customerId,
                        $newKey,
                        $preference->preference_value,
                        $preference->preference_type
                    );

                    // Remove old preference
                    $preference->delete();
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Backup customer preferences
     */
    public function backupCustomerPreferences(int $customerId): array
    {
        $preferences = $this->repository->findByCustomerId($customerId);
        $backup = [];

        foreach ($preferences as $preference) {
            $backup[] = [
                'customer_id' => $preference->customer_id,
                'preference_key' => $preference->preference_key,
                'preference_value' => $preference->preference_value,
                'preference_type' => $preference->preference_type,
                'is_active' => $preference->is_active,
                'description' => $preference->description,
                'metadata' => $preference->metadata,
                'created_at' => $preference->created_at,
                'updated_at' => $preference->updated_at,
            ];
        }

        return $backup;
    }

    /**
     * Restore customer preferences from backup
     */
    public function restoreCustomerPreferences(int $customerId, array $backup): bool
    {
        DB::beginTransaction();

        try {
            // Clear existing preferences
            $this->repository->resetCustomerPreferences($customerId);

            // Restore from backup
            foreach ($backup as $preferenceData) {
                if ($preferenceData['customer_id'] == $customerId) {
                    $this->repository->create($preferenceData);
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Get preference audit trail
     */
    public function getPreferenceAuditTrail(int $customerId, ?string $key = null): Collection
    {
        // This would typically query an audit table
        // For now, return empty collection as audit trail would need additional implementation
        return new Collection;
    }

    /**
     * Get preference version history
     */
    public function getPreferenceVersionHistory(int $customerId, string $key): Collection
    {
        // This would typically query a version history table
        // For now, return empty collection as versioning would need additional implementation
        return new Collection;
    }

    /**
     * Validate preference data
     */
    protected function validatePreferenceData(int $customerId, string $key, $value, string $type): void
    {
        $rules = [
            'customer_id' => 'required|integer|exists:customers,id',
            'key' => 'required|string|max:100',
            'value' => 'required',
            'type' => 'required|string|in:string,integer,float,boolean,json,array,object',
        ];

        $data = [
            'customer_id' => $customerId,
            'key' => $key,
            'value' => $value,
            'type' => $type,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
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
        return match ($type) {
            'string' => $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array', 'object' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Convert value to string for storage
     */
    protected function convertValueToString($value, string $type): string
    {
        return match ($type) {
            'string' => (string) $value,
            'integer' => (string) (int) $value,
            'float' => (string) (float) $value,
            'boolean' => $value ? 'true' : 'false',
            'json', 'array', 'object' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };
    }
}
