<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Fereydooni\Shopping\app\Services\ProductDiscountService;
use Fereydooni\Shopping\app\Http\Requests\StoreProductDiscountRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductDiscountRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductDiscountStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\ExtendProductDiscountRequest;
use Fereydooni\Shopping\app\Http\Requests\ShortenProductDiscountRequest;
use Fereydooni\Shopping\app\Http\Requests\CalculateProductDiscountRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductDiscountRequest;

class ProductDiscountController extends Controller
{
    public function __construct(
        private ProductDiscountService $service
    ) {}

    /**
     * Display a listing of product discounts.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->paginate($request->get('per_page', 15));

        return view('shopping::product-discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new product discount.
     */
    public function create(): View
    {
        $this->authorize('create', ProductDiscount::class);

        return view('shopping::product-discounts.create');
    }

    /**
     * Store a newly created product discount.
     */
    public function store(StoreProductDiscountRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductDiscount::class);

        $discount = $this->service->create($request->validated());

        return redirect()
            ->route('shopping.product-discounts.show', $discount)
            ->with('success', 'Product discount created successfully.');
    }

    /**
     * Display the specified product discount.
     */
    public function show(ProductDiscount $discount): View
    {
        $this->authorize('view', $discount);

        return view('shopping::product-discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified product discount.
     */
    public function edit(ProductDiscount $discount): View
    {
        $this->authorize('update', $discount);

        return view('shopping::product-discounts.edit', compact('discount'));
    }

    /**
     * Update the specified product discount.
     */
    public function update(UpdateProductDiscountRequest $request, ProductDiscount $discount): RedirectResponse
    {
        $this->authorize('update', $discount);

        $this->service->update($discount, $request->validated());

        return redirect()
            ->route('shopping.product-discounts.show', $discount)
            ->with('success', 'Product discount updated successfully.');
    }

    /**
     * Remove the specified product discount.
     */
    public function destroy(ProductDiscount $discount): RedirectResponse
    {
        $this->authorize('delete', $discount);

        $this->service->delete($discount);

        return redirect()
            ->route('shopping.product-discounts.index')
            ->with('success', 'Product discount deleted successfully.');
    }

    /**
     * Toggle the active status of a product discount.
     */
    public function toggleActive(ToggleProductDiscountStatusRequest $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('toggleActive', $discount);

        $success = $this->service->toggleActive($discount);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Status toggled successfully.' : 'Failed to toggle status.',
            'is_active' => $discount->fresh()->is_active,
        ]);
    }

    /**
     * Extend the end date of a product discount.
     */
    public function extend(ExtendProductDiscountRequest $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('extend', $discount);

        $success = $this->service->extend($discount, $request->validated()['new_end_date']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Discount extended successfully.' : 'Failed to extend discount.',
        ]);
    }

    /**
     * Shorten the end date of a product discount.
     */
    public function shorten(ShortenProductDiscountRequest $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('shorten', $discount);

        $success = $this->service->shorten($discount, $request->validated()['new_end_date']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Discount shortened successfully.' : 'Failed to shorten discount.',
        ]);
    }

    /**
     * Search product discounts.
     */
    public function search(SearchProductDiscountRequest $request): View
    {
        $this->authorize('search', ProductDiscount::class);

        $query = $request->validated()['query'];
        $discounts = $this->service->search($query);

        return view('shopping::product-discounts.search', compact('discounts', 'query'));
    }

    /**
     * Display active product discounts.
     */
    public function active(): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findActive();

        return view('shopping::product-discounts.active', compact('discounts'));
    }

    /**
     * Display expired product discounts.
     */
    public function expired(): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findExpired();

        return view('shopping::product-discounts.expired', compact('discounts'));
    }

    /**
     * Display upcoming product discounts.
     */
    public function upcoming(): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findUpcoming();

        return view('shopping::product-discounts.upcoming', compact('discounts'));
    }

    /**
     * Display current product discounts.
     */
    public function current(): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findCurrent();

        return view('shopping::product-discounts.current', compact('discounts'));
    }

    /**
     * Display discounts by product.
     */
    public function byProduct(int $productId): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findByProductId($productId);

        return view('shopping::product-discounts.by-product', compact('discounts', 'productId'));
    }

    /**
     * Display discounts by type.
     */
    public function byType(string $type): View
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $discounts = $this->service->findByDiscountType($type);

        return view('shopping::product-discounts.by-type', compact('discounts', 'type'));
    }

    /**
     * Calculate discount for a product.
     */
    public function calculate(CalculateProductDiscountRequest $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('calculate', $discount);

        $data = $request->validated();
        $discountAmount = $this->service->calculateDiscount(
            $discount,
            $data['original_price'],
            $data['quantity'] ?? 1
        );

        return response()->json([
            'discount_amount' => $discountAmount,
            'final_price' => ($data['original_price'] * ($data['quantity'] ?? 1)) - $discountAmount,
        ]);
    }

    /**
     * Apply discount to a product.
     */
    public function apply(Request $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('apply', $discount);

        $data = $request->validate([
            'original_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:1',
        ]);

        $result = $this->service->applyDiscountToProduct(
            $discount->product_id,
            $data['original_price'],
            $data['quantity'] ?? 1
        );

        return response()->json($result);
    }

    /**
     * Validate discount for a product.
     */
    public function validate(Request $request, ProductDiscount $discount): JsonResponse
    {
        $this->authorize('validate', $discount);

        $data = $request->validate([
            'quantity' => 'nullable|numeric|min:1',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $isValid = $this->service->validateDiscount(
            $discount,
            $data['quantity'] ?? 1,
            $data['amount'] ?? 0
        );

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Discount is valid.' : 'Discount is not valid.',
        ]);
    }

    /**
     * Get discount analytics.
     */
    public function analytics(ProductDiscount $discount): JsonResponse
    {
        $this->authorize('viewAnalytics', $discount);

        $analytics = $this->service->getDiscountAnalytics($discount->id);

        return response()->json($analytics);
    }

    /**
     * Get discount performance.
     */
    public function performance(ProductDiscount $discount): JsonResponse
    {
        $this->authorize('viewAnalytics', $discount);

        $revenue = $this->service->getDiscountRevenue($discount->id);
        $savings = $this->service->getDiscountSavings($discount->id);

        return response()->json([
            'revenue' => $revenue,
            'savings' => $savings,
            'performance_score' => $revenue > 0 ? ($savings / $revenue) * 100 : 0,
        ]);
    }

    /**
     * Get discount forecast.
     */
    public function forecast(ProductDiscount $discount): JsonResponse
    {
        $this->authorize('viewForecast', ProductDiscount::class);

        // Placeholder for forecast logic
        $forecast = [
            'estimated_usage' => 0,
            'estimated_revenue' => 0,
            'estimated_savings' => 0,
        ];

        return response()->json($forecast);
    }

    /**
     * Get discount recommendations for a product.
     */
    public function recommendations(int $productId): JsonResponse
    {
        $this->authorize('viewRecommendations', ProductDiscount::class);

        $recommendations = $this->service->getDiscountRecommendations($productId);

        return response()->json($recommendations);
    }
}
