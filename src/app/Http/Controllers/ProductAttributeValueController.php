<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Http\Requests\AssignProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProductAttributeValueRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductAttributeValueStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductAttributeValueRequest;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\Services\ProductAttributeValueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductAttributeValueController extends Controller
{
    public function __construct(
        private ProductAttributeValueService $service
    ) {}

    /**
     * Display a listing of the product attribute values.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $perPage = $request->get('per_page', 15);
        $values = $this->service->paginate($perPage);

        return view('shopping::product-attribute-values.index', compact('values'));
    }

    /**
     * Show the form for creating a new product attribute value.
     */
    public function create(): View
    {
        $this->authorize('create', ProductAttributeValue::class);

        return view('shopping::product-attribute-values.create');
    }

    /**
     * Store a newly created product attribute value in storage.
     */
    public function store(StoreProductAttributeValueRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductAttributeValue::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $value = $this->service->create($data);

        return redirect()
            ->route('shopping.product-attribute-values.show', $value)
            ->with('success', 'Product attribute value created successfully.');
    }

    /**
     * Display the specified product attribute value.
     */
    public function show(ProductAttributeValue $value): View
    {
        $this->authorize('view', $value);

        $value->load('attribute', 'variants', 'creator', 'updater');

        return view('shopping::product-attribute-values.show', compact('value'));
    }

    /**
     * Show the form for editing the specified product attribute value.
     */
    public function edit(ProductAttributeValue $value): View
    {
        $this->authorize('update', $value);

        $value->load('attribute');

        return view('shopping::product-attribute-values.edit', compact('value'));
    }

    /**
     * Update the specified product attribute value in storage.
     */
    public function update(UpdateProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('update', $value);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->service->update($value, $data);

        return redirect()
            ->route('shopping.product-attribute-values.show', $value)
            ->with('success', 'Product attribute value updated successfully.');
    }

    /**
     * Remove the specified product attribute value from storage.
     */
    public function destroy(ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('delete', $value);

        $this->service->delete($value);

        return redirect()
            ->route('shopping.product-attribute-values.index')
            ->with('success', 'Product attribute value deleted successfully.');
    }

    /**
     * Toggle the active status of the product attribute value.
     */
    public function toggleActive(ToggleProductAttributeValueStatusRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('toggleActive', $value);

        $this->service->toggleActive($value);

        $status = $value->fresh()->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Product attribute value {$status} successfully.");
    }

    /**
     * Toggle the default status of the product attribute value.
     */
    public function toggleDefault(ToggleProductAttributeValueStatusRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('toggleDefault', $value);

        $this->service->toggleDefault($value);

        $status = $value->fresh()->is_default ? 'set as default' : 'removed as default';

        return redirect()
            ->back()
            ->with('success', "Product attribute value {$status} successfully.");
    }

    /**
     * Set the product attribute value as default.
     */
    public function setDefault(ToggleProductAttributeValueStatusRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('setDefault', $value);

        $this->service->setDefault($value);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value set as default successfully.');
    }

    /**
     * Search product attribute values.
     */
    public function search(SearchProductAttributeValueRequest $request): View
    {
        $this->authorize('search', ProductAttributeValue::class);

        $query = $request->get('query');
        $values = $this->service->search($query);

        return view('shopping::product-attribute-values.search', compact('values', 'query'));
    }

    /**
     * Display active product attribute values.
     */
    public function active(): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findActive();

        return view('shopping::product-attribute-values.active', compact('values'));
    }

    /**
     * Display default product attribute values.
     */
    public function default(): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findDefault();

        return view('shopping::product-attribute-values.default', compact('values'));
    }

    /**
     * Display most used product attribute values.
     */
    public function mostUsed(Request $request): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $limit = $request->get('limit', 10);
        $values = $this->service->getMostUsedValues($limit);

        return view('shopping::product-attribute-values.most-used', compact('values'));
    }

    /**
     * Display least used product attribute values.
     */
    public function leastUsed(Request $request): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $limit = $request->get('limit', 10);
        $values = $this->service->getLeastUsedValues($limit);

        return view('shopping::product-attribute-values.least-used', compact('values'));
    }

    /**
     * Display unused product attribute values.
     */
    public function unused(): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->getUnusedValues();

        return view('shopping::product-attribute-values.unused', compact('values'));
    }

    /**
     * Display product attribute values by attribute.
     */
    public function byAttribute(int $attributeId): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findByAttributeId($attributeId);

        return view('shopping::product-attribute-values.by-attribute', compact('values', 'attributeId'));
    }

    /**
     * Display product attribute values by variant.
     */
    public function byVariant(int $variantId): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findByVariantId($variantId);

        return view('shopping::product-attribute-values.by-variant', compact('values', 'variantId'));
    }

    /**
     * Display product attribute values by product.
     */
    public function byProduct(int $productId): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findByProductId($productId);

        return view('shopping::product-attribute-values.by-product', compact('values', 'productId'));
    }

    /**
     * Display product attribute values by category.
     */
    public function byCategory(int $categoryId): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findByCategoryId($categoryId);

        return view('shopping::product-attribute-values.by-category', compact('values', 'categoryId'));
    }

    /**
     * Display product attribute values by brand.
     */
    public function byBrand(int $brandId): View
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        $values = $this->service->findByBrandId($brandId);

        return view('shopping::product-attribute-values.by-brand', compact('values', 'brandId'));
    }

    /**
     * Assign product attribute value to variant.
     */
    public function assignToVariant(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $variantId = $request->validated()['variant_id'];

        $this->service->assignToVariant($value->id, $variantId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value assigned to variant successfully.');
    }

    /**
     * Remove product attribute value from variant.
     */
    public function removeFromVariant(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $variantId = $request->validated()['variant_id'];

        $this->service->removeFromVariant($value->id, $variantId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value removed from variant successfully.');
    }

    /**
     * Assign product attribute value to product.
     */
    public function assignToProduct(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $productId = $request->validated()['product_id'];

        $this->service->assignToProduct($value->id, $productId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value assigned to product successfully.');
    }

    /**
     * Remove product attribute value from product.
     */
    public function removeFromProduct(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $productId = $request->validated()['product_id'];

        $this->service->removeFromProduct($value->id, $productId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value removed from product successfully.');
    }

    /**
     * Assign product attribute value to category.
     */
    public function assignToCategory(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $categoryId = $request->validated()['category_id'];

        $this->service->assignToCategory($value->id, $categoryId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value assigned to category successfully.');
    }

    /**
     * Remove product attribute value from category.
     */
    public function removeFromCategory(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $categoryId = $request->validated()['category_id'];

        $this->service->removeFromCategory($value->id, $categoryId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value removed from category successfully.');
    }

    /**
     * Assign product attribute value to brand.
     */
    public function assignToBrand(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $brandId = $request->validated()['brand_id'];

        $this->service->assignToBrand($value->id, $brandId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value assigned to brand successfully.');
    }

    /**
     * Remove product attribute value from brand.
     */
    public function removeFromBrand(AssignProductAttributeValueRequest $request, ProductAttributeValue $value): RedirectResponse
    {
        $this->authorize('manageRelationships', ProductAttributeValue::class);

        $brandId = $request->validated()['brand_id'];

        $this->service->removeFromBrand($value->id, $brandId);

        return redirect()
            ->back()
            ->with('success', 'Product attribute value removed from brand successfully.');
    }
}
