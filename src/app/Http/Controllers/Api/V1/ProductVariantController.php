<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\Services\ProductVariantService;
use Fereydooni\Shopping\app\Http\Requests\StoreProductVariantRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductVariantRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductVariantStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductVariantStockRequest;
use Fereydooni\Shopping\app\Http\Requests\SetProductVariantPriceRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductVariantRequest;
use Fereydooni\Shopping\app\Http\Requests\BulkProductVariantRequest;
use Fereydooni\Shopping\app\Http\Requests\ImportProductVariantRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductVariantResource;
use Fereydooni\Shopping\app\Http\Resources\ProductVariantCollection;
use Fereydooni\Shopping\app\Http\Resources\ProductVariantSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProductVariantBulkResource;
use Fereydooni\Shopping\app\Http\Resources\ProductVariantAnalyticsResource;

class ProductVariantController extends Controller
{
    public function __construct(
        private ProductVariantService $variantService
    ) {
        $this->authorizeResource(ProductVariant::class, 'variant');
    }

    /**
     * Display a listing of product variants.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $perPage = $request->get('per_page', 15);
        $variants = $this->variantService->paginate($perPage);

        return response()->json(new ProductVariantCollection($variants));
    }

    /**
     * Store a newly created product variant.
     */
    public function store(StoreProductVariantRequest $request): JsonResponse
    {
        $this->authorize('create', ProductVariant::class);

        $variant = $this->variantService->create($request->validated());

        return response()->json(new ProductVariantResource($variant), 201);
    }

    /**
     * Display the specified product variant.
     */
    public function show(ProductVariant $variant): JsonResponse
    {
        $this->authorize('view', $variant);

        return response()->json(new ProductVariantResource($variant));
    }

    /**
     * Update the specified product variant.
     */
    public function update(UpdateProductVariantRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('update', $variant);

        $this->variantService->update($variant, $request->validated());

        return response()->json(new ProductVariantResource($variant->fresh()));
    }

    /**
     * Remove the specified product variant.
     */
    public function destroy(ProductVariant $variant): JsonResponse
    {
        $this->authorize('delete', $variant);

        $this->variantService->delete($variant);

        return response()->json(['message' => 'Product variant deleted successfully.'], 200);
    }

    /**
     * Toggle the active status of a product variant.
     */
    public function toggleActive(ToggleProductVariantStatusRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('toggleActive', $variant);

        $success = $this->variantService->toggleActive($variant);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Status toggled successfully.' : 'Failed to toggle status.',
            'is_active' => $variant->fresh()->is_active
        ]);
    }

    /**
     * Toggle the featured status of a product variant.
     */
    public function toggleFeatured(ToggleProductVariantStatusRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('toggleFeatured', $variant);

        $success = $this->variantService->toggleFeatured($variant);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Featured status toggled successfully.' : 'Failed to toggle featured status.',
            'is_featured' => $variant->fresh()->is_featured
        ]);
    }

    /**
     * Update the stock of a product variant.
     */
    public function updateStock(UpdateProductVariantStockRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $quantity = $request->validated()['quantity'];
        $success = $this->variantService->updateStock($variant, $quantity);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Stock updated successfully.' : 'Failed to update stock.',
            'stock' => $variant->fresh()->stock
        ]);
    }

    /**
     * Reserve stock for a product variant.
     */
    public function reserveStock(UpdateProductVariantStockRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $quantity = $request->validated()['quantity'];
        $success = $this->variantService->reserveStock($variant, $quantity);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Stock reserved successfully.' : 'Failed to reserve stock.',
            'reserved_stock' => $variant->fresh()->reserved_stock
        ]);
    }

    /**
     * Release reserved stock for a product variant.
     */
    public function releaseStock(UpdateProductVariantStockRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $quantity = $request->validated()['quantity'];
        $success = $this->variantService->releaseStock($variant, $quantity);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Stock released successfully.' : 'Failed to release stock.',
            'reserved_stock' => $variant->fresh()->reserved_stock
        ]);
    }

    /**
     * Adjust stock for a product variant.
     */
    public function adjustStock(UpdateProductVariantStockRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $quantity = $request->validated()['quantity'];
        $reason = $request->validated()['reason'] ?? null;
        $success = $this->variantService->adjustStock($variant, $quantity, $reason);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Stock adjusted successfully.' : 'Failed to adjust stock.',
            'stock' => $variant->fresh()->stock
        ]);
    }

    /**
     * Set the price of a product variant.
     */
    public function setPrice(SetProductVariantPriceRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('managePricing', $variant);

        $price = $request->validated()['price'];
        $success = $this->variantService->setPrice($variant, $price);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Price set successfully.' : 'Failed to set price.',
            'price' => $variant->fresh()->price
        ]);
    }

    /**
     * Set the sale price of a product variant.
     */
    public function setSalePrice(SetProductVariantPriceRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('managePricing', $variant);

        $salePrice = $request->validated()['price'];
        $success = $this->variantService->setSalePrice($variant, $salePrice);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Sale price set successfully.' : 'Failed to set sale price.',
            'sale_price' => $variant->fresh()->sale_price
        ]);
    }

    /**
     * Set the compare price of a product variant.
     */
    public function setComparePrice(SetProductVariantPriceRequest $request, ProductVariant $variant): JsonResponse
    {
        $this->authorize('managePricing', $variant);

        $comparePrice = $request->validated()['price'];
        $success = $this->variantService->setComparePrice($variant, $comparePrice);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Compare price set successfully.' : 'Failed to set compare price.',
            'compare_price' => $variant->fresh()->compare_price
        ]);
    }

    /**
     * Search product variants.
     */
    public function search(SearchProductVariantRequest $request): JsonResponse
    {
        $this->authorize('search', ProductVariant::class);

        $query = $request->validated()['query'];
        $variants = $this->variantService->search($query);

        return response()->json(new ProductVariantSearchResource($variants, $query));
    }

    /**
     * Display active product variants.
     */
    public function active(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variants = $this->variantService->findActive();

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display in-stock product variants.
     */
    public function inStock(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variants = $this->variantService->findInStock();

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display out-of-stock product variants.
     */
    public function outOfStock(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variants = $this->variantService->findOutOfStock();

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display low-stock product variants.
     */
    public function lowStock(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variants = $this->variantService->findLowStock();

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display variants by product.
     */
    public function byProduct(int $productId): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variants = $this->variantService->findByProductId($productId);

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Find variant by SKU.
     */
    public function bySku(string $sku): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variant = $this->variantService->findBySku($sku);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found.'], 404);
        }

        return response()->json(new ProductVariantResource($variant));
    }

    /**
     * Find variant by barcode.
     */
    public function byBarcode(string $barcode): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $variant = $this->variantService->findByBarcode($barcode);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found.'], 404);
        }

        return response()->json(new ProductVariantResource($variant));
    }

    /**
     * Get variant count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $count = $this->variantService->getVariantCount();

        return response()->json(['count' => $count]);
    }

    /**
     * Display variants by price range.
     */
    public function byPriceRange(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $minPrice = $request->get('min_price', 0);
        $maxPrice = $request->get('max_price', 1000);
        $variants = $this->variantService->findByPriceRange($minPrice, $maxPrice);

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display variants by stock range.
     */
    public function byStockRange(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $minStock = $request->get('min_stock', 0);
        $maxStock = $request->get('max_stock', 100);
        $variants = $this->variantService->findByStockRange($minStock, $maxStock);

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Display variants by weight range.
     */
    public function byWeightRange(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $minWeight = $request->get('min_weight', 0);
        $maxWeight = $request->get('max_weight', 10);
        $variants = $this->variantService->findByStockRange($minWeight, $maxWeight); // Using stock range method as placeholder

        return response()->json(ProductVariantResource::collection($variants));
    }

    /**
     * Get variant SKUs.
     */
    public function getSkus(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $skus = $this->variantService->getVariantSkus();

        return response()->json($skus);
    }

    /**
     * Get variant barcodes.
     */
    public function getBarcodes(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $barcodes = $this->variantService->getVariantBarcodes();

        return response()->json($barcodes);
    }

    /**
     * Get variant prices.
     */
    public function getPrices(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $prices = $this->variantService->getVariantPrices();

        return response()->json($prices);
    }

    /**
     * Get variant weights.
     */
    public function getWeights(): JsonResponse
    {
        $this->authorize('viewAny', ProductVariant::class);

        $weights = $this->variantService->getVariantWeights();

        return response()->json($weights);
    }

    /**
     * Bulk create variants.
     */
    public function bulkCreate(BulkProductVariantRequest $request, int $productId): JsonResponse
    {
        $this->authorize('bulkManage', ProductVariant::class);

        $variantData = $request->validated()['variants'];
        $variants = $this->variantService->bulkCreate($productId, $variantData);

        return response()->json([
            'success' => true,
            'message' => 'Variants created successfully.',
            'count' => $variants->count(),
            'variants' => ProductVariantResource::collection($variants)
        ]);
    }

    /**
     * Bulk update variants.
     */
    public function bulkUpdate(BulkProductVariantRequest $request): JsonResponse
    {
        $this->authorize('bulkManage', ProductVariant::class);

        $variantData = $request->validated()['variants'];
        $success = $this->variantService->bulkUpdate($variantData);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Variants updated successfully.' : 'Failed to update variants.'
        ]);
    }

    /**
     * Bulk delete variants.
     */
    public function bulkDelete(BulkProductVariantRequest $request): JsonResponse
    {
        $this->authorize('bulkManage', ProductVariant::class);

        $variantIds = $request->validated()['variant_ids'];
        $success = $this->variantService->bulkDelete($variantIds);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Variants deleted successfully.' : 'Failed to delete variants.'
        ]);
    }

    /**
     * Import variants.
     */
    public function import(ImportProductVariantRequest $request, int $productId): JsonResponse
    {
        $this->authorize('import', ProductVariant::class);

        $variantData = $request->validated()['variants'];
        $success = $this->variantService->importVariants($productId, $variantData);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Variants imported successfully.' : 'Failed to import variants.'
        ]);
    }

    /**
     * Export variants.
     */
    public function export(int $productId): JsonResponse
    {
        $this->authorize('export', ProductVariant::class);

        $exportData = $this->variantService->exportVariants($productId);

        return response()->json($exportData);
    }

    /**
     * Sync variants.
     */
    public function sync(BulkProductVariantRequest $request, int $productId): JsonResponse
    {
        $this->authorize('sync', ProductVariant::class);

        $variantData = $request->validated()['variants'];
        $success = $this->variantService->syncVariants($productId, $variantData);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Variants synced successfully.' : 'Failed to sync variants.'
        ]);
    }

    /**
     * Get variant inventory.
     */
    public function inventory(ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $inventory = $this->variantService->getVariantInventory($variant->id);

        return response()->json($inventory);
    }

    /**
     * Get variant inventory history.
     */
    public function inventoryHistory(ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $history = $this->variantService->getVariantInventoryHistory($variant->id);

        return response()->json($history);
    }

    /**
     * Get variant inventory alerts.
     */
    public function inventoryAlerts(ProductVariant $variant): JsonResponse
    {
        $this->authorize('manageInventory', $variant);

        $alerts = $this->variantService->getVariantInventoryAlerts($variant->id);

        return response()->json($alerts);
    }

    /**
     * Get variant analytics.
     */
    public function analytics(ProductVariant $variant): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $analytics = $this->variantService->getVariantAnalytics($variant->id);

        return response()->json(new ProductVariantAnalyticsResource($analytics));
    }

    /**
     * Get variant analytics by product.
     */
    public function analyticsByProduct(int $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $analytics = $this->variantService->getVariantAnalyticsByProduct($productId);

        return response()->json($analytics);
    }

    /**
     * Get variant sales.
     */
    public function sales(ProductVariant $variant): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $sales = $this->variantService->getVariantSales($variant->id);

        return response()->json(['sales' => $sales]);
    }

    /**
     * Get variant revenue.
     */
    public function revenue(ProductVariant $variant): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $revenue = $this->variantService->getVariantRevenue($variant->id);

        return response()->json(['revenue' => $revenue]);
    }

    /**
     * Get variant profit.
     */
    public function profit(ProductVariant $variant): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $profit = $this->variantService->getVariantProfit($variant->id);

        return response()->json(['profit' => $profit]);
    }

    /**
     * Get variant margin.
     */
    public function margin(ProductVariant $variant): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductVariant::class);

        $margin = $this->variantService->getVariantMargin($variant->id);

        return response()->json(['margin' => $margin]);
    }
}
