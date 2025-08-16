<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\OrderItem as OrderItemModel;
use Fereydooni\Shopping\app\DTOs\OrderItemDTO;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static OrderItemModel|null find(int $id)
 * @method static OrderItemDTO|null findDTO(int $id)
 * @method static OrderItemModel create(array $data)
 * @method static OrderItemDTO createDTO(array $data)
 * @method static bool update(OrderItemModel $orderItem, array $data)
 * @method static OrderItemDTO|null updateDTO(OrderItemModel $orderItem, array $data)
 * @method static bool delete(OrderItemModel $orderItem)
 *
 * @method static Collection findByOrderId(int $orderId)
 * @method static Collection findByOrderIdDTO(int $orderId)
 * @method static Collection findByProductId(int $productId)
 * @method static Collection findByProductIdDTO(int $productId)
 * @method static Collection findByVariantId(int $variantId)
 * @method static Collection findByVariantIdDTO(int $variantId)
 * @method static Collection findBySku(string $sku)
 * @method static Collection findBySkuDTO(string $sku)
 *
 * @method static Collection findShipped()
 * @method static Collection findShippedDTO()
 * @method static Collection findUnshipped()
 * @method static Collection findUnshippedDTO()
 *
 * @method static bool markAsShipped(OrderItemModel $orderItem, int $shippedQuantity = null)
 * @method static OrderItemDTO|null markAsShippedDTO(OrderItemModel $orderItem, int $shippedQuantity = null)
 * @method static bool markAsReturned(OrderItemModel $orderItem, int $returnedQuantity = null)
 * @method static OrderItemDTO|null markAsReturnedDTO(OrderItemModel $orderItem, int $returnedQuantity = null)
 * @method static bool processRefund(OrderItemModel $orderItem, float $refundAmount = null)
 * @method static OrderItemDTO|null processRefundDTO(OrderItemModel $orderItem, float $refundAmount = null)
 *
 * @method static int getOrderItemCount()
 * @method static int getOrderItemCountByOrderId(int $orderId)
 * @method static int getOrderItemCountByProductId(int $productId)
 * @method static int getTotalQuantity()
 * @method static int getTotalQuantityByProductId(int $productId)
 * @method static float getTotalRevenue()
 * @method static float getTotalRevenueByProductId(int $productId)
 * @method static float getTotalRevenueByDateRange(string $startDate, string $endDate)
 *
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 *
 * @method static Collection getTopSellingItems(int $limit = 10)
 * @method static Collection getTopSellingItemsDTO(int $limit = 10)
 * @method static Collection getLowStockItems(int $threshold = 10)
 * @method static Collection getLowStockItemsDTO(int $threshold = 10)
 * @method static Collection getItemsByPriceRange(float $minPrice, float $maxPrice)
 * @method static Collection getItemsByPriceRangeDTO(float $minPrice, float $maxPrice)
 *
 * @method static bool validateOrderItem(array $data)
 * @method static array calculateItemTotals(array $itemData)
 * @method static Collection getItemHistory(int $orderItemId)
 *
 * @method static bool checkInventory(int $productId, int $variantId = null, int $quantity = 1)
 * @method static bool reserveInventory(int $productId, int $variantId = null, int $quantity = 1)
 * @method static bool releaseInventory(int $productId, int $variantId = null, int $quantity = 1)
 * @method static bool updateInventory(int $productId, int $variantId = null, int $quantity = 1, string $operation = 'decrease')
 * @method static int getInventoryLevel(int $productId, int $variantId = null)
 * @method static bool isLowStock(int $productId, int $variantId = null, int $threshold = 10)
 * @method static Collection getInventoryHistory(int $productId, int $variantId = null)
 * @method static Collection getInventoryAlerts()
 * @method static bool processInventoryAdjustment(int $productId, int $variantId = null, int $quantity, string $reason = '')
 *
 * @method static OrderItemModel createOrderItem(array $data)
 * @method static OrderItemDTO createOrderItemDTO(array $data)
 * @method static bool updateOrderItem(OrderItemModel $orderItem, array $data)
 * @method static OrderItemDTO|null updateOrderItemDTO(OrderItemModel $orderItem, array $data)
 * @method static bool deleteOrderItem(OrderItemModel $orderItem)
 *
 * @method static bool markOrderItemAsShipped(OrderItemModel $orderItem, int $shippedQuantity = null)
 * @method static OrderItemDTO|null markOrderItemAsShippedDTO(OrderItemModel $orderItem, int $shippedQuantity = null)
 * @method static bool markOrderItemAsReturned(OrderItemModel $orderItem, int $returnedQuantity = null)
 * @method static OrderItemDTO|null markOrderItemAsReturnedDTO(OrderItemModel $orderItem, int $returnedQuantity = null)
 * @method static bool processOrderItemRefund(OrderItemModel $orderItem, float $refundAmount = null)
 * @method static OrderItemDTO|null processOrderItemRefundDTO(OrderItemModel $orderItem, float $refundAmount = null)
 *
 * @method static Collection searchOrderItems(string $query)
 * @method static Collection searchOrderItemsDTO(string $query)
 * @method static array getOrderItemStatus(OrderItemModel $orderItem)
 *
 * @method static array bulkMarkAsShipped(array $orderItemIds, int $shippedQuantity = null)
 * @method static array bulkMarkAsReturned(array $orderItemIds, int $returnedQuantity = null)
 * @method static array bulkProcessRefund(array $orderItemIds, float $refundAmount = null)
 *
 * @method static float calculateShippingCosts(OrderItemModel $orderItem)
 * @method static string getShippingStatus(OrderItemModel $orderItem)
 * @method static string getReturnStatus(OrderItemModel $orderItem)
 * @method static string getRefundStatus(OrderItemModel $orderItem)
 * @method static Collection getShippingHistory(OrderItemModel $orderItem)
 *
 * @method static Collection getShippedItems()
 * @method static Collection getShippedItemsDTO()
 * @method static Collection getUnshippedItems()
 * @method static Collection getUnshippedItemsDTO()
 *
 * @see \Fereydooni\Shopping\app\Services\OrderItemService
 */
class OrderItem extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.order-item';
    }
}
