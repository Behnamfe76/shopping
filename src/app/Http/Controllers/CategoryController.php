<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
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

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $perPage = $request->get('per_page', 15);
        $paginationType = $request->get('pagination', 'regular');
        $status = $request->get('status');
        $parentId = $request->get('parent_id');

        $categories = match($paginationType) {
            'simple' => CategoryFacade::simplePaginate($perPage),
            'cursor' => CategoryFacade::cursorPaginate($perPage),
            default => CategoryFacade::paginate($perPage),
        };

        $stats = CategoryFacade::getCategoryStats();
        $rootCategories = CategoryFacade::getRootCategories();

        return view('shopping::categories.index', compact('categories', 'stats', 'rootCategories', 'status', 'parentId'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        $categories = CategoryFacade::getRootCategories();
        $statuses = CategoryStatus::cases();

        return view('shopping::categories.create', compact('categories', 'statuses'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        try {
            $categoryDTO = CategoryFacade::createDTO($request->validated());

            return redirect()
                ->route('shopping.categories.show', $categoryDTO->slug)
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $this->authorize('view', $category);

        $categoryDTO = CategoryFacade::findDTO($category->id);
        $children = CategoryFacade::getChildren($category->id);
        $ancestors = CategoryFacade::getAncestors($category->id);
        $descendants = CategoryFacade::getDescendants($category->id);

        return view('shopping::categories.show', compact('categoryDTO', 'children', 'ancestors', 'descendants'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        $categoryDTO = CategoryFacade::findDTO($category->id);
        $categories = CategoryFacade::getRootCategories();
        $statuses = CategoryStatus::cases();

        return view('shopping::categories.edit', compact('categoryDTO', 'categories', 'statuses'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        try {
            $categoryDTO = CategoryFacade::updateDTO($category, $request->validated());

            if (!$categoryDTO) {
                throw new \Exception('Failed to update category.');
            }

            return redirect()
                ->route('shopping.categories.show', $categoryDTO->slug)
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        try {
            $deleted = CategoryFacade::delete($category);

            if (!$deleted) {
                throw new \Exception('Failed to delete category.');
            }

            return redirect()
                ->route('shopping.categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Set the category as default.
     */
    public function setDefault(SetDefaultCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('setDefault', $category);

        try {
            $categoryDTO = CategoryFacade::setDefaultDTO($category);

            if (!$categoryDTO) {
                throw new \Exception('Failed to set category as default.');
            }

            return back()->with('success', 'Category set as default successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to set category as default: ' . $e->getMessage());
        }
    }

    /**
     * Search categories.
     */
    public function search(SearchCategoryRequest $request): View|JsonResponse
    {
        $this->authorize('search', Category::class);

        $query = $request->get('query');
        $status = $request->get('status');
        $parentId = $request->get('parent_id');
        $perPage = $request->get('per_page', 15);

        try {
            if ($request->expectsJson()) {
                $categories = CategoryFacade::searchWithPagination($query, $perPage, null, $status ? CategoryStatus::from($status) : null);
                return response()->json($categories);
            }

            $categories = CategoryFacade::searchWithPagination($query, $perPage, null, $status ? CategoryStatus::from($status) : null);
            $stats = CategoryFacade::getCategoryStats();

            return view('shopping::categories.search', compact('categories', 'stats', 'query', 'status', 'parentId'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get category tree.
     */
    public function tree(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        try {
            $tree = CategoryFacade::getTreeDTO();

            if ($request->expectsJson()) {
                return response()->json($tree);
            }

            return view('shopping::categories.tree', compact('tree'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load category tree: ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(ReorderCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('reorder', Category::class);

        try {
            $reordered = CategoryFacade::reorderCategories($request->validated());

            if (!$reordered) {
                throw new \Exception('Failed to reorder categories.');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Categories reordered successfully']);
            }

            return back()->with('success', 'Categories reordered successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to reorder categories: ' . $e->getMessage());
        }
    }

    /**
     * Move category to new parent.
     */
    public function move(MoveCategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $this->authorize('move', $category);

        try {
            $moved = CategoryFacade::moveCategory($category, $request->get('parent_id'));

            if (!$moved) {
                throw new \Exception('Failed to move category.');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Category moved successfully']);
            }

            return back()->with('success', 'Category moved successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to move category: ' . $e->getMessage());
        }
    }

    /**
     * Get category children.
     */
    public function children(Category $category, Request $request): View|JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $children = CategoryFacade::getChildrenDTO($category->id);

            if ($request->expectsJson()) {
                return response()->json($children);
            }

            return view('shopping::categories.children', compact('category', 'children'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load category children: ' . $e->getMessage());
        }
    }

    /**
     * Get category ancestors.
     */
    public function ancestors(Category $category, Request $request): View|JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $ancestors = CategoryFacade::getAncestorsDTO($category->id);

            if ($request->expectsJson()) {
                return response()->json($ancestors);
            }

            return view('shopping::categories.ancestors', compact('category', 'ancestors'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load category ancestors: ' . $e->getMessage());
        }
    }

    /**
     * Get category descendants.
     */
    public function descendants(Category $category, Request $request): View|JsonResponse
    {
        $this->authorize('view', $category);

        try {
            $descendants = CategoryFacade::getDescendantsDTO($category->id);

            if ($request->expectsJson()) {
                return response()->json($descendants);
            }

            return view('shopping::categories.descendants', compact('category', 'descendants'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load category descendants: ' . $e->getMessage());
        }
    }

    /**
     * Get category statistics.
     */
    public function stats(Request $request): View|JsonResponse
    {
        $this->authorize('viewStats', Category::class);

        try {
            $stats = CategoryFacade::getCategoryStats();
            $statsByStatus = CategoryFacade::getCategoryStatsByStatus();

            if ($request->expectsJson()) {
                return response()->json([
                    'stats' => $stats,
                    'statsByStatus' => $statsByStatus
                ]);
            }

            return view('shopping::categories.stats', compact('stats', 'statsByStatus'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load category statistics: ' . $e->getMessage());
        }
    }
}
