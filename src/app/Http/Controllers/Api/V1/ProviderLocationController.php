<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\DTOs\ProviderLocationDTO;
use Fereydooni\Shopping\app\Services\ProviderLocationService;
use Fereydooni\Shopping\app\Http\Requests\StoreProviderLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProviderLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\SetPrimaryLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateCoordinatesRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\GeocodeLocationRequest;
use Fereydooni\Shopping\app\Http\Resources\ProviderLocationResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderLocationCollection;
use Fereydooni\Shopping\app\Http\Resources\ProviderLocationSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderLocationStatisticsResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderLocationMapResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Exception;

class ProviderLocationController extends Controller
{
    protected ProviderLocationService $providerLocationService;

    public function __construct(ProviderLocationService $providerLocationService)
    {
        $this->providerLocationService = $providerLocationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of provider locations
     */
    public function index(Request $request): JsonResource
    {
        try {
            $this->authorize('viewAny', ProviderLocation::class);

            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $providerId = $request->get('provider_id');
            $locationType = $request->get('location_type');
            $country = $request->get('country');
            $state = $request->get('state');
            $city = $request->get('city');
            $isActive = $request->get('is_active');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $locations = $this->providerLocationService->getFilteredLocations([
                'per_page' => $perPage,
                'search' => $search,
                'provider_id' => $providerId,
                'location_type' => $locationType,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'is_active' => $isActive,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);

            return new ProviderLocationCollection($locations);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading provider locations.'
            ], 500);
        }
    }

    /**
     * Store a newly created provider location
     */
    public function store(StoreProviderLocationRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', ProviderLocation::class);

            $data = $request->validated();
            $location = $this->providerLocationService->create($data);

            if ($location) {
                return response()->json([
                    'success' => true,
                    'message' => 'Provider location created successfully.',
                    'data' => new ProviderLocationResource($location)
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create provider location.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the provider location.'
            ], 500);
        }
    }

    /**
     * Display the specified provider location
     */
    public function show(ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('view', $providerLocation);

            $location = $this->providerLocationService->find($providerLocation->id);

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider location not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProviderLocationResource($location)
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading the provider location.'
            ], 500);
        }
    }

    /**
     * Update the specified provider location
     */
    public function update(UpdateProviderLocationRequest $request, ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('update', $providerLocation);

            $data = $request->validated();
            $updated = $this->providerLocationService->update($providerLocation, $data);

            if ($updated) {
                $location = $this->providerLocationService->find($providerLocation->id);
                return response()->json([
                    'success' => true,
                    'message' => 'Provider location updated successfully.',
                    'data' => new ProviderLocationResource($location)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update provider location.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the provider location.'
            ], 500);
        }
    }

    /**
     * Remove the specified provider location
     */
    public function destroy(ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('delete', $providerLocation);

            $deleted = $this->providerLocationService->delete($providerLocation);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Provider location deleted successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete provider location.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the provider location.'
            ], 500);
        }
    }

    /**
     * Set a location as primary
     */
    public function setPrimary(SetPrimaryLocationRequest $request, ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('setPrimary', $providerLocation);

            $set = $this->providerLocationService->setPrimary($providerLocation);

            if ($set) {
                return response()->json([
                    'success' => true,
                    'message' => 'Primary location set successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary location.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@setPrimary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while setting the primary location.'
            ], 500);
        }
    }

    /**
     * Update coordinates for a location
     */
    public function updateCoordinates(UpdateCoordinatesRequest $request, ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('updateCoordinates', $providerLocation);

            $data = $request->validated();
            $updated = $this->providerLocationService->updateCoordinates(
                $providerLocation,
                $data['latitude'],
                $data['longitude']
            );

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Coordinates updated successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update coordinates.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@updateCoordinates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating coordinates.'
            ], 500);
        }
    }

    /**
     * Search locations
     */
    public function search(SearchLocationRequest $request): JsonResource
    {
        try {
            $this->authorize('search', ProviderLocation::class);

            $data = $request->validated();
            $results = $this->providerLocationService->searchLocations($data['query'], $data['limit'] ?? 20);
            $searchStats = $this->providerLocationService->getSearchStatistics($data['query']);

            return new ProviderLocationSearchResource($results, $searchStats);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching locations.'
            ], 500);
        }
    }

    /**
     * Geocode a location address
     */
    public function geocode(GeocodeLocationRequest $request): JsonResponse
    {
        try {
            $this->authorize('geocode', ProviderLocation::class);

            $data = $request->validated();
            $coordinates = $this->providerLocationService->geocodeAddress($data['address']);

            if ($coordinates) {
                return response()->json([
                    'success' => true,
                    'data' => $coordinates
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to geocode the provided address.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@geocode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while geocoding the address.'
            ], 500);
        }
    }

    /**
     * Get location analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAnalytics', ProviderLocation::class);

            $providerId = $request->get('provider_id');
            $locationType = $request->get('location_type');
            $country = $request->get('country');
            $state = $request->get('state');
            $city = $request->get('city');

            $analytics = $this->providerLocationService->getLocationAnalytics([
                'provider_id' => $providerId,
                'location_type' => $locationType,
                'country' => $country,
                'state' => $state,
                'city' => $city
            ]);

            return response()->json([
                'success' => true,
                'data' => new ProviderLocationStatisticsResource($analytics)
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading analytics.'
            ], 500);
        }
    }

    /**
     * Get locations for map
     */
    public function map(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewMap', ProviderLocation::class);

            $providerId = $request->get('provider_id');
            $locationType = $request->get('location_type');
            $country = $request->get('country');
            $state = $request->get('state');
            $city = $request->get('city');
            $bounds = $request->get('bounds'); // Map bounds for filtering

            $locations = $this->providerLocationService->getLocationsForMap([
                'provider_id' => $providerId,
                'location_type' => $locationType,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'bounds' => $bounds
            ]);

            return response()->json([
                'success' => true,
                'data' => new ProviderLocationMapResource($locations)
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@map: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading map data.'
            ], 500);
        }
    }

    /**
     * Toggle location status
     */
    public function toggleStatus(ProviderLocation $providerLocation): JsonResponse
    {
        try {
            $this->authorize('update', $providerLocation);

            $toggled = $this->providerLocationService->toggleStatus($providerLocation);

            if ($toggled) {
                $status = $providerLocation->fresh()->is_active ? 'activated' : 'deactivated';
                return response()->json([
                    'success' => true,
                    'message' => "Location {$status} successfully."
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle location status.'
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@toggleStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while toggling the location status.'
            ], 500);
        }
    }

    /**
     * Get locations by coordinates (geospatial query)
     */
    public function nearby(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', ProviderLocation::class);

            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'numeric|min:0.1|max:1000',
                'limit' => 'integer|min:1|max:100'
            ]);

            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // Default 10km
            $limit = $request->get('limit', 20);

            $locations = $this->providerLocationService->findNearby(
                $latitude,
                $longitude,
                $radius,
                $limit
            );

            return response()->json([
                'success' => true,
                'data' => new ProviderLocationCollection($locations),
                'meta' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius_km' => $radius,
                    'count' => $locations->count()
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@nearby: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while finding nearby locations.'
            ], 500);
        }
    }

    /**
     * Get locations by provider
     */
    public function byProvider(int $providerId, Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', ProviderLocation::class);

            $perPage = $request->get('per_page', 15);
            $locationType = $request->get('location_type');
            $isActive = $request->get('is_active');

            $locations = $this->providerLocationService->getLocationsByProvider(
                $providerId,
                [
                    'per_page' => $perPage,
                    'location_type' => $locationType,
                    'is_active' => $isActive
                ]
            );

            return response()->json([
                'success' => true,
                'data' => new ProviderLocationCollection($locations),
                'meta' => [
                    'provider_id' => $providerId,
                    'total_count' => $this->providerLocationService->getLocationCountByProvider($providerId)
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@byProvider: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading provider locations.'
            ], 500);
        }
    }

    /**
     * Export locations
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $this->authorize('export', ProviderLocation::class);

            $request->validate([
                'format' => 'string|in:csv,json,xml,xlsx',
                'provider_id' => 'integer|exists:providers,id',
                'location_type' => 'string',
                'country' => 'string|size:2',
                'state' => 'string',
                'city' => 'string',
                'is_active' => 'boolean'
            ]);

            $format = $request->get('format', 'csv');
            $filters = $request->only(['provider_id', 'location_type', 'country', 'state', 'city', 'is_active']);

            $exportData = $this->providerLocationService->exportLocations($filters, $format);

            return response()->json([
                'success' => true,
                'data' => $exportData
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@export: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while exporting locations.'
            ], 500);
        }
    }

    /**
     * Bulk operations on locations
     */
    public function bulk(Request $request): JsonResponse
    {
        try {
            $this->authorize('bulkOperations', ProviderLocation::class);

            $request->validate([
                'action' => 'required|string|in:activate,deactivate,delete,export',
                'location_ids' => 'required|array|min:1',
                'location_ids.*' => 'integer|exists:provider_locations,id'
            ]);

            $action = $request->get('action');
            $locationIds = $request->get('location_ids');

            $result = $this->providerLocationService->bulkOperation($action, $locationIds);

            return response()->json([
                'success' => true,
                'message' => "Bulk {$action} operation completed successfully.",
                'data' => $result
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@bulk: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while performing bulk operation.'
            ], 500);
        }
    }
}
