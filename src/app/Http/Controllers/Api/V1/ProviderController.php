<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Services\ProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProviderController extends Controller
{
    protected ProviderService $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    /**
     * Display a listing of providers
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $providers = $this->providerService->getPaginatedProviders($perPage);

        return response()->json([
            'data' => $providers->items(),
            'pagination' => [
                'current_page' => $providers->currentPage(),
                'last_page' => $providers->lastPage(),
                'per_page' => $providers->perPage(),
                'total' => $providers->total(),
            ],
        ]);
    }

    /**
     * Store a newly created provider
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'company_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|unique:providers,email',
                'phone' => 'required|string|max:20',
                'website' => 'nullable|url|max:255',
                'tax_id' => 'nullable|string|max:100',
                'business_license' => 'nullable|string|max:100',
                'provider_type' => 'required|string',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'payment_terms' => 'nullable|string|max:100',
                'credit_limit' => 'nullable|numeric|min:0',
                'commission_rate' => 'nullable|numeric|min:0|max:1',
                'discount_rate' => 'nullable|numeric|min:0|max:1',
                'specializations' => 'nullable|array',
                'certifications' => 'nullable|array',
                'insurance_info' => 'nullable|array',
                'contract_start_date' => 'nullable|date',
                'contract_end_date' => 'nullable|date|after:contract_start_date',
            ]);

            $provider = $this->providerService->createProvider($validated);

            return response()->json([
                'message' => 'Provider created successfully',
                'data' => $provider,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create provider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified provider
     */
    public function show(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        return response()->json([
            'data' => $provider,
        ]);
    }

    /**
     * Update the specified provider
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $provider = $this->providerService->getProvider($id);

            if (! $provider) {
                return response()->json([
                    'message' => 'Provider not found',
                ], 404);
            }

            $validated = $request->validate([
                'company_name' => 'sometimes|string|max:255',
                'contact_person' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:providers,email,'.$id,
                'phone' => 'sometimes|string|max:20',
                'website' => 'nullable|url|max:255',
                'tax_id' => 'nullable|string|max:100',
                'business_license' => 'nullable|string|max:100',
                'provider_type' => 'sometimes|string',
                'address' => 'sometimes|string|max:500',
                'city' => 'sometimes|string|max:100',
                'state' => 'sometimes|string|max:100',
                'postal_code' => 'sometimes|string|max:20',
                'country' => 'sometimes|string|max:100',
                'payment_terms' => 'nullable|string|max:100',
                'credit_limit' => 'nullable|numeric|min:0',
                'commission_rate' => 'nullable|numeric|min:0|max:1',
                'discount_rate' => 'nullable|numeric|min:0|max:1',
                'specializations' => 'nullable|array',
                'certifications' => 'nullable|array',
                'insurance_info' => 'nullable|array',
                'contract_start_date' => 'nullable|date',
                'contract_end_date' => 'nullable|date|after:contract_start_date',
            ]);

            $updated = $this->providerService->updateProvider($provider, $validated);

            if ($updated) {
                $provider->refresh();

                return response()->json([
                    'message' => 'Provider updated successfully',
                    'data' => $provider,
                ]);
            }

            return response()->json([
                'message' => 'Failed to update provider',
            ], 500);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update provider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified provider
     */
    public function destroy(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $deleted = $this->providerService->deleteProvider($provider);

        if ($deleted) {
            return response()->json([
                'message' => 'Provider deleted successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to delete provider',
        ], 500);
    }

    /**
     * Activate a provider
     */
    public function activate(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $activated = $this->providerService->activateProvider($provider);

        if ($activated) {
            return response()->json([
                'message' => 'Provider activated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to activate provider',
        ], 500);
    }

    /**
     * Deactivate a provider
     */
    public function deactivate(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $deactivated = $this->providerService->deactivateProvider($provider);

        if ($deactivated) {
            return response()->json([
                'message' => 'Provider deactivated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to deactivate provider',
        ], 500);
    }

    /**
     * Suspend a provider
     */
    public function suspend(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $suspended = $this->providerService->suspendProvider($provider, $validated['reason'] ?? null);

        if ($suspended) {
            return response()->json([
                'message' => 'Provider suspended successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to suspend provider',
        ], 500);
    }

    /**
     * Update provider rating
     */
    public function updateRating(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $updated = $this->providerService->updateProviderRating($provider, $validated['rating']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider rating updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider rating',
        ], 500);
    }

    /**
     * Update provider quality rating
     */
    public function updateQualityRating(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'quality_rating' => 'required|numeric|min:0|max:5',
        ]);

        $updated = $this->providerService->updateProviderQualityRating($provider, $validated['quality_rating']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider quality rating updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider quality rating',
        ], 500);
    }

    /**
     * Update provider delivery rating
     */
    public function updateDeliveryRating(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'delivery_rating' => 'required|numeric|min:0|max:5',
        ]);

        $updated = $this->providerService->updateProviderDeliveryRating($provider, $validated['delivery_rating']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider delivery rating updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider delivery rating',
        ], 500);
    }

    /**
     * Update provider communication rating
     */
    public function updateCommunicationRating(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'communication_rating' => 'required|numeric|min:0|max:5',
        ]);

        $updated = $this->providerService->updateProviderCommunicationRating($provider, $validated['communication_rating']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider communication rating updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider communication rating',
        ], 500);
    }

    /**
     * Update provider credit limit
     */
    public function updateCreditLimit(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
        ]);

        $updated = $this->providerService->updateProviderCreditLimit($provider, $validated['credit_limit']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider credit limit updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider credit limit',
        ], 500);
    }

    /**
     * Update provider commission rate
     */
    public function updateCommissionRate(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:1',
        ]);

        $updated = $this->providerService->updateProviderCommissionRate($provider, $validated['commission_rate']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider commission rate updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider commission rate',
        ], 500);
    }

    /**
     * Update provider discount rate
     */
    public function updateDiscountRate(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'discount_rate' => 'required|numeric|min:0|max:1',
        ]);

        $updated = $this->providerService->updateProviderDiscountRate($provider, $validated['discount_rate']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider discount rate updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider discount rate',
        ], 500);
    }

    /**
     * Extend provider contract
     */
    public function extendContract(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'contract_end_date' => 'required|date|after:today',
        ]);

        $extended = $this->providerService->extendProviderContract($provider, $validated['contract_end_date']);

        if ($extended) {
            return response()->json([
                'message' => 'Provider contract extended successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to extend provider contract',
        ], 500);
    }

    /**
     * Terminate provider contract
     */
    public function terminateContract(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $terminated = $this->providerService->terminateProviderContract($provider, $validated['reason'] ?? null);

        if ($terminated) {
            return response()->json([
                'message' => 'Provider contract terminated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to terminate provider contract',
        ], 500);
    }

    /**
     * Get provider orders
     */
    public function getOrders(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $orders = $this->providerService->getProviderOrders($provider);

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Get provider products
     */
    public function getProducts(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $products = $this->providerService->getProviderProducts($provider);

        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Get provider analytics
     */
    public function getAnalytics(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $analytics = $this->providerService->getProviderAnalytics($provider);

        return response()->json([
            'data' => $analytics,
        ]);
    }

    /**
     * Get provider performance metrics
     */
    public function getPerformanceMetrics(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $metrics = $this->providerService->getProviderPerformanceMetrics($provider);

        return response()->json([
            'data' => $metrics,
        ]);
    }

    /**
     * Add provider note
     */
    public function addNote(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'nullable|string|max:50',
        ]);

        $added = $this->providerService->addProviderNote($provider, $validated['note'], $validated['type'] ?? 'general');

        if ($added) {
            return response()->json([
                'message' => 'Provider note added successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to add provider note',
        ], 500);
    }

    /**
     * Get provider notes
     */
    public function getNotes(int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $notes = $this->providerService->getProviderNotes($provider);

        return response()->json([
            'data' => $notes,
        ]);
    }

    /**
     * Update provider specializations
     */
    public function updateSpecializations(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'specializations' => 'required|array',
        ]);

        $updated = $this->providerService->updateProviderSpecializations($provider, $validated['specializations']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider specializations updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider specializations',
        ], 500);
    }

    /**
     * Update provider certifications
     */
    public function updateCertifications(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'certifications' => 'required|array',
        ]);

        $updated = $this->providerService->updateProviderCertifications($provider, $validated['certifications']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider certifications updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider certifications',
        ], 500);
    }

    /**
     * Update provider insurance
     */
    public function updateInsurance(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerService->getProvider($id);

        if (! $provider) {
            return response()->json([
                'message' => 'Provider not found',
            ], 404);
        }

        $validated = $request->validate([
            'insurance_info' => 'required|array',
        ]);

        $updated = $this->providerService->updateProviderInsurance($provider, $validated['insurance_info']);

        if ($updated) {
            return response()->json([
                'message' => 'Provider insurance updated successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to update provider insurance',
        ], 500);
    }
}
