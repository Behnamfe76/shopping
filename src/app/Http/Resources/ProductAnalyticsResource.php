<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProductAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_products' => $this->resource['total_products'] ?? 0,
            'active_products' => $this->resource['active_products'] ?? 0,
            'inactive_products' => $this->resource['inactive_products'] ?? 0,
            'featured_products' => $this->resource['featured_products'] ?? 0,
            'published_products' => $this->resource['published_products'] ?? 0,
            'draft_products' => $this->resource['draft_products'] ?? 0,
            'archived_products' => $this->resource['archived_products'] ?? 0,
            'in_stock_products' => $this->resource['in_stock_products'] ?? 0,
            'low_stock_products' => $this->resource['low_stock_products'] ?? 0,
            'out_of_stock_products' => $this->resource['out_of_stock_products'] ?? 0,
            'total_stock_quantity' => $this->resource['total_stock_quantity'] ?? 0,
            'total_stock_value' => $this->resource['total_stock_value'] ?? 0,
            'average_price' => $this->resource['average_price'] ?? 0,
            'average_rating' => $this->resource['average_rating'] ?? 0,
            'total_views' => $this->resource['total_views'] ?? 0,
            'total_wishlists' => $this->resource['total_wishlists'] ?? 0,
            'total_sales' => $this->resource['total_sales'] ?? 0,
            'total_revenue' => $this->resource['total_revenue'] ?? 0,
            'top_selling_products' => $this->resource['top_selling_products'] ?? [],
            'most_viewed_products' => $this->resource['most_viewed_products'] ?? [],
            'best_rated_products' => $this->resource['best_rated_products'] ?? [],
            'low_stock_alerts' => $this->resource['low_stock_alerts'] ?? [],
            'category_distribution' => $this->resource['category_distribution'] ?? [],
            'brand_distribution' => $this->resource['brand_distribution'] ?? [],
            'status_distribution' => $this->resource['status_distribution'] ?? [],
            'type_distribution' => $this->resource['type_distribution'] ?? [],
            'price_range_distribution' => $this->resource['price_range_distribution'] ?? [],
            'stock_range_distribution' => $this->resource['stock_range_distribution'] ?? [],
            'recent_activity' => $this->resource['recent_activity'] ?? [],
            'performance_metrics' => [
                'conversion_rate' => $this->resource['performance_metrics']['conversion_rate'] ?? 0,
                'average_order_value' => $this->resource['performance_metrics']['average_order_value'] ?? 0,
                'customer_satisfaction' => $this->resource['performance_metrics']['customer_satisfaction'] ?? 0,
                'inventory_turnover' => $this->resource['performance_metrics']['inventory_turnover'] ?? 0,
                'profit_margin' => $this->resource['performance_metrics']['profit_margin'] ?? 0,
            ],
            'trends' => [
                'sales_trend' => $this->resource['trends']['sales_trend'] ?? [],
                'views_trend' => $this->resource['trends']['views_trend'] ?? [],
                'stock_trend' => $this->resource['trends']['stock_trend'] ?? [],
                'rating_trend' => $this->resource['trends']['rating_trend'] ?? [],
            ],
            'generated_at' => Carbon::now()->toISOString(),
        ];
    }
}
