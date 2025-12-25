<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Services\CustomerPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;

class CustomerPreferenceController extends Controller
{
    public function __construct(
        private CustomerPreferenceService $preferenceService
    ) {}

    /**
     * Display a listing of customer preferences.
     */
    public function index(Request $request): ResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $preferences = $this->preferenceService->getPaginatedPreferences($perPage);

        return JsonResource::collection($preferences);
    }

    /**
     * Store a newly created customer preference.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|integer|exists:customers,id',
                'preference_key' => 'required|string|max:100',
                'preference_value' => 'required|string|max:1000',
                'preference_type' => 'required|string|in:string,integer,float,boolean,json,array,object',
                'is_active' => 'boolean',
                'description' => 'nullable|string|max:500',
                'metadata' => 'nullable|array',
            ]);

            $preference = $this->preferenceService->createPreference($validated);

            return response()->json([
                'message' => 'Customer preference created successfully',
                'data' => $preference,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified customer preference.
     */
    public function show(CustomerPreference $preference): JsonResponse
    {
        $this->authorize('view', $preference);

        $preferenceDTO = $this->preferenceService->getPreferenceDTO($preference->id);

        return response()->json([
            'data' => $preferenceDTO,
        ]);
    }

    /**
     * Update the specified customer preference.
     */
    public function update(Request $request, CustomerPreference $preference): JsonResponse
    {
        $this->authorize('update', $preference);

        try {
            $validated = $request->validate([
                'preference_value' => 'sometimes|required|string|max:1000',
                'preference_type' => 'sometimes|required|string|in:string,integer,float,boolean,json,array,object',
                'is_active' => 'sometimes|boolean',
                'description' => 'nullable|string|max:500',
                'metadata' => 'nullable|array',
            ]);

            $updatedPreference = $this->preferenceService->updatePreference($preference, $validated);

            if (! $updatedPreference) {
                return response()->json([
                    'message' => 'Failed to update customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference updated successfully',
                'data' => $updatedPreference,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified customer preference.
     */
    public function destroy(CustomerPreference $preference): JsonResponse
    {
        $this->authorize('delete', $preference);

        try {
            $deleted = $this->preferenceService->deletePreference($preference);

            if (! $deleted) {
                return response()->json([
                    'message' => 'Failed to delete customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate the specified customer preference.
     */
    public function activate(CustomerPreference $preference, Request $request): JsonResponse
    {
        $this->authorize('activate', $preference);

        try {
            $reason = $request->get('reason');
            $activated = $this->preferenceService->activateCustomerPreference($preference, $reason);

            if (! $activated) {
                return response()->json([
                    'message' => 'Failed to activate customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference activated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to activate customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate the specified customer preference.
     */
    public function deactivate(CustomerPreference $preference, Request $request): JsonResponse
    {
        $this->authorize('deactivate', $preference);

        try {
            $reason = $request->get('reason');
            $deactivated = $this->preferenceService->deactivateCustomerPreference($preference, $reason);

            if (! $deactivated) {
                return response()->json([
                    'message' => 'Failed to deactivate customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference deactivated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deactivate customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer preferences.
     */
    public function getCustomerPreferences(Customer $customer, Request $request): JsonResponse
    {
        $this->authorize('getPreference', $customer);

        try {
            $type = $request->get('type');
            $category = $request->get('category');

            if ($type) {
                $preferences = $this->preferenceService->getCustomerPreferencesByType($customer->id, $type);
            } elseif ($category) {
                $preferences = $this->preferenceService->getCustomerPreferencesByCategory($customer->id, $category);
            } else {
                $preferences = $this->preferenceService->getAllCustomerPreferences($customer->id);
            }

            return response()->json([
                'data' => $preferences,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set customer preference.
     */
    public function setCustomerPreference(Customer $customer, Request $request): JsonResponse
    {
        $this->authorize('setPreference', $customer);

        try {
            $validated = $request->validate([
                'key' => 'required|string|max:100',
                'value' => 'required',
                'type' => 'sometimes|string|in:string,integer,float,boolean,json,array,object',
                'description' => 'nullable|string|max:500',
            ]);

            $set = $this->preferenceService->setCustomerPreference(
                $customer->id,
                $validated['key'],
                $validated['value'],
                $validated['type'] ?? 'string',
                $validated['description'] ?? null
            );

            if (! $set) {
                return response()->json([
                    'message' => 'Failed to set customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference set successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to set customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific customer preference.
     */
    public function getCustomerPreference(Customer $customer, string $key): JsonResponse
    {
        $this->authorize('getPreference', $customer);

        try {
            $preference = $this->preferenceService->getCustomerPreference($customer->id, $key);

            if ($preference === null) {
                return response()->json([
                    'message' => 'Customer preference not found',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'key' => $key,
                    'value' => $preference,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update specific customer preference.
     */
    public function updateCustomerPreference(Customer $customer, string $key, Request $request): JsonResponse
    {
        $this->authorize('setPreference', $customer);

        try {
            $validated = $request->validate([
                'value' => 'required',
                'type' => 'sometimes|string|in:string,integer,float,boolean,json,array,object',
                'description' => 'nullable|string|max:500',
            ]);

            $updated = $this->preferenceService->updateCustomerPreference(
                $customer->id,
                $key,
                $validated['value'],
                $validated['type'] ?? 'string',
                $validated['description'] ?? null
            );

            if (! $updated) {
                return response()->json([
                    'message' => 'Failed to update customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove specific customer preference.
     */
    public function removeCustomerPreference(Customer $customer, string $key): JsonResponse
    {
        $this->authorize('removePreference', $customer);

        try {
            $removed = $this->preferenceService->removeCustomerPreference($customer->id, $key);

            if (! $removed) {
                return response()->json([
                    'message' => 'Failed to remove customer preference',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preference removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove customer preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset all customer preferences.
     */
    public function resetCustomerPreferences(Customer $customer): JsonResponse
    {
        $this->authorize('resetPreferences', $customer);

        try {
            $reset = $this->preferenceService->resetCustomerPreferences($customer->id);

            if (! $reset) {
                return response()->json([
                    'message' => 'Failed to reset customer preferences',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preferences reset successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import customer preferences.
     */
    public function importCustomerPreferences(Customer $customer, Request $request): JsonResponse
    {
        $this->authorize('importPreferences', $customer);

        try {
            $validated = $request->validate([
                'preferences' => 'required|array',
                'preferences.*.key' => 'required|string|max:100',
                'preferences.*.value' => 'required',
                'preferences.*.type' => 'sometimes|string|in:string,integer,float,boolean,json,array,object',
                'preferences.*.description' => 'nullable|string|max:500',
            ]);

            $imported = $this->preferenceService->importCustomerPreferences($customer->id, $validated['preferences']);

            if (! $imported) {
                return response()->json([
                    'message' => 'Failed to import customer preferences',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preferences imported successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to import customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export customer preferences.
     */
    public function exportCustomerPreferences(Customer $customer): JsonResponse
    {
        $this->authorize('exportPreferences', $customer);

        try {
            $preferences = $this->preferenceService->exportCustomerPreferences($customer->id);

            return response()->json([
                'data' => $preferences,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to export customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync customer preferences.
     */
    public function syncCustomerPreferences(Customer $customer, Request $request): JsonResponse
    {
        $this->authorize('syncPreferences', $customer);

        try {
            $validated = $request->validate([
                'preferences' => 'required|array',
                'preferences.*.key' => 'required|string|max:100',
                'preferences.*.value' => 'required',
                'preferences.*.type' => 'sometimes|string|in:string,integer,float,boolean,json,array,object',
                'preferences.*.description' => 'nullable|string|max:500',
            ]);

            $synced = $this->preferenceService->syncCustomerPreferences($customer->id, $validated['preferences']);

            if (! $synced) {
                return response()->json([
                    'message' => 'Failed to sync customer preferences',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preferences synced successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to sync customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search customer preferences.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:1',
                'customer_id' => 'nullable|integer|exists:customers,id',
            ]);

            if (isset($validated['customer_id'])) {
                $preferences = $this->preferenceService->searchCustomerPreferences($validated['customer_id'], $validated['query']);
            } else {
                $preferences = $this->preferenceService->searchPreferences($validated['query']);
            }

            return response()->json([
                'data' => $preferences,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to search customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get preference statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $customerId = $request->get('customer_id');

            if ($customerId) {
                $stats = $this->preferenceService->getCustomerPreferenceStats($customerId);
            } else {
                $stats = $this->preferenceService->getPreferenceStats();
            }

            return response()->json([
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get preference statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer preference summary.
     */
    public function summary(Customer $customer): JsonResponse
    {
        $this->authorize('viewCustomerStatistics', $customer);

        try {
            $summary = $this->preferenceService->getCustomerPreferenceSummary($customer->id);

            return response()->json([
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get customer preference summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initialize customer preferences.
     */
    public function initialize(Customer $customer): JsonResponse
    {
        $this->authorize('initializePreferences', $customer);

        try {
            $initialized = $this->preferenceService->initializeCustomerPreferences($customer->id);

            if (! $initialized) {
                return response()->json([
                    'message' => 'Failed to initialize customer preferences',
                ], 500);
            }

            return response()->json([
                'message' => 'Customer preferences initialized successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initialize customer preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get preference templates.
     */
    public function templates(): JsonResponse
    {
        try {
            $templates = $this->preferenceService->getPreferenceTemplates();

            return response()->json([
                'data' => $templates,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get preference templates',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply preference template to customer.
     */
    public function applyTemplate(Customer $customer, Request $request): JsonResponse
    {
        $this->authorize('applyTemplate', $customer);

        try {
            $validated = $request->validate([
                'template_name' => 'required|string',
            ]);

            $applied = $this->preferenceService->applyPreferenceTemplate($customer->id, $validated['template_name']);

            if (! $applied) {
                return response()->json([
                    'message' => 'Failed to apply preference template',
                ], 500);
            }

            return response()->json([
                'message' => 'Preference template applied successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to apply preference template',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
