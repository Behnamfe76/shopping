<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
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
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Transaction::class);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->paginate($perPage);

        return view('shopping::transactions.index', [
            'transactions' => $transactions,
            'statistics' => app('shopping.transaction')->getTransactionStatistics(),
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(): View
    {
        $this->authorize('create', Transaction::class);

        return view('shopping::transactions.create');
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $transaction = app('shopping.transaction')->create($request->validated());

        return redirect()
            ->route('shopping.transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);

        return view('shopping::transactions.show', [
            'transaction' => $transaction,
            'order' => $transaction->order,
            'user' => $transaction->user,
        ]);
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);

        return view('shopping::transactions.edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        app('shopping.transaction')->update($transaction, $request->validated());

        return redirect()
            ->route('shopping.transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        app('shopping.transaction')->delete($transaction);

        return redirect()
            ->route('shopping.transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Mark transaction as success.
     */
    public function markAsSuccess(ProcessTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('markAsSuccess', $transaction);

        app('shopping.transaction')->markAsSuccess($transaction, $request->validated());

        return redirect()
            ->route('shopping.transactions.show', $transaction)
            ->with('success', 'Transaction marked as success.');
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(ProcessTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('markAsFailed', $transaction);

        app('shopping.transaction')->markAsFailed($transaction, $request->validated());

        return redirect()
            ->route('shopping.transactions.show', $transaction)
            ->with('success', 'Transaction marked as failed.');
    }

    /**
     * Mark transaction as refunded.
     */
    public function markAsRefunded(ProcessTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('markAsRefunded', $transaction);

        app('shopping.transaction')->markAsRefunded($transaction, $request->validated());

        return redirect()
            ->route('shopping.transactions.show', $transaction)
            ->with('success', 'Transaction marked as refunded.');
    }

    /**
     * Search transactions.
     */
    public function search(SearchTransactionRequest $request): View
    {
        $this->authorize('search', Transaction::class);

        $query = $request->get('query');
        $transactions = app('shopping.transaction')->search($query);

        return view('shopping::transactions.search', [
            'transactions' => $transactions,
            'query' => $query,
        ]);
    }

    /**
     * Display transaction statistics.
     */
    public function statistics(Request $request): View
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

        return view('shopping::transactions.statistics', [
            'statistics' => $statistics,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gateway' => $gateway,
            'status' => $status,
        ]);
    }

    /**
     * Export transactions.
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', Transaction::class);

        $format = $request->get('format', 'csv');
        $filters = $request->only(['status', 'gateway', 'start_date', 'end_date']);

        // Implementation for export functionality
        // This would typically generate a file download

        return response()->json([
            'message' => 'Export functionality not implemented yet.',
            'filters' => $filters,
            'format' => $format,
        ]);
    }

    /**
     * Import transactions.
     */
    public function import(Request $request): RedirectResponse
    {
        $this->authorize('import', Transaction::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        // Implementation for import functionality
        // This would typically process uploaded file

        return redirect()
            ->route('shopping.transactions.index')
            ->with('success', 'Import functionality not implemented yet.');
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
            'valid' => $isValid,
            'message' => $isValid ? 'Transaction data is valid.' : 'Transaction data is invalid.',
        ]);
    }

    /**
     * Get transactions by gateway.
     */
    public function byGateway(Request $request, string $gateway): View
    {
        $this->authorize('viewByGateway', $gateway);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->getTransactionsByGateway($gateway);

        return view('shopping::transactions.by-gateway', [
            'transactions' => $transactions,
            'gateway' => $gateway,
        ]);
    }

    /**
     * Get transactions by status.
     */
    public function byStatus(Request $request, string $status): View
    {
        $this->authorize('viewByStatus', $status);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->findByStatus($status);

        return view('shopping::transactions.by-status', [
            'transactions' => $transactions,
            'status' => $status,
        ]);
    }

    /**
     * Get transactions by order.
     */
    public function byOrder(Request $request, int $orderId): View
    {
        $this->authorize('viewByOrder', $orderId);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->findByOrderId($orderId);

        return view('shopping::transactions.by-order', [
            'transactions' => $transactions,
            'orderId' => $orderId,
        ]);
    }

    /**
     * Get transactions by user.
     */
    public function byUser(Request $request, int $userId): View
    {
        $this->authorize('viewByUser', $userId);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->findByUserId($userId);

        return view('shopping::transactions.by-user', [
            'transactions' => $transactions,
            'userId' => $userId,
        ]);
    }

    /**
     * Get transactions by currency.
     */
    public function byCurrency(Request $request, string $currency): View
    {
        $this->authorize('viewByCurrency', $currency);

        $perPage = $request->get('per_page', 15);
        $transactions = app('shopping.transaction')->findByCurrency($currency);

        return view('shopping::transactions.by-currency', [
            'transactions' => $transactions,
            'currency' => $currency,
        ]);
    }

    /**
     * Get transaction revenue.
     */
    public function revenue(Request $request): View
    {
        $this->authorize('viewRevenue', Transaction::class);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $gateway = $request->get('gateway');
        $status = $request->get('status');

        $revenue = app('shopping.transaction')->getTotalAmount();

        if ($startDate && $endDate) {
            $revenue = app('shopping.transaction')->getTotalAmountByDateRange($startDate, $endDate);
        } elseif ($gateway) {
            $revenue = app('shopping.transaction')->getTotalAmountByGateway($gateway);
        } elseif ($status) {
            $revenue = app('shopping.transaction')->getTotalAmountByStatus($status);
        }

        return view('shopping::transactions.revenue', [
            'revenue' => $revenue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gateway' => $gateway,
            'status' => $status,
        ]);
    }

    /**
     * Get gateway performance.
     */
    public function gatewayPerformance(Request $request): View
    {
        $this->authorize('viewGatewayPerformance', Transaction::class);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $statistics = app('shopping.transaction')->getTransactionStatistics();

        if ($startDate && $endDate) {
            $statistics = app('shopping.transaction')->getTransactionStatisticsByDateRange($startDate, $endDate);
        }

        return view('shopping::transactions.gateway-performance', [
            'statistics' => $statistics,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Get success rate.
     */
    public function successRate(Request $request): View
    {
        $this->authorize('viewSuccessRate', Transaction::class);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $gateway = $request->get('gateway');

        $totalTransactions = app('shopping.transaction')->getTransactionCount();
        $successfulTransactions = app('shopping.transaction')->getTransactionCountByStatus('success');

        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;

        return view('shopping::transactions.success-rate', [
            'successRate' => $successRate,
            'totalTransactions' => $totalTransactions,
            'successfulTransactions' => $successfulTransactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gateway' => $gateway,
        ]);
    }

    /**
     * Get refund rate.
     */
    public function refundRate(Request $request): View
    {
        $this->authorize('viewRefundRate', Transaction::class);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $gateway = $request->get('gateway');

        $totalTransactions = app('shopping.transaction')->getTransactionCount();
        $refundedTransactions = app('shopping.transaction')->getTransactionCountByStatus('refunded');

        $refundRate = $totalTransactions > 0 ? ($refundedTransactions / $totalTransactions) * 100 : 0;

        return view('shopping::transactions.refund-rate', [
            'refundRate' => $refundRate,
            'totalTransactions' => $totalTransactions,
            'refundedTransactions' => $refundedTransactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gateway' => $gateway,
        ]);
    }
}
