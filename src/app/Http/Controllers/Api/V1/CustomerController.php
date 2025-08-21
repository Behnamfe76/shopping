<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Services\CustomerService;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): ResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $customers = $this->customerService->getPaginatedCustomers($perPage);

        return JsonResource::collection($customers);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $customer = $this->customerService->createCustomer($request->all());

            return response()->json([
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        $customerDTO = $this->customerService->getCustomerDTO($customer->id);

        return response()->json([
            'data' => $customerDTO
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        try {
            $updatedCustomer = $this->customerService->updateCustomer($customer, $request->all());

            if (!$updatedCustomer) {
                return response()->json([
                    'message' => 'Failed to update customer'
                ], 500);
            }

            return response()->json([
                'message' => 'Customer updated successfully',
                'data' => $updatedCustomer
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        try {
            $deleted = $this->customerService->deleteCustomer($customer);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Failed to delete customer'
                ], 500);
            }

            return response()->json([
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate the specified customer.
     */
    public function activate(Customer $customer): JsonResponse
    {
        $this->authorize('activate', $customer);

        try {
            $activated = $this->customerService->activateCustomer($customer);

            if (!$activated) {
                return response()->json([
                    'message' => 'Failed to activate customer'
                ], 500);
            }

            return response()->json([
                'message' => 'Customer activated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to activate customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate the specified customer.
     */
    public function deactivate(Customer $customer): JsonResponse
    {
        $this->authorize('deactivate', $customer);

        try {
            $deactivated = $this->customerService->deactivateCustomer($customer);

            if (!$deactivated) {
                return response()->json([
                    'message' => 'Failed to deactivate customer'
                ], 500);
            }

            return response()->json([
                'message' => 'Customer deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deactivate customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend the specified customer.
     */
    public function suspend(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('suspend', $customer);

        try {
            $reason = $request->get('reason');
            $suspended = $this->customerService->suspendCustomer($customer, $reason);

            if (!$suspended) {
                return response()->json([
                    'message' => 'Failed to suspend customer'
                ], 500);
            }

            return response()->json([
                'message' => 'Customer suspended successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to suspend customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manage loyalty points for the specified customer.
     */
    public function loyaltyPoints(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('manageLoyaltyPoints', $customer);

        try {
            $action = $request->get('action'); // 'add' or 'deduct'
            $points = $request->get('points');
            $reason = $request->get('reason');

            if (!in_array($action, ['add', 'deduct'])) {
                return response()->json([
                    'message' => 'Invalid action. Use "add" or "deduct"'
                ], 400);
            }

            if ($action === 'add') {
                $success = $this->customerService->addLoyaltyPoints($customer, $points, $reason);
            } else {
                $success = $this->customerService->deductLoyaltyPoints($customer, $points, $reason);
            }

            if (!$success) {
                return response()->json([
                    'message' => "Failed to {$action} loyalty points"
                ], 500);
            }

            return response()->json([
                'message' => "Loyalty points {$action}ed successfully",
                'new_balance' => $this->customerService->getLoyaltyBalance($customer)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to manage loyalty points',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer orders.
     */
    public function orders(Customer $customer): JsonResponse
    {
        $this->authorize('viewOrderHistory', $customer);

        $orders = $this->customerService->getCustomerOrderHistory($customer->id);

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Get customer addresses.
     */
    public function addresses(Customer $customer): JsonResponse
    {
        $this->authorize('viewAddresses', $customer);

        $addresses = $this->customerService->getCustomerAddresses($customer->id);

        return response()->json([
            'data' => $addresses
        ]);
    }

    /**
     * Get customer reviews.
     */
    public function reviews(Customer $customer): JsonResponse
    {
        $this->authorize('viewReviews', $customer);

        $reviews = $this->customerService->getCustomerReviews($customer->id);

        return response()->json([
            'data' => $reviews
        ]);
    }

    /**
     * Get customer wishlist.
     */
    public function wishlist(Customer $customer): JsonResponse
    {
        $this->authorize('viewWishlist', $customer);

        $wishlist = $this->customerService->getCustomerWishlist($customer->id);

        return response()->json([
            'data' => $wishlist
        ]);
    }

    /**
     * Get customer analytics.
     */
    public function analytics(Customer $customer): JsonResponse
    {
        $this->authorize('viewAnalytics', $customer);

        $lifetimeValue = $this->customerService->getCustomerLifetimeValue($customer->id);
        $loyaltyBalance = $this->customerService->getLoyaltyBalance($customer);

        return response()->json([
            'data' => [
                'lifetime_value' => $lifetimeValue,
                'loyalty_balance' => $loyaltyBalance,
                'total_orders' => $customer->total_orders,
                'total_spent' => $customer->total_spent,
                'average_order_value' => $customer->average_order_value,
                'last_order_date' => $customer->last_order_date,
                'first_order_date' => $customer->first_order_date,
            ]
        ]);
    }

    /**
     * Add a note to the customer.
     */
    public function addNote(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('addNotes', $customer);

        try {
            $request->validate([
                'note' => 'required|string|max:1000',
                'type' => 'nullable|string|max:50'
            ]);

            $note = $request->get('note');
            $type = $request->get('type', 'general');

            $success = $this->customerService->addCustomerNote($customer, $note, $type);

            if (!$success) {
                return response()->json([
                    'message' => 'Failed to add note'
                ], 500);
            }

            return response()->json([
                'message' => 'Note added successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer notes.
     */
    public function notes(Customer $customer): JsonResponse
    {
        $this->authorize('viewNotes', $customer);

        $notes = $this->customerService->getCustomerNotes($customer);

        return response()->json([
            'data' => $notes
        ]);
    }

    /**
     * Update customer preferences.
     */
    public function updatePreferences(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('updatePreferences', $customer);

        try {
            $success = $this->customerService->updatePreferences($customer, $request->all());

            if (!$success) {
                return response()->json([
                    'message' => 'Failed to update preferences'
                ], 500);
            }

            return response()->json([
                'message' => 'Preferences updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers.
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorize('search');

        try {
            $request->validate([
                'query' => 'required|string|min:2'
            ]);

            $query = $request->get('query');
            $customers = $this->customerService->searchCustomers($query);

            return response()->json([
                'data' => $customers
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer statistics.
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewStats');

        $stats = $this->customerService->getCustomerStats();
        $statsByStatus = $this->customerService->getCustomerStatsByStatus();
        $statsByType = $this->customerService->getCustomerStatsByType();

        return response()->json([
            'data' => [
                'overall' => $stats,
                'by_status' => $statsByStatus,
                'by_type' => $statsByType
            ]
        ]);
    }
}
