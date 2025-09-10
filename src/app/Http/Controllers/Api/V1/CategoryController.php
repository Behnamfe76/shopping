<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Fereydooni\Shopping\app\Enums\CategoryStatus;
use Fereydooni\Shopping\app\Facades\Category as CategoryFacade;
use Fereydooni\Shopping\app\Http\Requests\StoreCategoryRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateCategoryRequest;
use Fereydooni\Shopping\app\Http\Requests\SetDefaultCategoryRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchCategoryRequest;
use Fereydooni\Shopping\app\Http\Requests\ReorderCategoryRequest;
use Fereydooni\Shopping\app\Http\Requests\MoveCategoryRequest;
use Fereydooni\Shopping\app\Http\Resources\CategoryResource;
use Fereydooni\Shopping\app\Http\Resources\CategoryCollection;
use Fereydooni\Shopping\app\Http\Resources\CategorySearchResource;
use Fereydooni\Shopping\app\Http\Resources\CategoryTreeResource;

class CategoryController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        try {
            $perPage = $request->get('per_page', 15);
            $paginationType = $request->get('pagination', 'regular');
            $status = $request->get('status');
            $parentId = $request->get('parent_id');

            $categories = match($paginationType) {
                'simplePaginate' => CategoryFacade::simplePaginate($perPage),
                'cursorPaginate' => CategoryFacade::cursorPaginate($perPage),
                default => CategoryFacade::paginate($perPage),
            };

            return (new CategoryCollection($categories))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        try {
            $categoryDTO = CategoryFacade::createDTO($request->validated());

            return (new CategoryResource($categoryDTO))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $categoryDTO = CategoryFacade::findDTO($category->id);

            if (!$categoryDTO) {
                return response()->json([
                    'error' => 'Category not found',
                ], 404);
            }

            return (new CategoryResource($categoryDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        try {
            $categoryDTO = CategoryFacade::updateDTO($category, $request->validated());

            if (!$categoryDTO) {
                return response()->json([
                    'error' => 'Failed to update category',
                ], 500);
            }

            return (new CategoryResource($categoryDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        try {
            $deleted = CategoryFacade::delete($category);

            if (!$deleted) {
                return response()->json([
                    'error' => 'Failed to delete category',
                ], 500);
            }

            return response()->json([
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set the category as default.
     */
    public function setDefault(SetDefaultCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('setDefault', $category);

        try {
            $categoryDTO = CategoryFacade::setDefaultDTO($category);

            if (!$categoryDTO) {
                return response()->json([
                    'error' => 'Failed to set category as default',
                ], 500);
            }

            return (new CategoryResource($categoryDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to set category as default',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search categories.
     */
    public function search(SearchCategoryRequest $request): JsonResponse
    {
        $this->authorize('search', Category::class);

        try {
            $query = $request->get('query');
            $status = $request->get('status');
            $parentId = $request->get('parent_id');
            $perPage = $request->get('per_page', 15);

            $categories = CategoryFacade::searchWithPagination($query, $perPage, null, $status ? CategoryStatus::from($status) : null);

            return (new CategorySearchResource($categories))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category tree.
     */
    public function tree(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        try {
            $tree = CategoryFacade::getTreeDTO();

            return (new CategoryTreeResource($tree))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load category tree',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category count.
     */
    public function getCount(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        try {
            $count = CategoryFacade::getCategoryCount();

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get category count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get root categories.
     */
    public function getRoot(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        try {
            $rootCategories = CategoryFacade::getRootCategoriesDTO();

            return (new CategoryCollection($rootCategories))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get root categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category children.
     */
    public function getChildren(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $children = CategoryFacade::getChildrenDTO($category->id);

            return (new CategoryCollection($children))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get category children',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category ancestors.
     */
    public function getAncestors(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $ancestors = CategoryFacade::getAncestorsDTO($category->id);

            return (new CategoryCollection($ancestors))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get category ancestors',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category descendants.
     */
    public function getDescendants(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $descendants = CategoryFacade::getDescendantsDTO($category->id);

            return (new CategoryCollection($descendants))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get category descendants',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(ReorderCategoryRequest $request): JsonResponse
    {
        $this->authorize('reorder', Category::class);

        try {
            $reordered = CategoryFacade::reorderCategories($request->validated());

            if (!$reordered) {
                return response()->json([
                    'error' => 'Failed to reorder categories',
                ], 500);
            }

            return response()->json([
                'message' => 'Categories reordered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reorder categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move category to new parent.
     */
    public function move(MoveCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('move', $category);

        try {
            $moved = CategoryFacade::moveCategory($category, $request->get('parent_id'));

            if (!$moved) {
                return response()->json([
                    'error' => 'Failed to move category',
                ], 500);
            }

            return response()->json([
                'message' => 'Category moved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to move category',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        $this->authorize('viewStats', Category::class);

        try {
            $stats = CategoryFacade::getCategoryStats();
            $statsByStatus = CategoryFacade::getCategoryStatsByStatus();

            return response()->json([
                'stats' => $stats,
                'statsByStatus' => $statsByStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get category statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
