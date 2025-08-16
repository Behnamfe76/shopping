<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\Transaction;
use Fereydooni\Shopping\app\DTOs\TransactionDTO;
use Fereydooni\Shopping\app\Http\Requests\StoreTransactionRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateTransactionRequest;
use Fereydooni\Shopping\app\Http\Requests\ProcessTransactionRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchTransactionRequest;
use Fereydooni\Shopping\app\Http\Resources\TransactionResource;
use Fereydooni\Shopping\app\Http\Resources\TransactionCollection;
use Fereydooni\Shopping\app\Http\Resources\TransactionSearchResource;
use Fereydooni\Shopping\app\Http\Resources\TransactionStatisticsResource;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->paginate($perPage);

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $transaction = app('shopping.transaction')->create($request->validated());

        return response()->json([
            'data' => TransactionResource::make($transaction),
            'message' => 'Transaction created successfully.',
        ], 201);
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        return response()->json([
            'data' => TransactionResource::make($transaction),
        ]);
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);

        app('shopping.transaction')->update($transaction, $request->validated());

        return response()->json([
            'data' => TransactionResource::make($transaction->fresh()),
            'message' => 'Transaction updated successfully.',
        ]);
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);

        app('shopping.transaction')->delete($transaction);

        return response()->json([
            'message' => 'Transaction deleted successfully.',
        ]);
    }

    /**
     * Mark transaction as success.
     */
    public function markAsSuccess(ProcessTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('markAsSuccess', $transaction);

        app('shopping.transaction')->markAsSuccess($transaction, $request->validated());

        return response()->json([
            'data' => TransactionResource::make($transaction->fresh()),
            'message' => 'Transaction marked as success.',
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(ProcessTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('markAsFailed', $transaction);

        app('shopping.transaction')->markAsFailed($transaction, $request->validated());

        return response()->json([
            'data' => TransactionResource::make($transaction->fresh()),
            'message' => 'Transaction marked as failed.',
        ]);
    }

    /**
     * Mark transaction as refunded.
     */
    public function markAsRefunded(ProcessTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('markAsRefunded', $transaction);

        app('shopping.transaction')->markAsRefunded($transaction, $request->validated());

        return response()->json([
            'data' => TransactionResource::make($transaction->fresh()),
            'message' => 'Transaction marked as refunded.',
        ]);
    }

    /**
     * Search transactions.
     */
    public function search(SearchTransactionRequest $request): JsonResponse
    {
        $this->authorize('search', Transaction::class);

        $query = $request->get('query');
        $transactions = app('shopping.transaction')->search($query);

        return response()->json([
            'data' => TransactionSearchResource::collection($transactions),
            'meta' => [
                'query' => $query,
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get transaction statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewStatistics', Transaction::class);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $gateway = $request->get('gateway');
        $status = $request->get('status');

        $statistics = app('shopping.transaction')->getTransactionStatistics();

        if ($startDate && $endDate) {
            $statistics = app('shopping.transaction')->getTransactionStatisticsByDateRange($startDate, $endDate);
        }

        if ($gateway) {
            $statistics = app('shopping.transaction')->getTransactionStatisticsByGateway($gateway);
        }

        if ($status) {
            $statistics = app('shopping.transaction')->getTransactionStatisticsByStatus($status);
        }

        return response()->json([
            'data' => TransactionStatisticsResource::make($statistics),
        ]);
    }

    /**
     * Get transaction count.
     */
    public function getCount(Request $request): JsonResponse
    {
        $this->authorize('viewCount', Transaction::class);

        $status = $request->get('status');
        $userId = $request->get('user_id');
        $gateway = $request->get('gateway');

        $count = app('shopping.transaction')->getTransactionCount();

        if ($status) {
            $count = app('shopping.transaction')->getTransactionCountByStatus($status);
        }

        if ($userId) {
            $count = app('shopping.transaction')->getTransactionCountByUserId($userId);
        }

        if ($gateway) {
            $count = app('shopping.transaction')->getTransactionCountByGateway($gateway);
        }

        return response()->json([
            'data' => [
                'count' => $count,
                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'gateway' => $gateway,
                ],
            ],
        ]);
    }

    /**
     * Get transaction revenue.
     */
    public function getRevenue(Request $request): JsonResponse
    {
        $this->authorize('viewRevenue', Transaction::class);

        $status = $request->get('status');
        $userId = $request->get('user_id');
        $gateway = $request->get('gateway');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $revenue = app('shopping.transaction')->getTotalAmount();

        if ($status) {
            $revenue = app('shopping.transaction')->getTotalAmountByStatus($status);
        }

        if ($userId) {
            $revenue = app('shopping.transaction')->getTotalAmountByUserId($userId);
        }

        if ($gateway) {
            $revenue = app('shopping.transaction')->getTotalAmountByGateway($gateway);
        }

        if ($startDate && $endDate) {
            $revenue = app('shopping.transaction')->getTotalAmountByDateRange($startDate, $endDate);
        }

        return response()->json([
            'data' => [
                'revenue' => $revenue,
                'currency' => 'USD', // This should be configurable
                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'gateway' => $gateway,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ],
        ]);
    }

    /**
     * Get transactions by gateway.
     */
    public function getByGateway(Request $request, string $gateway): JsonResponse
    {
        $this->authorize('viewByGateway', $gateway);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->getTransactionsByGateway($gateway);

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'gateway' => $gateway,
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get transactions by status.
     */
    public function getByStatus(Request $request, string $status): JsonResponse
    {
        $this->authorize('viewByStatus', $status);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->findByStatus($status);

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'status' => $status,
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get recent transactions.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $limit = $request->get('limit', 10);
        $transactions = app('shopping.transaction')->getRecentTransactions($limit);

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'limit' => $limit,
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get successful transactions.
     */
    public function getSuccessful(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = app('shopping.transaction')->getSuccessfulTransactions();

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get failed transactions.
     */
    public function getFailed(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = app('shopping.transaction')->getFailedTransactions();

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Get refunded transactions.
     */
    public function getRefunded(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = app('shopping.transaction')->getRefundedTransactions();

        return response()->json([
            'data' => TransactionCollection::make($transactions),
            'meta' => [
                'count' => $transactions->count(),
            ],
        ]);
    }

    /**
     * Validate transaction data.
     */
    public function validate(Request $request): JsonResponse
    {
        $this->authorize('validate', Transaction::class);

        $data = $request->all();
        $isValid = app('shopping.transaction')->validateTransaction($data);

        return response()->json([
            'data' => [
                'valid' => $isValid,
                'message' => $isValid ? 'Transaction data is valid.' : 'Transaction data is invalid.',
            ],
        ]);
    }

}
