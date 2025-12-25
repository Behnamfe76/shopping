<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\OrderDTO;
use Fereydooni\Shopping\app\Enums\OrderStatus;
use Fereydooni\Shopping\app\Enums\PaymentStatus;
use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderRepository implements OrderRepositoryInterface
{
    protected Order $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * Get all orders
     */
    public function all(): Collection
    {
        return $this->model->recent()->get();
    }

    /**
     * Get paginated orders (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->recent()->paginate($perPage);
    }

    /**
     * Get simple paginated orders
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->recent()->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated orders
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->recent()->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    /**
     * Find order by ID
     */
    public function find(int $id): ?Order
    {
        return $this->model->find($id);
    }

    /**
     * Find order by ID and return DTO
     */
    public function findDTO(int $id): ?OrderDTO
    {
        $order = $this->find($id);

        return $order ? OrderDTO::fromModel($order) : null;
    }

    /**
     * Find orders by user ID
     */
    public function findByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->recent()->get();
    }

    /**
     * Find orders by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Find orders by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->recent()->get();
    }

    /**
     * Find orders by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Find orders by payment status
     */
    public function findByPaymentStatus(string $paymentStatus): Collection
    {
        return $this->model->where('payment_status', $paymentStatus)->recent()->get();
    }

    /**
     * Find orders by payment status and return DTOs
     */
    public function findByPaymentStatusDTO(string $paymentStatus): Collection
    {
        return $this->findByPaymentStatus($paymentStatus)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Find orders by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->byDateRange($startDate, $endDate)->recent()->get();
    }

    /**
     * Find orders by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Create a new order
     */
    public function create(array $data): Order
    {
        // Set placed_at if not provided
        if (! isset($data['placed_at'])) {
            $data['placed_at'] = now();
        }

        // Calculate totals if not provided
        if (! isset($data['subtotal']) || ! isset($data['grand_total'])) {
            $totals = $this->calculateOrderTotals($data['items'] ?? []);
            $data = array_merge($data, $totals);
        }

        return $this->model->create($data);
    }

    /**
     * Create a new order and return DTO
     */
    public function createAndReturnDTO(array $data): OrderDTO
    {
        $order = $this->create($data);

        return OrderDTO::fromModel($order);
    }

    /**
     * Update order
     */
    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    /**
     * Update order and return DTO
     */
    public function updateAndReturnDTO(Order $order, array $data): ?OrderDTO
    {
        $updated = $this->update($order, $data);

        return $updated ? OrderDTO::fromModel($order->fresh()) : null;
    }

    /**
     * Delete order
     */
    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order, ?string $reason = null): bool
    {
        $data = ['status' => OrderStatus::CANCELLED];

        if ($reason) {
            $data['notes'] = $this->addStatusChangeNote($order, 'cancelled', $reason);
        }

        return $this->update($order, $data);
    }

    /**
     * Cancel order and return DTO
     */
    public function cancelAndReturnDTO(Order $order, ?string $reason = null): ?OrderDTO
    {
        $cancelled = $this->cancel($order, $reason);

        return $cancelled ? OrderDTO::fromModel($order->fresh()) : null;
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order): bool
    {
        return $this->update($order, [
            'status' => OrderStatus::PAID,
            'payment_status' => PaymentStatus::PAID,
        ]);
    }

    /**
     * Mark order as shipped
     */
    public function markAsShipped(Order $order, ?string $trackingNumber = null): bool
    {
        $data = ['status' => OrderStatus::SHIPPED];

        if ($trackingNumber) {
            $data['tracking_number'] = $trackingNumber;
        }

        return $this->update($order, $data);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(Order $order): bool
    {
        return $this->update($order, ['status' => OrderStatus::COMPLETED]);
    }

    /**
     * Get order count
     */
    public function getOrderCount(): int
    {
        return $this->model->count();
    }

    /**
     * Get order count by status
     */
    public function getOrderCountByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    /**
     * Get order count by user ID
     */
    public function getOrderCountByUserId(int $userId): int
    {
        return $this->model->where('user_id', $userId)->count();
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->model->where('status', OrderStatus::COMPLETED)
            ->sum('grand_total');
    }

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->model->where('status', OrderStatus::COMPLETED)
            ->byDateRange($startDate, $endDate)
            ->sum('grand_total');
    }

    /**
     * Search orders
     */
    public function search(string $query): Collection
    {
        return $this->model->search($query)->recent()->get();
    }

    /**
     * Search orders and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->model->recent()->limit($limit)->get();
    }

    /**
     * Get recent orders and return DTOs
     */
    public function getRecentOrdersDTO(int $limit = 10): Collection
    {
        return $this->getRecentOrders($limit)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get orders by payment method
     */
    public function getOrdersByPaymentMethod(string $paymentMethod): Collection
    {
        return $this->model->byPaymentMethod($paymentMethod)->recent()->get();
    }

    /**
     * Get orders by payment method and return DTOs
     */
    public function getOrdersByPaymentMethodDTO(string $paymentMethod): Collection
    {
        return $this->getOrdersByPaymentMethod($paymentMethod)->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get pending orders
     */
    public function getPendingOrders(): Collection
    {
        return $this->model->pending()->recent()->get();
    }

    /**
     * Get pending orders and return DTOs
     */
    public function getPendingOrdersDTO(): Collection
    {
        return $this->getPendingOrders()->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get shipped orders
     */
    public function getShippedOrders(): Collection
    {
        return $this->model->shipped()->recent()->get();
    }

    /**
     * Get shipped orders and return DTOs
     */
    public function getShippedOrdersDTO(): Collection
    {
        return $this->getShippedOrders()->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get completed orders
     */
    public function getCompletedOrders(): Collection
    {
        return $this->model->completed()->recent()->get();
    }

    /**
     * Get completed orders and return DTOs
     */
    public function getCompletedOrdersDTO(): Collection
    {
        return $this->getCompletedOrders()->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get cancelled orders
     */
    public function getCancelledOrders(): Collection
    {
        return $this->model->cancelled()->recent()->get();
    }

    /**
     * Get cancelled orders and return DTOs
     */
    public function getCancelledOrdersDTO(): Collection
    {
        return $this->getCancelledOrders()->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Validate order data
     */
    public function validateOrder(array $data): bool
    {
        $validator = Validator::make($data, OrderDTO::rules(), OrderDTO::messages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Calculate order totals
     */
    public function calculateOrderTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
        }

        $taxAmount = $subtotal * 0.10; // 10% tax
        $shippingAmount = $subtotal >= 100 ? 0 : 10; // Free shipping over $100
        $discountAmount = 0;
        $grandTotal = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Apply discount to order
     */
    public function applyDiscount(Order $order, float $discountAmount, string $discountType = 'fixed'): bool
    {
        $currentDiscount = $order->discount_amount ?? 0;
        $newDiscount = $discountType === 'percentage'
            ? ($order->subtotal * $discountAmount / 100)
            : $discountAmount;

        $data = [
            'discount_amount' => $currentDiscount + $newDiscount,
        ];

        // Recalculate grand total
        $data['grand_total'] = $order->subtotal + $order->tax_amount + $order->shipping_amount - $data['discount_amount'];

        return $this->update($order, $data);
    }

    /**
     * Remove discount from order
     */
    public function removeDiscount(Order $order): bool
    {
        $data = [
            'discount_amount' => 0,
            'coupon_discount' => 0,
            'coupon_code' => null,
        ];

        // Recalculate grand total
        $data['grand_total'] = $order->subtotal + $order->tax_amount + $order->shipping_amount;

        return $this->update($order, $data);
    }

    /**
     * Add order note
     */
    public function addOrderNote(Order $order, string $note, string $type = 'general'): bool
    {
        return $order->addNote($note, $type);
    }

    /**
     * Get order notes
     */
    public function getOrderNotes(Order $order): Collection
    {
        return collect($order->getNotes());
    }

    /**
     * Add status change note
     */
    protected function addStatusChangeNote(Order $order, string $newStatus, ?string $reason = null): string
    {
        $currentNotes = $order->notes ? json_decode($order->notes, true) : [];

        $note = "Status changed to {$newStatus}";
        if ($reason) {
            $note .= " - {$reason}";
        }

        $currentNotes[] = [
            'note' => $note,
            'type' => 'status_change',
            'created_at' => now()->toISOString(),
        ];

        return json_encode($currentNotes);
    }

    /**
     * Get model instance
     */
    public function getModel(): Order
    {
        return $this->model;
    }
}
