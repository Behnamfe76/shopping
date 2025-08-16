<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Fereydooni\Shopping\app\DTOs\ShipmentItemDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;

class ShipmentItemRepository implements ShipmentItemRepositoryInterface
{
    use HasCrudOperations;
    use HasSearchOperations;

    protected ShipmentItem $model;
    protected string $dtoClass = ShipmentItemDTO::class;

    public function __construct()
    {
        $this->model = new ShipmentItem();
    }

    // Basic CRUD Operations
    public function all(): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model::with(['shipment', 'orderItem'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model::with(['shipment', 'orderItem'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model::with(['shipment', 'orderItem'])->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?ShipmentItem
    {
        return $this->model::with(['shipment', 'orderItem'])->find($id);
    }

    public function findDTO(int $id): ?ShipmentItemDTO
    {
        $model = $this->find($id);
        return $model ? ShipmentItemDTO::fromModel($model) : null;
    }

    public function create(array $data): ShipmentItem
    {
        $validated = $this->validateData($data);
        return $this->model::create($validated);
    }

    public function createAndReturnDTO(array $data): ShipmentItemDTO
    {
        $model = $this->create($data);
        return ShipmentItemDTO::fromModel($model);
    }

    public function update(ShipmentItem $shipmentItem, array $data): bool
    {
        $validated = $this->validateData($data, $shipmentItem->id);
        return $shipmentItem->update($validated);
    }

    public function updateAndReturnDTO(ShipmentItem $shipmentItem, array $data): ?ShipmentItemDTO
    {
        $updated = $this->update($shipmentItem, $data);
        return $updated ? ShipmentItemDTO::fromModel($shipmentItem->fresh()) : null;
    }

    public function delete(ShipmentItem $shipmentItem): bool
    {
        return $shipmentItem->delete();
    }

    // Shipment-specific queries
    public function findByShipmentId(int $shipmentId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->get();
    }

    public function findByShipmentIdDTO(int $shipmentId): Collection
    {
        $models = $this->findByShipmentId($shipmentId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Order item queries
    public function findByOrderItemId(int $orderItemId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('order_item_id', $orderItemId)
            ->get();
    }

    public function findByOrderItemIdDTO(int $orderItemId): Collection
    {
        $models = $this->findByOrderItemId($orderItemId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Combined queries
    public function findByShipmentAndOrderItem(int $shipmentId, int $orderItemId): ?ShipmentItem
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->where('order_item_id', $orderItemId)
            ->first();
    }

    public function findByShipmentAndOrderItemDTO(int $shipmentId, int $orderItemId): ?ShipmentItemDTO
    {
        $model = $this->findByShipmentAndOrderItem($shipmentId, $orderItemId);
        return $model ? ShipmentItemDTO::fromModel($model) : null;
    }

    // Count operations
    public function getShipmentItemCount(int $shipmentId): int
    {
        return $this->model::where('shipment_id', $shipmentId)->count();
    }

    public function getTotalQuantityByShipment(int $shipmentId): int
    {
        return $this->model::where('shipment_id', $shipmentId)->sum('quantity');
    }

    public function getTotalQuantityByOrderItem(int $orderItemId): int
    {
        return $this->model::where('order_item_id', $orderItemId)->sum('quantity');
    }

    // Search operations
    public function search(int $shipmentId, string $query): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereHas('orderItem', function ($q) use ($query) {
                $q->where('product_name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->get();
    }

    public function searchDTO(int $shipmentId, string $query): Collection
    {
        $models = $this->search($shipmentId, $query);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Quantity-based operations
    public function getByQuantityRange(int $shipmentId, int $minQuantity, int $maxQuantity): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereBetween('quantity', [$minQuantity, $maxQuantity])
            ->get();
    }

    public function getByQuantityRangeDTO(int $shipmentId, int $minQuantity, int $maxQuantity): Collection
    {
        $models = $this->getByQuantityRange($shipmentId, $minQuantity, $maxQuantity);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Validation operations
    public function validateShipmentItem(array $data): bool
    {
        $validator = Validator::make($data, ShipmentItemDTO::rules(), ShipmentItemDTO::messages());
        return !$validator->fails();
    }

    public function checkQuantityAvailability(int $orderItemId, int $quantity): bool
    {
        $orderItem = DB::table('order_items')->find($orderItemId);
        if (!$orderItem) {
            return false;
        }

        $shippedQuantity = $this->getTotalQuantityByOrderItem($orderItemId);
        return ($orderItem->quantity - $shippedQuantity) >= $quantity;
    }

    public function validateShipmentItemQuantity(ShipmentItem $shipmentItem, int $newQuantity): bool
    {
        $orderItem = DB::table('order_items')->find($shipmentItem->order_item_id);
        if (!$orderItem) {
            return false;
        }

        $otherShippedQuantity = $this->model::where('order_item_id', $shipmentItem->order_item_id)
            ->where('id', '!=', $shipmentItem->id)
            ->sum('quantity');

        return ($orderItem->quantity - $otherShippedQuantity) >= $newQuantity;
    }

    // Product and variant filtering
    public function getShipmentItemsByProduct(int $shipmentId, int $productId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereHas('orderItem', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->get();
    }

    public function getShipmentItemsByProductDTO(int $shipmentId, int $productId): Collection
    {
        $models = $this->getShipmentItemsByProduct($shipmentId, $productId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    public function getShipmentItemsByVariant(int $shipmentId, int $variantId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereHas('orderItem', function ($q) use ($variantId) {
                $q->where('variant_id', $variantId);
            })
            ->get();
    }

    public function getShipmentItemsByVariantDTO(int $shipmentId, int $variantId): Collection
    {
        $models = $this->getShipmentItemsByVariant($shipmentId, $variantId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Calculation operations
    public function calculateShipmentWeight(int $shipmentId): float
    {
        return $this->model::where('shipment_id', $shipmentId)
            ->join('order_items', 'shipment_items.order_item_id', '=', 'order_items.id')
            ->sum(DB::raw('shipment_items.quantity * order_items.weight'));
    }

    public function calculateShipmentVolume(int $shipmentId): float
    {
        // This is a simplified calculation - actual implementation would depend on dimension format
        return $this->model::where('shipment_id', $shipmentId)
            ->join('order_items', 'shipment_items.order_item_id', '=', 'order_items.id')
            ->sum(DB::raw('shipment_items.quantity * COALESCE(order_items.volume, 0)'));
    }

    public function getShipmentItemsSummary(int $shipmentId): array
    {
        $items = $this->findByShipmentId($shipmentId);
        $totalQuantity = $items->sum('quantity');
        $totalWeight = $this->calculateShipmentWeight($shipmentId);
        $totalVolume = $this->calculateShipmentVolume($shipmentId);

        return [
            'total_items' => $items->count(),
            'total_quantity' => $totalQuantity,
            'total_weight' => $totalWeight,
            'total_volume' => $totalVolume,
            'items' => $items->map(fn($item) => ShipmentItemDTO::fromModel($item)->getShipmentItemSummary()),
        ];
    }

    // Bulk operations
    public function bulkCreate(array $items): Collection
    {
        $validatedItems = [];
        foreach ($items as $item) {
            $validatedItems[] = $this->validateData($item);
        }

        $createdItems = [];
        foreach ($validatedItems as $item) {
            $createdItems[] = $this->create($item);
        }

        return collect($createdItems);
    }

    public function bulkUpdate(array $updates): bool
    {
        return DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                if (!isset($update['id'])) {
                    continue;
                }
                $model = $this->find($update['id']);
                if ($model) {
                    $this->update($model, $update);
                }
            }
            return true;
        });
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->model::whereIn('id', $ids)->delete() > 0;
    }

    // Analytics operations
    public function getTopShippedItems(int $limit = 10): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->select('order_item_id', DB::raw('SUM(quantity) as total_shipped'))
            ->groupBy('order_item_id')
            ->orderBy('total_shipped', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopShippedItemsDTO(int $limit = 10): Collection
    {
        $models = $this->getTopShippedItems($limit);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    public function getShipmentItemsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getShipmentItemsByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $models = $this->getShipmentItemsByDateRange($startDate, $endDate);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Status-based operations
    public function getFullyShippedItems(int $shipmentId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereHas('orderItem', function ($q) {
                $q->whereRaw('order_items.quantity <= (SELECT COALESCE(SUM(si.quantity), 0) FROM shipment_items si WHERE si.order_item_id = order_items.id)');
            })
            ->get();
    }

    public function getFullyShippedItemsDTO(int $shipmentId): Collection
    {
        $models = $this->getFullyShippedItems($shipmentId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    public function getPartiallyShippedItems(int $shipmentId): Collection
    {
        return $this->model::with(['shipment', 'orderItem'])
            ->where('shipment_id', $shipmentId)
            ->whereHas('orderItem', function ($q) {
                $q->whereRaw('order_items.quantity > (SELECT COALESCE(SUM(si.quantity), 0) FROM shipment_items si WHERE si.order_item_id = order_items.id)');
            })
            ->get();
    }

    public function getPartiallyShippedItemsDTO(int $shipmentId): Collection
    {
        $models = $this->getPartiallyShippedItems($shipmentId);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // History operations
    public function getShipmentItemHistory(int $shipmentItemId): Collection
    {
        // This would typically query a history/audit table
        // For now, returning the current item
        $item = $this->find($shipmentItemId);
        return $item ? collect([$item]) : collect();
    }

    public function getShipmentItemsByStatus(string $status): Collection
    {
        // This would filter by shipment status
        return $this->model::with(['shipment', 'orderItem'])
            ->whereHas('shipment', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->get();
    }

    public function getShipmentItemsByStatusDTO(string $status): Collection
    {
        $models = $this->getShipmentItemsByStatus($status);
        return $models->map(fn($model) => ShipmentItemDTO::fromModel($model));
    }

    // Helper methods
    protected function validateData(array $data, ?int $excludeId = null): array
    {
        $rules = ShipmentItemDTO::rules();

        if ($excludeId) {
            $rules = $this->updateUniqueRules($rules, $excludeId);
        }

        $validator = Validator::make($data, $rules, ShipmentItemDTO::messages());

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    protected function updateUniqueRules(array $rules, int $excludeId): array
    {
        foreach ($rules as $field => $fieldRules) {
            $rules[$field] = array_map(function ($rule) use ($excludeId, $field) {
                if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                    $parts = explode(',', $rule);
                    if (count($parts) >= 2) {
                        $table = $parts[1];
                        $column = isset($parts[2]) ? $parts[2] : $field;
                        return "unique:{$table},{$column},{$excludeId}";
                    }
                }
                return $rule;
            }, $fieldRules);
        }
        return $rules;
    }
}
