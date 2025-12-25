<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Http\Requests\SearchShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateShipmentItemRequest;
use Fereydooni\Shopping\app\Http\Resources\ShipmentItemCollection;
use Fereydooni\Shopping\app\Models\Shipment;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Fereydooni\Shopping\app\Services\ShipmentItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentItemController extends Controller
{
    public function __construct(
        private ShipmentItemService $shipmentItemService
    ) {}

    /**
     * Display a listing of shipment items for a specific shipment.
     */
    public function index(Request $request, Shipment $shipment): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->findByShipmentId($shipment->id);
        $items = $items->paginate($request->get('per_page', 15));

        return view('shopping::shipment-items.index', compact('shipment', 'items'));
    }

    /**
     * Show the form for creating a new shipment item.
     */
    public function create(Shipment $shipment): View
    {
        $this->authorize('createForShipment', [ShipmentItem::class, $shipment->id]);

        return view('shopping::shipment-items.create', compact('shipment'));
    }

    /**
     * Store a newly created shipment item in storage.
     */
    public function store(StoreShipmentItemRequest $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('createForShipment', [ShipmentItem::class, $shipment->id]);

        $data = $request->validated();
        $data['shipment_id'] = $shipment->id;

        $item = $this->shipmentItemService->createShipmentItem($data);

        return redirect()->route('shopping.shipment-items.show', [$shipment, $item])
            ->with('success', 'Shipment item created successfully.');
    }

    /**
     * Display the specified shipment item.
     */
    public function show(Shipment $shipment, ShipmentItem $item): View
    {
        $this->authorize('view', $item);

        return view('shopping::shipment-items.show', compact('shipment', 'item'));
    }

    /**
     * Show the form for editing the specified shipment item.
     */
    public function edit(Shipment $shipment, ShipmentItem $item): View
    {
        $this->authorize('update', $item);

        return view('shopping::shipment-items.edit', compact('shipment', 'item'));
    }

    /**
     * Update the specified shipment item in storage.
     */
    public function update(UpdateShipmentItemRequest $request, Shipment $shipment, ShipmentItem $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $this->shipmentItemService->updateShipmentItem($item, $request->validated());

        return redirect()->route('shopping.shipment-items.show', [$shipment, $item])
            ->with('success', 'Shipment item updated successfully.');
    }

    /**
     * Remove the specified shipment item from storage.
     */
    public function destroy(Shipment $shipment, ShipmentItem $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $this->shipmentItemService->deleteShipmentItem($item);

        return redirect()->route('shopping.shipment-items.index', $shipment)
            ->with('success', 'Shipment item deleted successfully.');
    }

    /**
     * Search shipment items.
     */
    public function search(SearchShipmentItemRequest $request, Shipment $shipment): View
    {
        $this->authorize('search', ShipmentItem::class);

        $query = $request->validated()['query'];
        $items = $this->shipmentItemService->searchShipmentItems($shipment->id, $query);

        return view('shopping::shipment-items.search', compact('shipment', 'items', 'query'));
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
     * Get shipment items by quantity range.
     */
    public function byQuantityRange(Request $request, Shipment $shipment): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $minQuantity = $request->get('min_quantity', 1);
        $maxQuantity = $request->get('max_quantity', 100);

        $items = $this->shipmentItemService->getByQuantityRange($shipment->id, $minQuantity, $maxQuantity);

        return view('shopping::shipment-items.by-quantity-range', compact('shipment', 'items', 'minQuantity', 'maxQuantity'));
    }

    /**
     * Get fully shipped items.
     */
    public function fullyShipped(Shipment $shipment): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getFullyShippedItems($shipment->id);

        return view('shopping::shipment-items.fully-shipped', compact('shipment', 'items'));
    }

    /**
     * Get partially shipped items.
     */
    public function partiallyShipped(Shipment $shipment): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getPartiallyShippedItems($shipment->id);

        return view('shopping::shipment-items.partially-shipped', compact('shipment', 'items'));
    }

    /**
     * Get shipment items by product.
     */
    public function byProduct(Shipment $shipment, $productId): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getShipmentItemsByProduct($shipment->id, $productId);

        return view('shopping::shipment-items.by-product', compact('shipment', 'items', 'productId'));
    }

    /**
     * Get shipment items by variant.
     */
    public function byVariant(Shipment $shipment, $variantId): View
    {
        $this->authorize('viewForShipment', [ShipmentItem::class, $shipment->id]);

        $items = $this->shipmentItemService->getShipmentItemsByVariant($shipment->id, $variantId);

        return view('shopping::shipment-items.by-variant', compact('shipment', 'items', 'variantId'));
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
            'items' => ShipmentItemCollection::make($items),
        ]);
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
    public function topShipped(Shipment $shipment): View
    {
        $this->authorize('viewAnalytics', ShipmentItem::class);

        $items = $this->shipmentItemService->getTopShippedItems(10);

        return view('shopping::shipment-items.top-shipped', compact('shipment', 'items'));
    }
}
