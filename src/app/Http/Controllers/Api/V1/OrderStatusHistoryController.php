<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Requests\OrderStatusHistoryAnalyticsRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchOrderStatusHistoryRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreOrderStatusHistoryRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateOrderStatusHistoryRequest;
use Fereydooni\Shopping\app\Http\Resources\OrderStatusHistoryAnalyticsResource;
use Fereydooni\Shopping\app\Http\Resources\OrderStatusHistoryCollection;
use Fereydooni\Shopping\app\Http\Resources\OrderStatusHistoryResource;
use Fereydooni\Shopping\app\Http\Resources\OrderTimelineResource;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Fereydooni\Shopping\app\Services\OrderStatusHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderStatusHistoryController extends Controller
{
    public function __construct(
        protected OrderStatusHistoryService $orderStatusHistoryService
    ) {
        $this->authorizeResource(OrderStatusHistory::class, 'history');
    }

    /**
     * Display a listing of the status history records.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $perPage = $request->get('per_page', 15);
        $history = $this->orderStatusHistoryService->paginate($perPage);

        return response()->json([
            'data' => new OrderStatusHistoryCollection($history),
        ]);
    }

    /**
     * Display the specified status history record.
     */
    public function show(OrderStatusHistory $history): JsonResponse
    {
        $this->authorize('view', $history);

        return response()->json([
            'data' => new OrderStatusHistoryResource($history),
        ]);
    }

    /**
     * Store a newly created status history record.
     */
    public function store(StoreOrderStatusHistoryRequest $request): JsonResponse
    {
        $this->authorize('create', OrderStatusHistory::class);

        $data = $request->validated();
        $history = $this->orderStatusHistoryService->create($data);

        return response()->json([
            'message' => 'Status history created successfully',
            'data' => new OrderStatusHistoryResource($history),
        ], 201);
    }

    /**
     * Update the specified status history record.
     */
    public function update(UpdateOrderStatusHistoryRequest $request, OrderStatusHistory $history): JsonResponse
    {
        $this->authorize('update', $history);

        $data = $request->validated();
        $updated = $this->orderStatusHistoryService->update($history, $data);

        if (! $updated) {
            return response()->json(['message' => 'Failed to update status history'], 500);
        }

        return response()->json([
            'message' => 'Status history updated successfully',
            'data' => new OrderStatusHistoryResource($history->fresh()),
        ]);
    }

    /**
     * Remove the specified status history record.
     */
    public function destroy(OrderStatusHistory $history): JsonResponse
    {
        $this->authorize('delete', $history);

        $deleted = $this->orderStatusHistoryService->delete($history);

        if (! $deleted) {
            return response()->json(['message' => 'Failed to delete status history'], 500);
        }

        return response()->json(['message' => 'Status history deleted successfully']);
    }

    /**
     * Search status history records.
     */
    public function search(SearchOrderStatusHistoryRequest $request): JsonResponse
    {
        $this->authorize('search', OrderStatusHistory::class);

        $query = $request->get('query');
        $results = $this->orderStatusHistoryService->search($query);

        return response()->json([
            'data' => new OrderStatusHistoryCollection($results),
        ]);
    }

    /**
     * Get status history by order.
     */
    public function byOrder(int $orderId): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $history = $this->orderStatusHistoryService->findByOrderId($orderId);

        return response()->json([
            'data' => new OrderStatusHistoryCollection($history),
        ]);
    }

    /**
     * Get status history by user.
     */
    public function byUser(int $userId): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $history = $this->orderStatusHistoryService->findByUserId($userId);

        return response()->json([
            'data' => new OrderStatusHistoryCollection($history),
        ]);
    }

    /**
     * Get status history by status.
     */
    public function byStatus(string $status): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $history = $this->orderStatusHistoryService->findByStatus($status);

        return response()->json([
            'data' => new OrderStatusHistoryCollection($history),
        ]);
    }

    /**
     * Get order timeline.
     */
    public function timeline(int $orderId): JsonResponse
    {
        $this->authorize('viewTimeline', null, $orderId);

        $timeline = $this->orderStatusHistoryService->getOrderTimeline($orderId);

        return response()->json([
            'data' => new OrderTimelineResource($timeline),
        ]);
    }

    /**
     * Get analytics.
     */
    public function analytics(OrderStatusHistoryAnalyticsRequest $request): JsonResponse
    {
        $this->authorize('viewAnalytics', OrderStatusHistory::class);

        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $analytics = $this->orderStatusHistoryService->getStatusChangeAnalytics($startDate, $endDate);

        return response()->json([
            'data' => new OrderStatusHistoryAnalyticsResource($analytics),
        ]);
    }

    /**
     * Get reports.
     */
    public function reports(Request $request): JsonResponse
    {
        $this->authorize('viewReports', OrderStatusHistory::class);

        $reports = [
            'total_history_count' => $this->orderStatusHistoryService->getHistoryCount(),
            'recent_changes' => $this->orderStatusHistoryService->getRecentHistory(10),
            'most_frequent_changes' => $this->orderStatusHistoryService->getMostFrequentStatusChanges(10),
            'system_changes_count' => $this->orderStatusHistoryService->getSystemChanges()->count(),
            'user_changes_count' => $this->orderStatusHistoryService->getUserChanges()->count(),
        ];

        return response()->json([
            'data' => $reports,
        ]);
    }

    /**
     * Get history count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $count = $this->orderStatusHistoryService->getHistoryCount();

        return response()->json([
            'data' => [
                'total_count' => $count,
                'system_changes_count' => $this->orderStatusHistoryService->getSystemChanges()->count(),
                'user_changes_count' => $this->orderStatusHistoryService->getUserChanges()->count(),
            ],
        ]);
    }

    /**
     * Get status change frequency.
     */
    public function getFrequency(): JsonResponse
    {
        $this->authorize('viewAny', OrderStatusHistory::class);

        $frequencies = $this->orderStatusHistoryService->getMostFrequentStatusChanges(10);

        return response()->json([
            'data' => $frequencies,
        ]);
    }
}
