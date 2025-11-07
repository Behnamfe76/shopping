<?php

namespace Fereydooni\Shopping\app\Services;

use Attribute;
use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\DTOs\ProductDTO;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Collection;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSeoOperations;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSlugGeneration;
use Fereydooni\Shopping\app\Traits\HasMediaOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Traits\HasInventoryManagement;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface;

class ProductService
{
    use HasCrudOperations;
    use HasStatusToggle;
    use HasSearchOperations;
    use HasSlugGeneration;
    use HasMediaOperations;
    use HasInventoryManagement;
    use HasSeoOperations;
    use HasAnalyticsOperations;


    public function __construct(
        private ProductRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        $this->model = Product::class;
        $this->dtoClass = ProductDTO::class;
    }

    public function create(array $data): Product
    {
        try {
            DB::beginTransaction();
            $productData = $data;
            $productData['has_variant'] = $data['has_variant'] !== 'none';
            $productData['multi_variant'] = $data['has_variant'] === 'more_than_one';
            $product = $this->repository->create($productData);

            // storing product tags
            if (isset($data['product_tags']) && is_array($data['product_tags'])) {
                $product->tags()->sync($data['product_tags']);
            }

            // storing product attributes and variants
            if ($data['has_variant'] === 'one' && isset($data['product_single_variants'])) {
                $attribute = ProductAttribute::where('slug', $data['product_attribute'])->firstOrFail();

                foreach ($data['product_single_variants'] as $variantData) {
                    $attributeValue = $attribute->values()
                        ->where('value', 'ilike', '%' . $variantData['variant_name'] . '%')
                        ->first();

                    // If not found, create it
                    if (!$attributeValue) {
                        $attributeValue = $attribute->values()->create([
                            'value' => $variantData['variant_name']
                        ]);
                    }

                    $variant = $product->variants()->create([
                        'name' => $variantData['variant_name'],
                        'description' => $variantData['variant_description'] ?? null,
                        'price' => $variantData['variant_price'],
                        'sale_price' => $variantData['variant_sale_price'] ?? null,
                        'cost_price' => $variantData['variant_cost_price'] ?? null,
                        'stock_quantity' => $variantData['variant_stock'],
                        'in_stock' => $variantData['variant_stock'] > 0,
                    ]);

                    $variant->values()->create([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        'attribute_value_id' => $attributeValue->id,
                    ]);
                }
            } else if ($data['has_variant'] === 'more_than_one' && isset($data['product_multiple_variants'])) {

                foreach ($data['product_multiple_variants'] as $variantSet) {

                    $variant = $product->variants()->create([
                        'description' => $variantSet['variant_description'] ?? null,
                        'price' => $variantSet['variant_price'],
                        'sale_price' => $variantSet['variant_sale_price'] ?? null,
                        'cost_price' => $variantSet['variant_cost_price'] ?? null,
                        'stock_quantity' => $variantSet['variant_stock'],
                        'in_stock' => $variantSet['variant_stock'] > 0,
                        'multi_variant' => true,
                    ]);

                    foreach ($data['product_multi_attributes'] as $multiAttr) {
                        $attribute = ProductAttribute::where('slug', $multiAttr)->firstOrFail();
                        $attributeValue = $attribute->values()
                            ->where('value', 'ilike', $variantSet[$multiAttr]['variant_name'] . '%')
                            ->first();

                        // If not found, create it
                        if (!$attributeValue) {
                            $attributeValue = $attribute->values()->create([
                                'value' => $variantSet[$multiAttr]['variant_name']
                            ]);
                        }
                        $variant->values()->create([
                            'product_id' => $product->id,
                            'attribute_id' => $attribute->id,
                            'attribute_value_id' => $attributeValue->id,
                        ]);
                    }
                }
            }

            // storing main image
            if (isset($data['main_image'])) {
                $this->uploadMedia($product, $data['main_image'], 'product-images', ['is_main' => true]);
            }

            // storing product images
            if (isset($data['images'])) {
                if (is_array($data['images']) && count($data['images']) > 0) {
                    $this->uploadMultipleMedia($product, $data['images'], 'product-images');
                } else {
                    $this->uploadMedia($product, $data['images'], 'product-images', ['is_main' => true]);
                }
            }

            // storing product videos
            if (isset($data['videos'])) {
                if (is_array($data['videos']) && count($data['videos']) > 0) {
                    $this->uploadMultipleMedia($product, $data['videos'], 'product-videos');
                } else {
                    $this->uploadMedia($product, $data['videos'], 'product-videos', ['is_main' => true]);
                }
            }

            DB::commit();

            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();

            // === 1. Normalize variant mode flags ===
            $variantMode = $data['has_variant'] ?? 'none';
            $productData = $data;

            $productData['has_variant'] = $variantMode !== 'none';
            $productData['multi_variant'] = $variantMode === 'more_than_one';

            $product->update($productData);

            // === 2. Sync tags ===
            if (!empty($data['product_tags']) && is_array($data['product_tags'])) {
                $product->tags()->sync($data['product_tags']);
            }

            // === 3. Handle variants ===
            if ($variantMode === 'none') {
                // ➤ Downgrade to simple product
                // Delete all variants and their associated values
                $product->variants()->each(function ($variant) {
                    $variant->values()->delete();
                });
                $product->variants()->delete();
            } elseif ($variantMode === 'one' && !empty($data['product_single_variants'])) {

                // ➤ Single attribute variant mode
                // Clean up any existing multi-variants or mismatched single-variants
                $product->variants()->where('multi_variant', true)->each(function ($variant) {
                    $variant->values()->delete();
                });
                $product->variants()->where('multi_variant', true)->delete();

                $attribute = ProductAttribute::where('slug', $data['product_attribute'])->firstOrFail();
                $processedVariantNames = [];

                foreach ($data['product_single_variants'] as $variantData) {
                    $variantName = $variantData['variant_name'];
                    $processedVariantNames[] = $variantName;

                    $attributeValue = $attribute->values()
                        ->where('value', 'ilike', $variantName)
                        ->first();

                    if (!$attributeValue) {
                        $attributeValue = $attribute->values()->create([
                            'value' => $variantName,
                        ]);
                    }

                    $variant = $product->variants()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'name' => $variantName
                        ],
                        [
                            'description' => $variantData['variant_description'] ?? null,
                            'price' => $variantData['variant_price'],
                            'sale_price' => $variantData['variant_sale_price'] ?? null,
                            'cost_price' => $variantData['variant_cost_price'] ?? null,
                            'stock_quantity' => $variantData['variant_stock'],
                            'in_stock' => $variantData['variant_stock'] > 0,
                            'multi_variant' => false,
                        ]
                    );

                    // Clean up old attribute values for this variant
                    $variant->values()->where('attribute_id', '!=', $attribute->id)->delete();

                    $variant->values()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'attribute_id' => $attribute->id,
                        ],
                        [
                            'attribute_value_id' => $attributeValue->id,
                        ]
                    );
                }

                // Delete variants that are no longer in the submitted data
                $product->variants()
                    ->where('multi_variant', false)
                    ->whereNotIn('name', $processedVariantNames)
                    ->each(function ($variant) {
                        $variant->values()->delete();
                    });
                $product->variants()
                    ->where('multi_variant', false)
                    ->whereNotIn('name', $processedVariantNames)
                    ->delete();

            } elseif ($variantMode === 'more_than_one' && !empty($data['product_multiple_variants'])) {

                // ➤ Multi-attribute variant mode
                // Clean up any existing single-variants
                $product->variants()->where('multi_variant', false)->each(function ($variant) {
                    $variant->values()->delete();
                });
                $product->variants()->where('multi_variant', false)->delete();

                $processedVariantIds = [];

                foreach ($data['product_multiple_variants'] as $index => $variantSet) {
                    // Build a unique name for multi-variants from attribute values
                    $variantNameParts = [];
                    foreach ($data['product_multi_attributes'] as $multiAttr) {
                        if (isset($variantSet[$multiAttr]['variant_name'])) {
                            $variantNameParts[] = $variantSet[$multiAttr]['variant_name'];
                        }
                    }
                    $variantName = implode(' - ', $variantNameParts) ?: "Variant " . ($index + 1);

                    // Use a combination of product_id and name to identify the variant
                    $variant = $product->variants()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'name' => $variantName
                        ],
                        [
                            'description' => $variantSet['variant_description'] ?? null,
                            'price' => $variantSet['variant_price'],
                            'sale_price' => $variantSet['variant_sale_price'] ?? null,
                            'cost_price' => $variantSet['variant_cost_price'] ?? null,
                            'stock_quantity' => $variantSet['variant_stock'],
                            'in_stock' => $variantSet['variant_stock'] > 0,
                            'multi_variant' => true,
                        ]
                    );

                    $processedVariantIds[] = $variant->id;
                    $processedAttributeIds = [];

                    foreach ($data['product_multi_attributes'] as $multiAttr) {
                        $attribute = ProductAttribute::where('slug', $multiAttr)->firstOrFail();
                        $processedAttributeIds[] = $attribute->id;

                        $variantName = $variantSet[$multiAttr]['variant_name'] ?? null;
                        if (!$variantName) continue;

                        $attributeValue = $attribute->values()
                            ->where('value', 'ilike', $variantName)
                            ->first();

                        if (!$attributeValue) {
                            $attributeValue = $attribute->values()->create([
                                'value' => $variantName,
                            ]);
                        }

                        $variant->values()->updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'attribute_id' => $attribute->id,
                            ],
                            [
                                'attribute_value_id' => $attributeValue->id,
                            ]
                        );
                    }

                    // Clean up old attribute values for this variant that are no longer used
                    $variant->values()->whereNotIn('attribute_id', $processedAttributeIds)->delete();
                }

                // Delete variants that are no longer in the submitted data
                $product->variants()
                    ->where('multi_variant', true)
                    ->whereNotIn('id', $processedVariantIds)
                    ->each(function ($variant) {
                        $variant->values()->delete();
                    });
                $product->variants()
                    ->where('multi_variant', true)
                    ->whereNotIn('id', $processedVariantIds)
                    ->delete();
            }

            // storing main image
            if (isset($data['main_image'])) {
                $product->mainMedia()->update(
                    ['custom_properties' => ['is_main' => false]]
                );
                $this->uploadMedia($product, $data['main_image'], 'product-images', ['is_main' => true]);
            }

            // storing product images
            if (isset($data['images'])) {
                if (is_array($data['images']) && count($data['images']) > 0) {
                    $this->uploadMultipleMedia($product, $data['images'], 'product-images');
                } else {
                    $this->uploadMedia($product, $data['images'], 'product-images', ['is_main' => true]);
                }
            }

            // storing product videos
            if (isset($data['videos'])) {
                if (is_array($data['videos']) && count($data['videos']) > 0) {
                    $this->uploadMultipleMedia($product, $data['videos'], 'product-videos');
                } else {
                    $this->uploadMedia($product, $data['videos'], 'product-videos', ['is_main' => true]);
                }
            }

            DB::commit();

            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // Repository method delegation
    public function findBySlug(string $slug): ?Product
    {
        return $this->repository->findBySlug($slug);
    }

    public function findBySlugDTO(string $slug): ?ProductDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->repository->findBySku($sku);
    }

    public function findBySkuDTO(string $sku): ?ProductDTO
    {
        return $this->repository->findBySkuDTO($sku);
    }

    public function findByCategoryId(int $categoryId): Collection
    {
        return $this->repository->findByCategoryId($categoryId);
    }

    public function findByCategoryIdDTO(int $categoryId): Collection
    {
        return $this->repository->findByCategoryIdDTO($categoryId);
    }

    public function findByBrandId(int $brandId): Collection
    {
        return $this->repository->findByBrandId($brandId);
    }

    public function findByBrandIdDTO(int $brandId): Collection
    {
        return $this->repository->findByBrandIdDTO($brandId);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->repository->findByTypeDTO($type);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    public function findFeaturedDTO(): Collection
    {
        return $this->repository->findFeaturedDTO();
    }

    public function findInStock(): Collection
    {
        return $this->repository->findInStock();
    }

    public function findInStockDTO(): Collection
    {
        return $this->repository->findInStockDTO();
    }

    public function findLowStock(int $threshold = 10): Collection
    {
        return $this->repository->findLowStock($threshold);
    }

    public function findLowStockDTO(int $threshold = 10): Collection
    {
        return $this->repository->findLowStockDTO($threshold);
    }

    public function findOutOfStock(): Collection
    {
        return $this->repository->findOutOfStock();
    }

    public function findOutOfStockDTO(): Collection
    {
        return $this->repository->findOutOfStockDTO();
    }

    public function getProductCount(): int
    {
        return $this->repository->getProductCount();
    }

    public function getProductCountByCategory(int $categoryId): int
    {
        return $this->repository->getProductCountByCategory($categoryId);
    }

    public function getProductCountByBrand(int $brandId): int
    {
        return $this->repository->getProductCountByBrand($brandId);
    }

    public function getProductCountByStatus(string $status): int
    {
        return $this->repository->getProductCountByStatus($status);
    }

    public function getProductCountByType(string $type): int
    {
        return $this->repository->getProductCountByType($type);
    }

    public function getTotalStock(): int
    {
        return $this->repository->getTotalStock();
    }

    public function getTotalStockByCategory(int $categoryId): int
    {
        return $this->repository->getTotalStockByCategory($categoryId);
    }

    public function getTotalStockByBrand(int $brandId): int
    {
        return $this->repository->getTotalStockByBrand($brandId);
    }

    public function getTotalValue(): float
    {
        return $this->repository->getTotalValue();
    }

    public function getTotalValueByCategory(int $categoryId): float
    {
        return $this->repository->getTotalValueByCategory($categoryId);
    }

    public function getTotalValueByBrand(int $brandId): float
    {
        return $this->repository->getTotalValueByBrand($brandId);
    }

    public function searchByCategory(int $categoryId, string $query): Collection
    {
        return $this->repository->searchByCategory($categoryId, $query);
    }

    public function searchByCategoryDTO(int $categoryId, string $query): Collection
    {
        return $this->repository->searchByCategoryDTO($categoryId, $query);
    }

    public function searchByBrand(int $brandId, string $query): Collection
    {
        return $this->repository->searchByBrand($brandId, $query);
    }

    public function searchByBrandDTO(int $brandId, string $query): Collection
    {
        return $this->repository->searchByBrandDTO($brandId, $query);
    }

    public function getTopSelling(int $limit = 10): Collection
    {
        return $this->repository->getTopSelling($limit);
    }

    public function getTopSellingDTO(int $limit = 10): Collection
    {
        return $this->repository->getTopSellingDTO($limit);
    }

    public function getMostViewed(int $limit = 10): Collection
    {
        return $this->repository->getMostViewed($limit);
    }

    public function getMostViewedDTO(int $limit = 10): Collection
    {
        return $this->repository->getMostViewedDTO($limit);
    }

    public function getMostWishlisted(int $limit = 10): Collection
    {
        return $this->repository->getMostWishlisted($limit);
    }

    public function getMostWishlistedDTO(int $limit = 10): Collection
    {
        return $this->repository->getMostWishlistedDTO($limit);
    }

    public function getBestRated(int $limit = 10): Collection
    {
        return $this->repository->getBestRated($limit);
    }

    public function getBestRatedDTO(int $limit = 10): Collection
    {
        return $this->repository->getBestRatedDTO($limit);
    }

    public function getNewArrivals(int $limit = 10): Collection
    {
        return $this->repository->getNewArrivals($limit);
    }

    public function getNewArrivalsDTO(int $limit = 10): Collection
    {
        return $this->repository->getNewArrivalsDTO($limit);
    }

    public function getOnSale(int $limit = 10): Collection
    {
        return $this->repository->getOnSale($limit);
    }

    public function getOnSaleDTO(int $limit = 10): Collection
    {
        return $this->repository->getOnSaleDTO($limit);
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->getByPriceRange($minPrice, $maxPrice);
    }

    public function getByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->getByPriceRangeDTO($minPrice, $maxPrice);
    }

    public function getByStockRange(int $minStock, int $maxStock): Collection
    {
        return $this->repository->getByStockRange($minStock, $maxStock);
    }

    public function getByStockRangeDTO(int $minStock, int $maxStock): Collection
    {
        return $this->repository->getByStockRangeDTO($minStock, $maxStock);
    }

    public function getRelatedProducts(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getRelatedProducts($product, $limit);
    }

    public function getRelatedProductsDTO(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getRelatedProductsDTO($product, $limit);
    }

    public function getCrossSellProducts(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getCrossSellProducts($product, $limit);
    }

    public function getCrossSellProductsDTO(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getCrossSellProductsDTO($product, $limit);
    }

    public function getUpSellProducts(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getUpSellProducts($product, $limit);
    }

    public function getUpSellProductsDTO(Product $product, int $limit = 5): Collection
    {
        return $this->repository->getUpSellProductsDTO($product, $limit);
    }

    public function validateProduct(array $data): bool
    {
        return $this->repository->validateProduct($data);
    }

    public function isSkuUnique(string $sku, ?int $excludeId = null): bool
    {
        return $this->repository->isSkuUnique($sku, $excludeId);
    }

    public function updateStock(Product $product, int $quantity, string $operation = 'decrease'): bool
    {
        return $this->repository->updateStock($product, $quantity, $operation);
    }

    public function reserveStock(Product $product, int $quantity): bool
    {
        return $this->repository->reserveStock($product, $quantity);
    }

    public function releaseStock(Product $product, int $quantity): bool
    {
        return $this->repository->releaseStock($product, $quantity);
    }

    public function getInventoryLevel(Product $product): int
    {
        return $this->repository->getInventoryLevel($product);
    }

    public function getProductAnalytics(Product $product): array
    {
        return $this->repository->getProductAnalytics($product);
    }

    public function incrementViewCount(Product $product): bool
    {
        return $this->repository->incrementViewCount($product);
    }

    public function incrementWishlistCount(Product $product): bool
    {
        return $this->repository->incrementWishlistCount($product);
    }

    public function updateAverageRating(Product $product): bool
    {
        return $this->repository->updateAverageRating($product);
    }

    // Override trait methods to use repository
    public function find(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductDTO
    {
        return $this->repository->findDTO($id);
    }

    public function createAndReturnDTO(array $data): ProductDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function updateAndReturnDTO(Product $product, array $data): ?ProductDTO
    {
        return $this->repository->updateAndReturnDTO($product, $data);
    }

    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    public function toggleActive(Product $product): bool
    {
        return $this->repository->toggleActive($product);
    }

    public function toggleFeatured(Product $product): bool
    {
        return $this->repository->toggleFeatured($product);
    }

    public function publish(Product $product): bool
    {
        return $this->repository->publish($product);
    }

    public function unpublish(Product $product): bool
    {
        return $this->repository->unpublish($product);
    }

    public function archive(Product $product): bool
    {
        return $this->repository->archive($product);
    }
}
