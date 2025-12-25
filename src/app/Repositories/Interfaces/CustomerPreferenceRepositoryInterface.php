<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface CustomerPreferenceRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?CustomerPreference;

    public function findDTO(int $id): ?CustomerPreferenceDTO;

    public function findByCustomerId(int $customerId): Collection;

    public function findByCustomerIdDTO(int $customerId): Collection;

    public function findByCustomerAndKey(int $customerId, string $key): ?CustomerPreference;

    public function findByCustomerAndKeyDTO(int $customerId, string $key): ?CustomerPreferenceDTO;

    public function findByKey(string $key): Collection;

    public function findByKeyDTO(string $key): Collection;

    public function findByType(string $type): Collection;

    public function findByTypeDTO(string $type): Collection;

    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findInactive(): Collection;

    public function findInactiveDTO(): Collection;

    public function findByCustomerAndType(int $customerId, string $type): Collection;

    public function findByCustomerAndTypeDTO(int $customerId, string $type): Collection;

    // Create and Update operations
    public function create(array $data): CustomerPreference;

    public function createAndReturnDTO(array $data): CustomerPreferenceDTO;

    public function update(CustomerPreference $preference, array $data): bool;

    public function updateAndReturnDTO(CustomerPreference $preference, array $data): ?CustomerPreferenceDTO;

    public function delete(CustomerPreference $preference): bool;

    // Status management
    public function activate(CustomerPreference $preference): bool;

    public function deactivate(CustomerPreference $preference): bool;

    // Preference key-value management
    public function setPreference(int $customerId, string $key, $value, string $type = 'string'): bool;

    public function setPreferenceDTO(int $customerId, string $key, $value, string $type = 'string'): ?CustomerPreferenceDTO;

    public function getPreference(int $customerId, string $key, $default = null): mixed;

    public function getPreferenceDTO(int $customerId, string $key): ?CustomerPreferenceDTO;

    public function removePreference(int $customerId, string $key): bool;

    public function hasPreference(int $customerId, string $key): bool;

    // Bulk preference operations
    public function getAllPreferences(int $customerId): array;

    public function getAllPreferencesDTO(int $customerId): Collection;

    public function getPreferencesByType(int $customerId, string $type): array;

    public function getPreferencesByTypeDTO(int $customerId, string $type): Collection;

    // Statistics and counts
    public function getPreferenceCount(): int;

    public function getPreferenceCountByCustomer(int $customerId): int;

    public function getPreferenceCountByType(string $type): int;

    public function getActivePreferenceCount(): int;

    public function getInactivePreferenceCount(): int;

    // Search operations
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByCustomer(int $customerId, string $query): Collection;

    public function searchByCustomerDTO(int $customerId, string $query): Collection;

    // Popular preferences
    public function getPopularPreferences(int $limit = 10): array;

    public function getPopularPreferencesByType(string $type, int $limit = 10): array;

    // Analytics and statistics
    public function getCustomerPreferenceStats(int $customerId): array;

    public function getPreferenceStats(): array;

    public function getPreferenceStatsByType(): array;

    // Validation and defaults
    public function validatePreference(array $data): bool;

    public function getDefaultPreferences(): array;

    // Reset operations
    public function resetCustomerPreferences(int $customerId): bool;

    public function resetCustomerPreferencesByType(int $customerId, string $type): bool;

    // Import/Export operations
    public function importPreferences(int $customerId, array $preferences): bool;

    public function exportPreferences(int $customerId): array;

    public function syncPreferences(int $customerId, array $preferences): bool;

    // History tracking
    public function getPreferenceHistory(int $customerId, string $key): Collection;

    public function getPreferenceHistoryDTO(int $customerId, string $key): Collection;
}
