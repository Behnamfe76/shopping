<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariantRepository implements ProductVariantRepositoryInterface
{
    public function all(): Collection
    {
        return ProductVariant::with(['product', 'attributeValues'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductVariant::with(['product', 'attributeValues'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?ProductVariant
    {
        return ProductVariant::with(['product', 'attributeValues'])->find($id);
    }

    public function findDTO(int $id): ?ProductVariantDTO
    {
        $variant = $this->find($id);
        return $variant ? ProductVariantDTO::fromModel($variant) : null;
    }

    public function create(array $data): ProductVariant
    {
        return ProductVariant::create($data);
    }

    public function createAndReturnDTO(array $data): ProductVariantDTO
    {
        $variant = $this->create($data);
        return ProductVariantDTO::fromModel($variant);
    }

    public function update(ProductVariant $variant, array $data): bool
    {
        return $variant->update($data);
    }

    public function delete(ProductVariant $variant): bool
    {
        return $variant->delete();
    }

    public function findByProductId(int $productId): Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->with(['attributeValues'])
            ->orderBy('sort_order')
            ->get();
    }

    public function getVariantCountByProductId(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->count();
    }

    public function findBySku(string $sku): ?ProductVariant
    {
        return ProductVariant::where('sku', $sku)->first();
    }

    public function findByBarcode(string $barcode): ?ProductVariant
    {
        return ProductVariant::where('barcode', $barcode)->first();
    }

    public function getVariantSkus(): Collection
    {
        return ProductVariant::pluck('sku');
    }

    public function getVariantBarcodes(): Collection
    {
        return ProductVariant::whereNotNull('barcode')->pluck('barcode');
    }

    public function findActive(): Collection
    {
        return ProductVariant::where('is_active', true)
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function findInStock(): Collection
    {
        return ProductVariant::where('stock', '>', 0)
            ->where('is_active', true)
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function findOutOfStock(): Collection
    {
        return ProductVariant::where('stock', '<=', 0)
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function findLowStock(): Collection
    {
        return ProductVariant::whereRaw('stock <= low_stock_threshold')
            ->where('stock', '>', 0)
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function getActiveVariantCount(): int
    {
        return ProductVariant::where('is_active', true)->count();
    }

    public function getInStockVariantCount(): int
    {
        return ProductVariant::where('stock', '>', 0)->where('is_active', true)->count();
    }

    public function getOutOfStockVariantCount(): int
    {
        return ProductVariant::where('stock', '<=', 0)->count();
    }

    public function getLowStockVariantCount(): int
    {
        return ProductVariant::whereRaw('stock <= low_stock_threshold')
            ->where('stock', '>', 0)
            ->count();
    }

    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return ProductVariant::whereBetween('price', [$minPrice, $maxPrice])
            ->where('is_active', true)
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function findByStockRange(int $minStock, int $maxStock): Collection
    {
        return ProductVariant::whereBetween('stock', [$minStock, $maxStock])
            ->with(['product', 'attributeValues'])
            ->get();
    }

    public function getVariantPrices(): Collection
    {
        return ProductVariant::pluck('price');
    }

    public function getVariantWeights(): Collection
    {
        return ProductVariant::whereNotNull('weight')->pluck('weight');
    }

    public function findByAttributeCombination(int $productId, array $attributeValues): ?ProductVariant
    {
        $query = ProductVariant::where('product_id', $productId);

        // This is a simplified implementation - in a real scenario, you'd need to join with attribute values
        return $query->first();
    }

    public function toggleActive(ProductVariant $variant): bool
    {
        return $variant->update(['is_active' => !$variant->is_active]);
    }

    public function toggleFeatured(ProductVariant $variant): bool
    {
        return $variant->update(['is_featured' => !$variant->is_featured]);
    }

    public function updateStock(ProductVariant $variant, int $quantity): bool
    {
        $variant->stock = $quantity;
        $variant->available_stock = $quantity - $variant->reserved_stock;
        return $variant->save();
    }

    public function reserveStock(ProductVariant $variant, int $quantity): bool
    {
        if ($variant->available_stock >= $quantity) {
            $variant->reserved_stock += $quantity;
            $variant->available_stock -= $quantity;
            return $variant->save();
        }
        return false;
    }

    public function releaseStock(ProductVariant $variant, int $quantity): bool
    {
        if ($variant->reserved_stock >= $quantity) {
            $variant->reserved_stock -= $quantity;
            $variant->available_stock += $quantity;
            return $variant->save();
        }
        return false;
    }

    public function adjustStock(ProductVariant $variant, int $quantity, string $reason = null): bool
    {
        $variant->stock += $quantity;
        $variant->available_stock += $quantity;
        return $variant->save();
    }

    public function getVariantStock(int $variantId): int
    {
        $variant = ProductVariant::find($variantId);
        return $variant ? $variant->stock : 0;
    }

    public function getVariantAvailableStock(int $variantId): int
    {
        $variant = ProductVariant::find($variantId);
        return $variant ? $variant->available_stock : 0;
    }

    public function getVariantReservedStock(int $variantId): int
    {
        $variant = ProductVariant::find($variantId);
        return $variant ? $variant->reserved_stock : 0;
    }

    public function setPrice(ProductVariant $variant, float $price): bool
    {
        return $variant->update(['price' => $price]);
    }

    public function setSalePrice(ProductVariant $variant, float $salePrice): bool
    {
        return $variant->update(['sale_price' => $salePrice]);
    }

    public function setComparePrice(ProductVariant $variant, float $comparePrice): bool
    {
        return $variant->update(['compare_price' => $comparePrice]);
    }

    public function search(string $query): Collection
    {
        return ProductVariant::where(function ($q) use ($query) {
            $q->where('sku', 'like', "%{$query}%")
              ->orWhere('barcode', 'like', "%{$query}%")
              ->orWhereHas('product', function ($productQuery) use ($query) {
                  $productQuery->where('title', 'like', "%{$query}%");
              });
        })
        ->with(['product', 'attributeValues'])
        ->get();
    }

    public function getVariantCount(): int
    {
        return ProductVariant::count();
    }

    public function getVariantAnalytics(int $variantId): array
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return [];
        }

        return [
            'total_sales' => $this->getVariantSales($variantId),
            'total_revenue' => $this->getVariantRevenue($variantId),
            'total_profit' => $this->getVariantProfit($variantId),
            'profit_margin' => $this->getVariantMargin($variantId),
            'stock_level' => $variant->stock,
            'available_stock' => $variant->available_stock,
            'reserved_stock' => $variant->reserved_stock,
        ];
    }

    public function getVariantAnalyticsByProduct(int $productId): array
    {
        $variants = $this->findByProductId($productId);
        $analytics = [];

        foreach ($variants as $variant) {
            $analytics[$variant->id] = $this->getVariantAnalytics($variant->id);
        }

        return $analytics;
    }

    public function getVariantSales(int $variantId): float
    {
        // This would typically query order items to get actual sales data
        // For now, returning a placeholder
        return 0.0;
    }

    public function getVariantRevenue(int $variantId): float
    {
        // This would typically calculate revenue from sales data
        // For now, returning a placeholder
        return 0.0;
    }

    public function getVariantProfit(int $variantId): float
    {
        // This would typically calculate profit from revenue and cost
        // For now, returning a placeholder
        return 0.0;
    }

    public function getVariantMargin(int $variantId): float
    {
        // This would typically calculate profit margin
        // For now, returning a placeholder
        return 0.0;
    }

    public function getVariantInventory(int $variantId): array
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return [];
        }

        return [
            'current_stock' => $variant->stock,
            'available_stock' => $variant->available_stock,
            'reserved_stock' => $variant->reserved_stock,
            'low_stock_threshold' => $variant->low_stock_threshold,
            'is_low_stock' => $variant->stock <= $variant->low_stock_threshold,
            'is_out_of_stock' => $variant->stock <= 0,
        ];
    }

    public function getVariantInventoryHistory(int $variantId): array
    {
        // This would typically query inventory history table
        // For now, returning empty array
        return [];
    }

    public function getVariantInventoryAlerts(int $variantId): array
    {
        $inventory = $this->getVariantInventory($variantId);
        $alerts = [];

        if ($inventory['is_out_of_stock']) {
            $alerts[] = 'Out of stock';
        } elseif ($inventory['is_low_stock']) {
            $alerts[] = 'Low stock';
        }

        return $alerts;
    }

    public function bulkCreate(int $productId, array $variantData): Collection
    {
        $variants = collect();

        foreach ($variantData as $data) {
            $data['product_id'] = $productId;
            $variants->push($this->create($data));
        }

        return $variants;
    }

    public function bulkUpdate(array $variantData): bool
    {
        $success = true;

        foreach ($variantData as $data) {
            if (isset($data['id'])) {
                $variant = ProductVariant::find($data['id']);
                if ($variant) {
                    unset($data['id']);
                    if (!$this->update($variant, $data)) {
                        $success = false;
                    }
                }
            }
        }

        return $success;
    }

    public function bulkDelete(array $variantIds): bool
    {
        return ProductVariant::whereIn('id', $variantIds)->delete() > 0;
    }

    public function importVariants(int $productId, array $variantData): bool
    {
        try {
            DB::beginTransaction();

            // Delete existing variants for the product
            ProductVariant::where('product_id', $productId)->delete();

            // Create new variants
            $this->bulkCreate($productId, $variantData);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function exportVariants(int $productId): array
    {
        $variants = $this->findByProductId($productId);
        $exportData = [];

        foreach ($variants as $variant) {
            $exportData[] = [
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'weight' => $variant->weight,
                'dimensions' => $variant->dimensions,
                'barcode' => $variant->barcode,
                'is_active' => $variant->is_active,
                'is_featured' => $variant->is_featured,
                'sort_order' => $variant->sort_order,
                'cost_price' => $variant->cost_price,
                'sale_price' => $variant->sale_price,
                'compare_price' => $variant->compare_price,
                'inventory_tracking' => $variant->inventory_tracking,
                'low_stock_threshold' => $variant->low_stock_threshold,
            ];
        }

        return $exportData;
    }

    public function syncVariants(int $productId, array $variantData): bool
    {
        try {
            DB::beginTransaction();

            $existingVariants = ProductVariant::where('product_id', $productId)->get();
            $existingSkus = $existingVariants->pluck('sku')->toArray();

            $newVariants = [];
            $updateVariants = [];

            foreach ($variantData as $data) {
                if (in_array($data['sku'], $existingSkus)) {
                    $updateVariants[] = $data;
                } else {
                    $newVariants[] = $data;
                }
            }

            // Create new variants
            if (!empty($newVariants)) {
                $this->bulkCreate($productId, $newVariants);
            }

            // Update existing variants
            if (!empty($updateVariants)) {
                $this->bulkUpdate($updateVariants);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function validateVariant(array $data): bool
    {
        $rules = ProductVariantDTO::rules();

        $validator = validator($data, $rules);

        return !$validator->fails();
    }

    public function isSkuUnique(string $sku, ?int $excludeId = null): bool
    {
        $query = ProductVariant::where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function isBarcodeUnique(string $barcode, ?int $excludeId = null): bool
    {
        $query = ProductVariant::where('barcode', $barcode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function generateSku(int $productId, array $attributeValues): string
    {
        $product = \Fereydooni\Shopping\app\Models\Product::find($productId);
        $baseSku = $product ? $product->sku : 'PROD' . $productId;

        $attributeString = implode('-', array_values($attributeValues));
        $attributeString = Str::upper(Str::slug($attributeString));

        return $baseSku . '-' . $attributeString;
    }
}
