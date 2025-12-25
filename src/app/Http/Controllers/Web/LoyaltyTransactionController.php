<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Services\LoyaltyTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyTransactionController extends Controller
{
    protected LoyaltyTransactionService $service;

    public function __construct(LoyaltyTransactionService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('permission:loyalty-transactions.*');
    }

    /**
     * Display loyalty transaction dashboard.
     */
    public function dashboard(): View
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

        $recentTransactions = $this->service->getRecentTransactionsDTO(10);
        $insights = $this->service->calculateInsights();
        $recommendations = $this->service->generateRecommendations();

        return view('loyalty-transactions.dashboard', compact(
            'statistics',
            'recentTransactions',
            'insights',
            'recommendations'
        ));
    }

    /**
     * Display a listing of loyalty transactions.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $type = $request->get('type');
        $status = $request->get('status');
        $customerId = $request->get('customer_id');

        $query = $this->service->getRepository()->getModel();

        if ($search) {
            $transactions = $this->service->searchDTO($search);
        } elseif ($customerId) {
            $transactions = $this->service->findByCustomerIdDTO($customerId);
        } else {
            $transactions = $this->service->paginate($perPage);
        }

        $transactionTypes = LoyaltyTransactionType::cases();
        $transactionStatuses = LoyaltyTransactionStatus::cases();

        return view('loyalty-transactions.index', compact(
            'transactions',
            'transactionTypes',
            'transactionStatuses',
            'search',
            'type',
            'status',
            'customerId'
        ));
    }

    /**
     * Show the form for creating a new loyalty transaction.
     */
    public function create(): View
    {
        $transactionTypes = LoyaltyTransactionType::cases();
        $transactionStatuses = LoyaltyTransactionStatus::cases();
        $referenceTypes = LoyaltyReferenceType::cases();

        return view('loyalty-transactions.create', compact(
            'transactionTypes',
            'transactionStatuses',
            'referenceTypes'
        ));
    }

    /**
     * Store a newly created loyalty transaction.
     */
    public function store(Request $request): RedirectResponse
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

        return redirect()->route('loyalty-transactions.show', $transaction->id)
            ->with('success', 'Loyalty transaction created successfully.');
    }

    /**
     * Display the specified loyalty transaction.
     */
    public function show(int $id): View
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
        }

        $transactionDTO = LoyaltyTransactionDTO::fromModel($transaction);
        $analytics = $this->service->getTransactionAnalytics($transaction->customer_id);

        return view('loyalty-transactions.show', compact('transaction', 'transactionDTO', 'analytics'));
    }

    /**
     * Show the form for editing the specified loyalty transaction.
     */
    public function edit(int $id): View
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
        }

        $transactionTypes = LoyaltyTransactionType::cases();
        $transactionStatuses = LoyaltyTransactionStatus::cases();
        $referenceTypes = LoyaltyReferenceType::cases();

        return view('loyalty-transactions.edit', compact(
            'transaction',
            'transactionTypes',
            'transactionStatuses',
            'referenceTypes'
        ));
    }

    /**
     * Update the specified loyalty transaction.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
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

        if (! $updated) {
            return back()->with('error', 'Failed to update loyalty transaction.');
        }

        return redirect()->route('loyalty-transactions.show', $transaction->id)
            ->with('success', 'Loyalty transaction updated successfully.');
    }

    /**
     * Remove the specified loyalty transaction.
     */
    public function destroy(int $id): RedirectResponse
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
        }

        $deleted = $this->service->delete($transaction);

        if (! $deleted) {
            return back()->with('error', 'Failed to delete loyalty transaction.');
        }

        return redirect()->route('loyalty-transactions.index')
            ->with('success', 'Loyalty transaction deleted successfully.');
    }

    /**
     * Show points management interface.
     */
    public function pointsManagement(): View
    {
        return view('loyalty-transactions.points-management');
    }

    /**
     * Add points to customer.
     */
    public function addPoints(Request $request): RedirectResponse
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

        return redirect()->route('loyalty-transactions.show', $transaction->id)
            ->with('success', 'Loyalty points added successfully.');
    }

    /**
     * Deduct points from customer.
     */
    public function deductPoints(Request $request): RedirectResponse
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

            return redirect()->route('loyalty-transactions.show', $transaction->id)
                ->with('success', 'Loyalty points deducted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show transaction reversal interface.
     */
    public function showReverse(int $id): View
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
        }

        return view('loyalty-transactions.reverse', compact('transaction'));
    }

    /**
     * Reverse a loyalty transaction.
     */
    public function reverse(Request $request, int $id): RedirectResponse
    {
        $transaction = $this->service->find($id);

        if (! $transaction) {
            abort(404, 'Loyalty transaction not found');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reversed = $this->service->reverse($transaction, $validated['reason'] ?? null);

        if (! $reversed) {
            return back()->with('error', 'Failed to reverse loyalty transaction.');
        }

        return redirect()->route('loyalty-transactions.show', $transaction->id)
            ->with('success', 'Loyalty transaction reversed successfully.');
    }

    /**
     * Show loyalty analytics and reporting.
     */
    public function analytics(Request $request): View
    {
        $customerId = $request->get('customer_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($customerId) {
            $analytics = $this->service->getTransactionAnalytics($customerId);
            $trends = $this->service->getTransactionTrends($customerId);
            $recommendations = $this->service->getTransactionRecommendations($customerId);
        } else {
            $analytics = $this->service->calculateInsights();
            $trends = $this->service->forecastTrends();
            $recommendations = $this->service->generateRecommendations();
        }

        return view('loyalty-transactions.analytics', compact(
            'analytics',
            'trends',
            'recommendations',
            'customerId',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show import/export interface.
     */
    public function importExport(): View
    {
        return view('loyalty-transactions.import-export');
    }

    /**
     * Export customer loyalty history.
     */
    public function exportHistory(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'format' => 'required|string|in:json,csv,xml',
        ]);

        $history = $this->service->exportCustomerHistory($validated['customer_id']);

        if ($validated['format'] === 'csv') {
            return $this->exportToCsv($history, "loyalty_history_{$validated['customer_id']}.csv");
        } elseif ($validated['format'] === 'xml') {
            return $this->exportToXml($history, "loyalty_history_{$validated['customer_id']}.xml");
        }

        return response()->json($history);
    }

    /**
     * Import customer loyalty history.
     */
    public function importHistory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'file' => 'required|file|mimes:json,csv,xml',
        ]);

        $file = $request->file('file');
        $transactions = $this->parseImportFile($file);

        $imported = $this->service->importCustomerHistory($validated['customer_id'], $transactions);

        if (! $imported) {
            return back()->with('error', 'Failed to import loyalty history.');
        }

        return redirect()->route('loyalty-transactions.index')
            ->with('success', 'Loyalty history imported successfully.');
    }

    /**
     * Show loyalty tier management.
     */
    public function tierManagement(): View
    {
        return view('loyalty-transactions.tier-management');
    }

    /**
     * Show expiration tracking.
     */
    public function expirationTracking(): View
    {
        $expiringTransactions = $this->service->findExpiringTransactionsDTO(now()->addDays(30)->toDateString());

        return view('loyalty-transactions.expiration-tracking', compact('expiringTransactions'));
    }

    /**
     * Export data to CSV format.
     */
    protected function exportToCsv(array $data, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if (! empty($data)) {
                fputcsv($file, array_keys($data[0]));

                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data to XML format.
     */
    protected function exportToXml(array $data, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><loyalty_history></loyalty_history>');

        foreach ($data as $row) {
            $transaction = $xml->addChild('transaction');
            foreach ($row as $key => $value) {
                $transaction->addChild($key, htmlspecialchars($value));
            }
        }

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Parse import file and return transactions array.
     */
    protected function parseImportFile($file): array
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'json') {
            return json_decode(file_get_contents($file->getPathname()), true);
        } elseif ($extension === 'csv') {
            return $this->parseCsvFile($file);
        } elseif ($extension === 'xml') {
            return $this->parseXmlFile($file);
        }

        return [];
    }

    /**
     * Parse CSV file.
     */
    protected function parseCsvFile($file): array
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        if (($headers = fgetcsv($handle)) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return $data;
    }

    /**
     * Parse XML file.
     */
    protected function parseXmlFile($file): array
    {
        $xml = simplexml_load_file($file->getPathname());
        $data = [];

        foreach ($xml->transaction as $transaction) {
            $row = [];
            foreach ($transaction->children() as $child) {
                $row[$child->getName()] = (string) $child;
            }
            $data[] = $row;
        }

        return $data;
    }
}
