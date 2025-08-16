<?php

namespace Fereydooni\Shopping\app\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Fereydooni\Shopping\app\DTOs\ShipmentItemDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;

class ShipmentItemService
{
    use HasCrudOperations;
    use HasSearchOperations;

    protected ShipmentItemRepositoryInterface $repository;

    public function __construct(ShipmentItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Basic CRUD Operations
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id): ?ShipmentItem
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ShipmentItemDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ShipmentItem
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ShipmentItemDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ShipmentItem $shipmentItem, array $data): bool
    {
        return $this->repository->update($shipmentItem, $data);
    }

    public function updateAndReturnDTO(ShipmentItem $shipmentItem, array $data): ?ShipmentItemDTO
    {
        return $this->repository->updateAndReturnDTO($shipmentItem, $data);
    }

    public function delete(ShipmentItem $shipmentItem): bool
    {
        return $this->repository->delete($shipmentItem);
    }

    // Shipment-specific queries
    public function findByShipmentId(int $shipmentId): Collection
    {
        return $this->repository->findByShipmentId($shipmentId);
    }

    public function findByShipmentIdDTO(int $shipmentId): Collection
    {
        return $this->repository->findByShipmentIdDTO($shipmentId);
    }

    // Order item queries
    public function findByOrderItemId(int $orderItemId): Collection
    {
        return $this->repository->findByOrderItemId($orderItemId);
    }

    public function findByOrderItemIdDTO(int $orderItemId): Collection
    {
        return $this->repository->findByOrderItemIdDTO($orderItemId);
    }

    // Combined queries
    public function findByShipmentAndOrderItem(int $shipmentId, int $orderItemId): ?ShipmentItem
    {
        return $this->repository->findByShipmentAndOrderItem($shipmentId, $orderItemId);
    }

    public function findByShipmentAndOrderItemDTO(int $shipmentId, int $orderItemId): ?ShipmentItemDTO
    {
        return $this->repository->findByShipmentAndOrderItemDTO($shipmentId, $orderItemId);
    }

    // Count operations
    public function getShipmentItemCount(int $shipmentId): int
    {
        return $this->repository->getShipmentItemCount($shipmentId);
    }

    public function getTotalQuantityByShipment(int $shipmentId): int
    {
        return $this->repository->getTotalQuantityByShipment($shipmentId);
    }

    public function getTotalQuantityByOrderItem(int $orderItemId): int
    {
        return $this->repository->getTotalQuantityByOrderItem($orderItemId);
    }

    // Search operations
    public function search(int $shipmentId, string $query): Collection
    {
        return $this->repository->search($shipmentId, $query);
    }

    public function searchDTO(int $shipmentId, string $query): Collection
    {
        return $this->repository->searchDTO($shipmentId, $query);
    }

    // Quantity-based operations
    public function getByQuantityRange(int $shipmentId, int $minQuantity, int $maxQuantity): Collection
    {
        return $this->repository->getByQuantityRange($shipmentId, $minQuantity, $maxQuantity);
    }

    public function getByQuantityRangeDTO(int $shipmentId, int $minQuantity, int $maxQuantity): Collection
    {
        return $this->repository->getByQuantityRangeDTO($shipmentId, $minQuantity, $maxQuantity);
    }

    // Validation operations
    public function validateShipmentItem(array $data): bool
    {
        return $this->repository->validateShipmentItem($data);
    }

    public function checkQuantityAvailability(int $orderItemId, int $quantity): bool
    {
        return $this->repository->checkQuantityAvailability($orderItemId, $quantity);
    }

    public function validateShipmentItemQuantity(ShipmentItem $shipmentItem, int $newQuantity): bool
    {
        return $this->repository->validateShipmentItemQuantity($shipmentItem, $newQuantity);
    }

    // Product and variant filtering
    public function getShipmentItemsByProduct(int $shipmentId, int $productId): Collection
    {
        return $this->repository->getShipmentItemsByProduct($shipmentId, $productId);
    }

    public function getShipmentItemsByProductDTO(int $shipmentId, int $productId): Collection
    {
        return $this->repository->getShipmentItemsByProductDTO($shipmentId, $productId);
    }

    public function getShipmentItemsByVariant(int $shipmentId, int $variantId): Collection
    {
        return $this->repository->getShipmentItemsByVariant($shipmentId, $variantId);
    }

    public function getShipmentItemsByVariantDTO(int $shipmentId, int $variantId): Collection
    {
        return $this->repository->getShipmentItemsByVariantDTO($shipmentId, $variantId);
    }

    // Calculation operations
    public function calculateShipmentWeight(int $shipmentId): float
    {
        return $this->repository->calculateShipmentWeight($shipmentId);
    }

    public function calculateShipmentVolume(int $shipmentId): float
    {
        return $this->repository->calculateShipmentVolume($shipmentId);
    }

    public function getShipmentItemsSummary(int $shipmentId): array
    {
        return $this->repository->getShipmentItemsSummary($shipmentId);
    }

    // Bulk operations
    public function bulkCreate(array $items): Collection
    {
        return $this->repository->bulkCreate($items);
    }

    public function bulkUpdate(array $updates): bool
    {
        return $this->repository->bulkUpdate($updates);
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->repository->bulkDelete($ids);
    }

    // Analytics operations
    public function getTopShippedItems(int $limit = 10): Collection
    {
        return $this->repository->getTopShippedItems($limit);
    }

    public function getTopShippedItemsDTO(int $limit = 10): Collection
    {
        return $this->repository->getTopShippedItemsDTO($limit);
    }

    public function getShipmentItemsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->getShipmentItemsByDateRange($startDate, $endDate);
    }

    public function getShipmentItemsByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->getShipmentItemsByDateRangeDTO($startDate, $endDate);
    }

    // Status-based operations
    public function getFullyShippedItems(int $shipmentId): Collection
    {
        return $this->repository->getFullyShippedItems($shipmentId);
    }

    public function getFullyShippedItemsDTO(int $shipmentId): Collection
    {
        return $this->repository->getFullyShippedItemsDTO($shipmentId);
    }

    public function getPartiallyShippedItems(int $shipmentId): Collection
    {
        return $this->repository->getPartiallyShippedItems($shipmentId);
    }

    public function getPartiallyShippedItemsDTO(int $shipmentId): Collection
    {
        return $this->repository->getPartiallyShippedItemsDTO($shipmentId);
    }

    // History operations
    public function getShipmentItemHistory(int $shipmentItemId): Collection
    {
        return $this->repository->getShipmentItemHistory($shipmentItemId);
    }

    public function getShipmentItemsByStatus(string $status): Collection
    {
        return $this->repository->getShipmentItemsByStatus($status);
    }

    public function getShipmentItemsByStatusDTO(string $status): Collection
    {
        return $this->repository->getShipmentItemsByStatusDTO($status);
    }

    // Business logic methods
    public function createShipmentItem(array $data): ShipmentItem
    {
        // Validate quantity availability before creating
        if (!$this->checkQuantityAvailability($data['order_item_id'], $data['quantity'])) {
            throw new \InvalidArgumentException('Insufficient quantity available for this order item.');
        }

        return $this->create($data);
    }

    public function createShipmentItemDTO(array $data): ShipmentItemDTO
    {
        $model = $this->createShipmentItem($data);
        return ShipmentItemDTO::fromModel($model);
    }

    public function updateShipmentItem(ShipmentItem $shipmentItem, array $data): bool
    {
        // Validate quantity if it's being updated
        if (isset($data['quantity'])) {
            if (!$this->validateShipmentItemQuantity($shipmentItem, $data['quantity'])) {
                throw new \InvalidArgumentException('Invalid quantity for this shipment item.');
            }
        }

        return $this->update($shipmentItem, $data);
    }

    public function updateShipmentItemDTO(ShipmentItem $shipmentItem, array $data): ?ShipmentItemDTO
    {
        $updated = $this->updateShipmentItem($shipmentItem, $data);
        return $updated ? ShipmentItemDTO::fromModel($shipmentItem->fresh()) : null;
    }

    public function deleteShipmentItem(ShipmentItem $shipmentItem): bool
    {
        return $this->delete($shipmentItem);
    }

    public function searchShipmentItems(int $shipmentId, string $query): Collection
    {
        return $this->search($shipmentId, $query);
    }

    public function searchShipmentItemsDTO(int $shipmentId, string $query): Collection
    {
        return $this->searchDTO($shipmentId, $query);
    }

    public function getShipmentItemStatus(ShipmentItem $shipmentItem): array
    {
        $dto = ShipmentItemDTO::fromModel($shipmentItem);

        return [
            'is_fully_shipped' => $dto->isFullyShipped(),
            'shipped_percentage' => $dto->getShippedPercentage(),
            'remaining_quantity' => $dto->getRemainingQuantity(),
            'total_weight' => $dto->calculateTotalWeight(),
            'total_volume' => $dto->calculateTotalVolume(),
        ];
    }

    public function bulkCreateShipmentItems(array $items): Collection
    {
        // Validate all items before bulk creation
        foreach ($items as $item) {
            if (!$this->checkQuantityAvailability($item['order_item_id'], $item['quantity'])) {
                throw new \InvalidArgumentException("Insufficient quantity available for order item {$item['order_item_id']}.");
            }
        }

        return $this->bulkCreate($items);
    }

    public function bulkUpdateShipmentItems(array $updates): bool
    {
        // Validate quantity updates
        foreach ($updates as $update) {
            if (isset($update['id']) && isset($update['quantity'])) {
                $shipmentItem = $this->find($update['id']);
                if ($shipmentItem && !$this->validateShipmentItemQuantity($shipmentItem, $update['quantity'])) {
                    throw new \InvalidArgumentException("Invalid quantity for shipment item {$update['id']}.");
                }
            }
        }

        return $this->bulkUpdate($updates);
    }

    public function bulkDeleteShipmentItems(array $ids): bool
    {
        return $this->bulkDelete($ids);
    }

    public function getShipmentItemsAnalytics(int $shipmentId): array
    {
        $items = $this->findByShipmentId($shipmentId);
        $fullyShipped = $this->getFullyShippedItems($shipmentId);
        $partiallyShipped = $this->getPartiallyShippedItems($shipmentId);

        return [
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_weight' => $this->calculateShipmentWeight($shipmentId),
            'total_volume' => $this->calculateShipmentVolume($shipmentId),
            'fully_shipped_count' => $fullyShipped->count(),
            'partially_shipped_count' => $partiallyShipped->count(),
            'shipping_completion_percentage' => $items->count() > 0 ? ($fullyShipped->count() / $items->count()) * 100 : 0,
        ];
    }
}
