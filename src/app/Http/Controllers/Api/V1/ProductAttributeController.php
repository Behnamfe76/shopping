<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeResource;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeCollection;
use Fereydooni\Shopping\app\Http\Requests\StoreProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeValueResource;
use Fereydooni\Shopping\app\Http\Requests\AddProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeSearchResource;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductAttributeAnalyticsResource;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductAttributeStatusRequest;
use Fereydooni\Shopping\app\Facades\ProductAttribute as ProductAttributeFacade;

class ProductAttributeController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of the product attributes.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ProductAttribute::class);

        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $categories = match ($paginationType) {
                'simplePaginate' => ProductAttributeFacade::simplePaginate($perPage),
                'cursorPaginate' => ProductAttributeFacade::cursorPaginate($perPage),
                default => ProductAttributeFacade::paginate($perPage),
            };

            return ProductAttributeResource::collection($categories)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

     /**
     * Display a listing of category statuses.
     */
    public function types(): JsonResponse
    {
        Gate::authorize('viewAny', ProductAttribute::class);

        try {
            return response()->json([
                'data' => array_map(fn($status) => [
                    'id' => $status->value,
                    'name' => __('product-attributes.types.' . $status->value),
                ], ProductAttributeType::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product attribute statuses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

     /**
     * Display a listing of category statuses.
     */
    public function inputTypes(): JsonResponse
    {
        Gate::authorize('viewAny', ProductAttribute::class);

        try {
            return response()->json([
                'data' => array_map(fn($status) => [
                    'id' => $status->value,
                    'name' => __('product-attributes.input-types.' . $status->value),
                ], ProductAttributeInputType::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product attribute input types',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product attribute in storage.
     */
    public function store(StoreProductAttributeRequest $request): JsonResponse
    {
        Gate::authorize('create', ProductAttribute::class);

        try {
            $category = ProductAttributeFacade::create($request->validated());

            return (new ProductAttributeResource($category))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product attribute.
     */
    public function show(ProductAttribute $productAttribute): JsonResponse
    {
        Gate::authorize('view', $productAttribute);

        try {
            return (new ProductAttributeResource($productAttribute->load('values')))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product attribute',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product attribute in storage.
     */
    public function update(UpdateProductAttributeRequest $request, ProductAttribute $productAttribute): JsonResponse
    {
        Gate::authorize('update', $productAttribute);

        try {
            ProductAttributeFacade::update($productAttribute, $request->validated());

            return (new ProductAttributeResource($productAttribute))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update product attribute',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product attribute from storage.
     */
    public function destroy(ProductAttribute $productAttribute): JsonResponse
    {
        Gate::authorize('delete', $productAttribute);

        try {
            ProductAttributeFacade::delete($productAttribute);

            return response()->json([
                'message' => 'Product attribute deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete product attribute',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove all product tags from storage.
     */
    public function destroyAll(): JsonResponse
    {

        Gate::authorize('deleteAll', ProductAttribute::class);

        try {
            ProductAttributeFacade::deleteAll();

            return response()->json([
                'message' => 'All product attributes deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete all product attributes',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a selection of product tags from storage.
     */
    public function destroySome(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:product_attributes,id',
        ]);
        $ids = $request->input('ids');

        Gate::authorize('deleteSome', ProductAttribute::class);

        try {
            ProductAttributeFacade::deleteSome($ids);

            return response()->json([
                'message' => 'Selected product attributes deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete selected product attributes',
                'message' => $e->getMessage(),
            ], 500);
        }
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
