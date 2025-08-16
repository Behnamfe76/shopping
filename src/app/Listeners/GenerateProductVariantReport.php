<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\Events\ProductVariantCreated;
use Fereydooni\Shopping\app\Events\ProductVariantUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantDeleted;
use Fereydooni\Shopping\app\Events\ProductVariantStatusChanged;
use Fereydooni\Shopping\app\Events\ProductVariantStockUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantPriceUpdated;

class GenerateProductVariantReport implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $reportData = $this->generateReportData($event);
            $this->saveReport($reportData, $event);
            $this->logReportGeneration($event, $reportData);
        } catch (\Exception $e) {
            Log::error('Failed to generate product variant report', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Generate report data based on the event type
     */
    private function generateReportData($event): array
    {
        $variant = $event->variant ?? null;
        $eventType = $this->getEventType($event);
        $timestamp = now();

        $baseData = [
            'event_type' => $eventType,
            'timestamp' => $timestamp->toISOString(),
            'variant_id' => $variant?->id,
            'product_id' => $variant?->product_id,
            'sku' => $variant?->sku,
            'barcode' => $variant?->barcode,
        ];

        switch ($eventType) {
            case 'created':
                return array_merge($baseData, $this->getCreationReportData($variant));

            case 'updated':
                return array_merge($baseData, $this->getUpdateReportData($event, $variant));

            case 'deleted':
                return array_merge($baseData, $this->getDeletionReportData($variant));

            case 'status_changed':
                return array_merge($baseData, $this->getStatusChangeReportData($event, $variant));

            case 'stock_updated':
                return array_merge($baseData, $this->getStockUpdateReportData($event, $variant));

            case 'price_updated':
                return array_merge($baseData, $this->getPriceUpdateReportData($event, $variant));

            default:
                return $baseData;
        }
    }

    /**
     * Get event type from event class
     */
    private function getEventType($event): string
    {
        $className = class_basename($event);

        if (str_contains($className, 'Created')) return 'created';
        if (str_contains($className, 'Updated')) return 'updated';
        if (str_contains($className, 'Deleted')) return 'deleted';
        if (str_contains($className, 'StatusChanged')) return 'status_changed';
        if (str_contains($className, 'StockUpdated')) return 'stock_updated';
        if (str_contains($className, 'PriceUpdated')) return 'price_updated';

        return 'unknown';
    }

    /**
     * Get creation report data
     */
    private function getCreationReportData(?ProductVariant $variant): array
    {
        if (!$variant) return [];

        return [
            'action' => 'variant_created',
            'initial_data' => [
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'compare_price' => $variant->compare_price,
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'available_stock' => $variant->available_stock,
                'is_active' => $variant->is_active,
                'is_featured' => $variant->is_featured,
                'weight' => $variant->weight,
                'dimensions' => $variant->dimensions,
                'inventory_tracking' => $variant->inventory_tracking,
                'low_stock_threshold' => $variant->low_stock_threshold,
            ],
            'analytics' => [
                'total_variants_count' => $this->getTotalVariantsCount($variant->product_id),
                'product_variants_count' => $this->getProductVariantsCount($variant->product_id),
                'active_variants_count' => $this->getActiveVariantsCount($variant->product_id),
                'in_stock_variants_count' => $this->getInStockVariantsCount($variant->product_id),
            ]
        ];
    }

    /**
     * Get update report data
     */
    private function getUpdateReportData($event, ?ProductVariant $variant): array
    {
        if (!$variant) return [];

        $changes = $variant->getChanges();
        $original = $variant->getOriginal();

        return [
            'action' => 'variant_updated',
            'changes' => $changes,
            'original_values' => array_intersect_key($original, $changes),
            'current_data' => [
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'compare_price' => $variant->compare_price,
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'available_stock' => $variant->available_stock,
                'is_active' => $variant->is_active,
                'is_featured' => $variant->is_featured,
            ],
            'analytics' => [
                'total_variants_count' => $this->getTotalVariantsCount($variant->product_id),
                'product_variants_count' => $this->getProductVariantsCount($variant->product_id),
                'active_variants_count' => $this->getActiveVariantsCount($variant->product_id),
                'in_stock_variants_count' => $this->getInStockVariantsCount($variant->product_id),
            ]
        ];
    }

    /**
     * Get deletion report data
     */
    private function getDeletionReportData(?ProductVariant $variant): array
    {
        if (!$variant) return [];

        return [
            'action' => 'variant_deleted',
            'deleted_data' => [
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'compare_price' => $variant->compare_price,
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'available_stock' => $variant->available_stock,
                'is_active' => $variant->is_active,
                'is_featured' => $variant->is_featured,
                'weight' => $variant->weight,
                'dimensions' => $variant->dimensions,
                'inventory_tracking' => $variant->inventory_tracking,
                'low_stock_threshold' => $variant->low_stock_threshold,
            ],
            'analytics' => [
                'total_variants_count' => $this->getTotalVariantsCount($variant->product_id),
                'product_variants_count' => $this->getProductVariantsCount($variant->product_id),
                'active_variants_count' => $this->getActiveVariantsCount($variant->product_id),
                'in_stock_variants_count' => $this->getInStockVariantsCount($variant->product_id),
            ]
        ];
    }

    /**
     * Get status change report data
     */
    private function getStatusChangeReportData($event, ?ProductVariant $variant): array
    {
        if (!$variant) return [];

        $oldStatus = $event->oldStatus ?? null;
        $newStatus = $event->newStatus ?? null;

        return [
            'action' => 'status_changed',
            'status_change' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now()->toISOString(),
            ],
            'current_data' => [
                'is_active' => $variant->is_active,
                'is_featured' => $variant->is_featured,
            ],
            'analytics' => [
                'active_variants_count' => $this->getActiveVariantsCount($variant->product_id),
                'featured_variants_count' => $this->getFeaturedVariantsCount($variant->product_id),
            ]
        ];
    }

    /**
     * Get stock update report data
     */
    private function getStockUpdateReportData($event, ?ProductVariant $variant): array
    {
        if (!$variant) return [];

        $oldStock = $event->oldStock ?? null;
        $newStock = $event->newStock ?? null;
        $quantity = $event->quantity ?? null;
        $reason = $event->reason ?? null;

        return [
            'action' => 'stock_updated',
            'stock_change' => [
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'quantity' => $quantity,
                'reason' => $reason,
                'changed_at' => now()->toISOString(),
            ],
            'current_data' => [
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'available_stock' => $variant->available_stock,
                'low_stock_threshold' => $variant->low_stock_threshold,
            ],
            'analytics' => [
                'in_stock_variants_count' => $this->getInStockVariantsCount($variant->product_id),
                'out_of_stock_variants_count' => $this->getOutOfStockVariantsCount($variant->product_id),
                'low_stock_variants_count' => $this->getLowStockVariantsCount($variant->product_id),
            ]
        ];
    }

    /**
     * Get price update report data
     */
    private function getPriceUpdateReportData($event, ?ProductVariant $variant): array
    {
        if (!$variant) return [];

        $oldPrice = $event->oldPrice ?? null;
        $newPrice = $event->newPrice ?? null;
        $priceType = $event->priceType ?? 'base';

        return [
            'action' => 'price_updated',
            'price_change' => [
                'price_type' => $priceType,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'price_difference' => $newPrice - $oldPrice,
                'percentage_change' => $oldPrice > 0 ? (($newPrice - $oldPrice) / $oldPrice) * 100 : 0,
                'changed_at' => now()->toISOString(),
            ],
            'current_data' => [
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'compare_price' => $variant->compare_price,
            ],
            'analytics' => [
                'on_sale_variants_count' => $this->getOnSaleVariantsCount($variant->product_id),
                'average_price' => $this->getAveragePrice($variant->product_id),
                'price_range' => $this->getPriceRange($variant->product_id),
            ]
        ];
    }

    /**
     * Save the report to storage
     */
    private function saveReport(array $reportData, $event): void
    {
        $variant = $event->variant ?? null;
        $productId = $variant?->product_id ?? 'unknown';
        $variantId = $variant?->id ?? 'unknown';
        $timestamp = now();

        $filename = "product_variants/reports/{$productId}/variant_{$variantId}_{$timestamp->format('Y-m-d_H-i-s')}.json";

        Storage::disk('local')->put($filename, json_encode($reportData, JSON_PRETTY_PRINT));

        Log::info("Product variant report saved", [
            'filename' => $filename,
            'variant_id' => $variantId,
            'product_id' => $productId
        ]);
    }

    /**
     * Log report generation
     */
    private function logReportGeneration($event, array $reportData): void
    {
        $variant = $event->variant ?? null;

        Log::info("Product variant report generated", [
            'event_type' => $reportData['event_type'] ?? 'unknown',
            'variant_id' => $variant?->id,
            'product_id' => $variant?->product_id,
            'sku' => $variant?->sku,
            'action' => $reportData['action'] ?? 'unknown',
            'timestamp' => $reportData['timestamp'] ?? now()->toISOString(),
        ]);
    }

    // Analytics helper methods
    private function getTotalVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->count();
    }

    private function getProductVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->count();
    }

    private function getActiveVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->where('is_active', true)->count();
    }

    private function getInStockVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->where('stock', '>', 0)->count();
    }

    private function getOutOfStockVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->where('stock', '<=', 0)->count();
    }

    private function getLowStockVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)
            ->whereRaw('stock <= low_stock_threshold')
            ->where('stock', '>', 0)
            ->count();
    }

    private function getFeaturedVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)->where('is_featured', true)->count();
    }

    private function getOnSaleVariantsCount(int $productId): int
    {
        return ProductVariant::where('product_id', $productId)
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->whereRaw('sale_price < price')
            ->count();
    }

    private function getAveragePrice(int $productId): float
    {
        return ProductVariant::where('product_id', $productId)->avg('price') ?? 0;
    }

    private function getPriceRange(int $productId): array
    {
        $prices = ProductVariant::where('product_id', $productId)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min_price' => $prices->min_price ?? 0,
            'max_price' => $prices->max_price ?? 0,
        ];
    }
}
