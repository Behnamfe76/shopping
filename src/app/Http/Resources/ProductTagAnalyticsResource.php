<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTagAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'tag_id' => $this->tag_id ?? null,
            'usage_count' => $this->usage_count ?? 0,
            'is_active' => $this->is_active ?? false,
            'is_featured' => $this->is_featured ?? false,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,

            // Usage statistics
            'usage_stats' => [
                'total_usage' => $this->usage_count ?? 0,
                'average_usage_per_product' => $this->average_usage_per_product ?? 0,
                'usage_trend' => $this->usage_trend ?? 'stable',
                'popularity_rank' => $this->popularity_rank ?? 0,
            ],

            // Performance metrics
            'performance_metrics' => [
                'performance_score' => $this->performance_score ?? 0,
                'roi' => $this->roi ?? 0.0,
                'conversion_rate' => $this->conversion_rate ?? 0.0,
                'average_order_value' => $this->average_order_value ?? 0.0,
                'customer_retention' => $this->customer_retention ?? 0.0,
            ],

            // Product analytics
            'product_analytics' => [
                'total_products' => $this->total_products ?? 0,
                'active_products' => $this->active_products ?? 0,
                'featured_products' => $this->featured_products ?? 0,
                'average_product_rating' => $this->average_product_rating ?? 0.0,
            ],

            // Time-based analytics
            'time_analytics' => [
                'created_date' => $this->created_at ? date('Y-m-d', strtotime($this->created_at)) : null,
                'last_updated' => $this->updated_at ? date('Y-m-d', strtotime($this->updated_at)) : null,
                'days_since_creation' => $this->days_since_creation ?? 0,
                'days_since_update' => $this->days_since_update ?? 0,
            ],

            // Trends data
            'trends' => [
                'usage_growth' => $this->usage_growth ?? 0.0,
                'popularity_change' => $this->popularity_change ?? 0.0,
                'trend_direction' => $this->trend_direction ?? 'stable',
                'forecast' => $this->forecast ?? [],
            ],

            // Comparison data
            'comparison' => [
                'vs_average_usage' => $this->vs_average_usage ?? 0.0,
                'vs_top_performers' => $this->vs_top_performers ?? 0.0,
                'percentile_rank' => $this->percentile_rank ?? 0,
            ],

            // Recommendations
            'recommendations' => [
                'suggested_actions' => $this->suggested_actions ?? [],
                'optimization_tips' => $this->optimization_tips ?? [],
                'potential_improvements' => $this->potential_improvements ?? [],
            ],

            // Metadata
            'metadata' => [
                'analysis_date' => now()->toISOString(),
                'data_freshness' => $this->data_freshness ?? 'realtime',
                'confidence_level' => $this->confidence_level ?? 0.95,
            ],
        ];
    }
}
