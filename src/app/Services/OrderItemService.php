<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\OrderItemDTO;
use Fereydooni\Shopping\app\Models\OrderItem;
use Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasInventoryManagement;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasShippingOperations;
use Illuminate\Database\Eloquent\Collection;

class OrderItemService
{
    use HasCrudOperations;
    use HasInventoryManagement;
    use HasSearchOperations;
    use HasShippingOperations;

    protected OrderItemRepositoryInterface $repository;

    public function __construct(OrderItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Order-specific queries
    public function findByOrderId(int $orderId): Collection
    {
        return $this->repository->findByOrderId($orderId);
    }

    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->repository->findByOrderIdDTO($orderId);
    }

    // Product and variant queries
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->repository->findByProductIdDTO($productId);
    }

    public function findByVariantId(int $variantId): Collection
    {
        return $this->repository->findByVariantId($variantId);
    }

    public function findByVariantIdDTO(int $variantId): Collection
    {
        return $this->repository->findByVariantIdDTO($variantId);
    }

    // SKU-based queries
    public function findBySku(string $sku): Collection
    {
        return $this->repository->findBySku($sku);
    }

    public function findBySkuDTO(string $sku): Collection
    {
        return $this->repository->findBySkuDTO($sku);
    }

    // Count operations
    public function getOrderItemCount(): int
    {
        return $this->repository->getOrderItemCount();
    }

    public function getOrderItemCountByOrderId(int $orderId): int
    {
        return $this->repository->getOrderItemCountByOrderId($orderId);
    }

    public function getOrderItemCountByProductId(int $productId): int
    {
        return $this->repository->getOrderItemCountByProductId($productId);
    }

    // Quantity operations
    public function getTotalQuantity(): int
    {
        return $this->repository->getTotalQuantity();
    }

    public function getTotalQuantityByProductId(int $productId): int
    {
        return $this->repository->getTotalQuantityByProductId($productId);
    }

    // Revenue operations
    public function getTotalRevenue(): float
    {
        return $this->repository->getTotalRevenue();
    }

    public function getTotalRevenueByProductId(int $productId): float
    {
        return $this->repository->getTotalRevenueByProductId($productId);
    }

    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getTotalRevenueByDateRange($startDate, $endDate);
    }

    // Analytics operations
    public function getTopSellingItems(int $limit = 10): Collection
    {
        return $this->repository->getTopSellingItems($limit);
    }

    public function getTopSellingItemsDTO(int $limit = 10): Collection
    {
        return $this->repository->getTopSellingItemsDTO($limit);
    }

    // Price range operations
    public function getItemsByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->getItemsByPriceRange($minPrice, $maxPrice);
    }

    public function getItemsByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->getItemsByPriceRangeDTO($minPrice, $maxPrice);
    }

    // Validation and calculation operations
    public function validateOrderItem(array $data): bool
    {
        return $this->repository->validateOrderItem($data);
    }

    public function calculateItemTotals(array $itemData): array
    {
        return $this->repository->calculateItemTotals($itemData);
    }

    // History operations
    public function getItemHistory(int $orderItemId): Collection
    {
        return $this->repository->getItemHistory($orderItemId);
    }

    // Business logic methods
    public function createOrderItem(array $data): OrderItem
    {
        // Validate the data
        if (! $this->validateOrderItem($data)) {
            throw new \InvalidArgumentException('Invalid order item data');
        }

        // Check inventory availability
        if (! $this->checkInventory($data['product_id'], $data['variant_id'] ?? null, $data['quantity'])) {
            throw new \InvalidArgumentException('Insufficient inventory');
        }

        // Reserve inventory
        if (! $this->reserveInventory($data['product_id'], $data['variant_id'] ?? null, $data['quantity'])) {
            throw new \InvalidArgumentException('Failed to reserve inventory');
        }

        // Calculate totals if not provided
        if (! isset($data['subtotal'])) {
            $data['subtotal'] = ($data['price'] ?? 0) * ($data['quantity'] ?? 1);
        }

        if (! isset($data['total_amount'])) {
            $data['total_amount'] = ($data['subtotal'] ?? 0) - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0);
        }

        return $this->repository->create($data);
    }

    public function createOrderItemDTO(array $data): OrderItemDTO
    {
        $orderItem = $this->createOrderItem($data);

        return OrderItemDTO::fromModel($orderItem);
    }

    public function updateOrderItem(OrderItem $orderItem, array $data): bool
    {
        // Validate the data
        if (! $this->validateOrderItem($data)) {
            throw new \InvalidArgumentException('Invalid order item data');
        }

        // Handle quantity changes
        if (isset($data['quantity']) && $data['quantity'] != $orderItem->quantity) {
            $quantityDiff = $data['quantity'] - $orderItem->quantity;

            if ($quantityDiff > 0) {
                // Increasing quantity - check and reserve additional inventory
                if (! $this->checkInventory($orderItem->product_id, $orderItem->variant_id, $quantityDiff)) {
                    throw new \InvalidArgumentException('Insufficient inventory for quantity increase');
                }
                $this->reserveInventory($orderItem->product_id, $orderItem->variant_id, $quantityDiff);
            } else {
                // Decreasing quantity - release excess inventory
                $this->releaseInventory($orderItem->product_id, $orderItem->variant_id, abs($quantityDiff));
            }
        }

        return $this->repository->update($orderItem, $data);
    }

    public function updateOrderItemDTO(OrderItem $orderItem, array $data): ?OrderItemDTO
    {
        $updated = $this->updateOrderItem($orderItem, $data);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    public function deleteOrderItem(OrderItem $orderItem): bool
    {
        // Release reserved inventory
        $this->releaseInventory($orderItem->product_id, $orderItem->variant_id, $orderItem->quantity);

        return $this->repository->delete($orderItem);
    }

    // Shipping operations with business logic
    public function markOrderItemAsShipped(OrderItem $orderItem, ?int $shippedQuantity = null): bool
    {
        $shippedQuantity = $shippedQuantity ?? $orderItem->quantity;

        if ($shippedQuantity > $orderItem->quantity) {
            throw new \InvalidArgumentException('Shipped quantity cannot exceed ordered quantity');
        }

        return $this->markAsShipped($orderItem, $shippedQuantity);
    }

    public function markOrderItemAsShippedDTO(OrderItem $orderItem, ?int $shippedQuantity = null): ?OrderItemDTO
    {
        $updated = $this->markOrderItemAsShipped($orderItem, $shippedQuantity);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    public function markOrderItemAsReturned(OrderItem $orderItem, ?int $returnedQuantity = null): bool
    {
        $returnedQuantity = $returnedQuantity ?? $orderItem->shipped_quantity;

        if ($returnedQuantity > $orderItem->shipped_quantity) {
            throw new \InvalidArgumentException('Returned quantity cannot exceed shipped quantity');
        }

        return $this->markAsReturned($orderItem, $returnedQuantity);
    }

    public function markOrderItemAsReturnedDTO(OrderItem $orderItem, ?int $returnedQuantity = null): ?OrderItemDTO
    {
        $updated = $this->markOrderItemAsReturned($orderItem, $returnedQuantity);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    public function processOrderItemRefund(OrderItem $orderItem, ?float $refundAmount = null): bool
    {
        $refundAmount = $refundAmount ?? $orderItem->total_amount;

        if ($refundAmount > $orderItem->total_amount) {
            throw new \InvalidArgumentException('Refund amount cannot exceed total amount');
        }

        return $this->processRefund($orderItem, $refundAmount);
    }

    public function processOrderItemRefundDTO(OrderItem $orderItem, ?float $refundAmount = null): ?OrderItemDTO
    {
        $updated = $this->processOrderItemRefund($orderItem, $refundAmount);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    // Search operations
    public function searchOrderItems(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchOrderItemsDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    // Status methods
    public function getOrderItemStatus(OrderItem $orderItem): array
    {
        return [
            'shipping_status' => $this->getShippingStatus($orderItem),
            'return_status' => $this->getReturnStatus($orderItem),
            'refund_status' => $this->getRefundStatus($orderItem),
            'inventory_status' => $this->isLowStock($orderItem->product_id, $orderItem->variant_id) ? 'low_stock' : 'in_stock',
        ];
    }

    // Bulk operations
    public function bulkMarkAsShipped(array $orderItemIds, ?int $shippedQuantity = null): array
    {
        $results = [];

        foreach ($orderItemIds as $orderItemId) {
            $orderItem = $this->repository->find($orderItemId);
            if ($orderItem) {
                $results[$orderItemId] = $this->markOrderItemAsShipped($orderItem, $shippedQuantity);
            } else {
                $results[$orderItemId] = false;
            }
        }

        return $results;
    }

    public function bulkMarkAsReturned(array $orderItemIds, ?int $returnedQuantity = null): array
    {
        $results = [];

        foreach ($orderItemIds as $orderItemId) {
            $orderItem = $this->repository->find($orderItemId);
            if ($orderItem) {
                $results[$orderItemId] = $this->markOrderItemAsReturned($orderItem, $returnedQuantity);
            } else {
                $results[$orderItemId] = false;
            }
        }

        return $results;
    }

    public function bulkProcessRefund(array $orderItemIds, ?float $refundAmount = null): array
    {
        $results = [];

        foreach ($orderItemIds as $orderItemId) {
            $orderItem = $this->repository->find($orderItemId);
            if ($orderItem) {
                $results[$orderItemId] = $this->processOrderItemRefund($orderItem, $refundAmount);
            } else {
                $results[$orderItemId] = false;
            }
        }

        return $results;
    }
}
