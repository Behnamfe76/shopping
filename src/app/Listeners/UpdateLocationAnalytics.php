<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationDeleted;
use Fereydooni\Shopping\app\Events\Provider\PrimaryLocationChanged;
use Fereydooni\Shopping\app\Events\Provider\LocationCoordinatesUpdated;
use Fereydooni\Shopping\app\Events\Provider\LocationOperatingHoursUpdated;
use Fereydooni\Shopping\app\Events\Provider\LocationGeocoded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateLocationAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle location created event.
     */
    public function handleLocationCreated(ProviderLocationCreated $event): void
    {
        try {
            $location = $event->providerLocation;

            $this->updateLocationCounts($location->provider_id);
            $this->updateLocationTypeDistribution($location->provider_id);
            $this->updateGeographicDistribution($location->provider_id);
            $this->updateActiveStatusDistribution($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Location analytics updated for location creation', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location analytics for location creation', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle location updated event.
     */
    public function handleLocationUpdated(ProviderLocationUpdated $event): void
    {
        try {
            $location = $event->providerLocation;
            $changes = $event->changes;

            // Update relevant analytics based on what changed
            if (isset($changes['is_active'])) {
                $this->updateActiveStatusDistribution($location->provider_id);
            }

            if (isset($changes['location_type'])) {
                $this->updateLocationTypeDistribution($location->provider_id);
            }

            if (isset($changes['country']) || isset($changes['state']) || isset($changes['city'])) {
                $this->updateGeographicDistribution($location->provider_id);
            }

            if (isset($changes['latitude']) || isset($changes['longitude'])) {
                $this->updateGeospatialAnalytics($location->provider_id);
            }

            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Location analytics updated for location update', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
                'changes' => array_keys($changes)
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location analytics for location update', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle location deleted event.
     */
    public function handleLocationDeleted(ProviderLocationDeleted $event): void
    {
        try {
            $location = $event->providerLocation;

            $this->updateLocationCounts($location->provider_id);
            $this->updateLocationTypeDistribution($location->provider_id);
            $this->updateGeographicDistribution($location->provider_id);
            $this->updateActiveStatusDistribution($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Location analytics updated for location deletion', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location analytics for location deletion', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle primary location changed event.
     */
    public function handlePrimaryLocationChanged(PrimaryLocationChanged $event): void
    {
        try {
            $location = $event->newPrimaryLocation;

            $this->updatePrimaryLocationAnalytics($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Primary location analytics updated', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update primary location analytics', [
                'location_id' => $event->newPrimaryLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle coordinates updated event.
     */
    public function handleCoordinatesUpdated(LocationCoordinatesUpdated $event): void
    {
        try {
            $location = $event->providerLocation;

            $this->updateGeospatialAnalytics($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Geospatial analytics updated for coordinates change', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update geospatial analytics for coordinates change', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle operating hours updated event.
     */
    public function handleOperatingHoursUpdated(LocationOperatingHoursUpdated $event): void
    {
        try {
            $location = $event->providerLocation;

            $this->updateOperatingHoursAnalytics($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Operating hours analytics updated', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update operating hours analytics', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle location geocoded event.
     */
    public function handleLocationGeocoded(LocationGeocoded $event): void
    {
        try {
            $location = $event->providerLocation;

            $this->updateGeocodingAnalytics($location->provider_id);
            $this->clearAnalyticsCache($location->provider_id);

            Log::info('Geocoding analytics updated', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update geocoding analytics', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update location counts for a provider
     */
    protected function updateLocationCounts(int $providerId): void
    {
        try {
            $counts = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->selectRaw('
                    COUNT(*) as total_locations,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_locations,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_locations,
                    SUM(CASE WHEN is_primary = 1 THEN 1 ELSE 0 END) as primary_locations,
                    SUM(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 ELSE 0 END) as locations_with_coordinates
                ')
                ->first();

            // Store in cache for quick access
            Cache::put("provider.{$providerId}.location_counts", $counts, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update location counts', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update location type distribution for a provider
     */
    protected function updateLocationTypeDistribution(int $providerId): void
    {
        try {
            $distribution = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->selectRaw('location_type, COUNT(*) as count')
                ->groupBy('location_type')
                ->get()
                ->keyBy('location_type')
                ->map(function ($item) {
                    return $item->count;
                })
                ->toArray();

            Cache::put("provider.{$providerId}.location_type_distribution", $distribution, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update location type distribution', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update geographic distribution for a provider
     */
    protected function updateGeographicDistribution(int $providerId): void
    {
        try {
            $geographic = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->selectRaw('
                    country,
                    state,
                    city,
                    COUNT(*) as count
                ')
                ->groupBy('country', 'state', 'city')
                ->get();

            Cache::put("provider.{$providerId}.geographic_distribution", $geographic, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update geographic distribution', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update active status distribution for a provider
     */
    protected function updateActiveStatusDistribution(int $providerId): void
    {
        try {
            $distribution = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->selectRaw('
                    is_active,
                    COUNT(*) as count
                ')
                ->groupBy('is_active')
                ->get()
                ->keyBy('is_active')
                ->map(function ($item) {
                    return $item->count;
                })
                ->toArray();

            Cache::put("provider.{$providerId}.active_status_distribution", $distribution, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update active status distribution', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update primary location analytics for a provider
     */
    protected function updatePrimaryLocationAnalytics(int $providerId): void
    {
        try {
            $primaryLocation = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->where('is_primary', 1)
                ->first();

            if ($primaryLocation) {
                Cache::put("provider.{$providerId}.primary_location", $primaryLocation, 3600);
            }

        } catch (Exception $e) {
            Log::error('Failed to update primary location analytics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update geospatial analytics for a provider
     */
    protected function updateGeospatialAnalytics(int $providerId): void
    {
        try {
            $geospatial = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw('
                    COUNT(*) as locations_with_coordinates,
                    AVG(latitude) as avg_latitude,
                    AVG(longitude) as avg_longitude,
                    MIN(latitude) as min_latitude,
                    MAX(latitude) as max_latitude,
                    MIN(longitude) as min_longitude,
                    MAX(longitude) as max_longitude
                ')
                ->first();

            Cache::put("provider.{$providerId}.geospatial_analytics", $geospatial, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update geospatial analytics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update operating hours analytics for a provider
     */
    protected function updateOperatingHoursAnalytics(int $providerId): void
    {
        try {
            $operatingHours = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->whereNotNull('operating_hours')
                ->selectRaw('
                    COUNT(*) as locations_with_hours,
                    COUNT(CASE WHEN JSON_LENGTH(operating_hours) > 0 THEN 1 END) as locations_with_detailed_hours
                ')
                ->first();

            Cache::put("provider.{$providerId}.operating_hours_analytics", $operatingHours, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update operating hours analytics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update geocoding analytics for a provider
     */
    protected function updateGeocodingAnalytics(int $providerId): void
    {
        try {
            $geocoding = DB::table('provider_locations')
                ->where('provider_id', $providerId)
                ->selectRaw('
                    COUNT(*) as total_locations,
                    COUNT(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 END) as geocoded_locations,
                    COUNT(CASE WHEN latitude IS NULL OR longitude IS NULL THEN 1 END) as ungeocoded_locations
                ')
                ->first();

            if ($geocoding->total_locations > 0) {
                $geocoding->geocoding_percentage = round(($geocoding->geocoded_locations / $geocoding->total_locations) * 100, 2);
            } else {
                $geocoding->geocoding_percentage = 0;
            }

            Cache::put("provider.{$providerId}.geocoding_analytics", $geocoding, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update geocoding analytics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear analytics cache for a provider
     */
    protected function clearAnalyticsCache(int $providerId): void
    {
        try {
            Cache::forget("provider.{$providerId}.location_counts");
            Cache::forget("provider.{$providerId}.location_type_distribution");
            Cache::forget("provider.{$providerId}.geographic_distribution");
            Cache::forget("provider.{$providerId}.active_status_distribution");
            Cache::forget("provider.{$providerId}.primary_location");
            Cache::forget("provider.{$providerId}.geospatial_analytics");
            Cache::forget("provider.{$providerId}.operating_hours_analytics");
            Cache::forget("provider.{$providerId}.geocoding_analytics");
        } catch (Exception $e) {
            Log::error('Failed to clear analytics cache', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, Exception $exception): void
    {
        $locationId = $event->providerLocation->id ?? 'unknown';
        $providerId = $event->providerLocation->provider_id ?? 'unknown';

        Log::error('Location analytics update job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
