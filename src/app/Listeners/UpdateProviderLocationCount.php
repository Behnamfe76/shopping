<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateProviderLocationCount implements ShouldQueue
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

            $this->updateProviderCounts($location->provider_id);
            $this->updateGlobalCounts();
            $this->clearCountCache($location->provider_id);

            Log::info('Provider location count updated for location creation', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update provider location count for location creation', [
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

            // Only update counts if relevant fields changed
            if (isset($changes['is_active']) || isset($changes['is_primary']) || isset($changes['location_type'])) {
                $this->updateProviderCounts($location->provider_id);
                $this->clearCountCache($location->provider_id);
            }

            Log::info('Provider location count updated for location update', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update provider location count for location update', [
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

            $this->updateProviderCounts($location->provider_id);
            $this->updateGlobalCounts();
            $this->clearCountCache($location->provider_id);

            Log::info('Provider location count updated for location deletion', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to update provider location count for location deletion', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update location counts for a specific provider
     */
    protected function updateProviderCounts(int $providerId): void
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

            // Update provider table with counts
            DB::table('providers')
                ->where('id', $providerId)
                ->update([
                    'total_locations' => $counts->total_locations,
                    'active_locations' => $counts->active_locations,
                    'inactive_locations' => $counts->inactive_locations,
                    'primary_locations' => $counts->primary_locations,
                    'locations_with_coordinates' => $counts->locations_with_coordinates,
                    'updated_at' => now()
                ]);

            // Store in cache for quick access
            Cache::put("provider.{$providerId}.location_counts", $counts, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update provider location counts', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update global location counts
     */
    protected function updateGlobalCounts(): void
    {
        try {
            $globalCounts = DB::table('provider_locations')
                ->selectRaw('
                    COUNT(*) as total_locations,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_locations,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_locations,
                    SUM(CASE WHEN is_primary = 1 THEN 1 ELSE 0 END) as primary_locations,
                    SUM(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 ELSE 0 END) as locations_with_coordinates
                ')
                ->first();

            // Store global counts in cache
            Cache::put('global.location_counts', $globalCounts, 3600);

        } catch (Exception $e) {
            Log::error('Failed to update global location counts', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear count cache for a provider
     */
    protected function clearCountCache(int $providerId): void
    {
        try {
            Cache::forget("provider.{$providerId}.location_counts");
            Cache::forget("provider.{$providerId}.location_type_counts");
            Cache::forget("provider.{$providerId}.geographic_counts");
        } catch (Exception $e) {
            Log::error('Failed to clear count cache', [
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

        Log::error('Provider location count update job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
