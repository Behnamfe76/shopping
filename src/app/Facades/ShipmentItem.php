<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\ShipmentItemDTO;
use Fereydooni\Shopping\app\Models\ShipmentItem as ShipmentItemModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static ShipmentItemModel|null find(int $id)
 * @method static ShipmentItemDTO|null findDTO(int $id)
 * @method static ShipmentItemModel create(array $data)
 * @method static ShipmentItemDTO createAndReturnDTO(array $data)
 * @method static bool update(ShipmentItemModel $shipmentItem, array $data)
 * @method static ShipmentItemDTO|null updateAndReturnDTO(ShipmentItemModel $shipmentItem, array $data)
 * @method static bool delete(ShipmentItemModel $shipmentItem)
 * @method static Collection findByShipmentId(int $shipmentId)
 * @method static Collection findByShipmentIdDTO(int $shipmentId)
 * @method static Collection findByOrderItemId(int $orderItemId)
 * @method static Collection findByOrderItemIdDTO(int $orderItemId)
 * @method static ShipmentItemModel|null findByShipmentAndOrderItem(int $shipmentId, int $orderItemId)
 * @method static ShipmentItemDTO|null findByShipmentAndOrderItemDTO(int $shipmentId, int $orderItemId)
 * @method static int getShipmentItemCount(int $shipmentId)
 * @method static int getTotalQuantityByShipment(int $shipmentId)
 * @method static int getTotalQuantityByOrderItem(int $orderItemId)
 * @method static Collection search(int $shipmentId, string $query)
 * @method static Collection searchDTO(int $shipmentId, string $query)
 * @method static Collection getByQuantityRange(int $shipmentId, int $minQuantity, int $maxQuantity)
 * @method static Collection getByQuantityRangeDTO(int $shipmentId, int $minQuantity, int $maxQuantity)
 * @method static bool validateShipmentItem(array $data)
 * @method static bool checkQuantityAvailability(int $orderItemId, int $quantity)
 * @method static bool validateShipmentItemQuantity(ShipmentItemModel $shipmentItem, int $newQuantity)
 * @method static Collection getShipmentItemsByProduct(int $shipmentId, int $productId)
 * @method static Collection getShipmentItemsByProductDTO(int $shipmentId, int $productId)
 * @method static Collection getShipmentItemsByVariant(int $shipmentId, int $variantId)
 * @method static Collection getShipmentItemsByVariantDTO(int $shipmentId, int $variantId)
 * @method static float calculateShipmentWeight(int $shipmentId)
 * @method static float calculateShipmentVolume(int $shipmentId)
 * @method static array getShipmentItemsSummary(int $shipmentId)
 * @method static Collection bulkCreate(array $items)
 * @method static bool bulkUpdate(array $updates)
 * @method static bool bulkDelete(array $ids)
 * @method static Collection getTopShippedItems(int $limit = 10)
 * @method static Collection getTopShippedItemsDTO(int $limit = 10)
 * @method static Collection getShipmentItemsByDateRange(string $startDate, string $endDate)
 * @method static Collection getShipmentItemsByDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection getFullyShippedItems(int $shipmentId)
 * @method static Collection getFullyShippedItemsDTO(int $shipmentId)
 * @method static Collection getPartiallyShippedItems(int $shipmentId)
 * @method static Collection getPartiallyShippedItemsDTO(int $shipmentId)
 * @method static Collection getShipmentItemHistory(int $shipmentItemId)
 * @method static Collection getShipmentItemsByStatus(string $status)
 * @method static Collection getShipmentItemsByStatusDTO(string $status)
 * @method static ShipmentItemModel createShipmentItem(array $data)
 * @method static ShipmentItemDTO createShipmentItemDTO(array $data)
 * @method static bool updateShipmentItem(ShipmentItemModel $shipmentItem, array $data)
 * @method static ShipmentItemDTO|null updateShipmentItemDTO(ShipmentItemModel $shipmentItem, array $data)
 * @method static bool deleteShipmentItem(ShipmentItemModel $shipmentItem)
 * @method static Collection searchShipmentItems(int $shipmentId, string $query)
 * @method static Collection searchShipmentItemsDTO(int $shipmentId, string $query)
 * @method static array getShipmentItemStatus(ShipmentItemModel $shipmentItem)
 * @method static Collection bulkCreateShipmentItems(array $items)
 * @method static bool bulkUpdateShipmentItems(array $updates)
 * @method static bool bulkDeleteShipmentItems(array $ids)
 * @method static array getShipmentItemsAnalytics(int $shipmentId)
 *
 * @see \Fereydooni\Shopping\app\Services\ShipmentItemService
 */
class ShipmentItem extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.shipment-item';
    }
}
