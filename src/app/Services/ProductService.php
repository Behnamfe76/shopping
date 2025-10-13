<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasSlugGeneration;
use Fereydooni\Shopping\app\Traits\HasMediaOperations;
use Fereydooni\Shopping\app\Traits\HasInventoryManagement;
use Fereydooni\Shopping\app\Traits\HasSeoOperations;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\DTOs\ProductDTO;
use Illuminate\Database\Eloquent\Collection;

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
