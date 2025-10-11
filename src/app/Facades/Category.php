<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Models\Category as CategoryModel;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Fereydooni\Shopping\app\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

/**
 * @method static Collection all()
 * @method static CursorPaginator cursorAll()
 * @method static CategoryModel|null find(int $id)
 * @method static CategoryDTO|null findDTO(int $id)
 * @method static CategoryModel create(array $data)
 * @method static CategoryDTO createDTO(array $data)
 * @method static bool update(CategoryModel $category, array $data)
 * @method static CategoryDTO|null updateDTO(CategoryModel $category, array $data)
 * @method static bool delete(CategoryModel $category)
 *
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 *
 * @method static CategoryModel|null findBySlug(string $slug)
 * @method static CategoryDTO|null findBySlugDTO(string $slug)
 * @method static Collection findByStatus(CategoryStatus $status)
 * @method static Collection findByStatusDTO(CategoryStatus $status)
 *
 * @method static Collection getRootCategories()
 * @method static Collection getRootCategoriesDTO()
 * @method static Collection getChildren(int $parentId)
 * @method static Collection getChildrenDTO(int $parentId)
 * @method static Collection getAncestors(int $categoryId)
 * @method static Collection getAncestorsDTO(int $categoryId)
 * @method static Collection getDescendants(int $categoryId)
 * @method static Collection getDescendantsDTO(int $categoryId)
 * @method static Collection getTree()
 * @method static Collection getTreeDTO()
 *
 * @method static bool moveCategory(CategoryModel $category, ?int $newParentId)
 * @method static bool reorderCategories(array $orderData)
 *
 * @method static bool setDefault(CategoryModel $category)
 * @method static CategoryDTO|null setDefaultDTO(CategoryModel $category)
 * @method static bool unsetDefault(CategoryModel $category)
 * @method static CategoryModel|null getDefaultByUser(int $userId, CategoryStatus $status)
 * @method static CategoryDTO|null getDefaultDTOByUser(int $userId, CategoryStatus $status)
 * @method static Collection getDefaultItems(int $userId)
 * @method static Collection getDefaultItemsDTO(int $userId)
 * @method static bool hasDefaultItem(int $userId, CategoryStatus $status)
 * @method static bool canSetDefault(CategoryModel $category)
 * @method static bool isDefault(CategoryModel $category)
 *
 * @method static Collection search(string $query, ?int $userId = null, ?CategoryStatus $status = null)
 * @method static mixed searchWithPagination(string $query, int $perPage = 15, ?int $userId = null, ?CategoryStatus $status = null, string $paginationType = 'regular')
 * @method static array getSearchSuggestions(string $query, ?int $userId = null)
 * @method static Collection searchByField(string $field, string $value, ?int $userId = null)
 * @method static Collection advancedSearch(array $criteria, ?int $userId = null)
 *
 * @method static array getCategoryStats()
 * @method static array getCategoryStatsByStatus()
 * @method static Collection getCategoryPath(int $categoryId)
 * @method static Collection getCategoryPathDTO(int $categoryId)
 * @method static int getDepth(CategoryModel $category)
 * @method static Collection getByDepth(int $depth)
 *
 * @method static int getCategoryCount()
 * @method static int getCategoryCountByStatus(CategoryStatus $status)
 * @method static int getCategoryCountByParent(int $parentId)
 *
 * @method static bool validateCategory(array $data)
 */
class Category extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.category';
    }
}
