<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Http\Requests\StoreOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\CancelOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\MarkOrderStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchOrderRequest;
use Fereydooni\Shopping\app\Http\Requests\AddOrderNoteRequest;
use Fereydooni\Shopping\app\Http\Resources\OrderResource;
use Fereydooni\Shopping\app\Http\Resources\OrderCollection;
use Fereydooni\Shopping\app\Http\Resources\OrderSearchResource;
use Fereydooni\Shopping\app\Http\Resources\OrderNoteResource;
use Fereydooni\Shopping\app\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->paginate($request->get('per_page', 15));

        return view('shopping::orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $this->authorize('create', Order::class);

        return view('shopping::orders.create');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', Order::class);

        $order = $this->orderService->create($request->validated());

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('shopping::orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): View
    {
        $this->authorize('update', $order);

        return view('shopping::orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $this->orderService->update($order, $request->validated());

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $this->orderService->delete($order);

        return redirect()->route('shopping.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->orderService->cancel($order, $request->get('reason'));

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }

    /**
     * Mark the specified order as paid.
     */
    public function markPaid(MarkOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('markPaid', $order);

        $this->orderService->markAsPaid($order);

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order marked as paid successfully.');
    }

    /**
     * Mark the specified order as shipped.
     */
    public function markShipped(MarkOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('markShipped', $order);

        $this->orderService->markAsShipped($order, $request->get('tracking_number'));

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order marked as shipped successfully.');
    }

    /**
     * Mark the specified order as completed.
     */
    public function markCompleted(MarkOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('markCompleted', $order);

        $this->orderService->markAsCompleted($order);

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Order marked as completed successfully.');
    }

    /**
     * Search orders.
     */
    public function search(SearchOrderRequest $request): View
    {
        $this->authorize('search', Order::class);

        $query = $request->get('query');
        $orders = $this->orderService->search($query);

        return view('shopping::orders.search', compact('orders', 'query'));
    }

    /**
     * Display pending orders.
     */
    public function pending(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getPendingOrders();

        return view('shopping::orders.pending', compact('orders'));
    }

    /**
     * Display shipped orders.
     */
    public function shipped(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getShippedOrders();

        return view('shopping::orders.shipped', compact('orders'));
    }

    /**
     * Display completed orders.
     */
    public function completed(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getCompletedOrders();

        return view('shopping::orders.completed', compact('orders'));
    }

    /**
     * Display cancelled orders.
     */
    public function cancelled(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getCancelledOrders();

        return view('shopping::orders.cancelled', compact('orders'));
    }

    /**
     * Add a note to the specified order.
     */
    public function addNote(AddOrderNoteRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('addNote', $order);

        $this->orderService->addOrderNote($order, $request->get('note'), $request->get('type', 'general'));

        return redirect()->route('shopping.orders.show', $order)
            ->with('success', 'Note added successfully.');
    }

    /**
     * Get notes for the specified order.
     */
    public function getNotes(Order $order): View
    {
        $this->authorize('viewNotes', $order);

        $notes = $this->orderService->getOrderNotes($order);

        return view('shopping::orders.notes', compact('order', 'notes'));
    }
}
