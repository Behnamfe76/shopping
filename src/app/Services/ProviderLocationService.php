<?php

namespace Fereydooni\Shopping\App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderLocationRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderLocation;
use Fereydooni\Shopping\App\DTOs\ProviderLocationDTO;
use Fereydooni\Shopping\App\Events\ProviderLocationCreated;
use Fereydooni\Shopping\App\Events\ProviderLocationUpdated;
use Fereydooni\Shopping\App\Events\ProviderLocationDeleted;
use Fereydooni\Shopping\App\Events\PrimaryLocationChanged;
use Fereydooni\Shopping\App\Events\LocationCoordinatesUpdated;
use Fereydooni\Shopping\App\Events\LocationOperatingHoursUpdated;
use Fereydooni\Shopping\App\Events\LocationGeocoded;
use Fereydooni\Shopping\App\Enums\LocationType;
use Fereydooni\Shopping\App\Enums\Country;

class ProviderLocationService
{
    protected $repository;

    public function __construct(ProviderLocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    // Find operations
    public function find(int $id): ?ProviderLocation
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProviderLocationDTO
    {
        return $this->repository->findDTO($id);
    }

    // Find by provider
    public function findByProviderId(int $providerId): Collection
    {
        return $this->repository->findByProviderId($providerId);
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        return $this->repository->findByProviderIdDTO($providerId);
    }

    // Find by location type
    public function findByLocationType(string $locationType): Collection
    {
        return $this->repository->findByLocationType($locationType);
    }

    public function findByLocationTypeDTO(string $locationType): Collection
    {
        return $this->repository->findByLocationTypeDTO($locationType);
    }

    // Find by location
    public function findByCountry(string $country): Collection
    {
        return $this->repository->findByCountry($country);
    }

    public function findByCountryDTO(string $country): Collection
    {
        return $this->repository->findByCountryDTO($country);
    }

    public function findByState(string $state): Collection
    {
        return $this->repository->findByState($state);
    }

    public function findByStateDTO(string $state): Collection
    {
        return $this->repository->findByStateDTO($state);
    }

    public function findByCity(string $city): Collection
    {
        return $this->repository->findByCity($city);
    }

    public function findByCityDTO(string $city): Collection
    {
        return $this->repository->findByCityDTO($city);
    }

    public function findByPostalCode(string $postalCode): Collection
    {
        return $this->repository->findByPostalCode($postalCode);
    }

    public function findByPostalCodeDTO(string $postalCode): Collection
    {
        return $this->repository->findByPostalCodeDTO($postalCode);
    }

    // Find by contact information
    public function findByPhone(string $phone): ?ProviderLocation
    {
        return $this->repository->findByPhone($phone);
    }

    public function findByPhoneDTO(string $phone): ?ProviderLocationDTO
    {
        return $this->repository->findByPhoneDTO($phone);
    }

    public function findByEmail(string $email): ?ProviderLocation
    {
        return $this->repository->findByEmail($email);
    }

    public function findByEmailDTO(string $email): ?ProviderLocationDTO
    {
        return $this->repository->findByEmailDTO($email);
    }

    public function findByWebsite(string $website): ?ProviderLocation
    {
        return $this->repository->findByWebsite($website);
    }

    public function findByWebsiteDTO(string $website): ?ProviderLocationDTO
    {
        return $this->repository->findByWebsiteDTO($website);
    }

    // Find by status
    public function findPrimary(int $providerId): ?ProviderLocation
    {
        return $this->repository->findPrimary($providerId);
    }

    public function findPrimaryDTO(int $providerId): ?ProviderLocationDTO
    {
        return $this->repository->findPrimaryDTO($providerId);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findInactive(): Collection
    {
        return $this->repository->findInactive();
    }

    public function findInactiveDTO(): Collection
    {
        return $this->repository->findInactiveDTO();
    }

    // Find by combinations
    public function findByProviderAndType(int $providerId, string $locationType): Collection
    {
        return $this->repository->findByProviderAndType($providerId, $locationType);
    }

    public function findByProviderAndTypeDTO(int $providerId, string $locationType): Collection
    {
        return $this->repository->findByProviderAndTypeDTO($providerId, $locationType);
    }

    public function findByProviderAndStatus(int $providerId, bool $isActive): Collection
    {
        return $this->repository->findByProviderAndStatus($providerId, $isActive);
    }

    public function findByProviderAndStatusDTO(int $providerId, bool $isActive): Collection
    {
        return $this->repository->findByProviderAndStatusDTO($providerId, $isActive);
    }

    // Geospatial operations
    public function findByCoordinates(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->repository->findByCoordinates($latitude, $longitude, $radius);
    }

    public function findByCoordinatesDTO(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->repository->findByCoordinatesDTO($latitude, $longitude, $radius);
    }

    public function findByAddress(string $address): Collection
    {
        return $this->repository->findByAddress($address);
    }

    public function findByAddressDTO(string $address): Collection
    {
        return $this->repository->findByAddressDTO($address);
    }

    public function findNearby(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->repository->findNearby($latitude, $longitude, $radius);
    }

    public function findNearbyDTO(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->repository->findNearbyDTO($latitude, $longitude, $radius);
    }

    // Operating hours and timezone
    public function findByOperatingHours(string $dayOfWeek, string $time): Collection
    {
        return $this->repository->findByOperatingHours($dayOfWeek, $time);
    }

    public function findByOperatingHoursDTO(string $dayOfWeek, string $time): Collection
    {
        return $this->repository->findByOperatingHoursDTO($dayOfWeek, $time);
    }

    public function findByTimezone(string $timezone): Collection
    {
        return $this->repository->findByTimezone($timezone);
    }

    public function findByTimezoneDTO(string $timezone): Collection
    {
        return $this->repository->findByTimezoneDTO($timezone);
    }

    // Create and update operations
    public function create(array $data): ProviderLocation
    {
        $this->validateLocationData($data);
        $this->validateBusinessRules($data);

        $location = $this->repository->create($data);

        Event::dispatch(new ProviderLocationCreated($location));

        return $location;
    }

    public function createAndReturnDTO(array $data): ProviderLocationDTO
    {
        $location = $this->create($data);
        return ProviderLocationDTO::fromModel($location);
    }

    public function update(ProviderLocation $providerLocation, array $data): bool
    {
        $this->validateLocationData($data, $providerLocation->id);
        $this->validateBusinessRules($data, $providerLocation);

        $oldData = $providerLocation->toArray();
        $result = $this->repository->update($providerLocation, $data);

        if ($result) {
            Event::dispatch(new ProviderLocationUpdated($providerLocation, $oldData));
        }

        return $result;
    }

    public function updateAndReturnDTO(ProviderLocation $providerLocation, array $data): ?ProviderLocationDTO
    {
        $result = $this->update($providerLocation, $data);
        return $result ? ProviderLocationDTO::fromModel($providerLocation->fresh()) : null;
    }

    // Delete operations
    public function delete(ProviderLocation $providerLocation): bool
    {
        $result = $this->repository->delete($providerLocation);

        if ($result) {
            Event::dispatch(new ProviderLocationDeleted($providerLocation));
        }

        return $result;
    }

    // Status management
    public function activate(ProviderLocation $providerLocation): bool
    {
        $result = $this->repository->activate($providerLocation);

        if ($result) {
            Event::dispatch(new ProviderLocationUpdated($providerLocation, ['is_active' => false]));
        }

        return $result;
    }

    public function deactivate(ProviderLocation $providerLocation): bool
    {
        $result = $this->repository->deactivate($providerLocation);

        if ($result) {
            Event::dispatch(new ProviderLocationUpdated($providerLocation, ['is_active' => true]));
        }

        return $result;
    }

    public function setPrimary(ProviderLocation $providerLocation): bool
    {
        $oldPrimary = $this->findPrimary($providerLocation->provider_id);
        $result = $this->repository->setPrimary($providerLocation);

        if ($result) {
            Event::dispatch(new PrimaryLocationChanged($providerLocation, $oldPrimary));
        }

        return $result;
    }

    public function unsetPrimary(ProviderLocation $providerLocation): bool
    {
        $result = $this->repository->unsetPrimary($providerLocation);

        if ($result) {
            Event::dispatch(new PrimaryLocationChanged(null, $providerLocation));
        }

        return $result;
    }

    // Specific updates
    public function updateCoordinates(ProviderLocation $providerLocation, float $latitude, float $longitude): bool
    {
        $this->validateCoordinates($latitude, $longitude);

        $oldCoordinates = [
            'latitude' => $providerLocation->latitude,
            'longitude' => $providerLocation->longitude,
        ];

        $result = $this->repository->updateCoordinates($providerLocation, $latitude, $longitude);

        if ($result) {
            Event::dispatch(new LocationCoordinatesUpdated($providerLocation, $oldCoordinates, ['latitude' => $latitude, 'longitude' => $longitude]));
        }

        return $result;
    }

    public function updateOperatingHours(ProviderLocation $providerLocation, array $operatingHours): bool
    {
        $this->validateOperatingHours($operatingHours);

        $oldHours = $providerLocation->operating_hours;
        $result = $this->repository->updateOperatingHours($providerLocation, $operatingHours);

        if ($result) {
            Event::dispatch(new LocationOperatingHoursUpdated($providerLocation, $oldHours, $operatingHours));
        }

        return $result;
    }

    public function updateContactInfo(ProviderLocation $providerLocation, array $contactInfo): bool
    {
        $this->validateContactInfo($contactInfo);

        $oldContactInfo = [
            'contact_person' => $providerLocation->contact_person,
            'contact_phone' => $providerLocation->contact_phone,
            'contact_email' => $providerLocation->contact_email,
        ];

        $result = $this->repository->updateContactInfo($providerLocation, $contactInfo);

        if ($result) {
            Event::dispatch(new ProviderLocationUpdated($providerLocation, $oldContactInfo));
        }

        return $result;
    }

    // Count operations
    public function getLocationCount(int $providerId): int
    {
        return $this->repository->getLocationCount($providerId);
    }

    public function getLocationCountByType(int $providerId, string $locationType): int
    {
        return $this->repository->getLocationCountByType($providerId, $locationType);
    }

    public function getLocationCountByCountry(int $providerId, string $country): int
    {
        return $this->repository->getLocationCountByCountry($providerId, $country);
    }

    public function getLocationCountByState(int $providerId, string $state): int
    {
        return $this->repository->getLocationCountByState($providerId, $state);
    }

    public function getLocationCountByCity(int $providerId, string $city): int
    {
        return $this->repository->getLocationCountByCity($providerId, $city);
    }

    public function getActiveLocationCount(int $providerId): int
    {
        return $this->repository->getActiveLocationCount($providerId);
    }

    public function getInactiveLocationCount(int $providerId): int
    {
        return $this->repository->getInactiveLocationCount($providerId);
    }

    public function getPrimaryLocationCount(int $providerId): int
    {
        return $this->repository->getPrimaryLocationCount($providerId);
    }

    // Global counts
    public function getTotalLocationCount(): int
    {
        return $this->repository->getTotalLocationCount();
    }

    public function getTotalLocationCountByType(string $locationType): int
    {
        return $this->repository->getTotalLocationCountByType($locationType);
    }

    public function getTotalLocationCountByCountry(string $country): int
    {
        return $this->repository->getTotalLocationCountByCountry($country);
    }

    public function getTotalLocationCountByState(string $state): int
    {
        return $this->repository->getTotalLocationCountByState($state);
    }

    public function getTotalLocationCountByCity(string $city): int
    {
        return $this->repository->getTotalLocationCountByCity($city);
    }

    public function getTotalActiveLocationCount(): int
    {
        return $this->repository->getTotalActiveLocationCount();
    }

    public function getTotalInactiveLocationCount(): int
    {
        return $this->repository->getTotalInactiveLocationCount();
    }

    public function getTotalPrimaryLocationCount(): int
    {
        return $this->repository->getTotalPrimaryLocationCount();
    }

    // Distance-based operations
    public function getLocationsByDistance(float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->repository->getLocationsByDistance($latitude, $longitude, $limit);
    }

    public function getLocationsByDistanceDTO(float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->repository->getLocationsByDistanceDTO($latitude, $longitude, $limit);
    }

    public function getLocationsByDistanceForProvider(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->repository->getLocationsByDistanceForProvider($providerId, $latitude, $longitude, $limit);
    }

    public function getLocationsByDistanceForProviderDTO(int $providerId, float $latitude, float $longitude, int $limit = 10): Collection
    {
        return $this->repository->getLocationsByDistanceForProviderDTO($providerId, $latitude, $longitude, $limit);
    }

    // Search operations
    public function searchLocations(string $query): Collection
    {
        return $this->repository->searchLocations($query);
    }

    public function searchLocationsDTO(string $query): Collection
    {
        return $this->repository->searchLocationsDTO($query);
    }

    public function searchLocationsByProvider(int $providerId, string $query): Collection
    {
        return $this->repository->searchLocationsByProvider($providerId, $query);
    }

    public function searchLocationsByProviderDTO(int $providerId, string $query): Collection
    {
        return $this->repository->searchLocationsByProviderDTO($providerId, $query);
    }

    // Analytics operations
    public function getLocationAnalytics(int $providerId): array
    {
        return $this->repository->getLocationAnalytics($providerId);
    }

    public function getLocationAnalyticsByType(int $providerId, string $locationType): array
    {
        return $this->repository->getLocationAnalyticsByType($providerId, $locationType);
    }

    public function getLocationAnalyticsByCountry(int $providerId, string $country): array
    {
        return $this->repository->getLocationAnalyticsByCountry($providerId, $country);
    }

    public function getLocationAnalyticsByState(int $providerId, string $state): array
    {
        return $this->repository->getLocationAnalyticsByState($providerId, $state);
    }

    public function getLocationAnalyticsByCity(int $providerId, string $city): array
    {
        return $this->repository->getLocationAnalyticsByCity($providerId, $city);
    }

    // Global analytics
    public function getGlobalLocationAnalytics(): array
    {
        return $this->repository->getGlobalLocationAnalytics();
    }

    public function getGlobalLocationAnalyticsByType(string $locationType): array
    {
        return $this->repository->getGlobalLocationAnalyticsByType($locationType);
    }

    public function getGlobalLocationAnalyticsByCountry(string $country): array
    {
        return $this->repository->getGlobalLocationAnalyticsByCountry($country);
    }

    public function getGlobalLocationAnalyticsByState(string $state): array
    {
        return $this->repository->getGlobalLocationAnalyticsByState($state);
    }

    public function getGlobalLocationAnalyticsByCity(string $city): array
    {
        return $this->repository->getGlobalLocationAnalyticsByCity($city);
    }

    // Distribution and heatmap
    public function getLocationDistribution(int $providerId): array
    {
        return $this->repository->getLocationDistribution($providerId);
    }

    public function getGlobalLocationDistribution(): array
    {
        return $this->repository->getGlobalLocationDistribution();
    }

    public function getLocationHeatmap(int $providerId): array
    {
        return $this->repository->getLocationHeatmap($providerId);
    }

    public function getGlobalLocationHeatmap(): array
    {
        return $this->repository->getGlobalLocationHeatmap();
    }

    // Validation methods
    private function validateLocationData(array $data, ?int $locationId = null): void
    {
        $rules = ProviderLocationDTO::rules();

        if ($locationId) {
            // Remove unique constraints for updates
            unset($rules['provider_id']);
        }

        $validator = Validator::make($data, $rules, ProviderLocationDTO::messages());

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    private function validateBusinessRules(array $data, ?ProviderLocation $existingLocation = null): void
    {
        // Validate primary location constraints
        if (isset($data['is_primary']) && $data['is_primary']) {
            $this->validatePrimaryLocationConstraint($data['provider_id'], $existingLocation);
        }

        // Validate coordinates consistency
        if (isset($data['latitude']) || isset($data['longitude'])) {
            $this->validateCoordinatesConsistency($data);
        }

        // Validate operating hours structure
        if (isset($data['operating_hours'])) {
            $this->validateOperatingHoursStructure($data['operating_hours']);
        }
    }

    private function validatePrimaryLocationConstraint(int $providerId, ?ProviderLocation $existingLocation): void
    {
        $existingPrimary = $this->findPrimary($providerId);

        if ($existingPrimary && (!$existingLocation || $existingPrimary->id !== $existingLocation->id)) {
            throw new \InvalidArgumentException('Provider already has a primary location. Only one primary location is allowed per provider.');
        }
    }

    private function validateCoordinatesConsistency(array $data): void
    {
        $hasLatitude = isset($data['latitude']);
        $hasLongitude = isset($data['longitude']);

        if ($hasLatitude !== $hasLongitude) {
            throw new \InvalidArgumentException('Both latitude and longitude must be provided together.');
        }
    }

    private function validateCoordinates(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90 degrees.');
        }

        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180 degrees.');
        }
    }

    private function validateOperatingHours(array $operatingHours): void
    {
        $this->validateOperatingHoursStructure($operatingHours);
    }

    private function validateOperatingHoursStructure(array $operatingHours): void
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($operatingHours as $day => $hours) {
            if (!in_array(strtolower($day), $validDays)) {
                throw new \InvalidArgumentException("Invalid day of week: {$day}");
            }

            if (!is_array($hours)) {
                throw new \InvalidArgumentException("Operating hours for {$day} must be an array");
            }

            foreach ($hours as $time) {
                if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                    throw new \InvalidArgumentException("Invalid time format for {$day}: {$time}");
                }
            }
        }
    }

    private function validateContactInfo(array $contactInfo): void
    {
        $rules = [];

        if (isset($contactInfo['contact_person'])) {
            $rules['contact_person'] = 'string|min:2|max:255';
        }

        if (isset($contactInfo['contact_phone'])) {
            $rules['contact_phone'] = 'string|min:10|max:20|regex:/^[\+]?[1-9][\d]{0,15}$/';
        }

        if (isset($contactInfo['contact_email'])) {
            $rules['contact_email'] = 'email|max:255';
        }

        if (!empty($rules)) {
            $validator = Validator::make($contactInfo, $rules);

            if ($validator->fails()) {
                throw new \InvalidArgumentException($validator->errors()->first());
            }
        }
    }

    // Geocoding methods
    public function geocodeLocation(ProviderLocation $providerLocation): bool
    {
        try {
            $address = $providerLocation->getFullAddress();
            $coordinates = $this->geocodeAddress($address);

            if ($coordinates) {
                $this->updateCoordinates($providerLocation, $coordinates['latitude'], $coordinates['longitude']);
                Event::dispatch(new LocationGeocoded($providerLocation, $coordinates));
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Geocoding failed for location: ' . $providerLocation->id, [
                'error' => $e->getMessage(),
                'address' => $providerLocation->getFullAddress(),
            ]);

            return false;
        }
    }

    private function geocodeAddress(string $address): ?array
    {
        // This is a placeholder implementation
        // In a real application, you would integrate with a geocoding service like Google Maps, OpenStreetMap, etc.

        // For now, return null to indicate geocoding is not available
        return null;
    }

    // Distance calculation methods
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles;
    }

    public function calculateDistanceInKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $miles = $this->calculateDistance($lat1, $lon1, $lat2, $lon2);
        return $miles * 1.609344;
    }

    // Utility methods
    public function getLocationTypes(): array
    {
        return LocationType::labels();
    }

    public function getCountries(): array
    {
        return Country::names();
    }

    public function getTimezones(): array
    {
        return \DateTimeZone::listIdentifiers();
    }
}
