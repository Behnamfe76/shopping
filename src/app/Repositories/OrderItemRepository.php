<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\OrderItemDTO;
use Fereydooni\Shopping\app\Models\OrderItem;
use Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function all(): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?OrderItem
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])->find($id);
    }

    public function findDTO(int $id): ?OrderItemDTO
    {
        $orderItem = $this->find($id);

        return $orderItem ? OrderItemDTO::fromModel($orderItem) : null;
    }

    public function create(array $data): OrderItem
    {
        // Calculate totals if not provided
        if (! isset($data['subtotal'])) {
            $data['subtotal'] = ($data['price'] ?? 0) * ($data['quantity'] ?? 1);
        }

        if (! isset($data['total_amount'])) {
            $data['total_amount'] = ($data['subtotal'] ?? 0) - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0);
        }

        return OrderItem::create($data);
    }

    public function createAndReturnDTO(array $data): OrderItemDTO
    {
        $orderItem = $this->create($data);

        return OrderItemDTO::fromModel($orderItem);
    }

    public function update(OrderItem $orderItem, array $data): bool
    {
        // Recalculate totals if price or quantity changed
        if (isset($data['price']) || isset($data['quantity'])) {
            $price = $data['price'] ?? $orderItem->price;
            $quantity = $data['quantity'] ?? $orderItem->quantity;
            $data['subtotal'] = $price * $quantity;
        }

        if (isset($data['subtotal']) || isset($data['discount_amount']) || isset($data['tax_amount'])) {
            $subtotal = $data['subtotal'] ?? $orderItem->subtotal;
            $discount = $data['discount_amount'] ?? $orderItem->discount_amount ?? 0;
            $tax = $data['tax_amount'] ?? $orderItem->tax_amount ?? 0;
            $data['total_amount'] = $subtotal - $discount + $tax;
        }

        return $orderItem->update($data);
    }

    public function updateAndReturnDTO(OrderItem $orderItem, array $data): ?OrderItemDTO
    {
        $updated = $this->update($orderItem, $data);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    public function delete(OrderItem $orderItem): bool
    {
        return $orderItem->delete();
    }

    // Order-specific queries
    public function findByOrderId(int $orderId): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('order_id', $orderId)
            ->get();
    }

    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->findByOrderId($orderId)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // Product and variant queries
    public function findByProductId(int $productId): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('product_id', $productId)
            ->get();
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->findByProductId($productId)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    public function findByVariantId(int $variantId): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('variant_id', $variantId)
            ->get();
    }

    public function findByVariantIdDTO(int $variantId): Collection
    {
        return $this->findByVariantId($variantId)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // SKU-based queries
    public function findBySku(string $sku): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('sku', $sku)
            ->get();
    }

    public function findBySkuDTO(string $sku): Collection
    {
        return $this->findBySku($sku)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // Shipping status queries
    public function findShipped(): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('is_shipped', true)
            ->get();
    }

    public function findShippedDTO(): Collection
    {
        return $this->findShipped()->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    public function findUnshipped(): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where('is_shipped', false)
            ->get();
    }

    public function findUnshippedDTO(): Collection
    {
        return $this->findUnshipped()->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // Shipping operations
    public function markAsShipped(OrderItem $orderItem, ?int $shippedQuantity = null): bool
    {
        $shippedQuantity = $shippedQuantity ?? $orderItem->quantity;

        if ($shippedQuantity > $orderItem->quantity) {
            return false;
        }

        $data = [
            'is_shipped' => $shippedQuantity >= $orderItem->quantity,
            'shipped_quantity' => $shippedQuantity,
        ];

        return $this->update($orderItem, $data);
    }

    public function markAsShippedDTO(OrderItem $orderItem, ?int $shippedQuantity = null): ?OrderItemDTO
    {
        $updated = $this->markAsShipped($orderItem, $shippedQuantity);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    // Return operations
    public function markAsReturned(OrderItem $orderItem, ?int $returnedQuantity = null): bool
    {
        $returnedQuantity = $returnedQuantity ?? $orderItem->shipped_quantity;

        if ($returnedQuantity > $orderItem->shipped_quantity) {
            return false;
        }

        $data = [
            'returned_quantity' => $returnedQuantity,
        ];

        return $this->update($orderItem, $data);
    }

    public function markAsReturnedDTO(OrderItem $orderItem, ?int $returnedQuantity = null): ?OrderItemDTO
    {
        $updated = $this->markAsReturned($orderItem, $returnedQuantity);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    // Refund operations
    public function processRefund(OrderItem $orderItem, ?float $refundAmount = null): bool
    {
        $refundAmount = $refundAmount ?? $orderItem->total_amount;

        if ($refundAmount > $orderItem->total_amount) {
            return false;
        }

        $data = [
            'refunded_amount' => $refundAmount,
        ];

        return $this->update($orderItem, $data);
    }

    public function processRefundDTO(OrderItem $orderItem, ?float $refundAmount = null): ?OrderItemDTO
    {
        $updated = $this->processRefund($orderItem, $refundAmount);

        return $updated ? OrderItemDTO::fromModel($orderItem->fresh()) : null;
    }

    // Count operations
    public function getOrderItemCount(): int
    {
        return OrderItem::count();
    }

    public function getOrderItemCountByOrderId(int $orderId): int
    {
        return OrderItem::where('order_id', $orderId)->count();
    }

    public function getOrderItemCountByProductId(int $productId): int
    {
        return OrderItem::where('product_id', $productId)->count();
    }

    // Quantity operations
    public function getTotalQuantity(): int
    {
        return OrderItem::sum('quantity');
    }

    public function getTotalQuantityByProductId(int $productId): int
    {
        return OrderItem::where('product_id', $productId)->sum('quantity');
    }

    // Revenue operations
    public function getTotalRevenue(): float
    {
        return OrderItem::sum('total_amount');
    }

    public function getTotalRevenueByProductId(int $productId): float
    {
        return OrderItem::where('product_id', $productId)->sum('total_amount');
    }

    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
    }

    // Search operations
    public function search(string $query): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->where(function ($q) use ($query) {
                $q->where('sku', 'like', "%{$query}%")
                    ->orWhere('product_name', 'like', "%{$query}%")
                    ->orWhere('variant_name', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // Analytics operations
    public function getTopSellingItems(int $limit = 10): Collection
    {
        return OrderItem::select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(total_amount) as total_revenue')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopSellingItemsDTO(int $limit = 10): Collection
    {
        return $this->getTopSellingItems($limit);
    }

    public function getLowStockItems(int $threshold = 10): Collection
    {
        // This would typically integrate with a product inventory system
        // For now, we'll return items with low quantities
        return OrderItem::select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('product_id', 'product_name', 'sku')
            ->having('total_quantity', '<=', $threshold)
            ->get();
    }

    public function getLowStockItemsDTO(int $threshold = 10): Collection
    {
        return $this->getLowStockItems($threshold);
    }

    // Price range operations
    public function getItemsByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return OrderItem::with(['order', 'product', 'variant', 'shipmentItems'])
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->get();
    }

    public function getItemsByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        return $this->getItemsByPriceRange($minPrice, $maxPrice)->map(function ($orderItem) {
            return OrderItemDTO::fromModel($orderItem);
        });
    }

    // Validation and calculation operations
    public function validateOrderItem(array $data): bool
    {
        $validator = Validator::make($data, OrderItemDTO::rules(), OrderItemDTO::messages());

        return ! $validator->fails();
    }

    public function calculateItemTotals(array $itemData): array
    {
        $price = $itemData['price'] ?? 0;
        $quantity = $itemData['quantity'] ?? 1;
        $discount = $itemData['discount_amount'] ?? 0;
        $tax = $itemData['tax_amount'] ?? 0;

        $subtotal = $price * $quantity;
        $total = $subtotal - $discount + $tax;

        return [
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
        ];
    }

    // Inventory operations
    public function checkInventory(int $productId, ?int $variantId = null, int $quantity = 1): bool
    {
        // This would integrate with a product inventory system
        // For now, we'll assume inventory is available
        return true;
    }

    public function reserveInventory(int $productId, ?int $variantId = null, int $quantity = 1): bool
    {
        // This would integrate with a product inventory system
        // For now, we'll assume reservation is successful
        return true;
    }

    public function releaseInventory(int $productId, ?int $variantId = null, int $quantity = 1): bool
    {
        // This would integrate with a product inventory system
        // For now, we'll assume release is successful
        return true;
    }

    public function updateInventory(int $productId, ?int $variantId = null, int $quantity = 1, string $operation = 'decrease'): bool
    {
        // This would integrate with a product inventory system
        // For now, we'll assume update is successful
        return true;
    }

    public function getInventoryLevel(int $productId, ?int $variantId = null): int
    {
        // This would integrate with a product inventory system
        // For now, we'll return a default value
        return 100;
    }

    // History operations
    public function getItemHistory(int $orderItemId): Collection
    {
        // This would typically query an audit log or history table
        // For now, we'll return an empty collection
        return new Collection;
    }
}
