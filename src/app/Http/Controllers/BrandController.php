<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\Services\BrandService;
use Fereydooni\Shopping\app\Http\Requests\StoreBrandRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateBrandRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleBrandStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchBrandRequest;
use Fereydooni\Shopping\app\Http\Resources\BrandResource;
use Fereydooni\Shopping\app\Http\Resources\BrandCollection;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
        $this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of brands.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Brand::class);

        $perPage = $request->get('per_page', 15);
        $brands = $this->brandService->paginate($perPage);

        return view('shopping::brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(): View
    {
        $this->authorize('create', Brand::class);

        return view('shopping::brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $this->authorize('create', Brand::class);

        $brand = $this->brandService->create($request->validated());

        return response()->json([
            'message' => 'Brand created successfully',
            'brand' => new BrandResource($brand)
        ], 201);
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): View
    {
        $this->authorize('view', $brand);

        return view('shopping::brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand): View
    {
        $this->authorize('update', $brand);

        return view('shopping::brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $this->authorize('update', $brand);

        $updated = $this->brandService->update($brand, $request->validated());

        if ($updated) {
            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => new BrandResource($brand->fresh())
            ]);
        }

        return response()->json(['message' => 'Failed to update brand'], 500);
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $this->authorize('delete', $brand);

        $deleted = $this->brandService->delete($brand);

        if ($deleted) {
            return response()->json(['message' => 'Brand deleted successfully']);
        }

        return response()->json(['message' => 'Failed to delete brand'], 500);
    }

    /**
     * Toggle the brand's active status.
     */
    public function toggleActive(ToggleBrandStatusRequest $request, Brand $brand): JsonResponse
    {
        $this->authorize('toggleActive', $brand);

        $toggled = $this->brandService->toggleActive($brand);

        if ($toggled) {
            return response()->json([
                'message' => 'Brand active status toggled successfully',
                'brand' => new BrandResource($brand->fresh())
            ]);
        }

        return response()->json(['message' => 'Failed to toggle brand active status'], 500);
    }

    /**
     * Toggle the brand's featured status.
     */
    public function toggleFeatured(ToggleBrandStatusRequest $request, Brand $brand): JsonResponse
    {
        $this->authorize('toggleFeatured', $brand);

        $toggled = $this->brandService->toggleFeatured($brand);

        if ($toggled) {
            return response()->json([
                'message' => 'Brand featured status toggled successfully',
                'brand' => new BrandResource($brand->fresh())
            ]);
        }

        return response()->json(['message' => 'Failed to toggle brand featured status'], 500);
    }

    /**
     * Search brands.
     */
    public function search(SearchBrandRequest $request): JsonResponse
    {
        $this->authorize('search', Brand::class);

        $query = $request->get('query');
        $brands = $this->brandService->search($query);

        return response()->json([
            'brands' => new BrandCollection($brands),
            'query' => $query
        ]);
    }

    /**
     * Display active brands.
     */
    public function active(): JsonResponse
    {
        $this->authorize('viewAny', Brand::class);

        $brands = $this->brandService->getActive();

        return response()->json([
            'brands' => new BrandCollection($brands)
        ]);
    }

    /**
     * Display featured brands.
     */
    public function featured(): JsonResponse
    {
        $this->authorize('viewAny', Brand::class);

        $brands = $this->brandService->getFeatured();

        return response()->json([
            'brands' => new BrandCollection($brands)
        ]);
    }

    /**
     * Display popular brands.
     */
    public function popular(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Brand::class);

        $limit = $request->get('limit', 10);
        $brands = $this->brandService->getPopular($limit);

        return response()->json([
            'brands' => new BrandCollection($brands)
        ]);
    }

    /**
     * Display brands by first letter.
     */
    public function alphabetical(string $letter): JsonResponse
    {
        $this->authorize('viewAny', Brand::class);

        $brands = $this->brandService->getByFirstLetter($letter);

        return response()->json([
            'brands' => new BrandCollection($brands),
            'letter' => $letter
        ]);
    }
}
