<?php

namespace Fereydooni\Shopping\app\Traits;

use Exception;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Illuminate\Support\Facades\Log;

trait HasProviderLocationValidation
{
    /**
     * Validate location data
     */
    public function validateLocationData(array $data): array
    {
        $errors = [];

        try {
            // Required fields validation
            $requiredFields = ['provider_id', 'location_name', 'address', 'city', 'state', 'country'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $errors[$field] = "The {$field} field is required.";
                }
            }

            // Provider ID validation
            if (isset($data['provider_id']) && ! is_numeric($data['provider_id'])) {
                $errors['provider_id'] = 'Provider ID must be a valid number.';
            }

            // Phone number validation
            if (isset($data['phone']) && ! empty($data['phone'])) {
                if (! $this->validatePhoneNumber($data['phone'])) {
                    $errors['phone'] = 'Phone number format is invalid.';
                }
            }

            // Email validation
            if (isset($data['email']) && ! empty($data['email'])) {
                if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Email format is invalid.';
                }
            }

            // Website validation
            if (isset($data['website']) && ! empty($data['website'])) {
                if (! $this->validateWebsite($data['website'])) {
                    $errors['website'] = 'Website URL format is invalid.';
                }
            }

            // Coordinates validation
            if (isset($data['latitude']) && isset($data['longitude'])) {
                if (! $this->validateCoordinates($data['latitude'], $data['longitude'])) {
                    $errors['coordinates'] = 'Invalid coordinates provided.';
                }
            }

            // Postal code validation
            if (isset($data['postal_code']) && ! empty($data['postal_code'])) {
                if (! $this->validatePostalCode($data['postal_code'])) {
                    $errors['postal_code'] = 'Postal code format is invalid.';
                }
            }

            // Timezone validation
            if (isset($data['timezone']) && ! empty($data['timezone'])) {
                if (! $this->validateTimezone($data['timezone'])) {
                    $errors['timezone'] = 'Invalid timezone provided.';
                }
            }

            // Operating hours validation
            if (isset($data['operating_hours']) && ! empty($data['operating_hours'])) {
                if (! $this->validateOperatingHours($data['operating_hours'])) {
                    $errors['operating_hours'] = 'Operating hours format is invalid.';
                }
            }

        } catch (Exception $e) {
            Log::error('Error validating location data: '.$e->getMessage());
            $errors['general'] = 'An error occurred during validation.';
        }

        return $errors;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber(string $phone): bool
    {
        // Basic phone number validation - can be enhanced based on requirements
        $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);

        return strlen($phone) >= 10 && strlen($phone) <= 20;
    }

    /**
     * Validate website URL
     */
    public function validateWebsite(string $website): bool
    {
        // Add protocol if missing
        if (! preg_match('~^(?:f|ht)tps?://~i', $website)) {
            $website = 'https://'.$website;
        }

        return filter_var($website, FILTER_VALIDATE_URL) !== false;
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
     * Validate postal code
     */
    public function validatePostalCode(string $postalCode): bool
    {
        // Basic postal code validation - can be enhanced for specific countries
        return preg_match('/^[A-Z0-9\s\-]{3,10}$/i', $postalCode);
    }

    /**
     * Validate timezone
     */
    public function validateTimezone(string $timezone): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    /**
     * Validate operating hours
     */
    public function validateOperatingHours($operatingHours): bool
    {
        if (is_string($operatingHours)) {
            $operatingHours = json_decode($operatingHours, true);
        }

        if (! is_array($operatingHours)) {
            return false;
        }

        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($operatingHours as $day => $hours) {
            if (! in_array(strtolower($day), $validDays)) {
                return false;
            }

            if (is_array($hours)) {
                foreach ($hours as $timeSlot) {
                    if (! isset($timeSlot['open']) || ! isset($timeSlot['close'])) {
                        return false;
                    }

                    if (! $this->validateTimeFormat($timeSlot['open']) ||
                        ! $this->validateTimeFormat($timeSlot['close'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Validate time format (HH:MM)
     */
    public function validateTimeFormat(string $time): bool
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }

    /**
     * Check for duplicate locations
     */
    public function checkDuplicateLocation(array $data, ?int $excludeId = null): bool
    {
        try {
            $query = ProviderLocation::where('provider_id', $data['provider_id'])
                ->where('address', $data['address'])
                ->where('city', $data['city'])
                ->where('state', $data['state'])
                ->where('country', $data['country']);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            return $query->exists();
        } catch (Exception $e) {
            Log::error('Error checking for duplicate location: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Validate address components
     */
    public function validateAddressComponents(array $data): array
    {
        $errors = [];

        try {
            // Address length validation
            if (isset($data['address']) && strlen($data['address']) > 255) {
                $errors['address'] = 'Address must not exceed 255 characters.';
            }

            // City length validation
            if (isset($data['city']) && strlen($data['city']) > 100) {
                $errors['city'] = 'City must not exceed 100 characters.';
            }

            // State length validation
            if (isset($data['state']) && strlen($data['state']) > 100) {
                $errors['state'] = 'State must not exceed 100 characters.';
            }

            // Postal code length validation
            if (isset($data['postal_code']) && strlen($data['postal_code']) > 20) {
                $errors['postal_code'] = 'Postal code must not exceed 20 characters.';
            }

            // Notes length validation
            if (isset($data['notes']) && strlen($data['notes']) > 1000) {
                $errors['notes'] = 'Notes must not exceed 1000 characters.';
            }

        } catch (Exception $e) {
            Log::error('Error validating address components: '.$e->getMessage());
            $errors['general'] = 'An error occurred during address validation.';
        }

        return $errors;
    }

    /**
     * Validate location type
     */
    public function validateLocationType(string $locationType): bool
    {
        $validTypes = [
            'headquarters', 'warehouse', 'store', 'office', 'factory',
            'distribution_center', 'retail_outlet', 'service_center', 'other',
        ];

        return in_array(strtolower($locationType), $validTypes);
    }

    /**
     * Validate country code
     */
    public function validateCountry(string $country): bool
    {
        $validCountries = [
            'US', 'CA', 'MX', 'GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE',
            'CH', 'AT', 'SE', 'NO', 'DK', 'FI', 'PL', 'CZ', 'HU', 'RO',
            'BG', 'HR', 'SI', 'SK', 'EE', 'LV', 'LT', 'MT', 'CY', 'GR',
            'PT', 'IE', 'LU', 'AU', 'NZ', 'JP', 'CN', 'IN', 'BR', 'AR',
            'CL', 'PE', 'CO', 'VE', 'EC', 'BO', 'PY', 'UY', 'GY', 'SR',
            'GF', 'FK', 'ZA', 'EG', 'NG', 'KE', 'UG', 'TZ', 'ET', 'GH',
            'CI', 'SN', 'ML', 'BF', 'NE', 'TD', 'SD', 'LY', 'TN', 'DZ',
            'MA', 'MR', 'AO', 'CD', 'CG', 'GA', 'CM', 'CF', 'TD', 'SS',
        ];

        return in_array(strtoupper($country), $validCountries);
    }

    /**
     * Comprehensive location validation
     */
    public function validateLocationComprehensive(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        try {
            // Basic data validation
            $basicErrors = $this->validateLocationData($data);
            $errors = array_merge($errors, $basicErrors);

            // Address component validation
            $addressErrors = $this->validateAddressComponents($data);
            $errors = array_merge($errors, $addressErrors);

            // Location type validation
            if (isset($data['location_type']) && ! $this->validateLocationType($data['location_type'])) {
                $errors['location_type'] = 'Invalid location type provided.';
            }

            // Country validation
            if (isset($data['country']) && ! $this->validateCountry($data['country'])) {
                $errors['country'] = 'Invalid country code provided.';
            }

            // Duplicate location check
            if (empty($errors) && $this->checkDuplicateLocation($data, $excludeId)) {
                $errors['duplicate'] = 'A location with this address already exists for this provider.';
            }

        } catch (Exception $e) {
            Log::error('Error during comprehensive location validation: '.$e->getMessage());
            $errors['general'] = 'An error occurred during validation.';
        }

        return $errors;
    }

    /**
     * Sanitize location data
     */
    public function sanitizeLocationData(array $data): array
    {
        try {
            // Trim whitespace from string fields
            $stringFields = ['location_name', 'address', 'city', 'state', 'postal_code', 'phone', 'email', 'website', 'contact_person', 'contact_phone', 'contact_email', 'notes'];

            foreach ($stringFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = trim($data[$field]);
                }
            }

            // Normalize email and website
            if (isset($data['email'])) {
                $data['email'] = strtolower(trim($data['email']));
            }

            if (isset($data['website'])) {
                $data['website'] = strtolower(trim($data['website']));
            }

            // Normalize location type
            if (isset($data['location_type'])) {
                $data['location_type'] = strtolower(trim($data['location_type']));
            }

            // Normalize country
            if (isset($data['country'])) {
                $data['country'] = strtoupper(trim($data['country']));
            }

            // Normalize state
            if (isset($data['state'])) {
                $data['state'] = ucwords(strtolower(trim($data['state'])));
            }

            // Normalize city
            if (isset($data['city'])) {
                $data['city'] = ucwords(strtolower(trim($data['city'])));
            }

        } catch (Exception $e) {
            Log::error('Error sanitizing location data: '.$e->getMessage());
        }

        return $data;
    }
}
