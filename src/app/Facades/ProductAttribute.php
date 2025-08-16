<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\DTOs\ProductAttributeDTO;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static ProductAttribute|null find(int $id)
 * @method static ProductAttributeDTO|null findDTO(int $id)
 * @method static ProductAttribute create(array $data)
 * @method static ProductAttributeDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductAttribute $attribute, array $data)
 * @method static ProductAttributeDTO|null updateAndReturnDTO(ProductAttribute $attribute, array $data)
 * @method static bool delete(ProductAttribute $attribute)
 *
 * @method static ProductAttribute|null findBySlug(string $slug)
 * @method static ProductAttributeDTO|null findBySlugDTO(string $slug)
 * @method static ProductAttribute|null findByName(string $name)
 * @method static ProductAttributeDTO|null findByNameDTO(string $name)
 *
 * @method static Collection findByType(string $type)
 * @method static Collection findByTypeDTO(string $type)
 * @method static Collection findByInputType(string $inputType)
 * @method static Collection findByInputTypeDTO(string $inputType)
 * @method static Collection findByGroup(string $group)
 * @method static Collection findByGroupDTO(string $group)
 *
 * @method static Collection findRequired()
 * @method static Collection findRequiredDTO()
 * @method static Collection findSearchable()
 * @method static Collection findSearchableDTO()
 * @method static Collection findFilterable()
 * @method static Collection findFilterableDTO()
 * @method static Collection findComparable()
 * @method static Collection findComparableDTO()
 * @method static Collection findVisible()
 * @method static Collection findVisibleDTO()
 *
 * @method static Collection findSystem()
 * @method static Collection findSystemDTO()
 * @method static Collection findCustom()
 * @method static Collection findCustomDTO()
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 *
 * @method static bool toggleActive(ProductAttribute $attribute)
 * @method static bool toggleRequired(ProductAttribute $attribute)
 * @method static bool toggleSearchable(ProductAttribute $attribute)
 * @method static bool toggleFilterable(ProductAttribute $attribute)
 * @method static bool toggleComparable(ProductAttribute $attribute)
 * @method static bool toggleVisible(ProductAttribute $attribute)
 *
 * @method static int getAttributeCount()
 * @method static int getAttributeCountByType(string $type)
 * @method static int getAttributeCountByGroup(string $group)
 * @method static int getAttributeCountByInputType(string $inputType)
 * @method static int getRequiredAttributeCount()
 * @method static int getSearchableAttributeCount()
 * @method static int getFilterableAttributeCount()
 * @method static int getComparableAttributeCount()
 * @method static int getVisibleAttributeCount()
 * @method static int getSystemAttributeCount()
 * @method static int getCustomAttributeCount()
 *
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 *
 * @method static Collection getAttributeGroups()
 * @method static Collection getAttributeTypes()
 * @method static Collection getInputTypes()
 *
 * @method static bool validateAttribute(array $data)
 * @method static string generateSlug(string $name)
 * @method static bool isSlugUnique(string $slug, int|null $excludeId = null)
 * @method static bool isNameUnique(string $name, int|null $excludeId = null)
 *
 * @method static int getAttributeUsage(int $attributeId)
 * @method static int getAttributeUsageByProduct(int $attributeId, int $productId)
 * @method static int getAttributeUsageByCategory(int $attributeId, int $categoryId)
 * @method static int getAttributeUsageByBrand(int $attributeId, int $brandId)
 * @method static array getAttributeAnalytics(int $attributeId)
 *
 * @method static Collection getAttributeValues(int $attributeId)
 * @method static Collection getAttributeValuesDTO(int $attributeId)
 * @method static ProductAttributeValue addAttributeValue(int $attributeId, string $value, array $metadata = [])
 * @method static ProductAttributeValueDTO addAttributeValueDTO(int $attributeId, string $value, array $metadata = [])
 * @method static bool removeAttributeValue(int $attributeId, int $valueId)
 * @method static bool updateAttributeValue(int $attributeId, int $valueId, string $value, array $metadata = [])
 * @method static int getAttributeValueCount(int $attributeId)
 * @method static int getAttributeValueUsage(int $attributeId, int $valueId)
 *
 * @see \Fereydooni\Shopping\app\Services\ProductAttributeService
 */
class ProductAttributeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product-attribute';
    }
}
