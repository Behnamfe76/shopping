<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\ProviderLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

trait HasProviderLocationGeocoding
{
    /**
     * Update coordinates for a location
     */
    public function updateCoordinates(ProviderLocation $location, float $latitude, float $longitude): bool
    {
        try {
            if (!$this->validateCoordinates($latitude, $longitude)) {
                Log::warning("Invalid coordinates provided: lat={$latitude}, lng={$longitude}");
                return false;
            }

            $location->update([
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to update coordinates for location ID {$location->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate coordinates
     */
    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        return $latitude >= -90 && $latitude <= 90 &&
               $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Find locations by coordinates within a radius
     */
    public function findLocationsByCoordinates(float $latitude, float $longitude, float $radius = 10): Collection
    {
        try {
            if (!$this->validateCoordinates($latitude, $longitude)) {
                return collect();
            }

            return ProviderLocation::whereRaw(
                'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?',
                [$longitude, $latitude, $radius * 1000] // Convert km to meters
            )
            ->with(['provider'])
            ->get();
        } catch (Exception $e) {
            Log::error("Failed to find locations by coordinates: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Find nearby locations for a specific location
     */
    public function findNearbyLocations(ProviderLocation $location, float $radius = 10): Collection
    {
        try {
            if (!$location->latitude || !$location->longitude) {
                return collect();
            }

            return $this->findLocationsByCoordinates(
                $location->latitude,
                $location->longitude,
                $radius
            )->where('id', '!=', $location->id);
        } catch (Exception $e) {
            Log::error("Failed to find nearby locations for location ID {$location->id}: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations by distance from a point
     */
    public function getLocationsByDistance(float $latitude, float $longitude, int $limit = 10): Collection
    {
        try {
            if (!$this->validateCoordinates($latitude, $longitude)) {
                return collect();
            }

            return ProviderLocation::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw(
                    'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) IS NOT NULL',
                    [$longitude, $latitude]
                )
                ->orderByRaw(
                    'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?))',
                    [$longitude, $latitude]
                )
                ->with(['provider'])
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by distance: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        try {
            $earthRadius = 6371; // Earth's radius in kilometers

            $latDelta = deg2rad($lat2 - $lat1);
            $lonDelta = deg2rad($lon2 - $lon1);

            $a = sin($latDelta / 2) * sin($latDelta / 2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($lonDelta / 2) * sin($lonDelta / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earthRadius * $c;
        } catch (Exception $e) {
            Log::error("Failed to calculate distance: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Get locations within a bounding box
     */
    public function getLocationsInBoundingBox(
        float $minLat,
        float $maxLat,
        float $minLng,
        float $maxLng
    ): Collection {
        try {
            return ProviderLocation::whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations in bounding box: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations by postal code area
     */
    public function getLocationsByPostalCode(string $postalCode): Collection
    {
        try {
            return ProviderLocation::where('postal_code', $postalCode)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by postal code '{$postalCode}': " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations by city with coordinates
     */
    public function getLocationsByCityWithCoordinates(string $city): Collection
    {
        try {
            return ProviderLocation::where('city', $city)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by city '{$city}' with coordinates: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations by state with coordinates
     */
    public function getLocationsByStateWithCoordinates(string $state): Collection
    {
        try {
            return ProviderLocation::where('state', $state)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by state '{$state}' with coordinates: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations by country with coordinates
     */
    public function getLocationsByCountryWithCoordinates(string $country): Collection
    {
        try {
            return ProviderLocation::where('country', $country)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by country '{$country}' with coordinates: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get locations without coordinates
     */
    public function getLocationsWithoutCoordinates(): Collection
    {
        try {
            return ProviderLocation::whereNull('latitude')
                ->orWhereNull('longitude')
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations without coordinates: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get coordinate statistics
     */
    public function getCoordinateStatistics(): array
    {
        try {
            $totalLocations = ProviderLocation::count();
            $locationsWithCoordinates = ProviderLocation::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();

            return [
                'total_locations' => $totalLocations,
                'locations_with_coordinates' => $locationsWithCoordinates,
                'locations_without_coordinates' => $totalLocations - $locationsWithCoordinates,
                'coverage_percentage' => $totalLocations > 0 ? round(($locationsWithCoordinates / $totalLocations) * 100, 2) : 0
            ];
        } catch (Exception $e) {
            Log::error("Failed to get coordinate statistics: " . $e->getMessage());
            return [
                'total_locations' => 0,
                'locations_with_coordinates' => 0,
                'locations_without_coordinates' => 0,
                'coverage_percentage' => 0
            ];
        }
    }

    /**
     * Get locations by distance range
     */
    public function getLocationsByDistanceRange(
        float $latitude,
        float $longitude,
        float $minDistance,
        float $maxDistance
    ): Collection {
        try {
            if (!$this->validateCoordinates($latitude, $longitude)) {
                return collect();
            }

            return ProviderLocation::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw(
                    'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) BETWEEN ? AND ?',
                    [$longitude, $latitude, $minDistance * 1000, $maxDistance * 1000]
                )
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get locations by distance range: " . $e->getMessage());
            return collect();
        }
    }
}
