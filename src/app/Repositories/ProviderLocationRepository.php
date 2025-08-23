<?php

namespace Fereydooni\Shopping\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderLocationRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderLocation;
use Fereydooni\Shopping\App\DTOs\ProviderLocationDTO;
use Fereydooni\Shopping\App\Enums\LocationType;
use Fereydooni\Shopping\App\Enums\Country;

class ProviderLocationRepository implements ProviderLocationRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'provider_location_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderLocation $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with('provider')->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('provider')->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with('provider')->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with('provider')->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // Find operations
    public function find(int $id): ?ProviderLocation
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with('provider')->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderLocationDTO
    {
        $location = $this->find($id);
        return $location ? ProviderLocationDTO::fromModel($location) : null;
    }

    // Find by provider
    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)->with('provider')->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $locations = $this->findByProviderId($providerId);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Find by location type
    public function findByLocationType(string $locationType): Collection
    {
        return $this->model->byLocationType($locationType)->with('provider')->get();
    }

    public function findByLocationTypeDTO(string $locationType): Collection
    {
        $locations = $this->findByLocationType($locationType);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Find by location
    public function findByCountry(string $country): Collection
    {
        return $this->model->byCountry($country)->with('provider')->get();
    }

    public function findByCountryDTO(string $country): Collection
    {
        $locations = $this->findByCountry($country);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByState(string $state): Collection
    {
        return $this->model->byState($state)->with('provider')->get();
    }

    public function findByStateDTO(string $state): Collection
    {
        $locations = $this->findByState($state);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByCity(string $city): Collection
    {
        return $this->model->byCity($city)->with('provider')->get();
    }

    public function findByCityDTO(string $city): Collection
    {
        $locations = $this->findByCity($city);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByPostalCode(string $postalCode): Collection
    {
        return $this->model->byPostalCode($postalCode)->with('provider')->get();
    }

    public function findByPostalCodeDTO(string $postalCode): Collection
    {
        $locations = $this->findByPostalCode($postalCode);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Find by contact information
    public function findByPhone(string $phone): ?ProviderLocation
    {
        return $this->model->byPhone($phone)->with('provider')->first();
    }

    public function findByPhoneDTO(string $phone): ?ProviderLocationDTO
    {
        $location = $this->findByPhone($phone);
        return $location ? ProviderLocationDTO::fromModel($location) : null;
    }

    public function findByEmail(string $email): ?ProviderLocation
    {
        return $this->model->byEmail($email)->with('provider')->first();
    }

    public function findByEmailDTO(string $email): ?ProviderLocationDTO
    {
        $location = $this->findByEmail($email);
        return $location ? ProviderLocationDTO::fromModel($location) : null;
    }

    public function findByWebsite(string $website): ?ProviderLocation
    {
        return $this->model->byWebsite($website)->with('provider')->first();
    }

    public function findByWebsiteDTO(string $website): ?ProviderLocationDTO
    {
        $location = $this->findByWebsite($website);
        return $location ? ProviderLocationDTO::fromModel($location) : null;
    }

    // Find by status
    public function findPrimary(int $providerId): ?ProviderLocation
    {
        return $this->model->byProvider($providerId)->primary()->with('provider')->first();
    }

    public function findPrimaryDTO(int $providerId): ?ProviderLocationDTO
    {
        $location = $this->findPrimary($providerId);
        return $location ? ProviderLocationDTO::fromModel($location) : null;
    }

    public function findActive(): Collection
    {
        return $this->model->active()->with('provider')->get();
    }

    public function findActiveDTO(): Collection
    {
        $locations = $this->findActive();
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findInactive(): Collection
    {
        return $this->model->inactive()->with('provider')->get();
    }

    public function findInactiveDTO(): Collection
    {
        $locations = $this->findInactive();
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Find by combinations
    public function findByProviderAndType(int $providerId, string $locationType): Collection
    {
        return $this->model->byProvider($providerId)->byLocationType($locationType)->with('provider')->get();
    }

    public function findByProviderAndTypeDTO(int $providerId, string $locationType): Collection
    {
        $locations = $this->findByProviderAndType($providerId, $locationType);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByProviderAndStatus(int $providerId, bool $isActive): Collection
    {
        $query = $this->model->byProvider($providerId)->with('provider');
        return $isActive ? $query->active()->get() : $query->inactive()->get();
    }

    public function findByProviderAndStatusDTO(int $providerId, bool $isActive): Collection
    {
        $locations = $this->findByProviderAndStatus($providerId, $isActive);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Geospatial operations
    public function findByCoordinates(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->model->nearby($latitude, $longitude, $radius)->with('provider')->get();
    }

    public function findByCoordinatesDTO(float $latitude, float $longitude, float $radius = 10): Collection
    {
        $locations = $this->findByCoordinates($latitude, $longitude, $radius);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByAddress(string $address): Collection
    {
        return $this->model->search($address)->with('provider')->get();
    }

    public function findByAddressDTO(string $address): Collection
    {
        $locations = $this->findByAddress($address);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findNearby(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->findByCoordinates($latitude, $longitude, $radius);
    }

    public function findNearbyDTO(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->findByCoordinatesDTO($latitude, $longitude, $radius);
    }

    // Operating hours and timezone
    public function findByOperatingHours(string $dayOfWeek, string $time): Collection
    {
        return $this->model->whereJsonContains("operating_hours->{$dayOfWeek}", $time)->with('provider')->get();
    }

    public function findByOperatingHoursDTO(string $dayOfWeek, string $time): Collection
    {
        $locations = $this->findByOperatingHours($dayOfWeek, $time);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function findByTimezone(string $timezone): Collection
    {
        return $this->model->byTimezone($timezone)->with('provider')->get();
    }

    public function findByTimezoneDTO(string $timezone): Collection
    {
        $locations = $this->findByTimezone($timezone);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Create and update operations
    public function create(array $data): ProviderLocation
    {
        try {
            DB::beginTransaction();

            $location = $this->model->create($data);

            // Clear relevant caches
            $this->clearProviderCache($location->provider_id);
            $this->clearCache();

            DB::commit();
            return $location;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderLocationDTO
    {
        $location = $this->create($data);
        return ProviderLocationDTO::fromModel($location);
    }

    public function update(ProviderLocation $providerLocation, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $providerLocation->update($data);

            // Clear relevant caches
            $this->clearProviderCache($providerLocation->provider_id);
            $this->clearCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderLocation $providerLocation, array $data): ?ProviderLocationDTO
    {
        $result = $this->update($providerLocation, $data);
        return $result ? ProviderLocationDTO::fromModel($providerLocation->fresh()) : null;
    }

    // Delete operations
    public function delete(ProviderLocation $providerLocation): bool
    {
        try {
            DB::beginTransaction();

            $providerId = $providerLocation->provider_id;
            $result = $providerLocation->delete();

            // Clear relevant caches
            $this->clearProviderCache($providerId);
            $this->clearCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Status management
    public function activate(ProviderLocation $providerLocation): bool
    {
        return $this->update($providerLocation, ['is_active' => true]);
    }

    public function deactivate(ProviderLocation $providerLocation): bool
    {
        return $this->update($providerLocation, ['is_active' => false]);
    }

    public function setPrimary(ProviderLocation $providerLocation): bool
    {
        return $providerLocation->setPrimary();
    }

    public function unsetPrimary(ProviderLocation $providerLocation): bool
    {
        return $providerLocation->unsetPrimary();
    }

    // Specific updates
    public function updateCoordinates(ProviderLocation $providerLocation, float $latitude, float $longitude): bool
    {
        return $this->update($providerLocation, [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function updateOperatingHours(ProviderLocation $providerLocation, array $operatingHours): bool
    {
        return $this->update($providerLocation, ['operating_hours' => $operatingHours]);
    }

    public function updateContactInfo(ProviderLocation $providerLocation, array $contactInfo): bool
    {
        return $this->update($providerLocation, $contactInfo);
    }

    // Count operations
    public function getLocationCount(int $providerId): int
    {
        return Cache::remember($this->cachePrefix . 'count_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)->count();
        });
    }

    public function getLocationCountByType(int $providerId, string $locationType): int
    {
        return $this->model->byProvider($providerId)->byLocationType($locationType)->count();
    }

    public function getLocationCountByCountry(int $providerId, string $country): int
    {
        return $this->model->byProvider($providerId)->byCountry($country)->count();
    }

    public function getLocationCountByState(int $providerId, string $state): int
    {
        return $this->model->byProvider($providerId)->byState($state)->count();
    }

    public function getLocationCountByCity(int $providerId, string $city): int
    {
        return $this->model->byProvider($providerId)->byCity($city)->count();
    }

    public function getActiveLocationCount(int $providerId): int
    {
        return $this->model->byProvider($providerId)->active()->count();
    }

    public function getInactiveLocationCount(int $providerId): int
    {
        return $this->model->byProvider($providerId)->inactive()->count();
    }

    public function getPrimaryLocationCount(int $providerId): int
    {
        return $this->model->byProvider($providerId)->primary()->count();
    }

    // Global counts
    public function getTotalLocationCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_count', $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalLocationCountByType(string $locationType): int
    {
        return $this->model->byLocationType($locationType)->count();
    }

    public function getTotalLocationCountByCountry(string $country): int
    {
        return $this->model->byCountry($country)->count();
    }

    public function getTotalLocationCountByState(string $state): int
    {
        return $this->model->byState($state)->count();
    }

    public function getTotalLocationCountByCity(string $city): int
    {
        return $this->model->byCity($city)->count();
    }

    public function getTotalActiveLocationCount(): int
    {
        return $this->model->active()->count();
    }

    public function getTotalInactiveLocationCount(): int
    {
        return $this->model->inactive()->count();
    }

    public function getTotalPrimaryLocationCount(): int
    {
        return $this->model->primary()->count();
    }

    // Distance-based operations
    public function getLocationsByDistance(float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->model->nearby($latitude, $longitude)->limit($limit)->with('provider')->get();
    }

    public function getLocationsByDistanceDTO(float $latitude, float $longitude, int $limit = 10): Collection
    {
        $locations = $this->getLocationsByDistance($latitude, $longitude, $limit);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function getLocationsByDistanceForProvider(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->model->byProvider($providerId)->nearby($latitude, $longitude)->limit($limit)->with('provider')->get();
    }

    public function getLocationsByDistanceForProviderDTO(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection
    {
        $locations = $this->getLocationsByDistanceForProvider($providerId, $latitude, $longitude, $limit);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Search operations
    public function searchLocations(string $query): Collection
    {
        return $this->model->search($query)->with('provider')->get();
    }

    public function searchLocationsDTO(string $query): Collection
    {
        $locations = $this->searchLocations($query);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    public function searchLocationsByProvider(int $providerId, string $query): Collection
    {
        return $this->model->byProvider($providerId)->search($query)->with('provider')->get();
    }

    public function searchLocationsByProviderDTO(int $providerId, string $query): Collection
    {
        $locations = $this->searchLocationsByProvider($providerId, $query);
        return $locations->map(fn($location) => ProviderLocationDTO::fromModel($location));
    }

    // Analytics operations
    public function getLocationAnalytics(int $providerId): array
    {
        return Cache::remember($this->cachePrefix . 'analytics_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            $locations = $this->findByProviderId($providerId);

            return [
                'total' => $locations->count(),
                'active' => $locations->where('is_active', true)->count(),
                'inactive' => $locations->where('is_active', false)->count(),
                'primary' => $locations->where('is_primary', true)->count(),
                'by_type' => $locations->groupBy('location_type')->map->count(),
                'by_country' => $locations->groupBy('country')->map->count(),
                'with_coordinates' => $locations->whereNotNull('latitude')->whereNotNull('longitude')->count(),
            ];
        });
    }

    public function getLocationAnalyticsByType(int $providerId, string $locationType): array
    {
        $locations = $this->findByProviderAndType($providerId, $locationType);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'primary' => $locations->where('is_primary', true)->count(),
        ];
    }

    public function getLocationAnalyticsByCountry(int $providerId, string $country): array
    {
        $locations = $this->findByProviderAndType($providerId, $country);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    public function getLocationAnalyticsByState(int $providerId, string $state): array
    {
        $locations = $this->model->byProvider($providerId)->byState($state)->with('provider')->get();

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    public function getLocationAnalyticsByCity(int $providerId, string $city): array
    {
        $locations = $this->model->byProvider($providerId)->byCity($city)->with('provider')->get();

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    // Global analytics
    public function getGlobalLocationAnalytics(): array
    {
        return Cache::remember($this->cachePrefix . 'global_analytics', $this->cacheTtl, function () {
            return [
                'total' => $this->getTotalLocationCount(),
                'active' => $this->getTotalActiveLocationCount(),
                'inactive' => $this->getTotalInactiveLocationCount(),
                'primary' => $this->getTotalPrimaryLocationCount(),
                'by_type' => $this->getLocationTypeDistribution(),
                'by_country' => $this->getCountryDistribution(),
            ];
        });
    }

    public function getGlobalLocationAnalyticsByType(string $locationType): array
    {
        $locations = $this->findByLocationType($locationType);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_country' => $locations->groupBy('country')->map->count(),
        ];
    }

    public function getGlobalLocationAnalyticsByCountry(string $country): array
    {
        $locations = $this->findByCountry($country);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    public function getGlobalLocationAnalyticsByState(string $state): array
    {
        $locations = $this->findByState($state);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    public function getGlobalLocationAnalyticsByCity(string $city): array
    {
        $locations = $this->findByCity($city);

        return [
            'total' => $locations->count(),
            'active' => $locations->where('is_active', true)->count(),
            'inactive' => $locations->where('is_active', false)->count(),
            'by_type' => $locations->groupBy('location_type')->map->count(),
        ];
    }

    // Distribution and heatmap
    public function getLocationDistribution(int $providerId): array
    {
        $locations = $this->findByProviderId($providerId);

        return [
            'by_type' => $locations->groupBy('location_type')->map->count(),
            'by_country' => $locations->groupBy('country')->map->count(),
            'by_state' => $locations->groupBy('state')->map->count(),
            'by_city' => $locations->groupBy('city')->map->count(),
        ];
    }

    public function getGlobalLocationDistribution(): array
    {
        return Cache::remember($this->cachePrefix . 'global_distribution', $this->cacheTtl, function () {
            $locations = $this->model->get();

            return [
                'by_type' => $locations->groupBy('location_type')->map->count(),
                'by_country' => $locations->groupBy('country')->map->count(),
                'by_state' => $locations->groupBy('state')->map->count(),
                'by_city' => $locations->groupBy('city')->map->count(),
            ];
        });
    }

    public function getLocationHeatmap(int $providerId): array
    {
        $locations = $this->model->byProvider($providerId)->withCoordinates()->get();

        return $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->location_name,
                'coordinates' => [
                    'lat' => (float) $location->latitude,
                    'lng' => (float) $location->longitude,
                ],
                'type' => $location->location_type,
                'is_active' => $location->is_active,
                'is_primary' => $location->is_primary,
            ];
        })->toArray();
    }

    public function getGlobalLocationHeatmap(): array
    {
        return Cache::remember($this->cachePrefix . 'global_heatmap', $this->cacheTtl, function () {
            $locations = $this->model->withCoordinates()->get();

            return $locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->location_name,
                    'coordinates' => [
                        'lat' => (float) $location->latitude,
                        'lng' => (float) $location->longitude,
                    ],
                    'type' => $location->location_type,
                    'is_active' => $location->is_active,
                    'is_primary' => $location->is_primary,
                    'provider_id' => $location->provider_id,
                ];
            })->toArray();
        });
    }

    // Helper methods
    private function getLocationTypeDistribution(): array
    {
        $locations = $this->model->get();
        return $locations->groupBy('location_type')->map->count();
    }

    private function getCountryDistribution(): array
    {
        $locations = $this->model->get();
        return $locations->groupBy('country')->map->count();
    }

    private function clearProviderCache(int $providerId): void
    {
        Cache::forget($this->cachePrefix . 'provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'count_provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'analytics_provider_' . $providerId);
    }

    private function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'all');
        Cache::forget($this->cachePrefix . 'total_count');
        Cache::forget($this->cachePrefix . 'global_analytics');
        Cache::forget($this->cachePrefix . 'global_distribution');
        Cache::forget($this->cachePrefix . 'global_heatmap');
    }
}
