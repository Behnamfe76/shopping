<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static ProductAttributeValue|null find(int $id)
 * @method static ProductAttributeValueDTO|null findDTO(int $id)
 * @method static ProductAttributeValue create(array $data)
 * @method static ProductAttributeValueDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductAttributeValue $value, array $data)
 * @method static ProductAttributeValueDTO|null updateAndReturnDTO(ProductAttributeValue $value, array $data)
 * @method static bool delete(ProductAttributeValue $value)
 * @method static Collection findByAttributeId(int $attributeId)
 * @method static Collection findByAttributeIdDTO(int $attributeId)
 * @method static ProductAttributeValue|null findByAttributeIdAndValue(int $attributeId, string $value)
 * @method static ProductAttributeValueDTO|null findByAttributeIdAndValueDTO(int $attributeId, string $value)
 * @method static Collection findByValue(string $value)
 * @method static Collection findByValueDTO(string $value)
 * @method static ProductAttributeValue|null findBySlug(string $slug)
 * @method static ProductAttributeValueDTO|null findBySlugDTO(string $slug)
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findDefault()
 * @method static Collection findDefaultDTO()
 * @method static Collection findByVariantId(int $variantId)
 * @method static Collection findByVariantIdDTO(int $variantId)
 * @method static Collection findByProductId(int $productId)
 * @method static Collection findByProductIdDTO(int $productId)
 * @method static Collection findByCategoryId(int $categoryId)
 * @method static Collection findByCategoryIdDTO(int $categoryId)
 * @method static Collection findByBrandId(int $brandId)
 * @method static Collection findByBrandIdDTO(int $brandId)
 * @method static bool toggleActive(ProductAttributeValue $value)
 * @method static bool toggleDefault(ProductAttributeValue $value)
 * @method static bool setDefault(ProductAttributeValue $value)
 * @method static int getValueCount()
 * @method static int getValueCountByAttributeId(int $attributeId)
 * @method static int getActiveValueCount()
 * @method static int getDefaultValueCount()
 * @method static int getValueCountByVariantId(int $variantId)
 * @method static int getValueCountByProductId(int $productId)
 * @method static int getValueCountByCategoryId(int $categoryId)
 * @method static int getValueCountByBrandId(int $brandId)
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection searchByAttributeId(int $attributeId, string $query)
 * @method static Collection searchByAttributeIdDTO(int $attributeId, string $query)
 * @method static Collection getMostUsedValues(int $limit = 10)
 * @method static Collection getMostUsedValuesDTO(int $limit = 10)
 * @method static Collection getLeastUsedValues(int $limit = 10)
 * @method static Collection getLeastUsedValuesDTO(int $limit = 10)
 * @method static Collection getUnusedValues()
 * @method static Collection getUnusedValuesDTO()
 * @method static Collection getValuesByUsageRange(int $minUsage, int $maxUsage)
 * @method static Collection getValuesByUsageRangeDTO(int $minUsage, int $maxUsage)
 * @method static bool validateAttributeValue(array $data)
 * @method static string generateSlug(string $value)
 * @method static bool isSlugUnique(string $slug, ?int $excludeId = null)
 * @method static bool isValueUnique(int $attributeId, string $value, ?int $excludeId = null)
 * @method static int getValueUsage(int $valueId)
 * @method static int getValueUsageByProduct(int $valueId, int $productId)
 * @method static int getValueUsageByCategory(int $valueId, int $categoryId)
 * @method static int getValueUsageByBrand(int $valueId, int $brandId)
 * @method static int getValueUsageByVariant(int $valueId, int $variantId)
 * @method static array getValueAnalytics(int $valueId)
 * @method static bool incrementUsage(int $valueId)
 * @method static bool decrementUsage(int $valueId)
 * @method static Collection getValueVariants(int $valueId)
 * @method static Collection getValueVariantsDTO(int $valueId)
 * @method static Collection getValueProducts(int $valueId)
 * @method static Collection getValueProductsDTO(int $valueId)
 * @method static Collection getValueCategories(int $valueId)
 * @method static Collection getValueCategoriesDTO(int $valueId)
 * @method static Collection getValueBrands(int $valueId)
 * @method static Collection getValueBrandsDTO(int $valueId)
 * @method static bool assignToVariant(int $valueId, int $variantId)
 * @method static bool removeFromVariant(int $valueId, int $variantId)
 * @method static bool assignToProduct(int $valueId, int $productId)
 * @method static bool removeFromProduct(int $valueId, int $productId)
 * @method static bool assignToCategory(int $valueId, int $categoryId)
 * @method static bool removeFromCategory(int $valueId, int $categoryId)
 * @method static bool assignToBrand(int $valueId, int $brandId)
 * @method static bool removeFromBrand(int $valueId, int $brandId)
 */
class ProductAttributeValue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.product-attribute-value';
    }
}
