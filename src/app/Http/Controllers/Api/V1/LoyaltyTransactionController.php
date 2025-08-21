<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Services\LoyaltyTransactionService;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;

class LoyaltyTransactionController extends Controller
{
    protected LoyaltyTransactionService $service;

    public function __construct(LoyaltyTransactionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of loyalty transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $transactions = $this->service->paginate($perPage);

        return response()->json([
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created loyalty transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'user_id' => 'required|integer|exists:users,id',
            'transaction_type' => 'required|string|in:earned,redeemed,expired,reversed,bonus,adjustment',
            'points' => 'required|integer|min:1',
            'points_value' => 'required|numeric|min:0',
            'reference_type' => 'required|string|in:order,product,campaign,manual,system',
            'reference_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
            'reason' => 'nullable|string|max:500',
            'status' => 'required|string|in:pending,completed,failed,reversed,expired',
            'expires_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array',
        ]);

        $transaction = $this->service->create($validated);

        return response()->json([
            'data' => LoyaltyTransactionDTO::fromModel($transaction),
            'message' => 'Loyalty transaction created successfully',
        ], 201);
    }

    /**
     * Display the specified loyalty transaction.
     */
    public function show(int $id): JsonResponse
    {
        $transaction = $this->service->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Loyalty transaction not found',
            ], 404);
        }

        return response()->json([
            'data' => LoyaltyTransactionDTO::fromModel($transaction),
        ]);
    }

    /**
     * Update the specified loyalty transaction.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $transaction = $this->service->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Loyalty transaction not found',
            ], 404);
        }

        $validated = $request->validate([
            'customer_id' => 'sometimes|integer|exists:customers,id',
            'user_id' => 'sometimes|integer|exists:users,id',
            'transaction_type' => 'sometimes|string|in:earned,redeemed,expired,reversed,bonus,adjustment',
            'points' => 'sometimes|integer|min:1',
            'points_value' => 'sometimes|numeric|min:0',
            'reference_type' => 'sometimes|string|in:order,product,campaign,manual,system',
            'reference_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
            'reason' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:pending,completed,failed,reversed,expired',
            'expires_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array',
        ]);

        $updated = $this->service->update($transaction, $validated);

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update loyalty transaction',
            ], 500);
        }

        return response()->json([
            'data' => LoyaltyTransactionDTO::fromModel($transaction->fresh()),
            'message' => 'Loyalty transaction updated successfully',
        ]);
    }

    /**
     * Remove the specified loyalty transaction.
     */
    public function destroy(int $id): JsonResponse
    {
        $transaction = $this->service->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Loyalty transaction not found',
            ], 404);
        }

        $deleted = $this->service->delete($transaction);

        if (!$deleted) {
            return response()->json([
                'message' => 'Failed to delete loyalty transaction',
            ], 500);
        }

        return response()->json([
            'message' => 'Loyalty transaction deleted successfully',
        ]);
    }

    /**
     * Reverse a loyalty transaction.
     */
    public function reverse(Request $request, int $id): JsonResponse
    {
        $transaction = $this->service->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Loyalty transaction not found',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reversed = $this->service->reverse($transaction, $validated['reason'] ?? null);

        if (!$reversed) {
            return response()->json([
                'message' => 'Failed to reverse loyalty transaction',
            ], 500);
        }

        return response()->json([
            'data' => LoyaltyTransactionDTO::fromModel($transaction->fresh()),
            'message' => 'Loyalty transaction reversed successfully',
        ]);
    }

    /**
     * Add loyalty points to a customer.
     */
    public function addPoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        $transaction = $this->service->addPoints(
            $validated['customer_id'],
            $validated['points'],
            $validated['reason'] ?? null,
            $validated['metadata'] ?? []
        );

        return response()->json([
            'data' => LoyaltyTransactionDTO::fromModel($transaction),
            'message' => 'Loyalty points added successfully',
        ], 201);
    }

    /**
     * Deduct loyalty points from a customer.
     */
    public function deductPoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        try {
            $transaction = $this->service->deductPoints(
                $validated['customer_id'],
                $validated['points'],
                $validated['reason'] ?? null,
                $validated['metadata'] ?? []
            );

            return response()->json([
                'data' => LoyaltyTransactionDTO::fromModel($transaction),
                'message' => 'Loyalty points deducted successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get customer loyalty balance.
     */
    public function getBalance(int $customerId): JsonResponse
    {
        $balance = $this->service->calculateBalance($customerId);
        $balanceValue = $this->service->calculateBalanceValue($customerId);
        $tier = $this->service->calculateTier($customerId);

        return response()->json([
            'data' => [
                'customer_id' => $customerId,
                'balance' => $balance,
                'balance_value' => $balanceValue,
                'tier' => $tier,
            ],
        ]);
    }

    /**
     * Get customer loyalty tier.
     */
    public function getTier(int $customerId): JsonResponse
    {
        $tier = $this->service->calculateTier($customerId);

        return response()->json([
            'data' => [
                'customer_id' => $customerId,
                'tier' => $tier,
            ],
        ]);
    }

    /**
     * Get customer transaction history.
     */
    public function getHistory(int $customerId, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $transactions = $this->service->getCustomerTransactionHistoryDTO($customerId);

        return response()->json([
            'data' => $transactions,
        ]);
    }

    /**
     * Get customer transaction analytics.
     */
    public function getAnalytics(int $customerId): JsonResponse
    {
        $analytics = $this->service->getTransactionAnalytics($customerId);

        return response()->json([
            'data' => $analytics,
        ]);
    }

    /**
     * Search loyalty transactions.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:1',
            'customer_id' => 'nullable|integer|exists:customers,id',
        ]);

        if (isset($validated['customer_id'])) {
            $transactions = $this->service->searchByCustomerDTO($validated['customer_id'], $validated['query']);
        } else {
            $transactions = $this->service->searchDTO($validated['query']);
        }

        return response()->json([
            'data' => $transactions,
        ]);
    }

    /**
     * Get recent transactions.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $customerId = $request->get('customer_id');

        if ($customerId) {
            $transactions = $this->service->getRecentTransactionsByCustomerDTO($customerId, $limit);
        } else {
            $transactions = $this->service->getRecentTransactionsDTO($limit);
        }

        return response()->json([
            'data' => $transactions,
        ]);
    }

    /**
     * Export customer loyalty history.
     */
    public function exportHistory(int $customerId): JsonResponse
    {
        $history = $this->service->exportCustomerHistory($customerId);

        return response()->json([
            'data' => $history,
        ]);
    }

    /**
     * Get transaction statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $statistics = [
            'total_transactions' => $this->service->getTransactionCount(),
            'total_points_issued' => $this->service->getTotalPointsIssued(),
            'total_points_redeemed' => $this->service->getTotalPointsRedeemed(),
            'total_points_expired' => $this->service->getTotalPointsExpired(),
            'total_points_reversed' => $this->service->getTotalPointsReversed(),
            'average_points_per_transaction' => $this->service->getAveragePointsPerTransaction(),
            'average_points_per_customer' => $this->service->getAveragePointsPerCustomer(),
        ];

        return response()->json([
            'data' => $statistics,
        ]);
    }
}
