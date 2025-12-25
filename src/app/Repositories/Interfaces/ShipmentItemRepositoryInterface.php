<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ShipmentItemDTO;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ShipmentItemRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?ShipmentItem;

    public function findDTO(int $id): ?ShipmentItemDTO;

    public function create(array $data): ShipmentItem;

    public function createAndReturnDTO(array $data): ShipmentItemDTO;

    public function update(ShipmentItem $shipmentItem, array $data): bool;

    public function updateAndReturnDTO(ShipmentItem $shipmentItem, array $data): ?ShipmentItemDTO;

    public function delete(ShipmentItem $shipmentItem): bool;

    // Shipment-specific queries
    public function findByShipmentId(int $shipmentId): Collection;

    public function findByShipmentIdDTO(int $shipmentId): Collection;

    // Order item queries
    public function findByOrderItemId(int $orderItemId): Collection;

    public function findByOrderItemIdDTO(int $orderItemId): Collection;

    // Combined queries
    public function findByShipmentAndOrderItem(int $shipmentId, int $orderItemId): ?ShipmentItem;

    public function findByShipmentAndOrderItemDTO(int $shipmentId, int $orderItemId): ?ShipmentItemDTO;

    // Count operations
    public function getShipmentItemCount(int $shipmentId): int;

    public function getTotalQuantityByShipment(int $shipmentId): int;

    public function getTotalQuantityByOrderItem(int $orderItemId): int;

    // Search operations
    public function search(int $shipmentId, string $query): Collection;

    public function searchDTO(int $shipmentId, string $query): Collection;

    // Quantity-based operations
    public function getByQuantityRange(int $shipmentId, int $minQuantity, int $maxQuantity): Collection;

    public function getByQuantityRangeDTO(int $shipmentId, int $minQuantity, int $maxQuantity): Collection;

    // Validation operations
    public function validateShipmentItem(array $data): bool;

    public function checkQuantityAvailability(int $orderItemId, int $quantity): bool;

    public function validateShipmentItemQuantity(ShipmentItem $shipmentItem, int $newQuantity): bool;

    // Product and variant filtering
    public function getShipmentItemsByProduct(int $shipmentId, int $productId): Collection;

    public function getShipmentItemsByProductDTO(int $shipmentId, int $productId): Collection;

    public function getShipmentItemsByVariant(int $shipmentId, int $variantId): Collection;

    public function getShipmentItemsByVariantDTO(int $shipmentId, int $variantId): Collection;

    // Calculation operations
    public function calculateShipmentWeight(int $shipmentId): float;

    public function calculateShipmentVolume(int $shipmentId): float;

    public function getShipmentItemsSummary(int $shipmentId): array;

    // Bulk operations
    public function bulkCreate(array $items): Collection;

    public function bulkUpdate(array $updates): bool;

    public function bulkDelete(array $ids): bool;

    // Analytics operations
    public function getTopShippedItems(int $limit = 10): Collection;

    public function getTopShippedItemsDTO(int $limit = 10): Collection;

    public function getShipmentItemsByDateRange(string $startDate, string $endDate): Collection;

    public function getShipmentItemsByDateRangeDTO(string $startDate, string $endDate): Collection;

    // Status-based operations
    public function getFullyShippedItems(int $shipmentId): Collection;

    public function getFullyShippedItemsDTO(int $shipmentId): Collection;

    public function getPartiallyShippedItems(int $shipmentId): Collection;

    public function getPartiallyShippedItemsDTO(int $shipmentId): Collection;

    // History operations
    public function getShipmentItemHistory(int $shipmentItemId): Collection;

    public function getShipmentItemsByStatus(string $status): Collection;

    public function getShipmentItemsByStatusDTO(string $status): Collection;
}
