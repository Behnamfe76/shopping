<?php

namespace Fereydooni\Shopping\app\Traits;

use Exception;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasProviderLocationPrimaryManagement
{
    /**
     * Set a location as primary for a provider
     */
    public function setPrimaryLocation(ProviderLocation $location): bool
    {
        try {
            DB::beginTransaction();

            // First, unset any existing primary location for this provider
            ProviderLocation::where('provider_id', $location->provider_id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);

            // Set the new primary location
            $location->update(['is_primary' => true]);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to set primary location for location ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Unset primary location for a provider
     */
    public function unsetPrimaryLocation(ProviderLocation $location): bool
    {
        try {
            if (! $location->is_primary) {
                return true; // Already not primary
            }

            $location->update(['is_primary' => false]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to unset primary location for location ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get primary location for a provider
     */
    public function getPrimaryLocation(int $providerId): ?ProviderLocation
    {
        try {
            return ProviderLocation::where('provider_id', $providerId)
                ->where('is_primary', true)
                ->with(['provider'])
                ->first();
        } catch (Exception $e) {
            Log::error("Failed to get primary location for provider ID {$providerId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Check if a provider has a primary location
     */
    public function hasPrimaryLocation(int $providerId): bool
    {
        try {
            return ProviderLocation::where('provider_id', $providerId)
                ->where('is_primary', true)
                ->exists();
        } catch (Exception $e) {
            Log::error("Failed to check primary location for provider ID {$providerId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Activate a provider location
     */
    public function activateLocation(ProviderLocation $location): bool
    {
        try {
            $location->update(['is_active' => true]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to activate location ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Deactivate a provider location
     */
    public function deactivateLocation(ProviderLocation $location): bool
    {
        try {
            // If this is the primary location, we need to handle it
            if ($location->is_primary) {
                $this->handlePrimaryLocationDeactivation($location);
            }

            $location->update(['is_active' => false]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to deactivate location ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Handle primary location deactivation by setting another location as primary
     */
    protected function handlePrimaryLocationDeactivation(ProviderLocation $location): void
    {
        $alternativeLocation = ProviderLocation::where('provider_id', $location->provider_id)
            ->where('id', '!=', $location->id)
            ->where('is_active', true)
            ->first();

        if ($alternativeLocation) {
            $alternativeLocation->update(['is_primary' => true]);
        }
    }

    /**
     * Toggle location active status
     */
    public function toggleLocationStatus(ProviderLocation $location): bool
    {
        try {
            if ($location->is_active) {
                return $this->deactivateLocation($location);
            } else {
                return $this->activateLocation($location);
            }
        } catch (Exception $e) {
            Log::error("Failed to toggle status for location ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get all primary locations across all providers
     */
    public function getAllPrimaryLocations(): Collection
    {
        try {
            return ProviderLocation::where('is_primary', true)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to get all primary locations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get primary location count
     */
    public function getPrimaryLocationCount(): int
    {
        try {
            return ProviderLocation::where('is_primary', true)->count();
        } catch (Exception $e) {
            Log::error('Failed to get primary location count: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Get primary location count by provider
     */
    public function getPrimaryLocationCountByProvider(int $providerId): int
    {
        try {
            return ProviderLocation::where('provider_id', $providerId)
                ->where('is_primary', true)
                ->count();
        } catch (Exception $e) {
            Log::error("Failed to get primary location count for provider ID {$providerId}: ".$e->getMessage());

            return 0;
        }
    }

    /**
     * Ensure only one primary location per provider
     */
    public function ensureSinglePrimaryLocation(int $providerId): bool
    {
        try {
            $primaryCount = $this->getPrimaryLocationCountByProvider($providerId);

            if ($primaryCount > 1) {
                // Keep only the first primary location, unset the rest
                $primaryLocations = ProviderLocation::where('provider_id', $providerId)
                    ->where('is_primary', true)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Keep the first one, unset the rest
                for ($i = 1; $i < $primaryLocations->count(); $i++) {
                    $primaryLocations[$i]->update(['is_primary' => false]);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("Failed to ensure single primary location for provider ID {$providerId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get locations that need primary status (providers without primary locations)
     */
    public function getLocationsNeedingPrimaryStatus(): Collection
    {
        try {
            $providersWithoutPrimary = ProviderLocation::select('provider_id')
                ->groupBy('provider_id')
                ->havingRaw('SUM(CASE WHEN is_primary = 1 THEN 1 ELSE 0 END) = 0')
                ->pluck('provider_id');

            return ProviderLocation::whereIn('provider_id', $providersWithoutPrimary)
                ->where('is_active', true)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to get locations needing primary status: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Auto-assign primary locations for providers without them
     */
    public function autoAssignPrimaryLocations(): bool
    {
        try {
            $locationsNeedingPrimary = $this->getLocationsNeedingPrimaryStatus();

            foreach ($locationsNeedingPrimary->groupBy('provider_id') as $providerId => $locations) {
                if ($locations->isNotEmpty()) {
                    // Set the first active location as primary
                    $firstLocation = $locations->first();
                    $this->setPrimaryLocation($firstLocation);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('Failed to auto-assign primary locations: '.$e->getMessage());

            return false;
        }
    }
}
