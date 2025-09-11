<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\DTOs\ProductTagDTO;
use Fereydooni\Shopping\app\Http\Resources\ProductTagResource;
use Fereydooni\Shopping\app\Http\Requests\BulkProductTagRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductTagCollection;
use Fereydooni\Shopping\app\Http\Requests\StoreProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\ImportProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductTagRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductTagBulkResource;
use Fereydooni\Shopping\app\Facades\ProductTag as ProductTagFacade;
use Fereydooni\Shopping\app\Http\Resources\ProductTagSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProductTagAnalyticsResource;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductTagStatusRequest;

class ProductTagController extends Controller
{
    /**
     * Display a listing of product tags.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ProductTag::class);

        try {
            $perPage = $request->get('per_page', 15);
            $paginationType = $request->get('pagination', 'regular');

            $tags = match($paginationType) {
                'simplePaginate' => ProductTagFacade::simplePaginate($perPage),
                'cursorPaginate' => ProductTagFacade::cursorPaginate($perPage, $request->get('id')),
                default => ProductTagFacade::paginate($perPage),
            };

            return response()->json($tags);
            // return (new ProductTagCollection($tags))->response();
        } catch (\Throwable $tr) {
            return response()->json([
                'error' => 'Failed to retrieve product tags',
                'message' => $tr->getMessage(),
            ], 500);
        }
    }


    /**
     * Getting the model lens dynamically.
     */
    public function lens(Request $request): JsonResponse
    {
        Gate::authorize('viewLenses', ProductTag::class);

        try {
            return response()->json(
                ProductTagFacade::lens()
            );
        } catch (\Throwable $tr) {
            return response()->json([
                'error' => 'Failed to retrieve product tag\'s lens data',
                'message' => $tr->getMessage(),
            ], 500);
        }
    }



    /**
     * Store a newly created product tag in storage.
     */
    public function store(StoreProductTagRequest $request): JsonResponse
    {
        $this->authorize('create', ProductTag::class);

        try {
            $tagDTO = ProductTagFacade::createAndReturnDTO($request->validated());

            return (new ProductTagResource($tagDTO))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create product tag',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product tag.
     */
    public function show(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        try {
            $tagDTO = ProductTagFacade::findDTO($tag->id);

            if (!$tagDTO) {
                return response()->json([
                    'error' => 'Product tag not found',
                ], 404);
            }

            return (new ProductTagResource($tagDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product tag',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product tag in storage.
     */
    public function update(UpdateProductTagRequest $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('update', $tag);

        try {
            $result = ProductTagFacade::update($tag, $request->validated());

            if (!$result) {
                return response()->json([
                    'error' => 'Failed to update product tag',
                ], 500);
            }

            $tagDTO = ProductTagFacade::findDTO($tag->id);
            return (new ProductTagResource($tagDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update product tag',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product tag from storage.
     */
    public function destroy(int $tag): JsonResponse
    {
        $tag = ProductTag::findOrFail($tag);
        Gate::authorize('delete', $tag);

        try {

            $result = ProductTagFacade::delete($tag);

            if (!$result) {
                return response()->json([
                    'error' => 'Failed to delete product tag',
                ], 500);
            }

            return response()->json([
                'message' => 'Product tag deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete product tag',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove all product tags from storage.
     */
    public function destroyAll(): JsonResponse
    {

        Gate::authorize('deleteAll', ProductTag::class);

        try {
            ProductTagFacade::deleteAll();

            return response()->json([
                'message' => 'All product tags deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete all product tags',
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
            'ids.*' => 'required|integer|exists:product_tags,id',
        ]);
        $ids = $request->input('ids');

        Gate::authorize('deleteSome', ProductTag::class);

        try {
            ProductTagFacade::deleteSome($ids);

            return response()->json([
                'message' => 'Selected product tags deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete selected product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the active status of the specified product tag.
     */
    public function toggleActive(ToggleProductTagStatusRequest $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('toggleActive', $tag);

        try {
            $result = ProductTagFacade::toggleActive($tag);
            $tag->refresh();

            return response()->json([
                'success' => $result,
                'is_active' => $tag->is_active,
                'message' => $result ? 'Status updated successfully.' : 'Failed to update status.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the featured status of the specified product tag.
     */
    public function toggleFeatured(ToggleProductTagStatusRequest $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('toggleFeatured', $tag);

        try {
            $result = ProductTagFacade::toggleFeatured($tag);
            $tag->refresh();

            return response()->json([
                'success' => $result,
                'is_featured' => $tag->is_featured,
                'message' => $result ? 'Featured status updated successfully.' : 'Failed to update featured status.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update featured status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search product tags.
     */
    public function search(SearchProductTagRequest $request): JsonResponse
    {
        $this->authorize('search', ProductTag::class);

        try {
            $query = $request->get('query');
            $tags = ProductTagFacade::search($query);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to search product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display active product tags.
     */
    public function active(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tags = ProductTagFacade::findActive();

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve active product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display featured product tags.
     */
    public function featured(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tags = ProductTagFacade::findFeatured();

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve featured product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display popular product tags.
     */
    public function popular(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $limit = $request->get('limit', 10);
            $tags = ProductTagFacade::findPopular($limit);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve popular product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display recent product tags.
     */
    public function recent(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $limit = $request->get('limit', 10);
            $tags = ProductTagFacade::findRecent($limit);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve recent product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display product tags by color.
     */
    public function byColor(string $color): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tags = ProductTagFacade::findByColor($color);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product tags by color',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display product tags by icon.
     */
    public function byIcon(string $icon): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tags = ProductTagFacade::findByIcon($icon);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product tags by icon',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display product tags by usage count.
     */
    public function byUsage(int $count): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tags = ProductTagFacade::findByUsageCount($count);

            return (new ProductTagCollection($tags))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve product tags by usage count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get product tag count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $count = ProductTagFacade::getTagCount();

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get product tag count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag names.
     */
    public function getNames(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $names = ProductTagFacade::getTagNames();

            return response()->json($names);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag names',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag slugs.
     */
    public function getSlugs(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $slugs = ProductTagFacade::getTagSlugs();

            return response()->json($slugs);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag slugs',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag colors.
     */
    public function getColors(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $colors = ProductTagFacade::getTagColors();

            return response()->json($colors);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag colors',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag icons.
     */
    public function getIcons(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $icons = ProductTagFacade::getTagIcons();

            return response()->json($icons);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag icons',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk create product tags.
     */
    public function bulkCreate(BulkProductTagRequest $request): JsonResponse
    {
        $this->authorize('bulkManage', ProductTag::class);

        try {
            $tags = ProductTagFacade::bulkCreate($request->validated()['tags']);

            return response()->json([
                'success' => true,
                'message' => 'Product tags created successfully.',
                'count' => $tags->count(),
                'data' => ProductTagResource::collection($tags),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update product tags.
     */
    public function bulkUpdate(BulkProductTagRequest $request): JsonResponse
    {
        $this->authorize('bulkManage', ProductTag::class);

        try {
            $result = ProductTagFacade::bulkUpdate($request->validated()['tags']);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tags updated successfully.' : 'Failed to update product tags.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete product tags.
     */
    public function bulkDelete(BulkProductTagRequest $request): JsonResponse
    {
        $this->authorize('bulkManage', ProductTag::class);

        try {
            $result = ProductTagFacade::bulkDelete($request->validated()['tag_ids']);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tags deleted successfully.' : 'Failed to delete product tags.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import product tags.
     */
    public function import(ImportProductTagRequest $request): JsonResponse
    {
        $this->authorize('import', ProductTag::class);

        try {
            $result = ProductTagFacade::importTags($request->validated()['tags']);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tags imported successfully.' : 'Failed to import product tags.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to import product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export product tags.
     */
    public function export(): JsonResponse
    {
        $this->authorize('export', ProductTag::class);

        try {
            $tags = ProductTagFacade::exportTags();

            return response()->json([
                'success' => true,
                'data' => $tags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync product tags.
     */
    public function sync(Request $request, int $productId): JsonResponse
    {
        $this->authorize('sync', ProductTag::class);

        try {
            $result = ProductTagFacade::syncTags($productId, $request->get('tag_ids', []));

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tags synced successfully.' : 'Failed to sync product tags.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to sync product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Merge product tags.
     */
    public function merge(int $tagId1, int $tagId2): JsonResponse
    {
        $this->authorize('merge', ProductTag::class);

        try {
            $result = ProductTagFacade::mergeTags($tagId1, $tagId2);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tags merged successfully.' : 'Failed to merge product tags.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to merge product tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Split product tag.
     */
    public function split(Request $request, int $tagId): JsonResponse
    {
        $this->authorize('split', ProductTag::class);

        try {
            $result = ProductTagFacade::splitTags($tagId, $request->get('new_tag_names', []));

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Product tag split successfully.' : 'Failed to split product tag.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to split product tag',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag suggestions.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $this->authorize('search', ProductTag::class);

        try {
            $query = $request->get('query', '');
            $suggestions = ProductTagFacade::getTagSuggestions($query);

            return response()->json($suggestions);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag suggestions',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag autocomplete.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $this->authorize('search', ProductTag::class);

        try {
            $query = $request->get('query', '');
            $autocomplete = ProductTagFacade::getTagAutocomplete($query);

            return response()->json($autocomplete);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag autocomplete',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get related tags.
     */
    public function related(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        try {
            $relatedTags = ProductTagFacade::getTagRelated($tag->id);

            return response()->json($relatedTags);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get related tags',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag synonyms.
     */
    public function synonyms(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        try {
            $synonyms = ProductTagFacade::getTagSynonyms($tag->id);

            return response()->json($synonyms);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag synonyms',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag hierarchy.
     */
    public function hierarchy(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        try {
            $hierarchy = ProductTagFacade::getTagHierarchy($tag->id);

            return response()->json($hierarchy);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag hierarchy',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag tree.
     */
    public function tree(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $tree = ProductTagFacade::getTagTree();

            return response()->json($tree);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag tree',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag cloud.
     */
    public function cloud(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $cloud = ProductTagFacade::getTagCloud();

            return response()->json($cloud);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag cloud',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag stats.
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        try {
            $stats = ProductTagFacade::getTagStats();

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag stats',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag analytics.
     */
    public function analytics(ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $analytics = ProductTagFacade::getTagAnalytics($tag->id);

            return response()->json($analytics);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag analytics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag trends.
     */
    public function trends(Request $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $period = $request->get('period', 'month');
            $trends = ProductTagFacade::getTagTrends($tag->id, $period);

            return response()->json($trends);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag trends',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag comparison.
     */
    public function comparison(int $tagId1, int $tagId2): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $comparison = ProductTagFacade::getTagComparison($tagId1, $tagId2);

            return response()->json($comparison);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag comparison',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag recommendations.
     */
    public function recommendations(int $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $recommendations = ProductTagFacade::getTagRecommendations($productId);

            return response()->json($recommendations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag recommendations',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag forecast.
     */
    public function forecast(Request $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $period = $request->get('period', 'month');
            $forecast = ProductTagFacade::getTagForecast($tag->id, $period);

            return response()->json($forecast);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag forecast',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag performance.
     */
    public function performance(ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        try {
            $performance = ProductTagFacade::getTagPerformance($tag->id);

            return response()->json($performance);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get tag performance',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
