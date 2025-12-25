<?php

namespace Fereydooni\Shopping\App\Facades;

use Fereydooni\Shopping\App\Services\ProviderLocationService;
use Illuminate\Support\Facades\Facade;

/**
 * ProviderLocation Facade
 *
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation|null find(int $id)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null findDTO(int $id)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderId(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderIdDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByLocationType(string $locationType)
 * @method static \Illuminate\Database\Eloquent\Collection findByLocationTypeDTO(string $locationType)
 * @method static \Illuminate\Database\Eloquent\Collection findByCountry(string $country)
 * @method static \Illuminate\Database\Eloquent\Collection findByCountryDTO(string $country)
 * @method static \Illuminate\Database\Eloquent\Collection findByState(string $state)
 * @method static \Illuminate\Database\Eloquent\Collection findByStateDTO(string $state)
 * @method static \Illuminate\Database\Eloquent\Collection findByCity(string $city)
 * @method static \Illuminate\Database\Eloquent\Collection findByCityDTO(string $city)
 * @method static \Illuminate\Database\Eloquent\Collection findByPostalCode(string $postalCode)
 * @method static \Illuminate\Database\Eloquent\Collection findByPostalCodeDTO(string $postalCode)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation|null findByPhone(string $phone)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null findByPhoneDTO(string $phone)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation|null findByEmail(string $email)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null findByEmailDTO(string $email)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation|null findByWebsite(string $website)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null findByWebsiteDTO(string $website)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation|null findPrimary(int $providerId)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null findPrimaryDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection findActive()
 * @method static \Illuminate\Database\Eloquent\Collection findActiveDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findInactive()
 * @method static \Illuminate\Database\Eloquent\Collection findInactiveDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndType(int $providerId, string $locationType)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndTypeDTO(int $providerId, string $locationType)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndStatus(int $providerId, bool $isActive)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndStatusDTO(int $providerId, bool $isActive)
 * @method static \Illuminate\Database\Eloquent\Collection findByCoordinates(float $latitude, float $longitude, float $radius = 10)
 * @method static \Illuminate\Database\Eloquent\Collection findByCoordinatesDTO(float $latitude, float $longitude, float $radius = 10)
 * @method static \Illuminate\Database\Eloquent\Collection findByAddress(string $address)
 * @method static \Illuminate\Database\Eloquent\Collection findByAddressDTO(string $address)
 * @method static \Illuminate\Database\Eloquent\Collection findNearby(float $latitude, float $longitude, float $radius = 10)
 * @method static \Illuminate\Database\Eloquent\Collection findNearbyDTO(float $latitude, float $longitude, float $radius = 10)
 * @method static \Illuminate\Database\Eloquent\Collection findByOperatingHours(string $dayOfWeek, string $time)
 * @method static \Illuminate\Database\Eloquent\Collection findByOperatingHoursDTO(string $dayOfWeek, string $time)
 * @method static \Illuminate\Database\Eloquent\Collection findByTimezone(string $timezone)
 * @method static \Illuminate\Database\Eloquent\Collection findByTimezoneDTO(string $timezone)
 * @method static \Fereydooni\Shopping\App\Models\ProviderLocation create(array $data)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO createAndReturnDTO(array $data)
 * @method static bool update(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation, array $data)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null updateAndReturnDTO(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation, array $data)
 * @method static bool delete(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static bool activate(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static bool deactivate(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static bool setPrimary(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static bool unsetPrimary(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static bool updateCoordinates(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation, float $latitude, float $longitude)
 * @method static bool updateOperatingHours(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation, array $operatingHours)
 * @method static bool updateContactInfo(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation, array $contactInfo)
 * @method static int getLocationCount(int $providerId)
 * @method static int getLocationCountByType(int $providerId, string $locationType)
 * @method static int getLocationCountByCountry(int $providerId, string $country)
 * @method static int getLocationCountByState(int $providerId, string $state)
 * @method static int getLocationCountByCity(int $providerId, string $city)
 * @method static int getActiveLocationCount(int $providerId)
 * @method static int getInactiveLocationCount(int $providerId)
 * @method static int getPrimaryLocationCount(int $providerId)
 * @method static int getTotalLocationCount()
 * @method static int getTotalLocationCountByType(string $locationType)
 * @method static int getTotalLocationCountByCountry(string $country)
 * @method static int getTotalLocationCountByState(string $state)
 * @method static int getTotalLocationCountByCity(string $city)
 * @method static int getTotalActiveLocationCount()
 * @method static int getTotalInactiveLocationCount()
 * @method static int getTotalPrimaryLocationCount()
 * @method static \Illuminate\Database\Eloquent\Collection getLocationsByDistance(float $latitude, float $longitude, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getLocationsByDistanceDTO(float $latitude, float $longitude, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getLocationsByDistanceForProvider(int $providerId, float $latitude, float $longitude, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getLocationsByDistanceForProviderDTO(int $providerId, float $latitude, float $longitude, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection searchLocations(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchLocationsDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchLocationsByProvider(int $providerId, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchLocationsByProviderDTO(int $providerId, string $query)
 * @method static array getLocationAnalytics(int $providerId)
 * @method static array getLocationAnalyticsByType(int $providerId, string $locationType)
 * @method static array getLocationAnalyticsByCountry(int $providerId, string $country)
 * @method static array getLocationAnalyticsByState(int $providerId, string $state)
 * @method static array getLocationAnalyticsByCity(int $providerId, string $city)
 * @method static array getGlobalLocationAnalytics()
 * @method static array getGlobalLocationAnalyticsByType(string $locationType)
 * @method static array getGlobalLocationAnalyticsByCountry(string $country)
 * @method static array getGlobalLocationAnalyticsByState(string $state)
 * @method static array getGlobalLocationAnalyticsByCity(string $city)
 * @method static array getLocationDistribution(int $providerId)
 * @method static array getGlobalLocationDistribution()
 * @method static array getLocationHeatmap(int $providerId)
 * @method static array getGlobalLocationHeatmap()
 * @method static bool geocodeLocation(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
 * @method static float calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2)
 * @method static float calculateDistanceInKm(float $lat1, float $lon1, float $lat2, float $lon2)
 * @method static array getLocationTypes()
 * @method static array getCountries()
 * @method static array getTimezones()
 *
 * @see \Fereydooni\Shopping\App\Services\ProviderLocationService
 */
class ProviderLocation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ProviderLocationService::class;
    }

    /**
     * Get the service instance.
     *
     * @return \Fereydooni\Shopping\App\Services\ProviderLocationService
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get the service instance.
     *
     * @return \Fereydooni\Shopping\App\Services\ProviderLocationService
     */
    public static function service()
    {
        return static::getFacadeRoot();
    }

    /**
     * Get the repository instance.
     *
     * @return \Fereydooni\Shopping\App\Repositories\Interfaces\ProviderLocationRepositoryInterface
     */
    public static function repository()
    {
        return static::getFacadeRoot()->repository;
    }

    /**
     * Get the model instance.
     *
     * @return \Fereydooni\Shopping\App\Models\ProviderLocation
     */
    public static function model()
    {
        return new \Fereydooni\Shopping\App\Models\ProviderLocation;
    }

    /**
     * Get the DTO instance.
     *
     * @return \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO
     */
    public static function dto()
    {
        return new \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO;
    }

    /**
     * Get the enum instances.
     *
     * @return array
     */
    public static function enums()
    {
        return [
            'location_types' => \Fereydooni\Shopping\App\Enums\LocationType::class,
            'countries' => \Fereydooni\Shopping\App\Enums\Country::class,
        ];
    }

    /**
     * Get the location types.
     *
     * @return array
     */
    public static function locationTypes()
    {
        return \Fereydooni\Shopping\App\Enums\LocationType::labels();
    }

    /**
     * Get the countries.
     *
     * @return array
     */
    public static function countries()
    {
        return \Fereydooni\Shopping\App\Enums\Country::names();
    }

    /**
     * Get the timezones.
     *
     * @return array
     */
    public static function timezones()
    {
        return \DateTimeZone::listIdentifiers();
    }

    /**
     * Get the location types with descriptions.
     *
     * @return array
     */
    public static function locationTypesWithDescriptions()
    {
        $types = [];
        foreach (\Fereydooni\Shopping\App\Enums\LocationType::cases() as $type) {
            $types[$type->value] = [
                'label' => $type->label(),
                'description' => $type->description(),
            ];
        }

        return $types;
    }

    /**
     * Get the countries with additional information.
     *
     * @return array
     */
    public static function countriesWithInfo()
    {
        $countries = [];
        foreach (\Fereydooni\Shopping\App\Enums\Country::cases() as $country) {
            $countries[$country->value] = [
                'name' => $country->name(),
                'flag' => $country->flag(),
                'currency' => $country->currency(),
            ];
        }

        return $countries;
    }

    /**
     * Check if a location exists.
     */
    public static function exists(int $id): bool
    {
        return static::find($id) !== null;
    }

    /**
     * Check if a provider has locations.
     */
    public static function providerHasLocations(int $providerId): bool
    {
        return static::getLocationCount($providerId) > 0;
    }

    /**
     * Check if a provider has a primary location.
     */
    public static function providerHasPrimaryLocation(int $providerId): bool
    {
        return static::getPrimaryLocationCount($providerId) > 0;
    }

    /**
     * Check if a provider has active locations.
     */
    public static function providerHasActiveLocations(int $providerId): bool
    {
        return static::getActiveLocationCount($providerId) > 0;
    }

    /**
     * Get the primary location for a provider.
     *
     * @return \Fereydooni\Shopping\App\Models\ProviderLocation|null
     */
    public static function getPrimaryLocation(int $providerId)
    {
        return static::findPrimary($providerId);
    }

    /**
     * Get the primary location DTO for a provider.
     *
     * @return \Fereydooni\Shopping\App\DTOs\ProviderLocationDTO|null
     */
    public static function getPrimaryLocationDTO(int $providerId)
    {
        return static::findPrimaryDTO($providerId);
    }

    /**
     * Get active locations for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveLocations(int $providerId)
    {
        return static::findByProviderAndStatus($providerId, true);
    }

    /**
     * Get active locations DTO for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveLocationsDTO(int $providerId)
    {
        return static::findByProviderAndStatusDTO($providerId, true);
    }

    /**
     * Get inactive locations for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getInactiveLocations(int $providerId)
    {
        return static::findByProviderAndStatus($providerId, false);
    }

    /**
     * Get inactive locations DTO for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getInactiveLocationsDTO(int $providerId)
    {
        return static::findByProviderAndStatusDTO($providerId, false);
    }

    /**
     * Get locations by type for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLocationsByType(int $providerId, string $locationType)
    {
        return static::findByProviderAndType($providerId, $locationType);
    }

    /**
     * Get locations by type DTO for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLocationsByTypeDTO(int $providerId, string $locationType)
    {
        return static::findByProviderAndTypeDTO($providerId, $locationType);
    }

    /**
     * Get locations by country for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLocationsByCountry(int $providerId, string $country)
    {
        return static::findByProviderAndType($providerId, $country);
    }

    /**
     * Get locations by country DTO for a provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLocationsByCountryDTO(int $providerId, string $country)
    {
        return static::findByProviderAndTypeDTO($providerId, $country);
    }

    /**
     * Get nearby locations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNearbyLocations(float $latitude, float $longitude, float $radius = 10, int $limit = 10)
    {
        return static::getLocationsByDistance($latitude, $longitude, $limit);
    }

    /**
     * Get nearby locations DTO.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNearbyLocationsDTO(float $latitude, float $longitude, float $radius = 10, int $limit = 10)
    {
        return static::getLocationsByDistanceDTO($latitude, $longitude, $limit);
    }

    /**
     * Get nearby locations for a specific provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNearbyLocationsForProvider(int $providerId, float $latitude, float $longitude, float $radius = 10, int $limit = 10)
    {
        return static::getLocationsByDistanceForProvider($providerId, $latitude, $longitude, $limit);
    }

    /**
     * Get nearby locations DTO for a specific provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNearbyLocationsForProviderDTO(int $providerId, float $latitude, float $longitude, float $radius = 10, int $limit = 10)
    {
        return static::getLocationsByDistanceForProviderDTO($providerId, $latitude, $longitude, $limit);
    }

    /**
     * Search locations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function search(string $query)
    {
        return static::searchLocations($query);
    }

    /**
     * Search locations DTO.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchDTO(string $query)
    {
        return static::searchLocationsDTO($query);
    }

    /**
     * Search locations for a specific provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchForProvider(int $providerId, string $query)
    {
        return static::searchLocationsByProvider($providerId, $query);
    }

    /**
     * Search locations DTO for a specific provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchForProviderDTO(int $providerId, string $query)
    {
        return static::searchLocationsByProviderDTO($providerId, $query);
    }

    /**
     * Get analytics for a provider.
     *
     * @return array
     */
    public static function getAnalytics(int $providerId)
    {
        return static::getLocationAnalytics($providerId);
    }

    /**
     * Get global analytics.
     *
     * @return array
     */
    public static function getGlobalAnalytics()
    {
        return static::getGlobalLocationAnalytics();
    }

    /**
     * Get distribution for a provider.
     *
     * @return array
     */
    public static function getDistribution(int $providerId)
    {
        return static::getLocationDistribution($providerId);
    }

    /**
     * Get global distribution.
     *
     * @return array
     */
    public static function getGlobalDistribution()
    {
        return static::getGlobalLocationDistribution();
    }

    /**
     * Get heatmap for a provider.
     *
     * @return array
     */
    public static function getHeatmap(int $providerId)
    {
        return static::getLocationHeatmap($providerId);
    }

    /**
     * Get global heatmap.
     *
     * @return array
     */
    public static function getGlobalHeatmap()
    {
        return static::getGlobalLocationHeatmap();
    }

    /**
     * Geocode a location.
     *
     * @return bool
     */
    public static function geocode(\Fereydooni\Shopping\App\Models\ProviderLocation $providerLocation)
    {
        return static::geocodeLocation($providerLocation);
    }

    /**
     * Calculate distance between two points.
     *
     * @return float
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        return static::calculateDistance($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Calculate distance in kilometers between two points.
     *
     * @return float
     */
    public static function distanceInKm(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        return static::calculateDistanceInKm($lat1, $lon1, $lat2, $lon2);
    }
}
