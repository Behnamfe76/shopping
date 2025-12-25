<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RenewProviderInsuranceRequest;
use App\Http\Requests\SearchProviderInsuranceRequest;
use App\Http\Requests\StoreProviderInsuranceRequest;
use App\Http\Requests\UpdateProviderInsuranceRequest;
use App\Http\Requests\UploadInsuranceDocumentRequest;
use App\Http\Requests\VerifyProviderInsuranceRequest;
use App\Models\ProviderInsurance;
use App\Services\ProviderInsuranceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * ProviderInsurance Controller for Web Interface
 *
 * Handles CRUD operations, file uploads, search, and analytics
 * for provider insurance management.
 */
class ProviderInsuranceController extends Controller
{
    protected ProviderInsuranceService $service;

    public function __construct(ProviderInsuranceService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->authorizeResource(ProviderInsurance::class, 'providerInsurance');
    }

    /**
     * Display a listing of provider insurance records
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $status = $request->get('status');
        $insuranceType = $request->get('insurance_type');
        $verificationStatus = $request->get('verification_status');

        $query = ProviderInsurance::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('policy_number', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($insuranceType) {
            $query->where('insurance_type', $insuranceType);
        }

        if ($verificationStatus) {
            $query->where('verification_status', $verificationStatus);
        }

        $providerInsurance = $query->with(['provider', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('provider-insurance.index', compact('providerInsurance', 'search', 'status', 'insuranceType', 'verificationStatus'));
    }

    /**
     * Show the form for creating a new provider insurance record
     */
    public function create(): View
    {
        return view('provider-insurance.create');
    }

    /**
     * Store a newly created provider insurance record
     */
    public function store(StoreProviderInsuranceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('insurance-documents', 'public');
                $documents[] = $path;
            }
            $data['documents'] = $documents;
        }

        $providerInsurance = $this->service->create($data);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance created successfully.');
    }

    /**
     * Display the specified provider insurance record
     */
    public function show(ProviderInsurance $providerInsurance): View
    {
        $providerInsurance->load(['provider', 'verifiedBy']);

        return view('provider-insurance.show', compact('providerInsurance'));
    }

    /**
     * Show the form for editing the specified provider insurance record
     */
    public function edit(ProviderInsurance $providerInsurance): View
    {
        $providerInsurance->load(['provider', 'verifiedBy']);

        return view('provider-insurance.edit', compact('providerInsurance'));
    }

    /**
     * Update the specified provider insurance record
     */
    public function update(UpdateProviderInsuranceRequest $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('documents')) {
            $documents = $providerInsurance->documents ?? [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('insurance-documents', 'public');
                $documents[] = $path;
            }
            $data['documents'] = $documents;
        }

        $this->service->update($providerInsurance, $data);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance updated successfully.');
    }

    /**
     * Remove the specified provider insurance record
     */
    public function destroy(ProviderInsurance $providerInsurance): RedirectResponse
    {
        // Remove associated documents
        if ($providerInsurance->documents) {
            foreach ($providerInsurance->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        $this->service->delete($providerInsurance);

        return redirect()->route('provider-insurance.index')
            ->with('success', 'Provider insurance deleted successfully.');
    }

    /**
     * Verify provider insurance
     */
    public function verify(VerifyProviderInsuranceRequest $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $data = $request->validated();

        $this->service->verify(
            $providerInsurance,
            Auth::id(),
            $data['notes'] ?? null
        );

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance verified successfully.');
    }

    /**
     * Reject provider insurance
     */
    public function reject(Request $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->service->reject(
            $providerInsurance,
            Auth::id(),
            $request->reason
        );

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance rejected successfully.');
    }

    /**
     * Renew provider insurance
     */
    public function renew(RenewProviderInsuranceRequest $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $data = $request->validated();

        $this->service->renew($providerInsurance, $data);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance renewed successfully.');
    }

    /**
     * Activate provider insurance
     */
    public function activate(ProviderInsurance $providerInsurance): RedirectResponse
    {
        $this->service->activate($providerInsurance);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance activated successfully.');
    }

    /**
     * Deactivate provider insurance
     */
    public function deactivate(ProviderInsurance $providerInsurance): RedirectResponse
    {
        $this->service->deactivate($providerInsurance);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance deactivated successfully.');
    }

    /**
     * Expire provider insurance
     */
    public function expire(ProviderInsurance $providerInsurance): RedirectResponse
    {
        $this->service->expire($providerInsurance);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance expired successfully.');
    }

    /**
     * Cancel provider insurance
     */
    public function cancel(Request $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->service->cancel($providerInsurance, $request->reason);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance cancelled successfully.');
    }

    /**
     * Suspend provider insurance
     */
    public function suspend(Request $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->service->suspend($providerInsurance, $request->reason);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Provider insurance suspended successfully.');
    }

    /**
     * Upload insurance document
     */
    public function uploadDocument(UploadInsuranceDocumentRequest $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('insurance-documents', 'public');

        $this->service->addDocument($providerInsurance, $path);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Remove insurance document
     */
    public function removeDocument(Request $request, ProviderInsurance $providerInsurance): RedirectResponse
    {
        $request->validate([
            'document_path' => 'required|string',
        ]);

        $documentPath = $request->document_path;

        // Remove from storage
        Storage::disk('public')->delete($documentPath);

        $this->service->removeDocument($providerInsurance, $documentPath);

        return redirect()->route('provider-insurance.show', $providerInsurance)
            ->with('success', 'Document removed successfully.');
    }

    /**
     * Search provider insurance
     */
    public function search(SearchProviderInsuranceRequest $request): View
    {
        $query = $request->get('q');
        $providerId = $request->get('provider_id');
        $insuranceType = $request->get('insurance_type');
        $status = $request->get('status');
        $verificationStatus = $request->get('verification_status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $minCoverage = $request->get('min_coverage');
        $maxCoverage = $request->get('max_coverage');

        $results = $this->service->searchInsurance($query);

        // Apply additional filters
        if ($providerId) {
            $results = $results->where('provider_id', $providerId);
        }

        if ($insuranceType) {
            $results = $results->where('insurance_type', $insuranceType);
        }

        if ($status) {
            $results = $results->where('status', $status);
        }

        if ($verificationStatus) {
            $results = $results->where('verification_status', $verificationStatus);
        }

        if ($startDate && $endDate) {
            $results = $results->whereBetween('start_date', [$startDate, $endDate]);
        }

        if ($minCoverage && $maxCoverage) {
            $results = $results->whereBetween('coverage_amount', [$minCoverage, $maxCoverage]);
        }

        $results = $results->paginate(15);

        return view('provider-insurance.search', compact('results', 'query', 'providerId', 'insuranceType', 'status', 'verificationStatus', 'startDate', 'endDate', 'minCoverage', 'maxCoverage'));
    }

    /**
     * Show analytics dashboard
     */
    public function analytics(Request $request): View
    {
        $providerId = $request->get('provider_id');

        if ($providerId) {
            $analytics = $this->service->getInsuranceAnalytics($providerId);
        } else {
            $analytics = $this->service->getGlobalInsuranceAnalytics();
        }

        return view('provider-insurance.analytics', compact('analytics', 'providerId'));
    }

    /**
     * Export provider insurance data
     */
    public function export(Request $request): JsonResponse
    {
        $format = $request->get('format', 'json');
        $providerId = $request->get('provider_id');

        if ($providerId) {
            $data = $this->service->findByProviderIdDTO($providerId);
        } else {
            $data = $this->service->allDTO();
        }

        if ($format === 'csv') {
            // Return CSV download response
            return response()->json(['message' => 'CSV export not implemented yet']);
        }

        return response()->json($data);
    }

    /**
     * Bulk operations on provider insurance
     */
    public function bulkOperations(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,expire,verify,reject',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:provider_insurance,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;
        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $providerInsurance = ProviderInsurance::find($id);

                switch ($action) {
                    case 'activate':
                        $this->service->activate($providerInsurance);
                        break;
                    case 'deactivate':
                        $this->service->deactivate($providerInsurance);
                        break;
                    case 'expire':
                        $this->service->expire($providerInsurance);
                        break;
                    case 'verify':
                        $this->service->verify($providerInsurance, Auth::id());
                        break;
                    case 'reject':
                        $this->service->reject($providerInsurance, Auth::id(), 'Bulk rejection');
                        break;
                }

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$id}: ".$e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$action} operation completed on {$successCount} records",
            'success_count' => $successCount,
            'errors' => $errors,
        ]);
    }
}
