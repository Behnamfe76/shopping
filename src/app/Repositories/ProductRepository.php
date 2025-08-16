<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\DTOs\ProductDTO;
use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Enums\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection
    {
        return Product::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return Product::simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return Product::cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findDTO(int $id): ?ProductDTO
    {
        $product = $this->find($id);
        return $product ? ProductDTO::fromModel($product) : null;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function createAndReturnDTO(array $data): ProductDTO
    {
        $product = $this->create($data);
        return ProductDTO::fromModel($product);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function updateAndReturnDTO(Product $product, array $data): ?ProductDTO
    {
        $updated = $this->update($product, $data);
        return $updated ? ProductDTO::fromModel($product->fresh()) : null;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    // Slug and SKU based queries
    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->first();
    }

    public function findBySlugDTO(string $slug): ?ProductDTO
    {
        $product = $this->findBySlug($slug);
        return $product ? ProductDTO::fromModel($product) : null;
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function findBySkuDTO(string $sku): ?ProductDTO
    {
        $product = $this->findBySku($sku);
        return $product ? ProductDTO::fromModel($product) : null;
    }

    // Category and brand filtering
    public function findByCategoryId(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)->get();
    }

    public function findByCategoryIdDTO(int $categoryId): Collection
    {
        $products = $this->findByCategoryId($categoryId);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function findByBrandId(int $brandId): Collection
    {
        return Product::where('brand_id', $brandId)->get();
    }

    public function findByBrandIdDTO(int $brandId): Collection
    {
        $products = $this->findByBrandId($brandId);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Status and type filtering
    public function findByStatus(string $status): Collection
    {
        return Product::where('status', $status)->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $products = $this->findByStatus($status);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function findByType(string $type): Collection
    {
        return Product::where('product_type', $type)->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        $products = $this->findByType($type);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Active and featured products
    public function findActive(): Collection
    {
        return Product::where('is_active', true)->get();
    }

    public function findActiveDTO(): Collection
    {
        $products = $this->findActive();
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function findFeatured(): Collection
    {
        return Product::where('is_featured', true)->get();
    }

    public function findFeaturedDTO(): Collection
    {
        $products = $this->findFeatured();
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Stock level filtering
    public function findInStock(): Collection
    {
        return Product::where('stock_quantity', '>', 0)->get();
    }

    public function findInStockDTO(): Collection
    {
        $products = $this->findInStock();
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function findLowStock(int $threshold = 10): Collection
    {
        return Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->get();
    }

    public function findLowStockDTO(int $threshold = 10): Collection
    {
        $products = $this->findLowStock($threshold);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function findOutOfStock(): Collection
    {
        return Product::where('stock_quantity', '<=', 0)->get();
    }

    public function findOutOfStockDTO(): Collection
    {
        $products = $this->findOutOfStock();
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Status management
    public function toggleActive(Product $product): bool
    {
        $product->is_active = !$product->is_active;
        return $product->save();
    }

    public function toggleFeatured(Product $product): bool
    {
        $product->is_featured = !$product->is_featured;
        return $product->save();
    }

    public function publish(Product $product): bool
    {
        $product->status = ProductStatus::PUBLISHED;
        return $product->save();
    }

    public function unpublish(Product $product): bool
    {
        $product->status = ProductStatus::DRAFT;
        return $product->save();
    }

    public function archive(Product $product): bool
    {
        $product->status = ProductStatus::ARCHIVED;
        return $product->save();
    }

    // Count methods
    public function getProductCount(): int
    {
        return Product::count();
    }

    public function getProductCountByCategory(int $categoryId): int
    {
        return Product::where('category_id', $categoryId)->count();
    }

    public function getProductCountByBrand(int $brandId): int
    {
        return Product::where('brand_id', $brandId)->count();
    }

    public function getProductCountByStatus(string $status): int
    {
        return Product::where('status', $status)->count();
    }

    public function getProductCountByType(string $type): int
    {
        return Product::where('product_type', $type)->count();
    }

    // Stock and value methods
    public function getTotalStock(): int
    {
        return Product::sum('stock_quantity');
    }

    public function getTotalStockByCategory(int $categoryId): int
    {
        return Product::where('category_id', $categoryId)->sum('stock_quantity');
    }

    public function getTotalStockByBrand(int $brandId): int
    {
        return Product::where('brand_id', $brandId)->sum('stock_quantity');
    }

    public function getTotalValue(): float
    {
        return Product::sum(DB::raw('stock_quantity * price'));
    }

    public function getTotalValueByCategory(int $categoryId): float
    {
        return Product::where('category_id', $categoryId)
            ->sum(DB::raw('stock_quantity * price'));
    }

    public function getTotalValueByBrand(int $brandId): float
    {
        return Product::where('brand_id', $brandId)
            ->sum(DB::raw('stock_quantity * price'));
    }

    // Search methods
    public function search(string $query): Collection
    {
        return Product::where(function ($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%")
              ->orWhere('sku', 'LIKE', "%{$query}%")
              ->orWhere('slug', 'LIKE', "%{$query}%");
        })->get();
    }

    public function searchDTO(string $query): Collection
    {
        $products = $this->search($query);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function searchByCategory(int $categoryId, string $query): Collection
    {
        return Product::where('category_id', $categoryId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })->get();
    }

    public function searchByCategoryDTO(int $categoryId, string $query): Collection
    {
        $products = $this->searchByCategory($categoryId, $query);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function searchByBrand(int $brandId, string $query): Collection
    {
        return Product::where('brand_id', $brandId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })->get();
    }

    public function searchByBrandDTO(int $brandId, string $query): Collection
    {
        $products = $this->searchByBrand($brandId, $query);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Analytics and reporting
    public function getTopSelling(int $limit = 10): Collection
    {
        return Product::orderBy('total_sales', 'desc')->limit($limit)->get();
    }

    public function getTopSellingDTO(int $limit = 10): Collection
    {
        $products = $this->getTopSelling($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getMostViewed(int $limit = 10): Collection
    {
        return Product::orderBy('view_count', 'desc')->limit($limit)->get();
    }

    public function getMostViewedDTO(int $limit = 10): Collection
    {
        $products = $this->getMostViewed($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getMostWishlisted(int $limit = 10): Collection
    {
        return Product::orderBy('wishlist_count', 'desc')->limit($limit)->get();
    }

    public function getMostWishlistedDTO(int $limit = 10): Collection
    {
        $products = $this->getMostWishlisted($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getBestRated(int $limit = 10): Collection
    {
        return Product::orderBy('average_rating', 'desc')->limit($limit)->get();
    }

    public function getBestRatedDTO(int $limit = 10): Collection
    {
        $products = $this->getBestRated($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getNewArrivals(int $limit = 10): Collection
    {
        return Product::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function getNewArrivalsDTO(int $limit = 10): Collection
    {
        $products = $this->getNewArrivals($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getOnSale(int $limit = 10): Collection
    {
        return Product::whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOnSaleDTO(int $limit = 10): Collection
    {
        $products = $this->getOnSale($limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Price and stock range filtering
    public function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return Product::whereBetween('price', [$minPrice, $maxPrice])->get();
    }

    public function getByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        $products = $this->getByPriceRange($minPrice, $maxPrice);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getByStockRange(int $minStock, int $maxStock): Collection
    {
        return Product::whereBetween('stock_quantity', [$minStock, $maxStock])->get();
    }

    public function getByStockRangeDTO(int $minStock, int $maxStock): Collection
    {
        $products = $this->getByStockRange($minStock, $maxStock);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Related products
    public function getRelatedProducts(Product $product, int $limit = 5): Collection
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
    }

    public function getRelatedProductsDTO(Product $product, int $limit = 5): Collection
    {
        $products = $this->getRelatedProducts($product, $limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getCrossSellProducts(Product $product, int $limit = 5): Collection
    {
        // Implementation for cross-sell products
        return Product::where('category_id', '!=', $product->category_id)
            ->where('is_featured', true)
            ->limit($limit)
            ->get();
    }

    public function getCrossSellProductsDTO(Product $product, int $limit = 5): Collection
    {
        $products = $this->getCrossSellProducts($product, $limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    public function getUpSellProducts(Product $product, int $limit = 5): Collection
    {
        // Implementation for up-sell products (higher price)
        return Product::where('price', '>', $product->price)
            ->where('category_id', $product->category_id)
            ->limit($limit)
            ->get();
    }

    public function getUpSellProductsDTO(Product $product, int $limit = 5): Collection
    {
        $products = $this->getUpSellProducts($product, $limit);
        return $products->map(fn($product) => ProductDTO::fromModel($product));
    }

    // Validation and utility methods
    public function validateProduct(array $data): bool
    {
        $rules = ProductDTO::rules();
        $validator = validator($data, $rules);
        return !$validator->fails();
    }

    public function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (!$this->isSlugUnique($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function isSkuUnique(string $sku, ?int $excludeId = null): bool
    {
        $query = Product::where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    // Inventory management
    public function updateStock(Product $product, int $quantity, string $operation = 'decrease'): bool
    {
        if ($operation === 'decrease') {
            $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
        } else {
            $product->stock_quantity += $quantity;
        }

        return $product->save();
    }

    public function reserveStock(Product $product, int $quantity): bool
    {
        if ($product->stock_quantity < $quantity) {
            return false;
        }

        return $this->updateStock($product, $quantity, 'decrease');
    }

    public function releaseStock(Product $product, int $quantity): bool
    {
        return $this->updateStock($product, $quantity, 'increase');
    }

    public function getInventoryLevel(Product $product): int
    {
        return $product->stock_quantity;
    }

    // Analytics methods
    public function getProductAnalytics(Product $product): array
    {
        return [
            'total_sales' => $product->total_sales,
            'view_count' => $product->view_count,
            'wishlist_count' => $product->wishlist_count,
            'average_rating' => $product->average_rating,
            'reviews_count' => $product->reviews_count,
            'stock_level' => $product->stock_quantity,
            'conversion_rate' => $product->view_count > 0 ? ($product->total_sales / $product->view_count) * 100 : 0,
        ];
    }

    public function incrementViewCount(Product $product): bool
    {
        $product->increment('view_count');
        return true;
    }

    public function incrementWishlistCount(Product $product): bool
    {
        $product->increment('wishlist_count');
        return true;
    }

    public function updateAverageRating(Product $product): bool
    {
        $averageRating = $product->reviews()->avg('rating') ?? 0;
        $product->average_rating = $averageRating;
        $product->reviews_count = $product->reviews()->count();
        return $product->save();
    }
}
