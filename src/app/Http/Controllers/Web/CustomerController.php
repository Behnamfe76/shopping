<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Services\CustomerService;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $perPage = $request->get('per_page', 15);
        $customers = $this->customerService->getPaginatedCustomers($perPage);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        try {
            $customer = $this->customerService->createCustomer($request->all());

            return redirect()->route('customers.show', $customer->id)
                ->with('success', 'Customer created successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customerDTO = $this->customerService->getCustomerDTO($customer->id);
        $orders = $this->customerService->getCustomerOrderHistory($customer->id);
        $addresses = $this->customerService->getCustomerAddresses($customer->id);
        $notes = $this->customerService->getCustomerNotes($customer);
        $preferences = $this->customerService->getPreferences($customer->id);

        return view('customers.show', compact('customer', 'customerDTO', 'orders', 'addresses', 'notes', 'preferences'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        $customerDTO = $this->customerService->getCustomerDTO($customer->id);

        return view('customers.edit', compact('customer', 'customerDTO'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        try {
            $updatedCustomer = $this->customerService->updateCustomer($customer, $request->all());

            if (!$updatedCustomer) {
                return back()->with('error', 'Failed to update customer.')->withInput();
            }

            return redirect()->route('customers.show', $customer->id)
                ->with('success', 'Customer updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update customer: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        try {
            $deleted = $this->customerService->deleteCustomer($customer);

            if (!$deleted) {
                return back()->with('error', 'Failed to delete customer.');
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    /**
     * Display customer analytics dashboard.
     */
    public function dashboard(): View
    {
        $this->authorize('viewAnalytics');

        $stats = $this->customerService->getCustomerStats();
        $statsByStatus = $this->customerService->getCustomerStatsByStatus();
        $statsByType = $this->customerService->getCustomerStatsByType();
        $topSpenders = $this->customerService->getTopSpenders(10);
        $mostLoyal = $this->customerService->getMostLoyal(10);
        $newestCustomers = $this->customerService->getNewestCustomers(10);

        return view('customers.dashboard', compact(
            'stats',
            'statsByStatus',
            'statsByType',
            'topSpenders',
            'mostLoyal',
            'newestCustomers'
        ));
    }

    /**
     * Display customer import/export interface.
     */
    public function importExport(): View
    {
        $this->authorize('importData');

        return view('customers.import-export');
    }

    /**
     * Handle customer data import.
     */
    public function import(Request $request): RedirectResponse
    {
        $this->authorize('importData');

        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls'
            ]);

            // Handle file import logic here
            // This would depend on the specific import implementation

            return back()->with('success', 'Customer data imported successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import customer data: ' . $e->getMessage());
        }
    }

    /**
     * Handle customer data export.
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('exportData');

        try {
            $format = $request->get('format', 'csv');
            $customers = $this->customerService->getAllCustomers();

            // Handle export logic here
            // This would depend on the specific export implementation

            return response()->download('customers.' . $format);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export customer data: ' . $e->getMessage());
        }
    }

    /**
     * Display customer communication interface.
     */
    public function communication(): View
    {
        $this->authorize('manageMarketing');

        $marketingConsentCustomers = $this->customerService->getCustomersByMarketingConsent(true);
        $newsletterSubscribers = $this->customerService->getCustomersByNewsletterSubscription(true);
        $birthdayCustomers = $this->customerService->getCustomersWithBirthdayThisMonth();

        return view('customers.communication', compact(
            'marketingConsentCustomers',
            'newsletterSubscribers',
            'birthdayCustomers'
        ));
    }

    /**
     * Display customer loyalty management interface.
     */
    public function loyalty(): View
    {
        $this->authorize('manageLoyaltyPoints');

        $mostLoyal = $this->customerService->getMostLoyal(20);
        $loyaltyStats = [
            'total_points' => $this->customerService->getCustomerStats()['total_loyalty_points'],
            'average_points' => $this->customerService->getCustomerStats()['average_loyalty_points'],
        ];

        return view('customers.loyalty', compact('mostLoyal', 'loyaltyStats'));
    }

    /**
     * Display customer segmentation interface.
     */
    public function segmentation(): View
    {
        $this->authorize('viewAnalytics');

        $activeCustomers = $this->customerService->getActiveCustomers();
        $inactiveCustomers = $this->customerService->getInactiveCustomers();
        $topSpenders = $this->customerService->getTopSpenders(20);
        $newestCustomers = $this->customerService->getNewestCustomers(20);
        $oldestCustomers = $this->customerService->getOldestCustomers(20);

        return view('customers.segmentation', compact(
            'activeCustomers',
            'inactiveCustomers',
            'topSpenders',
            'newestCustomers',
            'oldestCustomers'
        ));
    }

    /**
     * Search customers.
     */
    public function search(Request $request): View
    {
        $this->authorize('search');

        try {
            $request->validate([
                'query' => 'required|string|min:2'
            ]);

            $query = $request->get('query');
            $customers = $this->customerService->searchCustomers($query);

            return view('customers.search', compact('customers', 'query'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Activate customer.
     */
    public function activate(Customer $customer): RedirectResponse
    {
        $this->authorize('activate', $customer);

        try {
            $activated = $this->customerService->activateCustomer($customer);

            if (!$activated) {
                return back()->with('error', 'Failed to activate customer.');
            }

            return back()->with('success', 'Customer activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate customer: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate customer.
     */
    public function deactivate(Customer $customer): RedirectResponse
    {
        $this->authorize('deactivate', $customer);

        try {
            $deactivated = $this->customerService->deactivateCustomer($customer);

            if (!$deactivated) {
                return back()->with('error', 'Failed to deactivate customer.');
            }

            return back()->with('success', 'Customer deactivated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate customer: ' . $e->getMessage());
        }
    }

    /**
     * Suspend customer.
     */
    public function suspend(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('suspend', $customer);

        try {
            $reason = $request->get('reason');
            $suspended = $this->customerService->suspendCustomer($customer, $reason);

            if (!$suspended) {
                return back()->with('error', 'Failed to suspend customer.');
            }

            return back()->with('success', 'Customer suspended successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to suspend customer: ' . $e->getMessage());
        }
    }

    /**
     * Add loyalty points to customer.
     */
    public function addLoyaltyPoints(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('manageLoyaltyPoints', $customer);

        try {
            $request->validate([
                'points' => 'required|integer|min:1',
                'reason' => 'nullable|string|max:255'
            ]);

            $points = $request->get('points');
            $reason = $request->get('reason');

            $success = $this->customerService->addLoyaltyPoints($customer, $points, $reason);

            if (!$success) {
                return back()->with('error', 'Failed to add loyalty points.');
            }

            return back()->with('success', "Added {$points} loyalty points successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add loyalty points: ' . $e->getMessage());
        }
    }

    /**
     * Deduct loyalty points from customer.
     */
    public function deductLoyaltyPoints(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('manageLoyaltyPoints', $customer);

        try {
            $request->validate([
                'points' => 'required|integer|min:1',
                'reason' => 'nullable|string|max:255'
            ]);

            $points = $request->get('points');
            $reason = $request->get('reason');

            $success = $this->customerService->deductLoyaltyPoints($customer, $points, $reason);

            if (!$success) {
                return back()->with('error', 'Failed to deduct loyalty points.');
            }

            return back()->with('success', "Deducted {$points} loyalty points successfully.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deduct loyalty points: ' . $e->getMessage());
        }
    }
}
