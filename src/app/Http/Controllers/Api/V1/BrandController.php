<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Enums\BrandStatus;
use Fereydooni\Shopping\app\Facades\Brand as BrandFacade;
use Fereydooni\Shopping\app\Http\Requests\SearchBrandRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreBrandRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleBrandStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateBrandRequest;
use Fereydooni\Shopping\app\Http\Resources\BrandCollection;
use Fereydooni\Shopping\app\Http\Resources\BrandResource;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function __construct(
        private BrandService $brandService
    ) {}

    /**
     * Display a listing of brands.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Brand::class);

        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $paginationType = $request->get('pagination', 'regular');

            $brands = match ($paginationType) {
                'simplePaginate' => BrandFacade::simplePaginate($perPage),
                'cursorPaginate' => BrandFacade::cursorPaginate($perPage),
                default => BrandFacade::paginate($perPage),
            };

            return BrandResource::collection($brands)->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve brands',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of brand statuses.
     */
    public function statuses(): JsonResponse
    {
        Gate::authorize('viewAny', Brand::class);

        try {
            return response()->json([
                'data' => array_map(fn ($status) => [
                    'id' => $status->value,
                    'name' => __('brands.statuses.'.$status->value),
                ], BrandStatus::cases()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve brand statuses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a all of categories.
     */
    public function cursorAll(): JsonResponse
    {
        Gate::authorize('viewAny', Brand::class);

        try {
            return response()->json(
                BrandFacade::cursorAll(),
                200
            );

            // return (new CategoryCollection($categories))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        Gate::authorize('create', Brand::class);

        try {
            $brand = $this->brandService->create($request->validated());

            return (new BrandResource($brand))->response()->setStatusCode(201);
        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Failed to create brand',
                'message' => 'server error',
            ], 500);
        }
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): JsonResponse
    {
        Gate::authorize('view', $brand);

        try {
            return (new BrandResource($brand))->response();
        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Failed to find the brand',
                'message' => 'server error',
            ], 500);
        }
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        Gate::authorize('update', $brand);

        try {
            BrandFacade::update($brand, $request->validated());

            return (new BrandResource($brand))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        Gate::authorize('delete', $brand);

        try {
            BrandFacade::delete($brand);

            return response()->json([
                'message' => 'Brand deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete brand',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove all product tags from storage.
     */
    public function destroyAll(): JsonResponse
    {

        Gate::authorize('deleteAll', Brand::class);

        try {
            BrandFacade::deleteAll();

            return response()->json([
                'message' => 'All brands deleted successfully',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to delete all brands',
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
            'ids.*' => 'required|integer|exists:brands,id',
        ]);
        $ids = $request->input('ids');

        Gate::authorize('deleteSome', Brand::class);

        try {
            BrandFacade::deleteSome($ids);

            return response()->json([
                'message' => 'Selected brands deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete selected brands',
                'message' => $e->getMessage(),
            ], 500);
        }
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
                'brand' => new BrandResource($brand->fresh()),
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
                'brand' => new BrandResource($brand->fresh()),
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
            'query' => $query,
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
            'brands' => new BrandCollection($brands),
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
            'brands' => new BrandCollection($brands),
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
            'brands' => new BrandCollection($brands),
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
            'letter' => $letter,
        ]);
    }
}
