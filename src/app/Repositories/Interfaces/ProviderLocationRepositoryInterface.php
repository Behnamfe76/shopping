<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderLocationDTO;
use Fereydooni\Shopping\App\Models\ProviderLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderLocationRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?ProviderLocation;

    public function findDTO(int $id): ?ProviderLocationDTO;

    // Find by provider
    public function findByProviderId(int $providerId): Collection;

    public function findByProviderIdDTO(int $providerId): Collection;

    // Find by location type
    public function findByLocationType(string $locationType): Collection;

    public function findByLocationTypeDTO(string $locationType): Collection;

    // Find by location
    public function findByCountry(string $country): Collection;

    public function findByCountryDTO(string $country): Collection;

    public function findByState(string $state): Collection;

    public function findByStateDTO(string $state): Collection;

    public function findByCity(string $city): Collection;

    public function findByCityDTO(string $city): Collection;

    public function findByPostalCode(string $postalCode): Collection;

    public function findByPostalCodeDTO(string $postalCode): Collection;

    // Find by contact information
    public function findByPhone(string $phone): ?ProviderLocation;

    public function findByPhoneDTO(string $phone): ?ProviderLocationDTO;

    public function findByEmail(string $email): ?ProviderLocation;

    public function findByEmailDTO(string $email): ?ProviderLocationDTO;

    public function findByWebsite(string $website): ?ProviderLocation;

    public function findByWebsiteDTO(string $website): ?ProviderLocationDTO;

    // Find by status
    public function findPrimary(int $providerId): ?ProviderLocation;

    public function findPrimaryDTO(int $providerId): ?ProviderLocationDTO;

    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findInactive(): Collection;

    public function findInactiveDTO(): Collection;

    // Find by combinations
    public function findByProviderAndType(int $providerId, string $locationType): Collection;

    public function findByProviderAndTypeDTO(int $providerId, string $locationType): Collection;

    public function findByProviderAndStatus(int $providerId, bool $isActive): Collection;

    public function findByProviderAndStatusDTO(int $providerId, bool $isActive): Collection;

    // Geospatial operations
    public function findByCoordinates(float $latitude, float $longitude, float $radius = 10): Collection;

    public function findByCoordinatesDTO(float $latitude, float $longitude, float $radius = 10): Collection;

    public function findByAddress(string $address): Collection;

    public function findByAddressDTO(string $address): Collection;

    public function findNearby(float $latitude, float $longitude, float $radius = 10): Collection;

    public function findNearbyDTO(float $latitude, float $longitude, float $radius = 10): Collection;

    // Operating hours and timezone
    public function findByOperatingHours(string $dayOfWeek, string $time): Collection;

    public function findByOperatingHoursDTO(string $dayOfWeek, string $time): Collection;

    public function findByTimezone(string $timezone): Collection;

    public function findByTimezoneDTO(string $timezone): Collection;

    // Create and update operations
    public function create(array $data): ProviderLocation;

    public function createAndReturnDTO(array $data): ProviderLocationDTO;

    public function update(ProviderLocation $providerLocation, array $data): bool;

    public function updateAndReturnDTO(ProviderLocation $providerLocation, array $data): ?ProviderLocationDTO;

    // Delete operations
    public function delete(ProviderLocation $providerLocation): bool;

    // Status management
    public function activate(ProviderLocation $providerLocation): bool;

    public function deactivate(ProviderLocation $providerLocation): bool;

    public function setPrimary(ProviderLocation $providerLocation): bool;

    public function unsetPrimary(ProviderLocation $providerLocation): bool;

    // Specific updates
    public function updateCoordinates(ProviderLocation $providerLocation, float $latitude, float $longitude): bool;

    public function updateOperatingHours(ProviderLocation $providerLocation, array $operatingHours): bool;

    public function updateContactInfo(ProviderLocation $providerLocation, array $contactInfo): bool;

    // Count operations
    public function getLocationCount(int $providerId): int;

    public function getLocationCountByType(int $providerId, string $locationType): int;

    public function getLocationCountByCountry(int $providerId, string $country): int;

    public function getLocationCountByState(int $providerId, string $state): int;

    public function getLocationCountByCity(int $providerId, string $city): int;

    public function getActiveLocationCount(int $providerId): int;

    public function getInactiveLocationCount(int $providerId): int;

    public function getPrimaryLocationCount(int $providerId): int;

    // Global counts
    public function getTotalLocationCount(): int;

    public function getTotalLocationCountByType(string $locationType): int;

    public function getTotalLocationCountByCountry(string $country): int;

    public function getTotalLocationCountByState(string $state): int;

    public function getTotalLocationCountByCity(string $city): int;

    public function getTotalActiveLocationCount(): int;

    public function getTotalInactiveLocationCount(): int;

    public function getTotalPrimaryLocationCount(): int;

    // Distance-based operations
    public function getLocationsByDistance(float $latitude, float $longitude, int $limit = 10): Collection;

    public function getLocationsByDistanceDTO(float $latitude, float $longitude, int $limit = 10): Collection;

    public function getLocationsByDistanceForProvider(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection;

    public function getLocationsByDistanceForProviderDTO(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection;

    // Search operations
    public function searchLocations(string $query): Collection;

    public function searchLocationsDTO(string $query): Collection;

    public function searchLocationsByProvider(int $providerId, string $query): Collection;

    public function searchLocationsByProviderDTO(int $providerId, string $query): Collection;

    // Analytics operations
    public function getLocationAnalytics(int $providerId): array;

    public function getLocationAnalyticsByType(int $providerId, string $locationType): array;

    public function getLocationAnalyticsByCountry(int $providerId, string $country): array;

    public function getLocationAnalyticsByState(int $providerId, string $state): array;

    public function getLocationAnalyticsByCity(int $providerId, string $city): array;

    // Global analytics
    public function getGlobalLocationAnalytics(): array;

    public function getGlobalLocationAnalyticsByType(string $locationType): array;

    public function getGlobalLocationAnalyticsByCountry(string $country): array;

    public function getGlobalLocationAnalyticsByState(string $state): array;

    public function getGlobalLocationAnalyticsByCity(string $city): array;

    // Distribution and heatmap
    public function getLocationDistribution(int $providerId): array;

    public function getGlobalLocationDistribution(): array;

    public function getLocationHeatmap(int $providerId): array;

    public function getGlobalLocationHeatmap(): array;
}
