<?php

namespace Fereydooni\Shopping\app\Listeners;

use Exception;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessLocationGeocoding implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

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

            // Check if location needs geocoding
            if ($this->needsGeocoding($location)) {
                $this->processGeocoding($location, 'automatic');
            }

            Log::info('Location geocoding processed for location creation', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to process location geocoding for location creation', [
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

            // Check if address-related fields changed and geocoding is needed
            if ($this->addressChanged($changes) && $this->needsGeocoding($location)) {
                $this->processGeocoding($location, 'automatic');
            }

            Log::info('Location geocoding processed for location update', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to process location geocoding for location update', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if location needs geocoding
     */
    protected function needsGeocoding($location): bool
    {
        // Skip if already has coordinates
        if (! is_null($location->latitude) && ! is_null($location->longitude)) {
            return false;
        }

        // Check if has address information
        $hasAddress = ! empty($location->address) ||
                     ! empty($location->city) ||
                     ! empty($location->state) ||
                     ! empty($location->country);

        return $hasAddress;
    }

    /**
     * Check if address-related fields changed
     */
    protected function addressChanged(array $changes): bool
    {
        $addressFields = ['address', 'city', 'state', 'country', 'postal_code'];

        foreach ($addressFields as $field) {
            if (isset($changes[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process geocoding for a location
     */
    protected function processGeocoding($location, string $method): void
    {
        try {
            // Build address string for geocoding
            $address = $this->buildAddressString($location);

            if (empty($address)) {
                Log::warning('Cannot process geocoding - insufficient address data', [
                    'location_id' => $location->id,
                    'address' => $address,
                ]);

                return;
            }

            // Process geocoding (this would integrate with your geocoding service)
            $geocodingResult = $this->callGeocodingService($address, $location);

            if ($geocodingResult && isset($geocodingResult['latitude']) && isset($geocodingResult['longitude'])) {
                // Update location with coordinates
                $this->updateLocationCoordinates($location, $geocodingResult, $method);

                Log::info('Location geocoding completed successfully', [
                    'location_id' => $location->id,
                    'coordinates' => [
                        'latitude' => $geocodingResult['latitude'],
                        'longitude' => $geocodingResult['longitude'],
                    ],
                    'method' => $method,
                ]);
            } else {
                Log::warning('Geocoding service returned no coordinates', [
                    'location_id' => $location->id,
                    'address' => $address,
                    'result' => $geocodingResult,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to process geocoding', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build address string for geocoding
     */
    protected function buildAddressString($location): string
    {
        $addressParts = [];

        if (! empty($location->address)) {
            $addressParts[] = $location->address;
        }

        if (! empty($location->city)) {
            $addressParts[] = $location->city;
        }

        if (! empty($location->state)) {
            $addressParts[] = $location->state;
        }

        if (! empty($location->postal_code)) {
            $addressParts[] = $location->postal_code;
        }

        if (! empty($location->country)) {
            $addressParts[] = $location->country;
        }

        return implode(', ', $addressParts);
    }

    /**
     * Call external geocoding service
     */
    protected function callGeocodingService(string $address, $location): ?array
    {
        try {
            // This is a placeholder for your actual geocoding service integration
            // You would implement calls to Google Maps API, OpenStreetMap, etc.

            // Example structure of expected response:
            $mockResult = [
                'latitude' => null,
                'longitude' => null,
                'formatted_address' => $address,
                'accuracy' => 'unknown',
                'place_id' => null,
                'verified' => false,
            ];

            // For now, return null to indicate no geocoding service is configured
            Log::info('Geocoding service called (placeholder)', [
                'location_id' => $location->id,
                'address' => $address,
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('Geocoding service call failed', [
                'location_id' => $location->id,
                'address' => $address,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update location with geocoded coordinates
     */
    protected function updateLocationCoordinates($location, array $geocodingResult, string $method): void
    {
        try {
            $updateData = [
                'latitude' => $geocodingResult['latitude'],
                'longitude' => $geocodingResult['longitude'],
                'updated_at' => now(),
            ];

            // Add additional geocoding metadata if available
            if (isset($geocodingResult['accuracy'])) {
                $updateData['geocoding_accuracy'] = $geocodingResult['accuracy'];
            }

            if (isset($geocodingResult['place_id'])) {
                $updateData['geocoding_place_id'] = $geocodingResult['place_id'];
            }

            if (isset($geocodingResult['formatted_address'])) {
                $updateData['geocoded_address'] = $geocodingResult['formatted_address'];
            }

            // Update the location
            $location->update($updateData);

        } catch (Exception $e) {
            Log::error('Failed to update location coordinates', [
                'location_id' => $location->id,
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

        Log::error('Location geocoding processing job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
