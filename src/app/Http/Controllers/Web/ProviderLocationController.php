<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Exception;
use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\GeocodeLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\SetPrimaryLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProviderLocationRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateCoordinatesRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProviderLocationRequest;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Services\ProviderLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProviderLocationController extends Controller
{
    protected ProviderLocationService $providerLocationService;

    public function __construct(ProviderLocationService $providerLocationService)
    {
        $this->providerLocationService = $providerLocationService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of provider locations
     */
    public function index(Request $request): View
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

            $locations = $this->providerLocationService->getFilteredLocations([
                'per_page' => $perPage,
                'search' => $search,
                'provider_id' => $providerId,
                'location_type' => $locationType,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'is_active' => $isActive,
            ]);

            $providers = $this->providerLocationService->getProvidersForFilter();
            $locationTypes = $this->providerLocationService->getLocationTypes();
            $countries = $this->providerLocationService->getCountries();
            $states = $this->providerLocationService->getStates($country);
            $cities = $this->providerLocationService->getCities($state);

            return view('provider-locations.index', compact(
                'locations',
                'providers',
                'locationTypes',
                'countries',
                'states',
                'cities',
                'search',
                'providerId',
                'locationType',
                'country',
                'state',
                'city',
                'isActive'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@index: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading provider locations.');
        }
    }

    /**
     * Show the form for creating a new provider location
     */
    public function create(): View
    {
        try {
            $this->authorize('create', ProviderLocation::class);

            $providers = $this->providerLocationService->getProvidersForSelect();
            $locationTypes = $this->providerLocationService->getLocationTypes();
            $countries = $this->providerLocationService->getCountries();
            $timezones = $this->providerLocationService->getTimezones();

            return view('provider-locations.create', compact(
                'providers',
                'locationTypes',
                'countries',
                'timezones'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@create: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading the create form.');
        }
    }

    /**
     * Store a newly created provider location
     */
    public function store(StoreProviderLocationRequest $request): RedirectResponse
    {
        try {
            $this->authorize('create', ProviderLocation::class);

            $data = $request->validated();
            $location = $this->providerLocationService->create($data);

            if ($location) {
                return redirect()->route('provider-locations.show', $location)
                    ->with('success', 'Provider location created successfully.');
            }

            return back()->withInput()
                ->with('error', 'Failed to create provider location.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@store: '.$e->getMessage());

            return back()->withInput()
                ->with('error', 'An error occurred while creating the provider location.');
        }
    }

    /**
     * Display the specified provider location
     */
    public function show(ProviderLocation $providerLocation): View
    {
        try {
            $this->authorize('view', $providerLocation);

            $location = $this->providerLocationService->find($providerLocation->id);
            $nearbyLocations = $this->providerLocationService->findNearby($location, 10);
            $locationAnalytics = $this->providerLocationService->getLocationAnalytics($location->id);

            return view('provider-locations.show', compact(
                'location',
                'nearbyLocations',
                'locationAnalytics'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@show: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading the provider location.');
        }
    }

    /**
     * Show the form for editing the specified provider location
     */
    public function edit(ProviderLocation $providerLocation): View
    {
        try {
            $this->authorize('update', $providerLocation);

            $location = $this->providerLocationService->find($providerLocation->id);
            $providers = $this->providerLocationService->getProvidersForSelect();
            $locationTypes = $this->providerLocationService->getLocationTypes();
            $countries = $this->providerLocationService->getCountries();
            $timezones = $this->providerLocationService->getTimezones();

            return view('provider-locations.edit', compact(
                'location',
                'providers',
                'locationTypes',
                'countries',
                'timezones'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@edit: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update the specified provider location
     */
    public function update(UpdateProviderLocationRequest $request, ProviderLocation $providerLocation): RedirectResponse
    {
        try {
            $this->authorize('update', $providerLocation);

            $data = $request->validated();
            $updated = $this->providerLocationService->update($providerLocation, $data);

            if ($updated) {
                return redirect()->route('provider-locations.show', $providerLocation)
                    ->with('success', 'Provider location updated successfully.');
            }

            return back()->withInput()
                ->with('error', 'Failed to update provider location.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@update: '.$e->getMessage());

            return back()->withInput()
                ->with('error', 'An error occurred while updating the provider location.');
        }
    }

    /**
     * Remove the specified provider location
     */
    public function destroy(ProviderLocation $providerLocation): RedirectResponse
    {
        try {
            $this->authorize('delete', $providerLocation);

            $deleted = $this->providerLocationService->delete($providerLocation);

            if ($deleted) {
                return redirect()->route('provider-locations.index')
                    ->with('success', 'Provider location deleted successfully.');
            }

            return back()->with('error', 'Failed to delete provider location.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@destroy: '.$e->getMessage());

            return back()->with('error', 'An error occurred while deleting the provider location.');
        }
    }

    /**
     * Set a location as primary
     */
    public function setPrimary(SetPrimaryLocationRequest $request, ProviderLocation $providerLocation): RedirectResponse
    {
        try {
            $this->authorize('setPrimary', $providerLocation);

            $set = $this->providerLocationService->setPrimary($providerLocation);

            if ($set) {
                return back()->with('success', 'Primary location set successfully.');
            }

            return back()->with('error', 'Failed to set primary location.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@setPrimary: '.$e->getMessage());

            return back()->with('error', 'An error occurred while setting the primary location.');
        }
    }

    /**
     * Update coordinates for a location
     */
    public function updateCoordinates(UpdateCoordinatesRequest $request, ProviderLocation $providerLocation): RedirectResponse
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
                return back()->with('success', 'Coordinates updated successfully.');
            }

            return back()->with('error', 'Failed to update coordinates.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@updateCoordinates: '.$e->getMessage());

            return back()->with('error', 'An error occurred while updating coordinates.');
        }
    }

    /**
     * Search locations
     */
    public function search(SearchLocationRequest $request): View
    {
        try {
            $this->authorize('search', ProviderLocation::class);

            $data = $request->validated();
            $results = $this->providerLocationService->searchLocations($data['query'], $data['limit'] ?? 20);
            $searchStats = $this->providerLocationService->getSearchStatistics($data['query']);

            return view('provider-locations.search', compact('results', 'searchStats', 'data'));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@search: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while searching locations.');
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
                    'coordinates' => $coordinates,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to geocode the provided address.',
            ], 422);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@geocode: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while geocoding the address.',
            ], 500);
        }
    }

    /**
     * Show location analytics
     */
    public function analytics(Request $request): View
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
                'city' => $city,
            ]);

            $providers = $this->providerLocationService->getProvidersForFilter();
            $locationTypes = $this->providerLocationService->getLocationTypes();
            $countries = $this->providerLocationService->getCountries();

            return view('provider-locations.analytics', compact(
                'analytics',
                'providers',
                'locationTypes',
                'countries',
                'providerId',
                'locationType',
                'country',
                'state',
                'city'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@analytics: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading analytics.');
        }
    }

    /**
     * Show location map
     */
    public function map(Request $request): View
    {
        try {
            $this->authorize('viewMap', ProviderLocation::class);

            $providerId = $request->get('provider_id');
            $locationType = $request->get('location_type');
            $country = $request->get('country');
            $state = $request->get('state');
            $city = $request->get('city');

            $locations = $this->providerLocationService->getLocationsForMap([
                'provider_id' => $providerId,
                'location_type' => $locationType,
                'country' => $country,
                'state' => $state,
                'city' => $city,
            ]);

            $providers = $this->providerLocationService->getProvidersForFilter();
            $locationTypes = $this->providerLocationService->getLocationTypes();
            $countries = $this->providerLocationService->getCountries();

            return view('provider-locations.map', compact(
                'locations',
                'providers',
                'locationTypes',
                'countries',
                'providerId',
                'locationType',
                'country',
                'state',
                'city'
            ));
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@map: '.$e->getMessage());

            return view('errors.general')->with('message', 'An error occurred while loading the map.');
        }
    }

    /**
     * Toggle location status
     */
    public function toggleStatus(ProviderLocation $providerLocation): RedirectResponse
    {
        try {
            $this->authorize('update', $providerLocation);

            $toggled = $this->providerLocationService->toggleStatus($providerLocation);

            if ($toggled) {
                $status = $providerLocation->fresh()->is_active ? 'activated' : 'deactivated';

                return back()->with('success', "Location {$status} successfully.");
            }

            return back()->with('error', 'Failed to toggle location status.');
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@toggleStatus: '.$e->getMessage());

            return back()->with('error', 'An error occurred while toggling the location status.');
        }
    }

    /**
     * Export locations
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $this->authorize('export', ProviderLocation::class);

            $format = $request->get('format', 'csv');
            $filters = $request->only(['provider_id', 'location_type', 'country', 'state', 'city', 'is_active']);

            $exportData = $this->providerLocationService->exportLocations($filters, $format);

            return response()->json([
                'success' => true,
                'download_url' => $exportData['download_url'],
                'filename' => $exportData['filename'],
            ]);
        } catch (Exception $e) {
            Log::error('Error in ProviderLocationController@export: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while exporting locations.',
            ], 500);
        }
    }
}
