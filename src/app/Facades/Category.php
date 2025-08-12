<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Models\Category as CategoryModel;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static CategoryModel|null find(int $id)
 * @method static CategoryModel|null findBySlug(string $slug)
 * @method static CategoryDTO|null findDTO(int $id)
 * @method static CategoryDTO|null findBySlugDTO(string $slug)
 * @method static CategoryModel create(array $data)
 * @method static CategoryDTO createDTO(array $data)
 * @method static bool update(CategoryModel $category, array $data)
 * @method static CategoryDTO|null updateDTO(CategoryModel $category, array $data)
 * @method static bool delete(CategoryModel $category)
 * @method static bool move(CategoryModel $category, ?int $newParentId)
 * @method static Collection getTree()
 * @method static Collection search(string $query)
 * @method static Collection getRootCategories()
 * @method static Collection getWithChildren()
 * @method static Collection getByParentId(?int $parentId)
 * @method static CategoryModel|null getWithAllChildren(int $categoryId)
 * @method static CategoryModel|null getWithAllParents(int $categoryId)
 * @method static Collection getWithProductsCount()
 * @method static bool hasChildren(CategoryModel $category)
 * @method static bool hasProducts(CategoryModel $category)
 * @method static Collection getPath(CategoryModel $category)
 * @method static int getDepth(CategoryModel $category)
 * @method static Collection getByDepth(int $depth)
 */
class Category extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.category';
    }
}
