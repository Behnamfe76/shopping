<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Services\CustomerPreferenceService;
use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CustomerPreferenceController extends Controller
{
    public function __construct(
        private CustomerPreferenceService $preferenceService
    ) {}

    /**
     * Display customer preference dashboard.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerPreference::class);

        $perPage = $request->get('per_page', 15);
        $preferences = $this->preferenceService->getPaginatedPreferences($perPage);
        $stats = $this->preferenceService->getPreferenceStats();

        return view('customer-preferences.index', compact('preferences', 'stats'));
    }

    /**
     * Show the form for creating a new customer preference.
     */
    public function create(): View
    {
        $this->authorize('create', CustomerPreference::class);

        $customers = \Fereydooni\Shopping\app\Models\Customer::all();
        $templates = $this->preferenceService->getPreferenceTemplates();

        return view('customer-preferences.create', compact('customers', 'templates'));
    }

    /**
     * Store a newly created customer preference.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CustomerPreference::class);

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

            return redirect()->route('customer-preferences.show', $preference->id)
                ->with('success', 'Customer preference created successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create customer preference: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified customer preference.
     */
    public function show(CustomerPreference $preference): View
    {
        $this->authorize('view', $preference);

        $preferenceDTO = $this->preferenceService->getPreferenceDTO($preference->id);
        $customer = $preference->customer;

        return view('customer-preferences.show', compact('preference', 'preferenceDTO', 'customer'));
    }

    /**
     * Show the form for editing the specified customer preference.
     */
    public function edit(CustomerPreference $preference): View
    {
        $this->authorize('update', $preference);

        $preferenceDTO = $this->preferenceService->getPreferenceDTO($preference->id);
        $customers = \Fereydooni\Shopping\app\Models\Customer::all();

        return view('customer-preferences.edit', compact('preference', 'preferenceDTO', 'customers'));
    }

    /**
     * Update the specified customer preference.
     */
    public function update(Request $request, CustomerPreference $preference): RedirectResponse
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

            if (!$updatedPreference) {
                return back()->with('error', 'Failed to update customer preference.')->withInput();
            }

            return redirect()->route('customer-preferences.show', $preference->id)
                ->with('success', 'Customer preference updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update customer preference: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified customer preference.
     */
    public function destroy(CustomerPreference $preference): RedirectResponse
    {
        $this->authorize('delete', $preference);

        try {
            $deleted = $this->preferenceService->deletePreference($preference);

            if (!$deleted) {
                return back()->with('error', 'Failed to delete customer preference.');
            }

            return redirect()->route('customer-preferences.index')
                ->with('success', 'Customer preference deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer preference: ' . $e->getMessage());
        }
    }

    /**
     * Customer preference management interface.
     */
    public function manage(Customer $customer): View
    {
        $this->authorize('getPreference', $customer);

        $preferences = $this->preferenceService->getAllCustomerPreferences($customer->id);
        $preferencesDTO = $this->preferenceService->getAllCustomerPreferencesDTO($customer->id);
        $stats = $this->preferenceService->getCustomerPreferenceStats($customer->id);
        $templates = $this->preferenceService->getPreferenceTemplates();

        return view('customer-preferences.manage', compact('customer', 'preferences', 'preferencesDTO', 'stats', 'templates'));
    }

    /**
     * Preference list and search interface.
     */
    public function list(Request $request): View
    {
        $this->authorize('viewAny', CustomerPreference::class);

        $query = $request->get('query');
        $customerId = $request->get('customer_id');
        $type = $request->get('type');
        $category = $request->get('category');

        if ($query) {
            if ($customerId) {
                $preferences = $this->preferenceService->searchCustomerPreferences($customerId, $query);
            } else {
                $preferences = $this->preferenceService->searchPreferences($query);
            }
        } else {
            $preferences = $this->preferenceService->getAllPreferences();
        }

        $customers = \Fereydooni\Shopping\app\Models\Customer::all();

        return view('customer-preferences.list', compact('preferences', 'customers', 'query', 'customerId', 'type', 'category'));
    }

    /**
     * Preference import/export interface.
     */
    public function importExport(Customer $customer): View
    {
        $this->authorize('importPreferences', $customer);

        $exportedPreferences = $this->preferenceService->exportCustomerPreferences($customer->id);
        $templates = $this->preferenceService->getPreferenceTemplates();

        return view('customer-preferences.import-export', compact('customer', 'exportedPreferences', 'templates'));
    }

    /**
     * Import preferences from file.
     */
    public function import(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('importPreferences', $customer);

        try {
            $request->validate([
                'preferences_file' => 'required|file|mimes:json,csv',
            ]);

            $file = $request->file('preferences_file');
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'json') {
                $preferences = json_decode($file->getContents(), true);
            } else {
                // Handle CSV import
                $preferences = $this->parseCsvPreferences($file);
            }

            $imported = $this->preferenceService->importCustomerPreferences($customer->id, $preferences);

            if (!$imported) {
                return back()->with('error', 'Failed to import preferences.');
            }

            return back()->with('success', 'Preferences imported successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import preferences: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Export preferences to file.
     */
    public function export(Customer $customer, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('exportPreferences', $customer);

        try {
            $format = $request->get('format', 'json');
            $preferences = $this->preferenceService->exportCustomerPreferences($customer->id);

            if ($format === 'csv') {
                return $this->exportToCsv($preferences, $customer);
            } else {
                return $this->exportToJson($preferences, $customer);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export preferences: ' . $e->getMessage());
        }
    }

    /**
     * Preference templates management interface.
     */
    public function templates(): View
    {
        $this->authorize('manageTemplates', CustomerPreference::class);

        $templates = $this->preferenceService->getPreferenceTemplates();

        return view('customer-preferences.templates', compact('templates'));
    }

    /**
     * Apply template to customer.
     */
    public function applyTemplate(Customer $customer, Request $request): RedirectResponse
    {
        $this->authorize('applyTemplate', $customer);

        try {
            $validated = $request->validate([
                'template_name' => 'required|string',
            ]);

            $applied = $this->preferenceService->applyPreferenceTemplate($customer->id, $validated['template_name']);

            if (!$applied) {
                return back()->with('error', 'Failed to apply template.');
            }

            return back()->with('success', 'Template applied successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to apply template: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Preference analytics dashboard.
     */
    public function analytics(Request $request): View
    {
        $this->authorize('viewAnalytics', CustomerPreference::class);

        $customerId = $request->get('customer_id');

        if ($customerId) {
            $analytics = $this->preferenceService->getCustomerPreferenceAnalytics($customerId);
            $customer = \Fereydooni\Shopping\app\Models\Customer::find($customerId);
        } else {
            $analytics = $this->preferenceService->getGlobalPreferenceAnalytics();
            $customer = null;
        }

        $customers = \Fereydooni\Shopping\app\Models\Customer::all();

        return view('customer-preferences.analytics', compact('analytics', 'customer', 'customers'));
    }

    /**
     * Preference settings interface.
     */
    public function settings(): View
    {
        $this->authorize('manageDefaults', CustomerPreference::class);

        $defaultPreferences = $this->preferenceService->getDefaultPreferences();
        $templates = $this->preferenceService->getPreferenceTemplates();

        return view('customer-preferences.settings', compact('defaultPreferences', 'templates'));
    }

    /**
     * Preference backup/restore interface.
     */
    public function backupRestore(Customer $customer): View
    {
        $this->authorize('backupPreferences', $customer);

        $backup = $this->preferenceService->backupCustomerPreferences($customer->id);

        return view('customer-preferences.backup-restore', compact('customer', 'backup'));
    }

    /**
     * Restore preferences from backup.
     */
    public function restore(Customer $customer, Request $request): RedirectResponse
    {
        $this->authorize('restorePreferences', $customer);

        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:json',
            ]);

            $file = $request->file('backup_file');
            $backup = json_decode($file->getContents(), true);

            $restored = $this->preferenceService->restoreCustomerPreferences($customer->id, $backup);

            if (!$restored) {
                return back()->with('error', 'Failed to restore preferences.');
            }

            return back()->with('success', 'Preferences restored successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore preferences: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Activate preference.
     */
    public function activate(CustomerPreference $preference, Request $request): RedirectResponse
    {
        $this->authorize('activate', $preference);

        try {
            $reason = $request->get('reason');
            $activated = $this->preferenceService->activateCustomerPreference($preference, $reason);

            if (!$activated) {
                return back()->with('error', 'Failed to activate preference.');
            }

            return back()->with('success', 'Preference activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate preference: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate preference.
     */
    public function deactivate(CustomerPreference $preference, Request $request): RedirectResponse
    {
        $this->authorize('deactivate', $preference);

        try {
            $reason = $request->get('reason');
            $deactivated = $this->preferenceService->deactivateCustomerPreference($preference, $reason);

            if (!$deactivated) {
                return back()->with('error', 'Failed to deactivate preference.');
            }

            return back()->with('success', 'Preference deactivated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate preference: ' . $e->getMessage());
        }
    }

    /**
     * Initialize customer preferences.
     */
    public function initialize(Customer $customer): RedirectResponse
    {
        $this->authorize('initializePreferences', $customer);

        try {
            $initialized = $this->preferenceService->initializeCustomerPreferences($customer->id);

            if (!$initialized) {
                return back()->with('error', 'Failed to initialize preferences.');
            }

            return back()->with('success', 'Customer preferences initialized successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initialize preferences: ' . $e->getMessage());
        }
    }

    /**
     * Parse CSV preferences file.
     */
    private function parseCsvPreferences($file): array
    {
        $preferences = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) >= 3) {
                $preferences[] = [
                    'key' => $data[0],
                    'value' => $data[1],
                    'type' => $data[2] ?? 'string',
                    'description' => $data[3] ?? null,
                ];
            }
        }
        
        fclose($handle);
        
        return $preferences;
    }

    /**
     * Export preferences to CSV.
     */
    private function exportToCsv(array $preferences, Customer $customer): \Symfony\Component\HttpFoundation\Response
    {
        $filename = "customer_{$customer->id}_preferences.csv";
        
        $handle = fopen('php://temp', 'r+');
        
        // Write header
        fputcsv($handle, ['Key', 'Value', 'Type', 'Description', 'Active', 'Created At']);
        
        // Write data
        foreach ($preferences as $key => $preference) {
            fputcsv($handle, [
                $key,
                $preference['value'],
                $preference['type'],
                $preference['description'] ?? '',
                $preference['is_active'] ? 'Yes' : 'No',
                $preference['created_at'] ?? '',
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export preferences to JSON.
     */
    private function exportToJson(array $preferences, Customer $customer): \Symfony\Component\HttpFoundation\Response
    {
        $filename = "customer_{$customer->id}_preferences.json";
        
        return response()->json($preferences)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}

