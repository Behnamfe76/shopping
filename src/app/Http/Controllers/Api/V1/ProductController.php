<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Enums\ProductType;
use Fereydooni\Shopping\app\Http\Resources\ProductResource;
use Fereydooni\Shopping\app\Facades\Product as ProductFacade;
use Fereydooni\Shopping\app\Http\Resources\ProductCollection;
use Fereydooni\Shopping\app\Http\Requests\StoreProductRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductMediaResource;
use Fereydooni\Shopping\app\Http\Resources\ProductSearchResource;
use Fereydooni\Shopping\app\Http\Requests\UploadProductMediaRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductAnalyticsResource;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\BulkProductOperationsRequest;

class ProductController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        try {
            $perPage = $request->get('per_page', 15);
            $paginationType = $request->get('pagination', 'regular');

            $products = match($paginationType) {
                'simplePaginate' => ProductFacade::simplePaginate($perPage),
                'cursorPaginate' => ProductFacade::cursorPaginate($perPage),
                default => ProductFacade::paginate($perPage),
            };

            return response()->json(
                new ProductCollection($products)
            );
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to retrieve products',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

     /**
     * Display a listing of product statuses.
     */
    public function statuses(): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        try {
            return response()->json([
                'data' => array_map(fn($status) => [
                    'id' => $status->toString(),
                    'name' => __('products.statuses.' . $status->toString()),
                ], ProductStatus::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product statuses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of product tpyes.
     */
    public function productTypes(): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        try {
            return response()->json([
                'data' => array_map(fn($type) => [
                    'id' => $type->toString(),
                    'name' => __('products.types.' . $type->toString()),
                ], ProductType::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product types',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        try {
            $product = ProductFacade::create($request->validated());

            return (new ProductResource($product))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create product',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        try {
            // Increment view count
            // app('shopping.product')->incrementViewCount($product);

            return (new ProductResource($product->load(['category', 'brand', 'tags', 'variants'])))->response()->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to find product',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);
        
        $updated = ProductFacade::update($product, $request->validated());

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update product'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $deleted = app('shopping.product')->delete($product);

        if (!$deleted) {
            return response()->json([
                'message' => 'Failed to delete product'
            ], 422);
        }

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(ToggleProductStatusRequest $request, Product $product): JsonResponse
    {
        $this->authorize('toggleActive', $product);

        $toggled = app('shopping.product')->toggleActive($product);

        if (!$toggled) {
            return response()->json([
                'message' => 'Failed to toggle product status'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product status toggled successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Toggle product featured status.
     */
    public function toggleFeatured(ToggleProductStatusRequest $request, Product $product): JsonResponse
    {
        $this->authorize('toggleFeatured', $product);

        $toggled = app('shopping.product')->toggleFeatured($product);

        if (!$toggled) {
            return response()->json([
                'message' => 'Failed to toggle product featured status'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product featured status toggled successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Publish product.
     */
    public function publish(Product $product): JsonResponse
    {
        $this->authorize('publish', $product);

        $published = app('shopping.product')->publish($product);

        if (!$published) {
            return response()->json([
                'message' => 'Failed to publish product'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product published successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Unpublish product.
     */
    public function unpublish(Product $product): JsonResponse
    {
        $this->authorize('unpublish', $product);

        $unpublished = app('shopping.product')->unpublish($product);

        if (!$unpublished) {
            return response()->json([
                'message' => 'Failed to unpublish product'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product unpublished successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Archive product.
     */
    public function archive(Product $product): JsonResponse
    {
        $this->authorize('archive', $product);

        $archived = app('shopping.product')->archive($product);

        if (!$archived) {
            return response()->json([
                'message' => 'Failed to archive product'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Product archived successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Search products.
     */
    public function search(SearchProductRequest $request): JsonResource
    {
        $this->authorize('search', Product::class);

        $query = $request->get('query');
        $products = app('shopping.product')->search($query);

        return new ProductSearchResource($products);
    }

    /**
     * List active products.
     */
    public function active(Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findActive();

        return new ProductCollection($products);
    }

    /**
     * List featured products.
     */
    public function featured(Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findFeatured();

        return new ProductCollection($products);
    }

    /**
     * List in-stock products.
     */
    public function inStock(Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findInStock();

        return new ProductCollection($products);
    }

    /**
     * List low stock products.
     */
    public function lowStock(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $threshold = $request->get('threshold', 10);
        $products = app('shopping.product')->findLowStock($threshold);

        return response()->json([
            'data' => ProductResource::collection($products),
            'threshold' => $threshold,
            'count' => $products->count()
        ]);
    }

    /**
     * List out-of-stock products.
     */
    public function outOfStock(Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findOutOfStock();

        return new ProductCollection($products);
    }

    /**
     * List top selling products.
     */
    public function topSelling(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = app('shopping.product')->getTopSelling($limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * List most viewed products.
     */
    public function mostViewed(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = app('shopping.product')->getMostViewed($limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * List best rated products.
     */
    public function bestRated(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = app('shopping.product')->getBestRated($limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * List new arrivals.
     */
    public function newArrivals(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = app('shopping.product')->getNewArrivals($limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * List products on sale.
     */
    public function onSale(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = app('shopping.product')->getOnSale($limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * List products by category.
     */
    public function byCategory(Category $category, Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findByCategoryId($category->id);

        return new ProductCollection($products);
    }

    /**
     * List products by brand.
     */
    public function byBrand(Brand $brand, Request $request): JsonResource
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = app('shopping.product')->findByBrandId($brand->id);

        return new ProductCollection($products);
    }

    /**
     * List related products.
     */
    public function related(Product $product, Request $request): JsonResponse
    {
        $this->authorize('view', $product);

        $limit = $request->get('limit', 5);
        $products = app('shopping.product')->getRelatedProducts($product, $limit);

        return response()->json([
            'data' => ProductResource::collection($products),
            'limit' => $limit,
            'count' => $products->count()
        ]);
    }

    /**
     * Get product count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $count = app('shopping.product')->getProductCount();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get product analytics.
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', Product::class);

        $analytics = app('shopping.product')->getProductAnalytics();

        return response()->json([
            'data' => new ProductAnalyticsResource($analytics)
        ]);
    }

    /**
     * Upload product media.
     */
    public function uploadMedia(UploadProductMediaRequest $request, Product $product): JsonResponse
    {
        $this->authorize('uploadMedia', $product);

        $media = app('shopping.product')->uploadMedia($product, $request->file('media'));

        return response()->json([
            'message' => 'Media uploaded successfully',
            'data' => new ProductMediaResource($media)
        ], 201);
    }

    /**
     * Delete product media.
     */
    public function deleteMedia(Product $product, $mediaId): JsonResponse
    {
        $this->authorize('deleteMedia', $product);

        $deleted = app('shopping.product')->deleteMedia($product, $mediaId);

        if (!$deleted) {
            return response()->json([
                'message' => 'Failed to delete media'
            ], 422);
        }

        return response()->json([
            'message' => 'Media deleted successfully'
        ]);
    }

    /**
     * Duplicate product.
     */
    public function duplicate(Product $product): JsonResponse
    {
        $this->authorize('duplicate', $product);

        $duplicated = app('shopping.product')->duplicate($product);

        if (!$duplicated) {
            return response()->json([
                'message' => 'Failed to duplicate product'
            ], 422);
        }

        return response()->json([
            'message' => 'Product duplicated successfully',
            'data' => new ProductResource($duplicated)
        ], 201);
    }

    /**
     * Bulk operations.
     */
    public function bulkOperations(BulkProductOperationsRequest $request): JsonResponse
    {
        $this->authorize('bulkOperations', Product::class);

        $operation = $request->get('operation');
        $productIds = $request->get('product_ids', []);

        $result = app('shopping.product')->bulkOperations($operation, $productIds);

        return response()->json([
            'message' => 'Bulk operation completed successfully',
            'data' => $result
        ]);
    }

    /**
     * Get inventory level.
     */
    public function getInventoryLevel(Product $product): JsonResponse
    {
        $this->authorize('viewInventory', $product);

        $level = app('shopping.product')->getInventoryLevel($product);

        return response()->json([
            'data' => [
                'product_id' => $product->id,
                'inventory_level' => $level,
                'stock_quantity' => $product->stock_quantity,
                'min_stock_level' => $product->min_stock_level,
                'max_stock_level' => $product->max_stock_level
            ]
        ]);
    }

    /**
     * Update inventory.
     */
    public function updateInventory(Request $request): JsonResponse
    {
        $this->authorize('manageInventory', Product::class);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'operation' => 'required|in:increase,decrease,set'
        ]);

        $product = Product::findOrFail($request->product_id);
        $this->authorize('manageInventory', $product);

        $updated = app('shopping.product')->updateStock(
            $product,
            $request->quantity,
            $request->operation
        );

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update inventory'
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'message' => 'Inventory updated successfully',
            'data' => [
                'product_id' => $product->id,
                'new_stock_quantity' => $product->stock_quantity
            ]
        ]);
    }
}
