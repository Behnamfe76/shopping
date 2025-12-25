<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\OrderDTO;
use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasFinancialOperations;
use Fereydooni\Shopping\app\Traits\HasNotesManagement;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasStatusManagement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class OrderService
{
    use HasCrudOperations, HasFinancialOperations, HasNotesManagement, HasSearchOperations, HasStatusManagement;

    protected OrderRepositoryInterface $repository;

    public function __construct(OrderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all orders
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get all orders as DTOs
     */
    public function allDTO(): Collection
    {
        return $this->all()->map(fn ($order) => OrderDTO::fromModel($order));
    }

    /**
     * Get paginated orders
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get simple paginated orders
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated orders
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find order by ID
     */
    public function find(int $id): ?Order
    {
        return $this->repository->find($id);
    }

    /**
     * Find order by ID and return DTO
     */
    public function findDTO(int $id): ?OrderDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find orders by user ID
     */
    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Find orders by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    /**
     * Find orders by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Find orders by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    /**
     * Find orders by payment status
     */
    public function findByPaymentStatus(string $paymentStatus): Collection
    {
        return $this->repository->findByPaymentStatus($paymentStatus);
    }

    /**
     * Find orders by payment status and return DTOs
     */
    public function findByPaymentStatusDTO(string $paymentStatus): Collection
    {
        return $this->repository->findByPaymentStatusDTO($paymentStatus);
    }

    /**
     * Find orders by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    /**
     * Find orders by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Create order
     */
    public function create(array $data): Order
    {
        return $this->repository->create($data);
    }

    /**
     * Create order and return DTO
     */
    public function createDTO(array $data): OrderDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Update order
     */
    public function update(Order $order, array $data): bool
    {
        return $this->repository->update($order, $data);
    }

    /**
     * Update order and return DTO
     */
    public function updateDTO(Order $order, array $data): ?OrderDTO
    {
        return $this->repository->updateAndReturnDTO($order, $data);
    }

    /**
     * Delete order
     */
    public function delete(Order $order): bool
    {
        return $this->repository->delete($order);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order, ?string $reason = null): bool
    {
        return $this->repository->cancel($order, $reason);
    }

    /**
     * Cancel order and return DTO
     */
    public function cancelDTO(Order $order, ?string $reason = null): ?OrderDTO
    {
        return $this->repository->cancelAndReturnDTO($order, $reason);
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order): bool
    {
        return $this->repository->markAsPaid($order);
    }

    /**
     * Mark order as shipped
     */
    public function markAsShipped(Order $order, ?string $trackingNumber = null): bool
    {
        return $this->repository->markAsShipped($order, $trackingNumber);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(Order $order): bool
    {
        return $this->repository->markAsCompleted($order);
    }

    /**
     * Get pending orders
     */
    public function getPending(): Collection
    {
        return $this->repository->getPendingOrders();
    }

    /**
     * Get pending orders as DTOs
     */
    public function getPendingDTO(): Collection
    {
        return $this->repository->getPendingOrdersDTO();
    }

    /**
     * Get shipped orders
     */
    public function getShipped(): Collection
    {
        return $this->repository->getShippedOrders();
    }

    /**
     * Get shipped orders as DTOs
     */
    public function getShippedDTO(): Collection
    {
        return $this->repository->getShippedOrdersDTO();
    }

    /**
     * Get completed orders
     */
    public function getCompleted(): Collection
    {
        return $this->repository->getCompletedOrders();
    }

    /**
     * Get completed orders as DTOs
     */
    public function getCompletedDTO(): Collection
    {
        return $this->repository->getCompletedOrdersDTO();
    }

    /**
     * Get cancelled orders
     */
    public function getCancelled(): Collection
    {
        return $this->repository->getCancelledOrders();
    }

    /**
     * Get cancelled orders as DTOs
     */
    public function getCancelledDTO(): Collection
    {
        return $this->repository->getCancelledOrdersDTO();
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->repository->getRecentOrders($limit);
    }

    /**
     * Get recent orders as DTOs
     */
    public function getRecentDTO(int $limit = 10): Collection
    {
        return $this->repository->getRecentOrdersDTO($limit);
    }

    /**
     * Get orders by payment method
     */
    public function getByPaymentMethod(string $paymentMethod): Collection
    {
        return $this->repository->getOrdersByPaymentMethod($paymentMethod);
    }

    /**
     * Get orders by payment method as DTOs
     */
    public function getByPaymentMethodDTO(string $paymentMethod): Collection
    {
        return $this->repository->getOrdersByPaymentMethodDTO($paymentMethod);
    }

    /**
     * Search orders
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search orders and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    /**
     * Get order count
     */
    public function getCount(): int
    {
        return $this->repository->getOrderCount();
    }

    /**
     * Get order count by status
     */
    public function getCountByStatus(string $status): int
    {
        return $this->repository->getOrderCountByStatus($status);
    }

    /**
     * Get order count by user ID
     */
    public function getCountByUserId(int $userId): int
    {
        return $this->repository->getOrderCountByUserId($userId);
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->repository->getTotalRevenue();
    }

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getTotalRevenueByDateRange($startDate, $endDate);
    }

    /**
     * Validate order data
     */
    public function validate(array $data): bool
    {
        return $this->repository->validateOrder($data);
    }

    /**
     * Calculate order totals
     */
    public function calculateTotals(array $items): array
    {
        return $this->repository->calculateOrderTotals($items);
    }

    /**
     * Apply discount to order
     */
    public function applyDiscount(Order $order, float $discountAmount, string $discountType = 'fixed'): bool
    {
        return $this->repository->applyDiscount($order, $discountAmount, $discountType);
    }

    /**
     * Remove discount from order
     */
    public function removeDiscount(Order $order): bool
    {
        return $this->repository->removeDiscount($order);
    }

    /**
     * Process payment
     */
    public function processPayment(Order $order, string $paymentMethod, float $amount): bool
    {
        return $this->processPayment($order, $paymentMethod, $amount);
    }

    /**
     * Process refund
     */
    public function processRefund(Order $order, float $amount, ?string $reason = null): bool
    {
        return $this->processRefund($order, $amount, $reason);
    }

    /**
     * Add order note
     */
    public function addNote(Order $order, string $note, string $type = 'general'): bool
    {
        return $this->repository->addOrderNote($order, $note, $type);
    }

    /**
     * Get order notes
     */
    public function getNotes(Order $order): Collection
    {
        return $this->repository->getOrderNotes($order);
    }

    /**
     * Get order notes by type
     */
    public function getNotesByType(Order $order, string $type): array
    {
        return $order->getNotesByType($type);
    }

    /**
     * Delete order note
     */
    public function deleteNote(Order $order, int $noteIndex): bool
    {
        return $this->deleteNote($order, $noteIndex);
    }

    /**
     * Update order note
     */
    public function updateNote(Order $order, int $noteIndex, string $note, ?string $type = null): bool
    {
        return $this->updateNote($order, $noteIndex, $note, $type);
    }

    /**
     * Get note types
     */
    public function getNoteTypes(): array
    {
        return $this->getNoteTypes();
    }

    /**
     * Get recent notes
     */
    public function getRecentNotes(Order $order, int $limit = 5): array
    {
        return $this->getRecentNotes($order, $limit);
    }

    /**
     * Search notes
     */
    public function searchNotes(Order $order, string $query): array
    {
        return $this->searchNotes($order, $query);
    }
}
