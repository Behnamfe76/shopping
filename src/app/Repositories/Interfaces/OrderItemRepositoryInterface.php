<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\OrderItem;
use Fereydooni\Shopping\app\DTOs\OrderItemDTO;

interface OrderItemRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?OrderItem;
    public function findDTO(int $id): ?OrderItemDTO;
    public function create(array $data): OrderItem;
    public function createAndReturnDTO(array $data): OrderItemDTO;
    public function update(OrderItem $orderItem, array $data): bool;
    public function updateAndReturnDTO(OrderItem $orderItem, array $data): ?OrderItemDTO;
    public function delete(OrderItem $orderItem): bool;

    // Order-specific queries
    public function findByOrderId(int $orderId): Collection;
    public function findByOrderIdDTO(int $orderId): Collection;

    // Product and variant queries
    public function findByProductId(int $productId): Collection;
    public function findByProductIdDTO(int $productId): Collection;
    public function findByVariantId(int $variantId): Collection;
    public function findByVariantIdDTO(int $variantId): Collection;

    // SKU-based queries
    public function findBySku(string $sku): Collection;
    public function findBySkuDTO(string $sku): Collection;

    // Shipping status queries
    public function findShipped(): Collection;
    public function findShippedDTO(): Collection;
    public function findUnshipped(): Collection;
    public function findUnshippedDTO(): Collection;

    // Shipping operations
    public function markAsShipped(OrderItem $orderItem, int $shippedQuantity = null): bool;
    public function markAsShippedDTO(OrderItem $orderItem, int $shippedQuantity = null): ?OrderItemDTO;

    // Return operations
    public function markAsReturned(OrderItem $orderItem, int $returnedQuantity = null): bool;
    public function markAsReturnedDTO(OrderItem $orderItem, int $returnedQuantity = null): ?OrderItemDTO;

    // Refund operations
    public function processRefund(OrderItem $orderItem, float $refundAmount = null): bool;
    public function processRefundDTO(OrderItem $orderItem, float $refundAmount = null): ?OrderItemDTO;

    // Count operations
    public function getOrderItemCount(): int;
    public function getOrderItemCountByOrderId(int $orderId): int;
    public function getOrderItemCountByProductId(int $productId): int;

    // Quantity operations
    public function getTotalQuantity(): int;
    public function getTotalQuantityByProductId(int $productId): int;

    // Revenue operations
    public function getTotalRevenue(): float;
    public function getTotalRevenueByProductId(int $productId): float;
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float;

    // Search operations
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;

    // Analytics operations
    public function getTopSellingItems(int $limit = 10): Collection;
    public function getTopSellingItemsDTO(int $limit = 10): Collection;
    public function getLowStockItems(int $threshold = 10): Collection;
    public function getLowStockItemsDTO(int $threshold = 10): Collection;

    // Price range operations
    public function getItemsByPriceRange(float $minPrice, float $maxPrice): Collection;
    public function getItemsByPriceRangeDTO(float $minPrice, float $maxPrice): Collection;

    // Validation and calculation operations
    public function validateOrderItem(array $data): bool;
    public function calculateItemTotals(array $itemData): array;

    // Inventory operations
    public function checkInventory(int $productId, int $variantId = null, int $quantity = 1): bool;
    public function reserveInventory(int $productId, int $variantId = null, int $quantity = 1): bool;
    public function releaseInventory(int $productId, int $variantId = null, int $quantity = 1): bool;
    public function updateInventory(int $productId, int $variantId = null, int $quantity = 1, string $operation = 'decrease'): bool;
    public function getInventoryLevel(int $productId, int $variantId = null): int;

    // History operations
    public function getItemHistory(int $orderItemId): Collection;
}
