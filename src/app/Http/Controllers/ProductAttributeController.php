<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Http\Requests\AddProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductAttributeStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeValueRequest;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Services\ProductAttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductAttributeController extends Controller
{
    public function __construct(
        private ProductAttributeService $productAttributeService
    ) {}

    /**
     * Display a listing of the product attributes.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->paginate($request->get('per_page', 15));

        return view('shopping::product-attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new product attribute.
     */
    public function create(): View
    {
        $this->authorize('create', ProductAttribute::class);

        $attributeTypes = $this->productAttributeService->getAttributeTypes();
        $inputTypes = $this->productAttributeService->getInputTypes();
        $groups = $this->productAttributeService->getAttributeGroups();

        return view('shopping::product-attributes.create', compact('attributeTypes', 'inputTypes', 'groups'));
    }

    /**
     * Store a newly created product attribute in storage.
     */
    public function store(StoreProductAttributeRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductAttribute::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $attribute = $this->productAttributeService->create($data);

        return redirect()->route('shopping.product-attributes.show', $attribute->slug)
            ->with('success', 'Product attribute created successfully.');
    }

    /**
     * Display the specified product attribute.
     */
    public function show(ProductAttribute $attribute): View
    {
        $this->authorize('view', $attribute);

        $values = $this->productAttributeService->getAttributeValues($attribute->id);
        $analytics = $this->productAttributeService->getAttributeAnalytics($attribute->id);

        return view('shopping::product-attributes.show', compact('attribute', 'values', 'analytics'));
    }

    /**
     * Show the form for editing the specified product attribute.
     */
    public function edit(ProductAttribute $attribute): View
    {
        $this->authorize('update', $attribute);

        $attributeTypes = $this->productAttributeService->getAttributeTypes();
        $inputTypes = $this->productAttributeService->getInputTypes();
        $groups = $this->productAttributeService->getAttributeGroups();

        return view('shopping::product-attributes.edit', compact('attribute', 'attributeTypes', 'inputTypes', 'groups'));
    }

    /**
     * Update the specified product attribute in storage.
     */
    public function update(UpdateProductAttributeRequest $request, ProductAttribute $attribute): RedirectResponse
    {
        $this->authorize('update', $attribute);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->productAttributeService->update($attribute, $data);

        return redirect()->route('shopping.product-attributes.show', $attribute->slug)
            ->with('success', 'Product attribute updated successfully.');
    }

    /**
     * Remove the specified product attribute from storage.
     */
    public function destroy(ProductAttribute $attribute): RedirectResponse
    {
        $this->authorize('delete', $attribute);

        $this->productAttributeService->delete($attribute);

        return redirect()->route('shopping.product-attributes.index')
            ->with('success', 'Product attribute deleted successfully.');
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
    public function search(SearchProductAttributeRequest $request): View
    {
        $this->authorize('search', ProductAttribute::class);

        $query = $request->validated()['query'];
        $attributes = $this->productAttributeService->search($query);

        return view('shopping::product-attributes.search', compact('attributes', 'query'));
    }

    /**
     * Display required product attributes.
     */
    public function required(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findRequired();

        return view('shopping::product-attributes.required', compact('attributes'));
    }

    /**
     * Display searchable product attributes.
     */
    public function searchable(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findSearchable();

        return view('shopping::product-attributes.searchable', compact('attributes'));
    }

    /**
     * Display filterable product attributes.
     */
    public function filterable(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findFilterable();

        return view('shopping::product-attributes.filterable', compact('attributes'));
    }

    /**
     * Display comparable product attributes.
     */
    public function comparable(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findComparable();

        return view('shopping::product-attributes.comparable', compact('attributes'));
    }

    /**
     * Display visible product attributes.
     */
    public function visible(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findVisible();

        return view('shopping::product-attributes.visible', compact('attributes'));
    }

    /**
     * Display system product attributes.
     */
    public function system(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findSystem();

        return view('shopping::product-attributes.system', compact('attributes'));
    }

    /**
     * Display custom product attributes.
     */
    public function custom(): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findCustom();

        return view('shopping::product-attributes.custom', compact('attributes'));
    }

    /**
     * Display product attributes by type.
     */
    public function byType(string $type): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByType($type);

        return view('shopping::product-attributes.by-type', compact('attributes', 'type'));
    }

    /**
     * Display product attributes by group.
     */
    public function byGroup(string $group): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByGroup($group);

        return view('shopping::product-attributes.by-group', compact('attributes', 'group'));
    }

    /**
     * Display product attributes by input type.
     */
    public function byInputType(string $inputType): View
    {
        $this->authorize('viewAny', ProductAttribute::class);

        $attributes = $this->productAttributeService->findByInputType($inputType);

        return view('shopping::product-attributes.by-input-type', compact('attributes', 'inputType'));
    }

    /**
     * Get attribute values.
     */
    public function getValues(ProductAttribute $attribute): View
    {
        $this->authorize('viewValues', $attribute);

        $values = $this->productAttributeService->getAttributeValues($attribute->id);

        return view('shopping::product-attributes.values', compact('attribute', 'values'));
    }

    /**
     * Add attribute value.
     */
    public function addValue(AddProductAttributeValueRequest $request, ProductAttribute $attribute): JsonResponse
    {
        $this->authorize('manageValues', $attribute);

        $data = $request->validated();
        $value = $this->productAttributeService->addAttributeValue($attribute->id, $data['value'], $data['metadata'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Value added successfully.',
            'value' => $value,
        ]);
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
}
