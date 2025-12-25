<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\OrderDTO;
use Fereydooni\Shopping\app\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface OrderRepositoryInterface
{
    /**
     * Get all orders
     */
    public function all(): Collection;

    /**
     * Get paginated orders (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated orders
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated orders
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find order by ID
     */
    public function find(int $id): ?Order;

    /**
     * Find order by ID and return DTO
     */
    public function findDTO(int $id): ?OrderDTO;

    /**
     * Find orders by user ID
     */
    public function findByUserId(int $userId): Collection;

    /**
     * Find orders by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection;

    /**
     * Find orders by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find orders by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find orders by payment status
     */
    public function findByPaymentStatus(string $paymentStatus): Collection;

    /**
     * Find orders by payment status and return DTOs
     */
    public function findByPaymentStatusDTO(string $paymentStatus): Collection;

    /**
     * Find orders by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find orders by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Create a new order
     */
    public function create(array $data): Order;

    /**
     * Create a new order and return DTO
     */
    public function createAndReturnDTO(array $data): OrderDTO;

    /**
     * Update order
     */
    public function update(Order $order, array $data): bool;

    /**
     * Update order and return DTO
     */
    public function updateAndReturnDTO(Order $order, array $data): ?OrderDTO;

    /**
     * Delete order
     */
    public function delete(Order $order): bool;

    /**
     * Cancel order
     */
    public function cancel(Order $order, ?string $reason = null): bool;

    /**
     * Cancel order and return DTO
     */
    public function cancelAndReturnDTO(Order $order, ?string $reason = null): ?OrderDTO;

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order): bool;

    /**
     * Mark order as shipped
     */
    public function markAsShipped(Order $order, ?string $trackingNumber = null): bool;

    /**
     * Mark order as completed
     */
    public function markAsCompleted(Order $order): bool;

    /**
     * Get order count
     */
    public function getOrderCount(): int;

    /**
     * Get order count by status
     */
    public function getOrderCountByStatus(string $status): int;

    /**
     * Get order count by user ID
     */
    public function getOrderCountByUserId(int $userId): int;

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float;

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float;

    /**
     * Search orders
     */
    public function search(string $query): Collection;

    /**
     * Search orders and return DTOs
     */
    public function searchDTO(string $query): Collection;

    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 10): Collection;

    /**
     * Get recent orders and return DTOs
     */
    public function getRecentOrdersDTO(int $limit = 10): Collection;

    /**
     * Get orders by payment method
     */
    public function getOrdersByPaymentMethod(string $paymentMethod): Collection;

    /**
     * Get orders by payment method and return DTOs
     */
    public function getOrdersByPaymentMethodDTO(string $paymentMethod): Collection;

    /**
     * Get pending orders
     */
    public function getPendingOrders(): Collection;

    /**
     * Get pending orders and return DTOs
     */
    public function getPendingOrdersDTO(): Collection;

    /**
     * Get shipped orders
     */
    public function getShippedOrders(): Collection;

    /**
     * Get shipped orders and return DTOs
     */
    public function getShippedOrdersDTO(): Collection;

    /**
     * Get completed orders
     */
    public function getCompletedOrders(): Collection;

    /**
     * Get completed orders and return DTOs
     */
    public function getCompletedOrdersDTO(): Collection;

    /**
     * Get cancelled orders
     */
    public function getCancelledOrders(): Collection;

    /**
     * Get cancelled orders and return DTOs
     */
    public function getCancelledOrdersDTO(): Collection;

    /**
     * Validate order data
     */
    public function validateOrder(array $data): bool;

    /**
     * Calculate order totals
     */
    public function calculateOrderTotals(array $items): array;

    /**
     * Apply discount to order
     */
    public function applyDiscount(Order $order, float $discountAmount, string $discountType = 'fixed'): bool;

    /**
     * Remove discount from order
     */
    public function removeDiscount(Order $order): bool;

    /**
     * Add order note
     */
    public function addOrderNote(Order $order, string $note, string $type = 'general'): bool;

    /**
     * Get order notes
     */
    public function getOrderNotes(Order $order): Collection;

    /**
     * Get model instance
     */
    public function getModel(): Order;
}
