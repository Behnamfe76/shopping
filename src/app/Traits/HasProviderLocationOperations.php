<?php

namespace Fereydooni\Shopping\app\Traits;

use Exception;
use Fereydooni\Shopping\app\DTOs\ProviderLocationDTO;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasProviderLocationOperations
{
    /**
     * Get all provider locations
     */
    public function getAllProviderLocations(): Collection
    {
        try {
            return ProviderLocation::with(['provider'])->get();
        } catch (Exception $e) {
            Log::error('Failed to get all provider locations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get paginated provider locations
     */
    public function getPaginatedProviderLocations(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return ProviderLocation::with(['provider'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get paginated provider locations: '.$e->getMessage());

            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    /**
     * Get simple paginated provider locations
     */
    public function getSimplePaginatedProviderLocations(int $perPage = 15): Paginator
    {
        try {
            return ProviderLocation::with(['provider'])
                ->orderBy('created_at', 'desc')
                ->simplePaginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get simple paginated provider locations: '.$e->getMessage());

            return new Paginator([], $perPage);
        }
    }

    /**
     * Get cursor paginated provider locations
     */
    public function getCursorPaginatedProviderLocations(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        try {
            $query = ProviderLocation::with(['provider'])
                ->orderBy('id', 'asc');

            if ($cursor) {
                $query->where('id', '>', $cursor);
            }

            return $query->cursorPaginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get cursor paginated provider locations: '.$e->getMessage());

            return new CursorPaginator([], $perPage);
        }
    }

    /**
     * Find provider location by ID
     */
    public function findProviderLocation(int $id): ?ProviderLocation
    {
        try {
            return ProviderLocation::with(['provider'])->find($id);
        } catch (Exception $e) {
            Log::error("Failed to find provider location with ID {$id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Find provider location by ID and return DTO
     */
    public function findProviderLocationDTO(int $id): ?ProviderLocationDTO
    {
        try {
            $location = $this->findProviderLocation($id);

            return $location ? ProviderLocationDTO::fromModel($location) : null;
        } catch (Exception $e) {
            Log::error("Failed to find provider location DTO with ID {$id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Create new provider location
     */
    public function createProviderLocation(array $data): ?ProviderLocation
    {
        try {
            DB::beginTransaction();

            $location = ProviderLocation::create($data);

            // If this is the first location for the provider, make it primary
            if (! ProviderLocation::where('provider_id', $data['provider_id'])->exists()) {
                $location->update(['is_primary' => true]);
            }

            DB::commit();

            return $location->load('provider');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider location: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Create new provider location and return DTO
     */
    public function createProviderLocationDTO(array $data): ?ProviderLocationDTO
    {
        try {
            $location = $this->createProviderLocation($data);

            return $location ? ProviderLocationDTO::fromModel($location) : null;
        } catch (Exception $e) {
            Log::error('Failed to create provider location DTO: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Update provider location
     */
    public function updateProviderLocation(ProviderLocation $location, array $data): bool
    {
        try {
            DB::beginTransaction();

            $location->update($data);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update provider location with ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Update provider location and return DTO
     */
    public function updateProviderLocationDTO(ProviderLocation $location, array $data): ?ProviderLocationDTO
    {
        try {
            if ($this->updateProviderLocation($location, $data)) {
                $location->refresh();

                return ProviderLocationDTO::fromModel($location);
            }

            return null;
        } catch (Exception $e) {
            Log::error("Failed to update provider location DTO with ID {$location->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Delete provider location
     */
    public function deleteProviderLocation(ProviderLocation $location): bool
    {
        try {
            DB::beginTransaction();

            // If this is the primary location, we need to handle it
            if ($location->is_primary) {
                $this->handlePrimaryLocationDeletion($location);
            }

            $location->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete provider location with ID {$location->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Handle primary location deletion by setting another location as primary
     */
    protected function handlePrimaryLocationDeletion(ProviderLocation $location): void
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
     * Search provider locations
     */
    public function searchProviderLocations(string $query, int $limit = 10): Collection
    {
        try {
            return ProviderLocation::with(['provider'])
                ->where(function ($q) use ($query) {
                    $q->where('location_name', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%")
                        ->orWhere('city', 'like', "%{$query}%")
                        ->orWhere('state', 'like', "%{$query}%")
                        ->orWhere('postal_code', 'like', "%{$query}%")
                        ->orWhere('country', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                })
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to search provider locations with query '{$query}': ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider locations by provider ID
     */
    public function getProviderLocationsByProvider(int $providerId): Collection
    {
        try {
            return ProviderLocation::where('provider_id', $providerId)
                ->with(['provider'])
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get provider locations for provider ID {$providerId}: ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get active provider locations
     */
    public function getActiveProviderLocations(): Collection
    {
        try {
            return ProviderLocation::where('is_active', true)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to get active provider locations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get inactive provider locations
     */
    public function getInactiveProviderLocations(): Collection
    {
        try {
            return ProviderLocation::where('is_active', false)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to get inactive provider locations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider locations by location type
     */
    public function getProviderLocationsByType(string $locationType): Collection
    {
        try {
            return ProviderLocation::where('location_type', $locationType)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get provider locations by type '{$locationType}': ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider locations by country
     */
    public function getProviderLocationsByCountry(string $country): Collection
    {
        try {
            return ProviderLocation::where('country', $country)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get provider locations by country '{$country}': ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider locations by state
     */
    public function getProviderLocationsByState(string $state): Collection
    {
        try {
            return ProviderLocation::where('state', $state)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get provider locations by state '{$state}': ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider locations by city
     */
    public function getProviderLocationsByCity(string $city): Collection
    {
        try {
            return ProviderLocation::where('city', $city)
                ->with(['provider'])
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to get provider locations by city '{$city}': ".$e->getMessage());

            return collect();
        }
    }

    /**
     * Get provider location count
     */
    public function getProviderLocationCount(): int
    {
        try {
            return ProviderLocation::count();
        } catch (Exception $e) {
            Log::error('Failed to get provider location count: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Get provider location count by provider
     */
    public function getProviderLocationCountByProvider(int $providerId): int
    {
        try {
            return ProviderLocation::where('provider_id', $providerId)->count();
        } catch (Exception $e) {
            Log::error("Failed to get provider location count for provider ID {$providerId}: ".$e->getMessage());

            return 0;
        }
    }
}
