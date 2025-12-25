<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Http\Requests\BulkProductMetaRequest;
use Fereydooni\Shopping\app\Http\Requests\ImportProductMetaRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductMetaRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProductMetaRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductMetaStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductMetaRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductMetaCollection;
use Fereydooni\Shopping\app\Http\Resources\ProductMetaResource;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\Services\ProductMetaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductMetaController extends Controller
{
    public function __construct(
        private ProductMetaService $productMetaService
    ) {
        $this->authorizeResource(ProductMeta::class, 'meta');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $perPage = $request->get('per_page', 15);
        $meta = $this->productMetaService->paginate($perPage);

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductMetaRequest $request): JsonResponse
    {
        $this->authorize('create', ProductMeta::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $meta = $this->productMetaService->create($data);

        return response()->json(new ProductMetaResource($meta), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductMeta $meta): JsonResponse
    {
        $this->authorize('view', $meta);

        return response()->json(new ProductMetaResource($meta));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductMetaRequest $request, ProductMeta $meta): JsonResponse
    {
        $this->authorize('update', $meta);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->productMetaService->update($meta, $data);

        return response()->json(new ProductMetaResource($meta->fresh()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductMeta $meta): JsonResponse
    {
        $this->authorize('delete', $meta);

        $this->productMetaService->delete($meta);

        return response()->json(['message' => 'Product meta deleted successfully']);
    }

    /**
     * Toggle public status
     */
    public function togglePublic(ToggleProductMetaStatusRequest $request, ProductMeta $meta): JsonResponse
    {
        $this->authorize('togglePublic', $meta);

        $this->productMetaService->togglePublic($meta);

        return response()->json(new ProductMetaResource($meta->fresh()));
    }

    /**
     * Toggle searchable status
     */
    public function toggleSearchable(ToggleProductMetaStatusRequest $request, ProductMeta $meta): JsonResponse
    {
        $this->authorize('toggleSearchable', $meta);

        $this->productMetaService->toggleSearchable($meta);

        return response()->json(new ProductMetaResource($meta->fresh()));
    }

    /**
     * Toggle filterable status
     */
    public function toggleFilterable(ToggleProductMetaStatusRequest $request, ProductMeta $meta): JsonResponse
    {
        $this->authorize('toggleFilterable', $meta);

        $this->productMetaService->toggleFilterable($meta);

        return response()->json(new ProductMetaResource($meta->fresh()));
    }

    /**
     * Search product meta
     */
    public function search(SearchProductMetaRequest $request): JsonResponse
    {
        $this->authorize('search', ProductMeta::class);

        $query = $request->get('query');
        $meta = $this->productMetaService->search($query);

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get public meta
     */
    public function public(): JsonResponse
    {
        $this->authorize('viewPublic', ProductMeta::class);

        $meta = $this->productMetaService->findPublic();

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get private meta
     */
    public function private(): JsonResponse
    {
        $this->authorize('viewPrivate', ProductMeta::class);

        $meta = $this->productMetaService->findPrivate();

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get searchable meta
     */
    public function searchable(): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $meta = $this->productMetaService->findSearchable();

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get filterable meta
     */
    public function filterable(): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $meta = $this->productMetaService->findFilterable();

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get meta by product
     */
    public function byProduct(int $productId): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $meta = $this->productMetaService->findByProductId($productId);

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get meta by key
     */
    public function byKey(string $key): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $meta = $this->productMetaService->findByMetaKey($key);

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get meta by type
     */
    public function byType(string $type): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $meta = $this->productMetaService->findByMetaType($type);

        return response()->json(new ProductMetaCollection($meta));
    }

    /**
     * Get meta keys
     */
    public function getKeys(): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $keys = $this->productMetaService->getMetaKeys();

        return response()->json(['keys' => $keys]);
    }

    /**
     * Get meta types
     */
    public function getTypes(): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $types = $this->productMetaService->getMetaTypes();

        return response()->json(['types' => $types]);
    }

    /**
     * Get meta values by key
     */
    public function getValuesByKey(string $key): JsonResponse
    {
        $this->authorize('viewAny', ProductMeta::class);

        $values = $this->productMetaService->getMetaValues($key);

        return response()->json(['values' => $values]);
    }

    /**
     * Bulk create meta
     */
    public function bulkCreate(BulkProductMetaRequest $request, int $productId): JsonResponse
    {
        $this->authorize('bulkManage', ProductMeta::class);

        $data = $request->validated();
        $meta = $this->productMetaService->bulkCreate($productId, $data);

        return response()->json(new ProductMetaCollection($meta), 201);
    }

    /**
     * Bulk update meta
     */
    public function bulkUpdate(BulkProductMetaRequest $request, int $productId): JsonResponse
    {
        $this->authorize('bulkManage', ProductMeta::class);

        $data = $request->validated();
        $success = $this->productMetaService->bulkUpdate($productId, $data);

        return response()->json(['success' => $success]);
    }

    /**
     * Bulk delete meta
     */
    public function bulkDelete(Request $request, int $productId): JsonResponse
    {
        $this->authorize('bulkManage', ProductMeta::class);

        $keys = $request->validate(['keys' => 'required|array|min:1'])['keys'];
        $success = $this->productMetaService->bulkDelete($productId, $keys);

        return response()->json(['success' => $success]);
    }

    /**
     * Import meta
     */
    public function import(ImportProductMetaRequest $request, int $productId): JsonResponse
    {
        $this->authorize('import', ProductMeta::class);

        $data = $request->validated();
        $success = $this->productMetaService->importMeta($productId, $data);

        return response()->json(['success' => $success]);
    }

    /**
     * Export meta
     */
    public function export(int $productId): JsonResponse
    {
        $this->authorize('export', ProductMeta::class);

        $data = $this->productMetaService->exportMeta($productId);

        return response()->json(['data' => $data]);
    }

    /**
     * Sync meta
     */
    public function sync(BulkProductMetaRequest $request, int $productId): JsonResponse
    {
        $this->authorize('sync', ProductMeta::class);

        $data = $request->validated();
        $success = $this->productMetaService->syncMeta($productId, $data);

        return response()->json(['success' => $success]);
    }

    /**
     * Get meta analytics
     */
    public function analytics(string $key): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductMeta::class);

        $analytics = $this->productMetaService->getMetaAnalytics($key);

        return response()->json(['analytics' => $analytics]);
    }
}
