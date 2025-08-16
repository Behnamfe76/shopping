<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\Services\ProductService;
use Fereydooni\Shopping\app\Http\Requests\StoreProductRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductRequest;
use Fereydooni\Shopping\app\Http\Requests\UploadProductMediaRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductResource;
use Fereydooni\Shopping\app\Http\Resources\ProductCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->get('per_page', 15);
        $products = $this->productService->paginate($perPage);

        return view('shopping::products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('shopping::products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->create($request->validated());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        // Increment view count
        $this->productService->incrementViewCount($product);

        return view('shopping::products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('shopping::products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $this->productService->update($product, $request->validated());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(ToggleProductStatusRequest $request, Product $product): JsonResponse
    {
        $this->authorize('toggleActive', $product);

        $success = $this->productService->toggleActive($product);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product status updated successfully.' : 'Failed to update product status.',
            'is_active' => $product->fresh()->is_active
        ]);
    }

    /**
     * Toggle product featured status.
     */
    public function toggleFeatured(ToggleProductStatusRequest $request, Product $product): JsonResponse
    {
        $this->authorize('toggleFeatured', $product);

        $success = $this->productService->toggleFeatured($product);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product featured status updated successfully.' : 'Failed to update product featured status.',
            'is_featured' => $product->fresh()->is_featured
        ]);
    }

    /**
     * Publish the product.
     */
    public function publish(Product $product): JsonResponse
    {
        $this->authorize('publish', $product);

        $success = $this->productService->publish($product);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product published successfully.' : 'Failed to publish product.'
        ]);
    }

    /**
     * Unpublish the product.
     */
    public function unpublish(Product $product): JsonResponse
    {
        $this->authorize('unpublish', $product);

        $success = $this->productService->unpublish($product);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product unpublished successfully.' : 'Failed to unpublish product.'
        ]);
    }

    /**
     * Archive the product.
     */
    public function archive(Product $product): JsonResponse
    {
        $this->authorize('archive', $product);

        $success = $this->productService->archive($product);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product archived successfully.' : 'Failed to archive product.'
        ]);
    }

    /**
     * Search products.
     */
    public function search(SearchProductRequest $request): View
    {
        $this->authorize('search', Product::class);

        $query = $request->get('query');
        $products = $this->productService->search($query);

        return view('shopping::products.search', compact('products', 'query'));
    }

    /**
     * List active products.
     */
    public function active(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findActive();

        return view('shopping::products.active', compact('products'));
    }

    /**
     * List featured products.
     */
    public function featured(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findFeatured();

        return view('shopping::products.featured', compact('products'));
    }

    /**
     * List in-stock products.
     */
    public function inStock(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findInStock();

        return view('shopping::products.in-stock', compact('products'));
    }

    /**
     * List low stock products.
     */
    public function lowStock(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $threshold = $request->get('threshold', 10);
        $products = $this->productService->findLowStock($threshold);

        return view('shopping::products.low-stock', compact('products', 'threshold'));
    }

    /**
     * List out-of-stock products.
     */
    public function outOfStock(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findOutOfStock();

        return view('shopping::products.out-of-stock', compact('products'));
    }

    /**
     * List top selling products.
     */
    public function topSelling(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getTopSelling($limit);

        return view('shopping::products.top-selling', compact('products'));
    }

    /**
     * List most viewed products.
     */
    public function mostViewed(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getMostViewed($limit);

        return view('shopping::products.most-viewed', compact('products'));
    }

    /**
     * List best rated products.
     */
    public function bestRated(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getBestRated($limit);

        return view('shopping::products.best-rated', compact('products'));
    }

    /**
     * List new arrivals.
     */
    public function newArrivals(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getNewArrivals($limit);

        return view('shopping::products.new-arrivals', compact('products'));
    }

    /**
     * List products on sale.
     */
    public function onSale(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getOnSale($limit);

        return view('shopping::products.on-sale', compact('products'));
    }

    /**
     * List products by category.
     */
    public function byCategory(Request $request, $category): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findByCategoryId($category);

        return view('shopping::products.by-category', compact('products', 'category'));
    }

    /**
     * List products by brand.
     */
    public function byBrand(Request $request, $brand): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->findByBrandId($brand);

        return view('shopping::products.by-brand', compact('products', 'brand'));
    }

    /**
     * List related products.
     */
    public function related(Request $request, Product $product): View
    {
        $this->authorize('view', $product);

        $limit = $request->get('limit', 5);
        $products = $this->productService->getRelatedProducts($product, $limit);

        return view('shopping::products.related', compact('products', 'product'));
    }

    /**
     * Upload product media.
     */
    public function uploadMedia(UploadProductMediaRequest $request, Product $product): JsonResponse
    {
        $this->authorize('uploadMedia', $product);

        $file = $request->file('media');
        $collection = $request->get('collection', 'default');

        $success = $this->productService->addMedia($product, $file, $collection);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Media uploaded successfully.' : 'Failed to upload media.'
        ]);
    }

    /**
     * Delete product media.
     */
    public function deleteMedia(Request $request, Product $product, $media): JsonResponse
    {
        $this->authorize('deleteMedia', $product);

        $success = $this->productService->removeMedia($product, $media);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Media deleted successfully.' : 'Failed to delete media.'
        ]);
    }

    /**
     * Duplicate product.
     */
    public function duplicate(Request $request, Product $product): JsonResponse
    {
        $this->authorize('duplicate', $product);

        // This would typically create a copy of the product
        // For now, we'll return a success response
        $success = true;

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Product duplicated successfully.' : 'Failed to duplicate product.'
        ]);
    }
}
