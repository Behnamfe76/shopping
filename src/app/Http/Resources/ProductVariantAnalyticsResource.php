<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'variant_id' => $this->resource['variant_id'] ?? null,
            'total_sales' => $this->resource['total_sales'] ?? 0.0,
            'total_revenue' => $this->resource['total_revenue'] ?? 0.0,
            'total_profit' => $this->resource['total_profit'] ?? 0.0,
            'profit_margin' => $this->resource['profit_margin'] ?? 0.0,
            'stock_level' => $this->resource['stock_level'] ?? 0,
            'available_stock' => $this->resource['available_stock'] ?? 0,
            'reserved_stock' => $this->resource['reserved_stock'] ?? 0,
            'stock_turnover_rate' => $this->resource['stock_turnover_rate'] ?? 0.0,
            'average_order_value' => $this->resource['average_order_value'] ?? 0.0,
            'conversion_rate' => $this->resource['conversion_rate'] ?? 0.0,
            'inventory_alerts' => $this->resource['inventory_alerts'] ?? [],
            'performance_metrics' => [
                'sales_velocity' => $this->resource['sales_velocity'] ?? 0.0,
                'profit_margin_percentage' => $this->resource['profit_margin_percentage'] ?? 0.0,
                'stock_efficiency' => $this->resource['stock_efficiency'] ?? 0.0,
                'revenue_per_unit' => $this->resource['revenue_per_unit'] ?? 0.0,
            ],
            'trends' => [
                'sales_trend' => $this->resource['sales_trend'] ?? 'stable',
                'stock_trend' => $this->resource['stock_trend'] ?? 'stable',
                'profit_trend' => $this->resource['profit_trend'] ?? 'stable',
            ],
            'recommendations' => $this->resource['recommendations'] ?? [],
            'generated_at' => now()->toISOString(),
        ];
    }
}
