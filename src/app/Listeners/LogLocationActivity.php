<?php

namespace Fereydooni\Shopping\app\Listeners;

use Exception;
use Fereydooni\Shopping\app\Events\Provider\LocationCoordinatesUpdated;
use Fereydooni\Shopping\app\Events\Provider\LocationGeocoded;
use Fereydooni\Shopping\app\Events\Provider\LocationOperatingHoursUpdated;
use Fereydooni\Shopping\app\Events\Provider\PrimaryLocationChanged;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationDeleted;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogLocationActivity implements ShouldQueue
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
            $user = $event->user;
            $locationData = $event->locationData;

            $this->logActivity('location_created', $location, $user, [
                'action' => 'location_created',
                'location_data' => $locationData,
                'geospatial_data' => $event->geospatialData,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log location created activity', [
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
            $user = $event->user;
            $changes = $event->changes;
            $originalData = $event->originalData;
            $newData = $event->newData;

            $this->logActivity('location_updated', $location, $user, [
                'action' => 'location_updated',
                'changes' => $changes,
                'original_data' => $originalData,
                'new_data' => $newData,
                'geospatial_changes' => $event->geospatialChanges,
                'has_significant_changes' => $event->hasSignificantChanges(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log location updated activity', [
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
            $user = $event->user;
            $locationData = $event->locationData;
            $reason = $event->reason;

            $this->logActivity('location_deleted', $location, $user, [
                'action' => 'location_deleted',
                'location_data' => $locationData,
                'geospatial_data' => $event->geospatialData,
                'reason' => $reason,
                'was_primary' => $event->wasPrimary(),
                'was_active' => $event->wasActive(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log location deleted activity', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle primary location changed event.
     */
    public function handlePrimaryLocationChanged(PrimaryLocationChanged $event): void
    {
        try {
            $newLocation = $event->newPrimaryLocation;
            $previousLocation = $event->previousPrimaryLocation;
            $user = $event->user;
            $changeData = $event->changeData;

            $this->logActivity('primary_location_changed', $newLocation, $user, [
                'action' => 'primary_location_changed',
                'change_type' => $event->determineChangeType(),
                'previous_primary_location' => $previousLocation ? [
                    'id' => $previousLocation->id,
                    'location_name' => $previousLocation->location_name,
                    'address' => $previousLocation->address,
                ] : null,
                'change_data' => $changeData,
                'is_first_primary' => $event->isFirstPrimary(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log primary location changed activity', [
                'location_id' => $event->newPrimaryLocation->id,
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
            $user = $event->user;
            $coordinateData = $event->coordinateData;

            $this->logActivity('coordinates_updated', $location, $user, [
                'action' => 'coordinates_updated',
                'coordinate_data' => $coordinateData,
                'coordinates_changed' => $event->coordinatesChanged(),
                'coordinates_added' => $event->coordinatesAdded(),
                'coordinates_removed' => $event->coordinatesRemoved(),
                'coordinates_updated' => $event->coordinatesUpdated(),
                'distance_change' => $event->getDistanceChange(),
                'update_source' => $event->getUpdateSource(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log coordinates updated activity', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
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
            $user = $event->user;
            $operatingHoursData = $event->operatingHoursData;

            $this->logActivity('operating_hours_updated', $location, $user, [
                'action' => 'operating_hours_updated',
                'operating_hours_data' => $operatingHoursData,
                'operating_hours_changed' => $event->operatingHoursChanged(),
                'has_days_added' => $event->hasDaysAdded(),
                'has_days_removed' => $event->hasDaysRemoved(),
                'has_days_closed' => $event->hasDaysClosed(),
                'has_days_opened' => $event->hasDaysOpened(),
                'has_hours_modified' => $event->hasHoursModified(),
                'changed_days_count' => $event->getChangedDaysCount(),
                'update_reason' => $event->getUpdateReason(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log operating hours updated activity', [
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
            $user = $event->user;
            $geocodingData = $event->geocodingData;

            $this->logActivity('location_geocoded', $location, $user, [
                'action' => 'location_geocoded',
                'geocoding_data' => $geocodingData,
                'geocoding_service' => $event->getGeocodingService(),
                'geocoding_method' => $event->getGeocodingMethod(),
                'coordinates_changed' => $event->coordinatesChanged(),
                'coordinates_added' => $event->coordinatesAdded(),
                'coordinates_removed' => $event->coordinatesRemoved(),
                'coordinates_updated' => $event->coordinatesUpdated(),
                'has_high_accuracy' => $event->hasHighAccuracy(),
                'distance_change' => $event->getDistanceChange(),
                'formatted_address' => $event->getFormattedAddress(),
                'place_id' => $event->getPlaceId(),
                'was_automatic' => $event->wasAutomatic(),
                'was_manual' => $event->wasManual(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log location geocoded activity', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log activity to database
     */
    protected function logActivity(string $action, $location, $user, array $details = []): void
    {
        try {
            $activityData = [
                'log_name' => 'provider_location',
                'description' => $this->getActivityDescription($action, $location),
                'subject_type' => get_class($location),
                'subject_id' => $location->id,
                'causer_type' => $user ? get_class($user) : null,
                'causer_id' => $user?->id,
                'properties' => json_encode($details),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert into activity log table
            DB::table('activity_log')->insert($activityData);

            // Also log to application log for immediate visibility
            Log::info('Location activity logged', [
                'action' => $action,
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
                'user_id' => $user?->id,
                'details' => $details,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to log activity to database', [
                'action' => $action,
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get human-readable activity description
     */
    protected function getActivityDescription(string $action, $location): string
    {
        $locationName = $location->location_name ?? 'Unknown Location';
        $providerName = $location->provider->name ?? 'Unknown Provider';

        switch ($action) {
            case 'location_created':
                return "Location '{$locationName}' was created for provider '{$providerName}'";

            case 'location_updated':
                return "Location '{$locationName}' was updated for provider '{$providerName}'";

            case 'location_deleted':
                return "Location '{$locationName}' was deleted from provider '{$providerName}'";

            case 'primary_location_changed':
                return "Location '{$locationName}' was set as primary for provider '{$providerName}'";

            case 'coordinates_updated':
                return "Coordinates were updated for location '{$locationName}'";

            case 'operating_hours_updated':
                return "Operating hours were updated for location '{$locationName}'";

            case 'location_geocoded':
                return "Location '{$locationName}' was geocoded";

            default:
                return "Activity '{$action}' performed on location '{$locationName}'";
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, Exception $exception): void
    {
        $locationId = $event->providerLocation->id ?? 'unknown';
        $providerId = $event->providerLocation->provider_id ?? 'unknown';

        Log::error('Location activity logging job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
