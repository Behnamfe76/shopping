<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\DTOs\ProductTagDTO;
use Fereydooni\Shopping\app\Facades\ProductTag as ProductTagFacade;
use Fereydooni\Shopping\app\Http\Requests\StoreProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\ToggleProductTagStatusRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\BulkProductTagRequest;
use Fereydooni\Shopping\app\Http\Requests\ImportProductTagRequest;

class ProductTagController extends Controller
{
    /**
     * Display a listing of product tags.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $perPage = $request->get('per_page', 15);
        $paginationType = $request->get('pagination', 'regular');
        $status = $request->get('status');
        $featured = $request->get('featured');

        $tags = match($paginationType) {
            'simple' => ProductTagFacade::simplePaginate($perPage),
            'cursor' => ProductTagFacade::cursorPaginate($perPage),
            default => ProductTagFacade::paginate($perPage),
        };

        $stats = ProductTagFacade::getTagStats();
        $activeTags = ProductTagFacade::findActive();
        $featuredTags = ProductTagFacade::findFeatured();

        return view('shopping::product-tags.index', compact('tags', 'stats', 'activeTags', 'featuredTags', 'status', 'featured'));
    }

    /**
     * Show the form for creating a new product tag.
     */
    public function create(): View
    {
        $this->authorize('create', ProductTag::class);

        $colors = ProductTagFacade::getTagColors();
        $icons = ProductTagFacade::getTagIcons();

        return view('shopping::product-tags.create', compact('colors', 'icons'));
    }

    /**
     * Store a newly created product tag in storage.
     */
    public function store(StoreProductTagRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductTag::class);

        try {
            $tagDTO = ProductTagFacade::createAndReturnDTO($request->validated());

            return redirect()
                ->route('shopping.product-tags.show', $tagDTO->slug)
                ->with('success', 'Product tag created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create product tag: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product tag.
     */
    public function show(ProductTag $tag): View
    {
        $this->authorize('view', $tag);

        $tagDTO = ProductTagFacade::findDTO($tag->id);
        $relatedTags = ProductTagFacade::getTagRelated($tag->id);
        $analytics = ProductTagFacade::getTagAnalytics($tag->id);

        return view('shopping::product-tags.show', compact('tagDTO', 'relatedTags', 'analytics'));
    }

    /**
     * Show the form for editing the specified product tag.
     */
    public function edit(ProductTag $tag): View
    {
        $this->authorize('update', $tag);

        $tagDTO = ProductTagFacade::findDTO($tag->id);
        $colors = ProductTagFacade::getTagColors();
        $icons = ProductTagFacade::getTagIcons();

        return view('shopping::product-tags.edit', compact('tagDTO', 'colors', 'icons'));
    }

    /**
     * Update the specified product tag in storage.
     */
    public function update(UpdateProductTagRequest $request, ProductTag $tag): RedirectResponse
    {
        $this->authorize('update', $tag);

        try {
            ProductTagFacade::update($tag, $request->validated());

            return redirect()
                ->route('shopping.product-tags.show', $tag->slug)
                ->with('success', 'Product tag updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product tag: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product tag from storage.
     */
    public function destroy(ProductTag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        try {
            ProductTagFacade::delete($tag);

            return redirect()
                ->route('shopping.product-tags.index')
                ->with('success', 'Product tag deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete product tag: ' . $e->getMessage());
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
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to update featured status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search product tags.
     */
    public function search(SearchProductTagRequest $request): View
    {
        $this->authorize('search', ProductTag::class);

        $query = $request->get('query');
        $tags = ProductTagFacade::search($query);

        return view('shopping::product-tags.search', compact('tags', 'query'));
    }

    /**
     * Display active product tags.
     */
    public function active(): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $tags = ProductTagFacade::findActive();

        return view('shopping::product-tags.active', compact('tags'));
    }

    /**
     * Display featured product tags.
     */
    public function featured(): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $tags = ProductTagFacade::findFeatured();

        return view('shopping::product-tags.featured', compact('tags'));
    }

    /**
     * Display popular product tags.
     */
    public function popular(Request $request): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $limit = $request->get('limit', 10);
        $tags = ProductTagFacade::findPopular($limit);

        return view('shopping::product-tags.popular', compact('tags'));
    }

    /**
     * Display recent product tags.
     */
    public function recent(Request $request): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $limit = $request->get('limit', 10);
        $tags = ProductTagFacade::findRecent($limit);

        return view('shopping::product-tags.recent', compact('tags'));
    }

    /**
     * Display product tags by color.
     */
    public function byColor(string $color): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $tags = ProductTagFacade::findByColor($color);

        return view('shopping::product-tags.by-color', compact('tags', 'color'));
    }

    /**
     * Display product tags by icon.
     */
    public function byIcon(string $icon): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $tags = ProductTagFacade::findByIcon($icon);

        return view('shopping::product-tags.by-icon', compact('tags', 'icon'));
    }

    /**
     * Display product tags by usage count.
     */
    public function byUsage(int $count): View
    {
        $this->authorize('viewAny', ProductTag::class);

        $tags = ProductTagFacade::findByUsageCount($count);

        return view('shopping::product-tags.by-usage', compact('tags', 'count'));
    }

    /**
     * Get tag names.
     */
    public function getNames(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $names = ProductTagFacade::getTagNames();

        return response()->json($names);
    }

    /**
     * Get tag slugs.
     */
    public function getSlugs(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $slugs = ProductTagFacade::getTagSlugs();

        return response()->json($slugs);
    }

    /**
     * Get tag colors.
     */
    public function getColors(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $colors = ProductTagFacade::getTagColors();

        return response()->json($colors);
    }

    /**
     * Get tag icons.
     */
    public function getIcons(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $icons = ProductTagFacade::getTagIcons();

        return response()->json($icons);
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
                'count' => $tags->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to update product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to delete product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to import product tags: ' . $e->getMessage()
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
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to sync product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to merge product tags: ' . $e->getMessage()
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
                'success' => false,
                'message' => 'Failed to split product tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tag suggestions.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $this->authorize('search', ProductTag::class);

        $query = $request->get('query', '');
        $suggestions = ProductTagFacade::getTagSuggestions($query);

        return response()->json($suggestions);
    }

    /**
     * Get tag autocomplete.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $this->authorize('search', ProductTag::class);

        $query = $request->get('query', '');
        $autocomplete = ProductTagFacade::getTagAutocomplete($query);

        return response()->json($autocomplete);
    }

    /**
     * Get related tags.
     */
    public function related(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        $relatedTags = ProductTagFacade::getTagRelated($tag->id);

        return response()->json($relatedTags);
    }

    /**
     * Get tag synonyms.
     */
    public function synonyms(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        $synonyms = ProductTagFacade::getTagSynonyms($tag->id);

        return response()->json($synonyms);
    }

    /**
     * Get tag hierarchy.
     */
    public function hierarchy(ProductTag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        $hierarchy = ProductTagFacade::getTagHierarchy($tag->id);

        return response()->json($hierarchy);
    }

    /**
     * Get tag tree.
     */
    public function tree(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $tree = ProductTagFacade::getTagTree();

        return response()->json($tree);
    }

    /**
     * Get tag cloud.
     */
    public function cloud(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $cloud = ProductTagFacade::getTagCloud();

        return response()->json($cloud);
    }

    /**
     * Get tag stats.
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', ProductTag::class);

        $stats = ProductTagFacade::getTagStats();

        return response()->json($stats);
    }

    /**
     * Get tag analytics.
     */
    public function analytics(ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $analytics = ProductTagFacade::getTagAnalytics($tag->id);

        return response()->json($analytics);
    }

    /**
     * Get tag trends.
     */
    public function trends(Request $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $period = $request->get('period', 'month');
        $trends = ProductTagFacade::getTagTrends($tag->id, $period);

        return response()->json($trends);
    }

    /**
     * Get tag comparison.
     */
    public function comparison(int $tagId1, int $tagId2): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $comparison = ProductTagFacade::getTagComparison($tagId1, $tagId2);

        return response()->json($comparison);
    }

    /**
     * Get tag recommendations.
     */
    public function recommendations(int $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $recommendations = ProductTagFacade::getTagRecommendations($productId);

        return response()->json($recommendations);
    }

    /**
     * Get tag forecast.
     */
    public function forecast(Request $request, ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $period = $request->get('period', 'month');
        $forecast = ProductTagFacade::getTagForecast($tag->id, $period);

        return response()->json($forecast);
    }

    /**
     * Get tag performance.
     */
    public function performance(ProductTag $tag): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductTag::class);

        $performance = ProductTagFacade::getTagPerformance($tag->id);

        return response()->json($performance);
    }
}
