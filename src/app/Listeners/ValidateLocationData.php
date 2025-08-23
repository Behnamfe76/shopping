<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationUpdated;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ValidateLocationData implements ShouldQueue
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

            $this->validateLocationIntegrity($location);
            $this->validateProviderConstraints($location);
            $this->validateGeographicData($location);
            $this->validateContactInformation($location);

            Log::info('Location data validation completed for location creation', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to validate location data for location creation', [
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

            // Validate based on what changed
            if (isset($changes['is_primary'])) {
                $this->validatePrimaryLocationConstraints($location);
            }

            if (isset($changes['address']) || isset($changes['city']) || isset($changes['state']) || isset($changes['country'])) {
                $this->validateGeographicData($location);
            }

            if (isset($changes['phone']) || isset($changes['email']) || isset($changes['website'])) {
                $this->validateContactInformation($location);
            }

            $this->validateLocationIntegrity($location);

            Log::info('Location data validation completed for location update', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to validate location data for location update', [
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

            $this->validateProviderConstraintsAfterDeletion($location);
            $this->validatePrimaryLocationAfterDeletion($location);

            Log::info('Location data validation completed for location deletion', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to validate location data for location deletion', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate basic location integrity
     */
    protected function validateLocationIntegrity($location): void
    {
        try {
            $errors = [];

            // Check required fields
            if (empty($location->location_name)) {
                $errors[] = 'Location name is required';
            }

            if (empty($location->address)) {
                $errors[] = 'Address is required';
            }

            if (empty($location->city)) {
                $errors[] = 'City is required';
            }

            if (empty($location->country)) {
                $errors[] = 'Country is required';
            }

            // Check coordinate validity
            if ($location->latitude !== null && ($location->latitude < -90 || $location->latitude > 90)) {
                $errors[] = 'Invalid latitude value';
            }

            if ($location->longitude !== null && ($location->longitude < -180 || $location->longitude > 180)) {
                $errors[] = 'Invalid longitude value';
            }

            // Log validation errors if any
            if (!empty($errors)) {
                Log::warning('Location data validation errors found', [
                    'location_id' => $location->id,
                    'errors' => $errors
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to validate location integrity', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate provider constraints
     */
    protected function validateProviderConstraints($location): void
    {
        try {
            // Check if provider exists
            $provider = DB::table('providers')->where('id', $location->provider_id)->first();

            if (!$provider) {
                Log::error('Provider not found for location', [
                    'location_id' => $location->id,
                    'provider_id' => $location->provider_id
                ]);
                return;
            }

            // Check if provider is active
            if (!$provider->is_active) {
                Log::warning('Location created for inactive provider', [
                    'location_id' => $location->id,
                    'provider_id' => $location->provider_id
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to validate provider constraints', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate primary location constraints
     */
    protected function validatePrimaryLocationConstraints($location): void
    {
        try {
            if ($location->is_primary) {
                // Check if another primary location exists
                $existingPrimary = DB::table('provider_locations')
                    ->where('provider_id', $location->provider_id)
                    ->where('is_primary', 1)
                    ->where('id', '!=', $location->id)
                    ->first();

                if ($existingPrimary) {
                    Log::error('Multiple primary locations found for provider', [
                        'location_id' => $location->id,
                        'provider_id' => $location->provider_id,
                        'existing_primary_id' => $existingPrimary->id
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error('Failed to validate primary location constraints', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate geographic data
     */
    protected function validateGeographicData($location): void
    {
        try {
            $errors = [];

            // Check if coordinates are provided when address is complete
            $hasCompleteAddress = !empty($location->address) && !empty($location->city) && !empty($location->country);
            $hasCoordinates = $location->latitude !== null && $location->longitude !== null;

            if ($hasCompleteAddress && !$hasCoordinates) {
                Log::info('Location has complete address but no coordinates - geocoding may be needed', [
                    'location_id' => $location->id
                ]);
            }

            // Validate postal code format if provided
            if (!empty($location->postal_code)) {
                if (!$this->isValidPostalCode($location->postal_code, $location->country)) {
                    $errors[] = 'Invalid postal code format for country';
                }
            }

            // Log validation errors if any
            if (!empty($errors)) {
                Log::warning('Geographic data validation errors found', [
                    'location_id' => $location->id,
                    'errors' => $errors
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to validate geographic data', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate contact information
     */
    protected function validateContactInformation($location): void
    {
        try {
            $errors = [];

            // Validate phone number if provided
            if (!empty($location->phone) && !$this->isValidPhoneNumber($location->phone)) {
                $errors[] = 'Invalid phone number format';
            }

            // Validate email if provided
            if (!empty($location->email) && !filter_var($location->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }

            // Validate website if provided
            if (!empty($location->website) && !filter_var($location->website, FILTER_VALIDATE_URL)) {
                $errors[] = 'Invalid website URL format';
            }

            // Log validation errors if any
            if (!empty($errors)) {
                Log::warning('Contact information validation errors found', [
                    'location_id' => $location->id,
                    'errors' => $errors
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to validate contact information', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate provider constraints after deletion
     */
    protected function validateProviderConstraintsAfterDeletion($location): void
    {
        try {
            // Check if provider still has locations
            $remainingLocations = DB::table('provider_locations')
                ->where('provider_id', $location->provider_id)
                ->count();

            if ($remainingLocations === 0) {
                Log::info('Provider has no remaining locations after deletion', [
                    'provider_id' => $location->provider_id,
                    'deleted_location_id' => $location->id
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to validate provider constraints after deletion', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate primary location after deletion
     */
    protected function validatePrimaryLocationAfterDeletion($location): void
    {
        try {
            if ($location->is_primary) {
                // Check if another primary location exists
                $remainingPrimary = DB::table('provider_locations')
                    ->where('provider_id', $location->provider_id)
                    ->where('is_primary', 1)
                    ->first();

                if (!$remainingPrimary) {
                    Log::warning('No primary location remaining after deletion', [
                        'provider_id' => $location->provider_id,
                        'deleted_location_id' => $location->id
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error('Failed to validate primary location after deletion', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate postal code format
     */
    protected function isValidPostalCode(string $postalCode, string $country): bool
    {
        // This is a basic validation - you might want to implement country-specific validation
        return preg_match('/^[A-Z0-9\s\-]{3,10}$/i', $postalCode);
    }

    /**
     * Validate phone number format
     */
    protected function isValidPhoneNumber(string $phone): bool
    {
        // Basic phone number validation - you might want to implement country-specific validation
        return preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone));
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, Exception $exception): void
    {
        $locationId = $event->providerLocation->id ?? 'unknown';
        $providerId = $event->providerLocation->provider_id ?? 'unknown';

        Log::error('Location data validation job failed', [
            'location_id' => $locationId,
            'provider_id' => $providerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
