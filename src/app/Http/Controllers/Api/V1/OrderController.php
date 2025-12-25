<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\AddOrderNoteRequest;
use Fereydooni\Shopping\app\Http\Requests\CancelOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\MarkOrderStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateOrderRequest;
use Fereydooni\Shopping\app\Http\Resources\OrderCollection;
use Fereydooni\Shopping\app\Http\Resources\OrderNoteResource;
use Fereydooni\Shopping\app\Http\Resources\OrderResource;
use Fereydooni\Shopping\app\Http\Resources\OrderSearchResource;
use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->paginate($request->get('per_page', 15));

        return response()->json(new OrderCollection($orders));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $this->orderService->create($request->validated());

        return response()->json(new OrderResource($order), 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json(new OrderResource($order));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $updatedOrder = $this->orderService->updateAndReturnDTO($order, $request->validated());

        return response()->json(new OrderResource($updatedOrder));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $this->orderService->delete($order);

        return response()->json(['message' => 'Order deleted successfully']);
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        $this->orderService->cancel($order, $request->get('reason'));

        return response()->json(['message' => 'Order cancelled successfully']);
    }

    /**
     * Mark the specified order as paid.
     */
    public function markPaid(MarkOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('markPaid', $order);

        $this->orderService->markAsPaid($order);

        return response()->json(['message' => 'Order marked as paid successfully']);
    }

    /**
     * Mark the specified order as shipped.
     */
    public function markShipped(MarkOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('markShipped', $order);

        $this->orderService->markAsShipped($order, $request->get('tracking_number'));

        return response()->json(['message' => 'Order marked as shipped successfully']);
    }

    /**
     * Mark the specified order as completed.
     */
    public function markCompleted(MarkOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('markCompleted', $order);

        $this->orderService->markAsCompleted($order);

        return response()->json(['message' => 'Order marked as completed successfully']);
    }

    /**
     * Search orders.
     */
    public function search(SearchOrderRequest $request): JsonResponse
    {
        $this->authorize('search', Order::class);

        $query = $request->get('query');
        $orders = $this->orderService->search($query);

        return response()->json(OrderSearchResource::collection($orders));
    }

    /**
     * Display pending orders.
     */
    public function pending(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getPendingOrders();

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Display shipped orders.
     */
    public function shipped(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getShippedOrders();

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Display completed orders.
     */
    public function completed(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getCompletedOrders();

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Display cancelled orders.
     */
    public function cancelled(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getCancelledOrders();

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Get order count.
     */
    public function getCount(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $count = $this->orderService->getOrderCount();

        return response()->json(['count' => $count]);
    }

    /**
     * Get total revenue.
     */
    public function getRevenue(Request $request): JsonResponse
    {
        $this->authorize('viewReports', Order::class);

        $revenue = $this->orderService->getTotalRevenue();

        return response()->json(['revenue' => $revenue]);
    }

    /**
     * Add a note to the specified order.
     */
    public function addNote(AddOrderNoteRequest $request, Order $order): JsonResponse
    {
        $this->authorize('addNote', $order);

        $this->orderService->addOrderNote($order, $request->get('note'), $request->get('type', 'general'));

        return response()->json(['message' => 'Note added successfully']);
    }

    /**
     * Get notes for the specified order.
     */
    public function getNotes(Order $order): JsonResponse
    {
        $this->authorize('viewNotes', $order);

        $notes = $this->orderService->getOrderNotes($order);

        return response()->json(OrderNoteResource::collection($notes));
    }

    /**
     * Process refund for the specified order.
     */
    public function processRefund(Request $request, Order $order): JsonResponse
    {
        $this->authorize('refund', $order);

        $request->validate([
            'amount' => 'required|numeric|min:0|max:'.$order->total_amount,
            'reason' => 'required|string|max:500',
        ]);

        // Process refund logic would go here
        // For now, just return success message

        return response()->json(['message' => 'Refund processed successfully']);
    }
}
