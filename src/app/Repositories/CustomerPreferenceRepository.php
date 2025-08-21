<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerPreferenceRepository implements CustomerPreferenceRepositoryInterface
{
    public function all(): Collection
    {
        return CustomerPreference::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CustomerPreference::with('customer')->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return CustomerPreference::with('customer')->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return CustomerPreference::with('customer')->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?CustomerPreference
    {
        return CustomerPreference::with('customer')->find($id);
    }

    public function findDTO(int $id): ?CustomerPreferenceDTO
    {
        $preference = $this->find($id);
        return $preference ? CustomerPreferenceDTO::fromModel($preference) : null;
    }

    public function findByCustomerId(int $customerId): Collection
    {
        return CustomerPreference::where('customer_id', $customerId)->get();
    }

    public function findByCustomerIdDTO(int $customerId): Collection
    {
        $preferences = $this->findByCustomerId($customerId);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function findByCustomerAndKey(int $customerId, string $key): ?CustomerPreference
    {
        return CustomerPreference::where('customer_id', $customerId)
            ->where('preference_key', $key)
            ->first();
    }

    public function findByCustomerAndKeyDTO(int $customerId, string $key): ?CustomerPreferenceDTO
    {
        $preference = $this->findByCustomerAndKey($customerId, $key);
        return $preference ? CustomerPreferenceDTO::fromModel($preference) : null;
    }

    public function findByKey(string $key): Collection
    {
        return CustomerPreference::where('preference_key', $key)->get();
    }

    public function findByKeyDTO(string $key): Collection
    {
        $preferences = $this->findByKey($key);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function findByType(string $type): Collection
    {
        return CustomerPreference::where('preference_type', $type)->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        $preferences = $this->findByType($type);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function findActive(): Collection
    {
        return CustomerPreference::where('is_active', true)->get();
    }

    public function findActiveDTO(): Collection
    {
        $preferences = $this->findActive();
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function findInactive(): Collection
    {
        return CustomerPreference::where('is_active', false)->get();
    }

    public function findInactiveDTO(): Collection
    {
        $preferences = $this->findInactive();
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function findByCustomerAndType(int $customerId, string $type): Collection
    {
        return CustomerPreference::where('customer_id', $customerId)
            ->where('preference_type', $type)
            ->get();
    }

    public function findByCustomerAndTypeDTO(int $customerId, string $type): Collection
    {
        $preferences = $this->findByCustomerAndType($customerId, $type);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function create(array $data): CustomerPreference
    {
        return CustomerPreference::create($data);
    }

    public function createAndReturnDTO(array $data): CustomerPreferenceDTO
    {
        $preference = $this->create($data);
        return CustomerPreferenceDTO::fromModel($preference);
    }

    public function update(CustomerPreference $preference, array $data): bool
    {
        return $preference->update($data);
    }

    public function updateAndReturnDTO(CustomerPreference $preference, array $data): ?CustomerPreferenceDTO
    {
        $updated = $this->update($preference, $data);
        return $updated ? CustomerPreferenceDTO::fromModel($preference->fresh()) : null;
    }

    public function delete(CustomerPreference $preference): bool
    {
        return $preference->delete();
    }

    public function activate(CustomerPreference $preference): bool
    {
        return $preference->update(['is_active' => true]);
    }

    public function deactivate(CustomerPreference $preference): bool
    {
        return $preference->update(['is_active' => false]);
    }

    public function setPreference(int $customerId, string $key, $value, string $type = 'string'): bool
    {
        $stringValue = $this->convertValueToString($value, $type);

        $preference = $this->findByCustomerAndKey($customerId, $key);

        if ($preference) {
            return $preference->update([
                'preference_value' => $stringValue,
                'preference_type' => $type,
                'is_active' => true,
            ]);
        }

        return (bool) $this->create([
            'customer_id' => $customerId,
            'preference_key' => $key,
            'preference_value' => $stringValue,
            'preference_type' => $type,
            'is_active' => true,
        ]);
    }

    public function setPreferenceDTO(int $customerId, string $key, $value, string $type = 'string'): ?CustomerPreferenceDTO
    {
        $success = $this->setPreference($customerId, $key, $value, $type);
        return $success ? $this->findByCustomerAndKeyDTO($customerId, $key) : null;
    }

    public function getPreference(int $customerId, string $key, $default = null): mixed
    {
        $preference = $this->findByCustomerAndKey($customerId, $key);

        if (!$preference || !$preference->is_active) {
            return $default;
        }

        return $this->convertStringToValue($preference->preference_value, $preference->preference_type);
    }

    public function getPreferenceDTO(int $customerId, string $key): ?CustomerPreferenceDTO
    {
        return $this->findByCustomerAndKeyDTO($customerId, $key);
    }

    public function removePreference(int $customerId, string $key): bool
    {
        $preference = $this->findByCustomerAndKey($customerId, $key);
        return $preference ? $preference->delete() : false;
    }

    public function hasPreference(int $customerId, string $key): bool
    {
        $preference = $this->findByCustomerAndKey($customerId, $key);
        return $preference && $preference->is_active;
    }

    public function getAllPreferences(int $customerId): array
    {
        $preferences = $this->findByCustomerId($customerId);
        $result = [];

        foreach ($preferences as $preference) {
            if ($preference->is_active) {
                $result[$preference->preference_key] = $this->convertStringToValue(
                    $preference->preference_value,
                    $preference->preference_type
                );
            }
        }

        return $result;
    }

    public function getAllPreferencesDTO(int $customerId): Collection
    {
        return $this->findByCustomerIdDTO($customerId);
    }

    public function getPreferencesByType(int $customerId, string $type): array
    {
        $preferences = $this->findByCustomerAndType($customerId, $type);
        $result = [];

        foreach ($preferences as $preference) {
            if ($preference->is_active) {
                $result[$preference->preference_key] = $this->convertStringToValue(
                    $preference->preference_value,
                    $preference->preference_type
                );
            }
        }

        return $result;
    }

    public function getPreferencesByTypeDTO(int $customerId, string $type): Collection
    {
        return $this->findByCustomerAndTypeDTO($customerId, $type);
    }

    public function getPreferenceCount(): int
    {
        return CustomerPreference::count();
    }

    public function getPreferenceCountByCustomer(int $customerId): int
    {
        return CustomerPreference::where('customer_id', $customerId)->count();
    }

    public function getPreferenceCountByType(string $type): int
    {
        return CustomerPreference::where('preference_type', $type)->count();
    }

    public function getActivePreferenceCount(): int
    {
        return CustomerPreference::where('is_active', true)->count();
    }

    public function getInactivePreferenceCount(): int
    {
        return CustomerPreference::where('is_active', false)->count();
    }

    public function search(string $query): Collection
    {
        return CustomerPreference::where(function($q) use ($query) {
            $q->where('preference_key', 'like', "%{$query}%")
              ->orWhere('preference_value', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })->get();
    }

    public function searchDTO(string $query): Collection
    {
        $preferences = $this->search($query);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function searchByCustomer(int $customerId, string $query): Collection
    {
        return CustomerPreference::where('customer_id', $customerId)
            ->where(function($q) use ($query) {
                $q->where('preference_key', 'like', "%{$query}%")
                  ->orWhere('preference_value', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })->get();
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        $preferences = $this->searchByCustomer($customerId, $query);
        return $preferences->map(fn($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    public function getPopularPreferences(int $limit = 10): array
    {
        return CustomerPreference::select('preference_key', DB::raw('count(*) as count'))
            ->groupBy('preference_key')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('count', 'preference_key')
            ->toArray();
    }

    public function getPopularPreferencesByType(string $type, int $limit = 10): array
    {
        return CustomerPreference::where('preference_type', $type)
            ->select('preference_key', DB::raw('count(*) as count'))
            ->groupBy('preference_key')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('count', 'preference_key')
            ->toArray();
    }

    public function getCustomerPreferenceStats(int $customerId): array
    {
        $preferences = $this->findByCustomerId($customerId);

        return [
            'total_preferences' => $preferences->count(),
            'active_preferences' => $preferences->where('is_active', true)->count(),
            'inactive_preferences' => $preferences->where('is_active', false)->count(),
            'preferences_by_type' => $preferences->groupBy('preference_type')->map->count(),
            'preferences_by_category' => $preferences->groupBy(function($pref) {
                return explode('.', $pref->preference_key)[0] ?? 'general';
            })->map->count(),
        ];
    }

    public function getPreferenceStats(): array
    {
        return [
            'total_preferences' => $this->getPreferenceCount(),
            'active_preferences' => $this->getActivePreferenceCount(),
            'inactive_preferences' => $this->getInactivePreferenceCount(),
            'preferences_by_type' => CustomerPreference::select('preference_type', DB::raw('count(*) as count'))
                ->groupBy('preference_type')
                ->pluck('count', 'preference_type')
                ->toArray(),
            'preferences_by_category' => CustomerPreference::select(
                DB::raw('SUBSTRING_INDEX(preference_key, ".", 1) as category'),
                DB::raw('count(*) as count')
            )
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
        ];
    }

    public function getPreferenceStatsByType(): array
    {
        return CustomerPreference::select('preference_type', DB::raw('count(*) as count'))
            ->groupBy('preference_type')
            ->pluck('count', 'preference_type')
            ->toArray();
    }

    public function validatePreference(array $data): bool
    {
        $rules = CustomerPreferenceDTO::rules();

        foreach ($rules as $field => $fieldRules) {
            if (!isset($data[$field]) && in_array('required', $fieldRules)) {
                return false;
            }

            if (isset($data[$field])) {
                foreach ($fieldRules as $rule) {
                    if (!$this->validateRule($rule, $data[$field])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getDefaultPreferences(): array
    {
        return [
            'ui.theme' => ['value' => 'light', 'type' => 'string', 'description' => 'User interface theme preference'],
            'ui.language' => ['value' => 'en', 'type' => 'string', 'description' => 'User interface language preference'],
            'notifications.email' => ['value' => 'true', 'type' => 'boolean', 'description' => 'Email notification preference'],
            'notifications.sms' => ['value' => 'false', 'type' => 'boolean', 'description' => 'SMS notification preference'],
            'privacy.data_sharing' => ['value' => 'false', 'type' => 'boolean', 'description' => 'Data sharing preference'],
            'shopping.currency' => ['value' => 'USD', 'type' => 'string', 'description' => 'Preferred currency for shopping'],
            'shopping.items_per_page' => ['value' => '20', 'type' => 'integer', 'description' => 'Number of items per page'],
        ];
    }

    public function resetCustomerPreferences(int $customerId): bool
    {
        return CustomerPreference::where('customer_id', $customerId)->delete();
    }

    public function resetCustomerPreferencesByType(int $customerId, string $type): bool
    {
        return CustomerPreference::where('customer_id', $customerId)
            ->where('preference_type', $type)
            ->delete();
    }

    public function importPreferences(int $customerId, array $preferences): bool
    {
        DB::beginTransaction();

        try {
            foreach ($preferences as $key => $preference) {
                $value = is_array($preference) ? $preference['value'] : $preference;
                $type = is_array($preference) ? ($preference['type'] ?? 'string') : 'string';
                $description = is_array($preference) ? ($preference['description'] ?? null) : null;

                $this->setPreference($customerId, $key, $value, $type);

                if ($description) {
                    $pref = $this->findByCustomerAndKey($customerId, $key);
                    if ($pref) {
                        $pref->update(['description' => $description]);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function exportPreferences(int $customerId): array
    {
        $preferences = $this->findByCustomerId($customerId);
        $result = [];

        foreach ($preferences as $preference) {
            $result[$preference->preference_key] = [
                'value' => $this->convertStringToValue($preference->preference_value, $preference->preference_type),
                'type' => $preference->preference_type,
                'description' => $preference->description,
                'is_active' => $preference->is_active,
                'metadata' => $preference->metadata,
            ];
        }

        return $result;
    }

    public function syncPreferences(int $customerId, array $preferences): bool
    {
        DB::beginTransaction();

        try {
            // Get existing preferences
            $existing = $this->findByCustomerId($customerId)->keyBy('preference_key');

            // Update or create new preferences
            foreach ($preferences as $key => $preference) {
                $value = is_array($preference) ? $preference['value'] : $preference;
                $type = is_array($preference) ? ($preference['type'] ?? 'string') : 'string';
                $description = is_array($preference) ? ($preference['description'] ?? null) : null;

                if ($existing->has($key)) {
                    $existingPref = $existing->get($key);
                    $existingPref->update([
                        'preference_value' => $this->convertValueToString($value, $type),
                        'preference_type' => $type,
                        'description' => $description,
                    ]);
                } else {
                    $this->setPreference($customerId, $key, $value, $type);
                    if ($description) {
                        $pref = $this->findByCustomerAndKey($customerId, $key);
                        if ($pref) {
                            $pref->update(['description' => $description]);
                        }
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getPreferenceHistory(int $customerId, string $key): Collection
    {
        // This would typically query a history/audit table
        // For now, return empty collection as history tracking would need additional implementation
        return new Collection();
    }

    public function getPreferenceHistoryDTO(int $customerId, string $key): Collection
    {
        $history = $this->getPreferenceHistory($customerId, $key);
        return $history->map(fn($item) => CustomerPreferenceDTO::fromModel($item));
    }

    // Helper methods
    private function convertValueToString($value, string $type): string
    {
        return match($type) {
            'string' => (string) $value,
            'integer' => (string) (int) $value,
            'float' => (string) (float) $value,
            'boolean' => $value ? 'true' : 'false',
            'json', 'array', 'object' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };
    }

    private function convertStringToValue(string $value, string $type): mixed
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

    private function validateRule(string $rule, $value): bool
    {
        // Basic validation implementation
        // In a real application, you'd use Laravel's Validator
        return match($rule) {
            'required' => !empty($value),
            'string' => is_string($value),
            'integer' => is_numeric($value) && floor($value) == $value,
            'boolean' => is_bool($value) || in_array($value, ['true', 'false', '1', '0']),
            default => true,
        };
    }
}

