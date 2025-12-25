<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\SearchShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Resources\ShipmentItemCollection;
use Fereydooni\Shopping\app\Http\Resources\ShipmentItemResource;
use Fereydooni\Shopping\app\Http\Resources\ShipmentItemSearchResource;
use Fereydooni\Shopping\app\Models\Shipment;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Fereydooni\Shopping\app\Services\ShipmentItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentItemController extends Controller
{
    public function __construct(
        private ShipmentItemService $shipmentItemService
    ) {}

    /**
     * Display a listing of shipment items for a specific shipment.
     */
    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->findByShipmentIdDTO($shipment->id);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Store a newly created shipment item in storage.
     */
    public function store(StoreShipmentItemRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('createForShipment', [ShipmentItem::class, $shipment->id]);

        $data = $request->validated();
        $data['shipment_id'] = $shipment->id;

        $item = $this->shipmentItemService->createShipmentItemDTO($data);

        return response()->json(new ShipmentItemResource($item), 201);
    }

    /**
     * Display the specified shipment item.
     */
    public function show(Shipment $shipment, ShipmentItem $item): JsonResponse
    {
        $this->authorize('view', $item);

        return response()->json(new ShipmentItemResource($item));
    }

    /**
     * Update the specified shipment item in storage.
     */
    public function update(UpdateShipmentItemRequest $request, Shipment $shipment, ShipmentItem $item): JsonResponse
    {
        $this->authorize('update', $item);

        $updatedItem = $this->shipmentItemService->updateShipmentItemDTO($item, $request->validated());

        return response()->json(new ShipmentItemResource($updatedItem));
    }

    /**
     * Remove the specified shipment item from storage.
     */
    public function destroy(Shipment $shipment, ShipmentItem $item): JsonResponse
    {
        $this->authorize('delete', $item);

        $this->shipmentItemService->deleteShipmentItem($item);

        return response()->json(['message' => 'Shipment item deleted successfully']);
    }

    /**
     * Search shipment items.
     */
    public function search(SearchShipmentItemRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('search', ShipmentItem::class);

        $query = $request->validated()['query'];
        $items = $this->shipmentItemService->searchShipmentItemsDTO($shipment->id, $query);

        return response()->json(new ShipmentItemSearchResource($items, $query));
    }

    /**
     * Get shipment items summary.
     */
    public function summary(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $summary = $this->shipmentItemService->getShipmentItemsSummary($shipment->id);

        return response()->json($summary);
    }

    /**
     * Calculate shipment weight.
     */
    public function calculateWeight(Shipment $shipment): JsonResponse
    {
        $this->authorize('calculateWeight', ShipmentItem::class);

        $weight = $this->shipmentItemService->calculateShipmentWeight($shipment->id);

        return response()->json(['weight' => $weight]);
    }

    /**
     * Calculate shipment volume.
     */
    public function calculateVolume(Shipment $shipment): JsonResponse
    {
        $this->authorize('calculateVolume', ShipmentItem::class);

        $volume = $this->shipmentItemService->calculateShipmentVolume($shipment->id);

        return response()->json(['volume' => $volume]);
    }

    /**
     * Get shipment items count.
     */
    public function getCount(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $count = $this->shipmentItemService->getShipmentItemCount($shipment->id);

        return response()->json(['count' => $count]);
    }

    /**
     * Get shipment items by quantity range.
     */
    public function byQuantityRange(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $request->validate([
            'min_quantity' => 'integer|min:0',
            'max_quantity' => 'integer|min:1',
        ]);

        $minQuantity = $request->get('min_quantity', 1);
        $maxQuantity = $request->get('max_quantity', 100);

        $items = $this->shipmentItemService->getByQuantityRangeDTO($shipment->id, $minQuantity, $maxQuantity);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Get fully shipped items.
     */
    public function fullyShipped(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getFullyShippedItemsDTO($shipment->id);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Get partially shipped items.
     */
    public function partiallyShipped(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getPartiallyShippedItemsDTO($shipment->id);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Get shipment items by product.
     */
    public function byProduct(Shipment $shipment, $productId): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getShipmentItemsByProductDTO($shipment->id, $productId);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Get shipment items by variant.
     */
    public function byVariant(Shipment $shipment, $variantId): JsonResponse
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getShipmentItemsByVariantDTO($shipment->id, $variantId);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Bulk create shipment items.
     */
    public function bulkCreate(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('bulkOperations', ShipmentItem::class);

        $request->validate([
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|integer|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = $this->shipmentItemService->bulkCreateShipmentItems($request->input('items'));

        return response()->json([
            'message' => 'Shipment items created successfully.',
            'items' => new ShipmentItemCollection($items),
        ], 201);
    }

    /**
     * Bulk update shipment items.
     */
    public function bulkUpdate(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('bulkOperations', ShipmentItem::class);

        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|integer|exists:shipment_items,id',
            'updates.*.quantity' => 'sometimes|integer|min:1',
        ]);

        $success = $this->shipmentItemService->bulkUpdateShipmentItems($request->input('updates'));

        return response()->json([
            'message' => $success ? 'Shipment items updated successfully.' : 'Some items could not be updated.',
            'success' => $success,
        ]);
    }

    /**
     * Bulk delete shipment items.
     */
    public function bulkDelete(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('bulkOperations', ShipmentItem::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:shipment_items,id',
        ]);

        $success = $this->shipmentItemService->bulkDeleteShipmentItems($request->input('ids'));

        return response()->json([
            'message' => $success ? 'Shipment items deleted successfully.' : 'Some items could not be deleted.',
            'success' => $success,
        ]);
    }

    /**
     * Get shipment items analytics.
     */
    public function analytics(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewAnalytics', ShipmentItem::class);

        $analytics = $this->shipmentItemService->getShipmentItemsAnalytics($shipment->id);

        return response()->json($analytics);
    }

    /**
     * Get top shipped items.
     */
    public function topShipped(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewAnalytics', ShipmentItem::class);

        $items = $this->shipmentItemService->getTopShippedItemsDTO(10);

        return response()->json(new ShipmentItemCollection($items));
    }

    /**
     * Get shipment item status.
     */
    public function getStatus(Shipment $shipment, ShipmentItem $item): JsonResponse
    {
        $this->authorize('view', $item);

        $status = $this->shipmentItemService->getShipmentItemStatus($item);

        return response()->json($status);
    }
}
