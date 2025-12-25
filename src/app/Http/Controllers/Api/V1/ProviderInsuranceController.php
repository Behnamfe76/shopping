<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Exception;
use Fereydooni\Shopping\app\Http\Requests\RenewProviderInsuranceRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProviderInsuranceRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProviderInsuranceRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProviderInsuranceRequest;
use Fereydooni\Shopping\app\Http\Requests\UploadInsuranceDocumentRequest;
use Fereydooni\Shopping\app\Http\Requests\VerifyProviderInsuranceRequest;
use Fereydooni\Shopping\app\Http\Resources\ProviderInsuranceCollection;
use Fereydooni\Shopping\app\Http\Resources\ProviderInsuranceResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderInsuranceSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderInsuranceStatisticsResource;
use Fereydooni\Shopping\app\Services\ProviderInsuranceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProviderInsuranceController extends Controller
{
    protected ProviderInsuranceService $providerInsuranceService;

    public function __construct(ProviderInsuranceService $providerInsuranceService)
    {
        $this->providerInsuranceService = $providerInsuranceService;
    }

    /**
     * Display a listing of provider insurance records.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $perPage = $request->get('per_page', 15);
            $providerInsurance = $this->providerInsuranceService->paginate($perPage);

            return ProviderInsuranceCollection::collection($providerInsurance);
        } catch (Exception $e) {
            Log::error('Error fetching provider insurance list: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch provider insurance list'], 500);
        }
    }

    /**
     * Store a newly created provider insurance record.
     */
    public function store(StoreProviderInsuranceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $providerInsurance = $this->providerInsuranceService->createAndReturnDTO($data);

            return response()->json([
                'message' => 'Provider insurance created successfully',
                'data' => new ProviderInsuranceResource($providerInsurance),
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to create provider insurance'], 500);
        }
    }

    /**
     * Display the specified provider insurance record.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->findDTO($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            return response()->json([
                'data' => new ProviderInsuranceResource($providerInsurance),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch provider insurance'], 500);
        }
    }

    /**
     * Update the specified provider insurance record.
     */
    public function update(UpdateProviderInsuranceRequest $request, int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $data = $request->validated();
            $updated = $this->providerInsuranceService->updateAndReturnDTO($providerInsurance, $data);

            if (! $updated) {
                return response()->json(['error' => 'Failed to update provider insurance'], 500);
            }

            return response()->json([
                'message' => 'Provider insurance updated successfully',
                'data' => new ProviderInsuranceResource($updated),
            ]);
        } catch (Exception $e) {
            Log::error('Error updating provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to update provider insurance'], 500);
        }
    }

    /**
     * Remove the specified provider insurance record.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $deleted = $this->providerInsuranceService->delete($providerInsurance);

            if (! $deleted) {
                return response()->json(['error' => 'Failed to delete provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance deleted successfully']);
        } catch (Exception $e) {
            Log::error('Error deleting provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to delete provider insurance'], 500);
        }
    }

    /**
     * Search provider insurance records.
     */
    public function search(SearchProviderInsuranceRequest $request): AnonymousResourceCollection
    {
        try {
            $query = $request->get('query');
            $providerId = $request->get('provider_id');

            if ($providerId) {
                $results = $this->providerInsuranceService->searchInsuranceByProviderDTO($providerId, $query);
            } else {
                $results = $this->providerInsuranceService->searchInsuranceDTO($query);
            }

            return ProviderInsuranceSearchResource::collection($results);
        } catch (Exception $e) {
            Log::error('Error searching provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to search provider insurance'], 500);
        }
    }

    /**
     * Get provider insurance statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $providerId = $request->get('provider_id');

            if ($providerId) {
                $analytics = $this->providerInsuranceService->getInsuranceAnalytics($providerId);
            } else {
                $analytics = $this->providerInsuranceService->getGlobalInsuranceAnalytics();
            }

            return response()->json([
                'data' => new ProviderInsuranceStatisticsResource($analytics),
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching provider insurance statistics: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Verify provider insurance.
     */
    public function verify(VerifyProviderInsuranceRequest $request, int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $data = $request->validated();
            $verified = $this->providerInsuranceService->verify(
                $providerInsurance,
                $data['verified_by'],
                $data['notes'] ?? null
            );

            if (! $verified) {
                return response()->json(['error' => 'Failed to verify provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance verified successfully']);
        } catch (Exception $e) {
            Log::error('Error verifying provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to verify provider insurance'], 500);
        }
    }

    /**
     * Reject provider insurance.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'rejected_by' => 'required|integer|exists:users,id',
                'reason' => 'required|string|max:1000',
            ]);

            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $rejected = $this->providerInsuranceService->reject(
                $providerInsurance,
                $request->rejected_by,
                $request->reason
            );

            if (! $rejected) {
                return response()->json(['error' => 'Failed to reject provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance rejected successfully']);
        } catch (Exception $e) {
            Log::error('Error rejecting provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to reject provider insurance'], 500);
        }
    }

    /**
     * Renew provider insurance.
     */
    public function renew(RenewProviderInsuranceRequest $request, int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $data = $request->validated();
            $renewed = $this->providerInsuranceService->renew($providerInsurance, $data);

            if (! $renewed) {
                return response()->json(['error' => 'Failed to renew provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance renewed successfully']);
        } catch (Exception $e) {
            Log::error('Error renewing provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to renew provider insurance'], 500);
        }
    }

    /**
     * Upload insurance document.
     */
    public function uploadDocument(UploadInsuranceDocumentRequest $request, int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $file = $request->file('document');
            $documentPath = $file->store('insurance-documents', 'public');

            $uploaded = $this->providerInsuranceService->addDocument($providerInsurance, $documentPath);

            if (! $uploaded) {
                return response()->json(['error' => 'Failed to upload document'], 500);
            }

            return response()->json([
                'message' => 'Document uploaded successfully',
                'document_path' => $documentPath,
            ]);
        } catch (Exception $e) {
            Log::error('Error uploading insurance document: '.$e->getMessage());

            return response()->json(['error' => 'Failed to upload document'], 500);
        }
    }

    /**
     * Remove insurance document.
     */
    public function removeDocument(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'document_path' => 'required|string',
            ]);

            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $removed = $this->providerInsuranceService->removeDocument($providerInsurance, $request->document_path);

            if (! $removed) {
                return response()->json(['error' => 'Failed to remove document'], 500);
            }

            // Delete the file from storage
            if (Storage::disk('public')->exists($request->document_path)) {
                Storage::disk('public')->delete($request->document_path);
            }

            return response()->json(['message' => 'Document removed successfully']);
        } catch (Exception $e) {
            Log::error('Error removing insurance document: '.$e->getMessage());

            return response()->json(['error' => 'Failed to remove document'], 500);
        }
    }

    /**
     * Get expiring insurance.
     */
    public function expiring(Request $request): AnonymousResourceCollection
    {
        try {
            $days = $request->get('days', 30);
            $limit = $request->get('limit', 10);
            $providerId = $request->get('provider_id');

            if ($providerId) {
                $expiring = $this->providerInsuranceService->getExpiringInsuranceByProviderDTO($providerId, $limit);
            } else {
                $expiring = $this->providerInsuranceService->getExpiringInsuranceDTO($limit);
            }

            return ProviderInsuranceResource::collection($expiring);
        } catch (Exception $e) {
            Log::error('Error fetching expiring insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch expiring insurance'], 500);
        }
    }

    /**
     * Get pending verification.
     */
    public function pendingVerification(Request $request): AnonymousResourceCollection
    {
        try {
            $limit = $request->get('limit', 10);
            $providerId = $request->get('provider_id');

            if ($providerId) {
                $pending = $this->providerInsuranceService->getPendingVerificationByProviderDTO($providerId, $limit);
            } else {
                $pending = $this->providerInsuranceService->getPendingVerificationDTO($limit);
            }

            return ProviderInsuranceResource::collection($pending);
        } catch (Exception $e) {
            Log::error('Error fetching pending verification: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch pending verification'], 500);
        }
    }

    /**
     * Activate provider insurance.
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $activated = $this->providerInsuranceService->activate($providerInsurance);

            if (! $activated) {
                return response()->json(['error' => 'Failed to activate provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance activated successfully']);
        } catch (Exception $e) {
            Log::error('Error activating provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to activate provider insurance'], 500);
        }
    }

    /**
     * Deactivate provider insurance.
     */
    public function deactivate(int $id): JsonResponse
    {
        try {
            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $deactivated = $this->providerInsuranceService->deactivate($providerInsurance);

            if (! $deactivated) {
                return response()->json(['error' => 'Failed to deactivate provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance deactivated successfully']);
        } catch (Exception $e) {
            Log::error('Error deactivating provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to deactivate provider insurance'], 500);
        }
    }

    /**
     * Cancel provider insurance.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $cancelled = $this->providerInsuranceService->cancel($providerInsurance, $request->reason);

            if (! $cancelled) {
                return response()->json(['error' => 'Failed to cancel provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance cancelled successfully']);
        } catch (Exception $e) {
            Log::error('Error cancelling provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to cancel provider insurance'], 500);
        }
    }

    /**
     * Suspend provider insurance.
     */
    public function suspend(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $providerInsurance = $this->providerInsuranceService->find($id);

            if (! $providerInsurance) {
                return response()->json(['error' => 'Provider insurance not found'], 404);
            }

            $suspended = $this->providerInsuranceService->suspend($providerInsurance, $request->reason);

            if (! $suspended) {
                return response()->json(['error' => 'Failed to suspend provider insurance'], 500);
            }

            return response()->json(['message' => 'Provider insurance suspended successfully']);
        } catch (Exception $e) {
            Log::error('Error suspending provider insurance: '.$e->getMessage());

            return response()->json(['error' => 'Failed to suspend provider insurance'], 500);
        }
    }
}
