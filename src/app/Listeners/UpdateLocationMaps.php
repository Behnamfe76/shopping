<?php

namespace Fereydooni\Shopping\app\Listeners;

use Exception;
use Fereydooni\Shopping\app\Events\Provider\LocationCoordinatesUpdated;
use Fereydooni\Shopping\app\Events\Provider\LocationGeocoded;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationDeleted;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateLocationMaps implements ShouldQueue
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

            $this->updateMapMarkers($location);
            $this->updateMapClusters($location->provider_id);
            $this->clearMapCache($location->provider_id);

            Log::info('Location maps updated for location creation', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location maps for location creation', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
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

            // Update maps if relevant fields changed
            if ($this->mapRelevantChanges($changes)) {
                $this->updateMapMarkers($location);
                $this->updateMapClusters($location->provider_id);
                $this->clearMapCache($location->provider_id);
            }

            Log::info('Location maps updated for location update', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location maps for location update', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
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

            $this->removeMapMarkers($location);
            $this->updateMapClusters($location->provider_id);
            $this->clearMapCache($location->provider_id);

            Log::info('Location maps updated for location deletion', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location maps for location deletion', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
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

            $this->updateMapMarkers($location);
            $this->updateMapClusters($location->provider_id);
            $this->clearMapCache($location->provider_id);

            Log::info('Location maps updated for coordinates change', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location maps for coordinates change', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
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

            $this->updateMapMarkers($location);
            $this->updateMapClusters($location->provider_id);
            $this->clearMapCache($location->provider_id);

            Log::info('Location maps updated for geocoding', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update location maps for geocoding', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if changes are relevant to map updates
     */
    protected function mapRelevantChanges(array $changes): bool
    {
        $relevantFields = [
            'location_name', 'address', 'city', 'state', 'country', 'postal_code',
            'latitude', 'longitude', 'is_active', 'location_type',
        ];

        foreach ($relevantFields as $field) {
            if (isset($changes[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update map markers for a location
     */
    protected function updateMapMarkers($location): void
    {
        try {
            // This would integrate with your map service (Google Maps, Mapbox, etc.)
            // For now, we'll just log the action

            if ($location->latitude && $location->longitude) {
                Log::info('Map marker updated', [
                    'location_id' => $location->id,
                    'coordinates' => [
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                    ],
                ]);
            } else {
                Log::info('Map marker removed (no coordinates)', [
                    'location_id' => $location->id,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to update map markers', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove map markers for a location
     */
    protected function removeMapMarkers($location): void
    {
        try {
            // This would integrate with your map service to remove markers
            Log::info('Map marker removed', [
                'location_id' => $location->id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to remove map markers', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update map clusters for a provider
     */
    protected function updateMapClusters(int $providerId): void
    {
        try {
            // This would recalculate map clusters based on location density
            // For now, we'll just log the action

            Log::info('Map clusters updated', [
                'provider_id' => $providerId,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update map clusters', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear map cache for a provider
     */
    protected function clearMapCache(int $providerId): void
    {
        try {
            Cache::forget("provider.{$providerId}.map_data");
            Cache::forget("provider.{$providerId}.map_markers");
            Cache::forget("provider.{$providerId}.map_clusters");
            Cache::forget("provider.{$providerId}.map_bounds");

        } catch (Exception $e) {
            Log::error('Failed to clear map cache', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
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

        Log::error('Location maps update job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
