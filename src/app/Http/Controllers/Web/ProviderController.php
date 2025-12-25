<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Services\ProviderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProviderController extends Controller
{
    protected ProviderService $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
        $this->authorizeResource(Provider::class, 'provider');
    }

    /**
     * Display a listing of providers
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $providers = $this->providerService->getPaginatedProviders($perPage);

        return view('providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new provider
     */
    public function create(): View
    {
        return view('providers.create');
    }

    /**
     * Store a newly created provider
     */
    public function store(Request $request): RedirectResponse
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

            return redirect()->route('providers.show', $provider)
                ->with('success', 'Provider created successfully');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create provider: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified provider
     */
    public function show(Provider $provider): View
    {
        $provider->load(['user', 'orders', 'products']);

        return view('providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified provider
     */
    public function edit(Provider $provider): View
    {
        return view('providers.edit', compact('provider'));
    }

    /**
     * Update the specified provider
     */
    public function update(Request $request, Provider $provider): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'company_name' => 'sometimes|string|max:255',
                'contact_person' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:providers,email,'.$provider->id,
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
                return redirect()->route('providers.show', $provider)
                    ->with('success', 'Provider updated successfully');
            }

            return back()->with('error', 'Failed to update provider')->withInput();

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update provider: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified provider
     */
    public function destroy(Provider $provider): RedirectResponse
    {
        try {
            $deleted = $this->providerService->deleteProvider($provider);

            if ($deleted) {
                return redirect()->route('providers.index')
                    ->with('success', 'Provider deleted successfully');
            }

            return back()->with('error', 'Failed to delete provider');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete provider: '.$e->getMessage());
        }
    }

    /**
     * Provider dashboard
     */
    public function dashboard(): View
    {
        $stats = $this->providerService->getProviderStats();
        $recentProviders = $this->providerService->getRecentProviders(5);
        $topProviders = $this->providerService->getTopRatedProviders(5);

        return view('providers.dashboard', compact('stats', 'recentProviders', 'topProviders'));
    }

    /**
     * Provider directory
     */
    public function directory(Request $request): View
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $status = $request->get('status');
        $perPage = $request->get('per_page', 20);

        $providers = $this->providerService->searchProviders($query, $type, $status, $perPage);

        return view('providers.directory', compact('providers', 'query', 'type', 'status'));
    }

    /**
     * Provider analytics dashboard
     */
    public function analytics(): View
    {
        $stats = $this->providerService->getProviderStats();
        $performanceStats = $this->providerService->getProviderPerformanceStats();
        $financialStats = $this->providerService->getProviderFinancialStats();
        $qualityStats = $this->providerService->getProviderQualityStats();

        return view('providers.analytics', compact('stats', 'performanceStats', 'financialStats', 'qualityStats'));
    }

    /**
     * Provider import/export interface
     */
    public function importExport(): View
    {
        return view('providers.import-export');
    }

    /**
     * Import providers from file
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
            ]);

            $imported = $this->providerService->importProviders($request->file('file'));

            return back()->with('success', "Successfully imported {$imported} providers");

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Export providers to file
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $filters = $request->only(['type', 'status', 'date_from', 'date_to']);

            $file = $this->providerService->exportProviders($format, $filters);

            return response()->download($file, "providers_{$format}.{$format}");

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    /**
     * Provider performance management interface
     */
    public function performance(): View
    {
        $topPerformers = $this->providerService->getTopPerformingProviders(10);
        $underPerformers = $this->providerService->getUnderPerformingProviders(10);
        $performanceMetrics = $this->providerService->getProviderPerformanceStats();

        return view('providers.performance', compact('topPerformers', 'underPerformers', 'performanceMetrics'));
    }

    /**
     * Provider contract management
     */
    public function contracts(): View
    {
        $activeContracts = $this->providerService->getProvidersWithActiveContracts();
        $expiringContracts = $this->providerService->getProvidersWithExpiringContracts(30);
        $expiredContracts = $this->providerService->getProvidersWithExpiredContracts();

        return view('providers.contracts', compact('activeContracts', 'expiringContracts', 'expiredContracts'));
    }

    /**
     * Provider financial management
     */
    public function financials(): View
    {
        $financialStats = $this->providerService->getProviderFinancialStats();
        $highCreditProviders = $this->providerService->getProvidersWithHighCreditLimits();
        $negativeBalanceProviders = $this->providerService->getProvidersWithNegativeBalances();

        return view('providers.financials', compact('financialStats', 'highCreditProviders', 'negativeBalanceProviders'));
    }

    /**
     * Provider quality management
     */
    public function quality(): View
    {
        $qualityStats = $this->providerService->getProviderQualityStats();
        $lowQualityProviders = $this->providerService->getProvidersWithLowRatings(3.0);
        $highQualityProviders = $this->providerService->getProvidersWithHighRatings(4.5);

        return view('providers.quality', compact('qualityStats', 'lowQualityProviders', 'highQualityProviders'));
    }

    /**
     * Provider qualification management
     */
    public function qualifications(): View
    {
        $providers = $this->providerService->getAllProviders();
        $specializations = $this->providerService->getAllSpecializations();
        $certifications = $this->providerService->getAllCertifications();

        return view('providers.qualifications', compact('providers', 'specializations', 'certifications'));
    }

    /**
     * Activate a provider
     */
    public function activate(Provider $provider): RedirectResponse
    {
        try {
            $activated = $this->providerService->activateProvider($provider);

            if ($activated) {
                return back()->with('success', 'Provider activated successfully');
            }

            return back()->with('error', 'Failed to activate provider');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate provider: '.$e->getMessage());
        }
    }

    /**
     * Deactivate a provider
     */
    public function deactivate(Provider $provider): RedirectResponse
    {
        try {
            $deactivated = $this->providerService->deactivateProvider($provider);

            if ($deactivated) {
                return back()->with('success', 'Provider deactivated successfully');
            }

            return back()->with('error', 'Failed to deactivate provider');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate provider: '.$e->getMessage());
        }
    }

    /**
     * Suspend a provider
     */
    public function suspend(Request $request, Provider $provider): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $suspended = $this->providerService->suspendProvider($provider, $validated['reason'] ?? null);

            if ($suspended) {
                return back()->with('success', 'Provider suspended successfully');
            }

            return back()->with('error', 'Failed to suspend provider');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to suspend provider: '.$e->getMessage());
        }
    }

    /**
     * Update provider rating
     */
    public function updateRating(Request $request, Provider $provider): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|numeric|min:0|max:5',
            ]);

            $updated = $this->providerService->updateProviderRating($provider, $validated['rating']);

            if ($updated) {
                return back()->with('success', 'Provider rating updated successfully');
            }

            return back()->with('error', 'Failed to update provider rating');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update provider rating: '.$e->getMessage());
        }
    }

    /**
     * Update provider credit limit
     */
    public function updateCreditLimit(Request $request, Provider $provider): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'credit_limit' => 'required|numeric|min:0',
            ]);

            $updated = $this->providerService->updateProviderCreditLimit($provider, $validated['credit_limit']);

            if ($updated) {
                return back()->with('success', 'Provider credit limit updated successfully');
            }

            return back()->with('error', 'Failed to update provider credit limit');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update provider credit limit: '.$e->getMessage());
        }
    }

    /**
     * Extend provider contract
     */
    public function extendContract(Request $request, Provider $provider): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'contract_end_date' => 'required|date|after:today',
            ]);

            $extended = $this->providerService->extendProviderContract($provider, $validated['contract_end_date']);

            if ($extended) {
                return back()->with('success', 'Provider contract extended successfully');
            }

            return back()->with('error', 'Failed to extend provider contract');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to extend provider contract: '.$e->getMessage());
        }
    }
}
