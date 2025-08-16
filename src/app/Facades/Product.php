<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\ProductService;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\DTOs\ProductDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static Product|null find(int $id)
 * @method static ProductDTO|null findDTO(int $id)
 * @method static Product create(array $data)
 * @method static ProductDTO createAndReturnDTO(array $data)
 * @method static bool update(Product $product, array $data)
 * @method static ProductDTO|null updateAndReturnDTO(Product $product, array $data)
 * @method static bool delete(Product $product)
 * @method static Product|null findBySlug(string $slug)
 * @method static ProductDTO|null findBySlugDTO(string $slug)
 * @method static Product|null findBySku(string $sku)
 * @method static ProductDTO|null findBySkuDTO(string $sku)
 * @method static Collection findByCategoryId(int $categoryId)
 * @method static Collection findByCategoryIdDTO(int $categoryId)
 * @method static Collection findByBrandId(int $brandId)
 * @method static Collection findByBrandIdDTO(int $brandId)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Collection findByType(string $type)
 * @method static Collection findByTypeDTO(string $type)
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findFeatured()
 * @method static Collection findFeaturedDTO()
 * @method static Collection findInStock()
 * @method static Collection findInStockDTO()
 * @method static Collection findLowStock(int $threshold = 10)
 * @method static Collection findLowStockDTO(int $threshold = 10)
 * @method static Collection findOutOfStock()
 * @method static Collection findOutOfStockDTO()
 * @method static bool toggleActive(Product $product)
 * @method static bool toggleFeatured(Product $product)
 * @method static bool publish(Product $product)
 * @method static bool unpublish(Product $product)
 * @method static bool archive(Product $product)
 * @method static int getProductCount()
 * @method static int getProductCountByCategory(int $categoryId)
 * @method static int getProductCountByBrand(int $brandId)
 * @method static int getProductCountByStatus(string $status)
 * @method static int getProductCountByType(string $type)
 * @method static int getTotalStock()
 * @method static int getTotalStockByCategory(int $categoryId)
 * @method static int getTotalStockByBrand(int $brandId)
 * @method static float getTotalValue()
 * @method static float getTotalValueByCategory(int $categoryId)
 * @method static float getTotalValueByBrand(int $brandId)
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection searchByCategory(int $categoryId, string $query)
 * @method static Collection searchByCategoryDTO(int $categoryId, string $query)
 * @method static Collection searchByBrand(int $brandId, string $query)
 * @method static Collection searchByBrandDTO(int $brandId, string $query)
 * @method static Collection getTopSelling(int $limit = 10)
 * @method static Collection getTopSellingDTO(int $limit = 10)
 * @method static Collection getMostViewed(int $limit = 10)
 * @method static Collection getMostViewedDTO(int $limit = 10)
 * @method static Collection getMostWishlisted(int $limit = 10)
 * @method static Collection getMostWishlistedDTO(int $limit = 10)
 * @method static Collection getBestRated(int $limit = 10)
 * @method static Collection getBestRatedDTO(int $limit = 10)
 * @method static Collection getNewArrivals(int $limit = 10)
 * @method static Collection getNewArrivalsDTO(int $limit = 10)
 * @method static Collection getOnSale(int $limit = 10)
 * @method static Collection getOnSaleDTO(int $limit = 10)
 * @method static Collection getByPriceRange(float $minPrice, float $maxPrice)
 * @method static Collection getByPriceRangeDTO(float $minPrice, float $maxPrice)
 * @method static Collection getByStockRange(int $minStock, int $maxStock)
 * @method static Collection getByStockRangeDTO(int $minStock, int $maxStock)
 * @method static Collection getRelatedProducts(Product $product, int $limit = 5)
 * @method static Collection getRelatedProductsDTO(Product $product, int $limit = 5)
 * @method static Collection getCrossSellProducts(Product $product, int $limit = 5)
 * @method static Collection getCrossSellProductsDTO(Product $product, int $limit = 5)
 * @method static Collection getUpSellProducts(Product $product, int $limit = 5)
 * @method static Collection getUpSellProductsDTO(Product $product, int $limit = 5)
 * @method static bool validateProduct(array $data)
 * @method static string generateSlug(string $title)
 * @method static bool isSlugUnique(string $slug, ?int $excludeId = null)
 * @method static bool isSkuUnique(string $sku, ?int $excludeId = null)
 * @method static bool updateStock(Product $product, int $quantity, string $operation = 'decrease')
 * @method static bool reserveStock(Product $product, int $quantity)
 * @method static bool releaseStock(Product $product, int $quantity)
 * @method static int getInventoryLevel(Product $product)
 * @method static array getProductAnalytics(Product $product)
 * @method static bool incrementViewCount(Product $product)
 * @method static bool incrementWishlistCount(Product $product)
 * @method static bool updateAverageRating(Product $product)
 */
class ProductFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product';
    }
}
