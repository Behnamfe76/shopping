<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Services\ProductAttributeService;
use Fereydooni\Shopping\app\Http\Requests\StoreProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductAttributeStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\AddProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeResource;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeCollection;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeValueResource;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeAnalyticsResource;

class ProductAttributeController extends Controller
{
    public function __construct(
        private ProductAttributeService $productAttributeService
    ) {
    }

    /**
     * Display a listing of the product attributes.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $perPage = $request->get('per_page', 15);
        $attributes = $this->productAttributeService->paginate($perPage);

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Store a newly created product attribute in storage.
     */
    public function store(StoreProductAttributeRequest $request): JsonResponse
    {
        $this->authorize('create', ProductAttribute::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $attribute = $this->productAttributeService->create($data);

        return response()->json(new ProductAttributeResource($attribute), 201);
    }

    /**
     * Display the specified product attribute.
     */
    public function show(ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('view', $attribute);

        return response()->json(new ProductAttributeResource($attribute));
    }

    /**
     * Update the specified product attribute in storage.
     */
    public function update(UpdateProductAttributeRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('update', $attribute);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->productAttributeService->update($attribute, $data);

        return response()->json(new ProductAttributeResource($attribute->fresh()));
    }

    /**
     * Remove the specified product attribute from storage.
     */
    public function destroy(ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('delete', $attribute);

        $this->productAttributeService->delete($attribute);

        return response()->json(['message' => 'Product attribute deleted successfully']);
    }

    /**
     * Toggle the active status of the product attribute.
     */
    public function toggleActive(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleActive', $attribute);

        $success = $this->productAttributeService->toggleActive($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Status toggled successfully.' : 'Failed to toggle status.',
            'is_active' => $attribute->fresh()->is_active,
        ]);
    }

    /**
     * Toggle the required status of the product attribute.
     */
    public function toggleRequired(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleRequired', $attribute);

        $success = $this->productAttributeService->toggleRequired($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Required status toggled successfully.' : 'Failed to toggle required status.',
            'is_required' => $attribute->fresh()->is_required,
        ]);
    }

    /**
     * Toggle the searchable status of the product attribute.
     */
    public function toggleSearchable(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleSearchable', $attribute);

        $success = $this->productAttributeService->toggleSearchable($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Searchable status toggled successfully.' : 'Failed to toggle searchable status.',
            'is_searchable' => $attribute->fresh()->is_searchable,
        ]);
    }

    /**
     * Toggle the filterable status of the product attribute.
     */
    public function toggleFilterable(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleFilterable', $attribute);

        $success = $this->productAttributeService->toggleFilterable($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Filterable status toggled successfully.' : 'Failed to toggle filterable status.',
            'is_filterable' => $attribute->fresh()->is_filterable,
        ]);
    }

    /**
     * Toggle the comparable status of the product attribute.
     */
    public function toggleComparable(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleComparable', $attribute);

        $success = $this->productAttributeService->toggleComparable($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Comparable status toggled successfully.' : 'Failed to toggle comparable status.',
            'is_comparable' => $attribute->fresh()->is_comparable,
        ]);
    }

    /**
     * Toggle the visible status of the product attribute.
     */
    public function toggleVisible(ToggleProductAttributeStatusRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('toggleVisible', $attribute);

        $success = $this->productAttributeService->toggleVisible($attribute);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Visible status toggled successfully.' : 'Failed to toggle visible status.',
            'is_visible' => $attribute->fresh()->is_visible,
        ]);
    }

    /**
     * Search product attributes.
     */
    public function search(SearchProductAttributeRequest $request): JsonResponse
    {
        $this->authorize('search', ProductAttribute::class);

        $query = $request->validated()['query'];
        $attributes = $this->productAttributeService->search($query);

        return response()->json(new ProductAttributeSearchResource($attributes, $query));
    }

    /**
     * Display required product attributes.
     */
    public function required(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findRequired();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display searchable product attributes.
     */
    public function searchable(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findSearchable();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display filterable product attributes.
     */
    public function filterable(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findFilterable();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display comparable product attributes.
     */
    public function comparable(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findComparable();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display visible product attributes.
     */
    public function visible(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findVisible();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display system product attributes.
     */
    public function system(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findSystem();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display custom product attributes.
     */
    public function custom(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findCustom();

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display product attributes by type.
     */
    public function byType(string $type): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByType($type);

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display product attributes by group.
     */
    public function byGroup(string $group): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByGroup($group);

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Display product attributes by input type.
     */
    public function byInputType(string $inputType): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByInputType($inputType);

        return response()->json(new ProductAttributeCollection($attributes));
    }

    /**
     * Get attribute count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $count = $this->productAttributeService->getAttributeCount();

        return response()->json(['count' => $count]);
    }

    /**
     * Get attribute groups.
     */
    public function getGroups(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $groups = $this->productAttributeService->getAttributeGroups();

        return response()->json(['groups' => $groups]);
    }

    /**
     * Get attribute types.
     */
    public function getTypes(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $types = $this->productAttributeService->getAttributeTypes();

        return response()->json(['types' => $types]);
    }

    /**
     * Get input types.
     */
    public function getInputTypes(): JsonResponse
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $inputTypes = $this->productAttributeService->getInputTypes();

        return response()->json(['input_types' => $inputTypes]);
    }

    /**
     * Get attribute values.
     */
    public function getValues(ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('viewValues', $attribute);

        $values = $this->productAttributeService->getAttributeValues($attribute->id);

        return response()->json(ProductAttributeValueResource::collection($values));
    }

    /**
     * Add attribute value.
     */
    public function addValue(AddProductAttributeValueRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('manageValues', $attribute);

        $data = $request->validated();
        $value = $this->productAttributeService->addAttributeValue($attribute->id, $data['value'], $data['metadata'] ?? []);

        return response()->json(new ProductAttributeValueResource($value), 201);
    }

    /**
     * Update attribute value.
     */
    public function updateValue(UpdateProductAttributeValueRequest $request, ProductAttribute $attribute, int $valueId): JsonResponse
    {
        $this->authorize('manageValues', $attribute);

        $data = $request->validated();
        $success = $this->productAttributeService->updateAttributeValue($attribute->id, $valueId, $data['value'], $data['metadata'] ?? []);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Value updated successfully.' : 'Failed to update value.',
        ]);
    }

    /**
     * Delete attribute value.
     */
    public function deleteValue(ProductAttribute $attribute, int $valueId): JsonResponse
    {
        $this->authorize('manageValues', $attribute);

        $success = $this->productAttributeService->removeAttributeValue($attribute->id, $valueId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Value deleted successfully.' : 'Failed to delete value.',
        ]);
    }

    /**
     * Get attribute analytics.
     */
    public function getAnalytics(ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('viewAnalytics', $attribute);

        $analytics = $this->productAttributeService->getAttributeAnalytics($attribute->id);

        return response()->json(new ProductAttributeAnalyticsResource($analytics));
    }

    /**
     * Get attribute usage.
     */
    public function getUsage(ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('viewAnalytics', $attribute);

        $usage = $this->productAttributeService->getAttributeUsage($attribute->id);

        return response()->json(['usage' => $usage]);
    }
}

