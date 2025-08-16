<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\ProductVariantService;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static ProductVariant|null find(int $id)
 * @method static ProductVariantDTO|null findDTO(int $id)
 * @method static ProductVariant create(array $data)
 * @method static ProductVariantDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductVariant $variant, array $data)
 * @method static bool delete(ProductVariant $variant)
 * @method static Collection findByProductId(int $productId)
 * @method static int getVariantCountByProductId(int $productId)
 * @method static ProductVariant|null findBySku(string $sku)
 * @method static ProductVariant|null findByBarcode(string $barcode)
 * @method static Collection getVariantSkus()
 * @method static Collection getVariantBarcodes()
 * @method static Collection findActive()
 * @method static Collection findInStock()
 * @method static Collection findOutOfStock()
 * @method static Collection findLowStock()
 * @method static int getActiveVariantCount()
 * @method static int getInStockVariantCount()
 * @method static int getOutOfStockVariantCount()
 * @method static int getLowStockVariantCount()
 * @method static Collection findByPriceRange(float $minPrice, float $maxPrice)
 * @method static Collection findByStockRange(int $minStock, int $maxStock)
 * @method static Collection getVariantPrices()
 * @method static Collection getVariantWeights()
 * @method static ProductVariant|null findByAttributeCombination(int $productId, array $attributeValues)
 * @method static bool toggleActive(ProductVariant $variant)
 * @method static bool toggleFeatured(ProductVariant $variant)
 * @method static bool updateStock(ProductVariant $variant, int $quantity)
 * @method static bool reserveStock(ProductVariant $variant, int $quantity)
 * @method static bool releaseStock(ProductVariant $variant, int $quantity)
 * @method static bool adjustStock(ProductVariant $variant, int $quantity, string $reason = null)
 * @method static int getVariantStock(int $variantId)
 * @method static int getVariantAvailableStock(int $variantId)
 * @method static int getVariantReservedStock(int $variantId)
 * @method static bool setPrice(ProductVariant $variant, float $price)
 * @method static bool setSalePrice(ProductVariant $variant, float $salePrice)
 * @method static bool setComparePrice(ProductVariant $variant, float $comparePrice)
 * @method static Collection search(string $query)
 * @method static int getVariantCount()
 * @method static array getVariantAnalytics(int $variantId)
 * @method static array getVariantAnalyticsByProduct(int $productId)
 * @method static float getVariantSales(int $variantId)
 * @method static float getVariantRevenue(int $variantId)
 * @method static float getVariantProfit(int $variantId)
 * @method static float getVariantMargin(int $variantId)
 * @method static array getVariantInventory(int $variantId)
 * @method static array getVariantInventoryHistory(int $variantId)
 * @method static array getVariantInventoryAlerts(int $variantId)
 * @method static Collection bulkCreate(int $productId, array $variantData)
 * @method static bool bulkUpdate(array $variantData)
 * @method static bool bulkDelete(array $variantIds)
 * @method static bool importVariants(int $productId, array $variantData)
 * @method static array exportVariants(int $productId)
 * @method static bool syncVariants(int $productId, array $variantData)
 * @method static bool validateVariant(array $data)
 * @method static bool isSkuUnique(string $sku, int|null $excludeId = null)
 * @method static bool isBarcodeUnique(string $barcode, int|null $excludeId = null)
 * @method static string generateSku(int $productId, array $attributeValues)
 */
class ProductVariantFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product-variant';
    }
}
