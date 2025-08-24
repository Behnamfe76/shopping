<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderPerformanceStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'summary' => [
                'total_providers' => $this->resource['total_providers'] ?? 0,
                'total_performances' => $this->resource['total_performances'] ?? 0,
                'verified_performances' => $this->resource['verified_performances'] ?? 0,
                'unverified_performances' => $this->resource['unverified_performances'] ?? 0,
            ],
            'averages' => [
                'performance_score' => $this->resource['avg_performance_score'] ?? 0,
                'total_revenue' => $this->resource['avg_total_revenue'] ?? 0,
                'total_orders' => $this->resource['avg_total_orders'] ?? 0,
                'customer_satisfaction' => $this->resource['avg_customer_satisfaction'] ?? 0,
                'on_time_delivery_rate' => $this->resource['avg_on_time_delivery_rate'] ?? 0,
                'return_rate' => $this->resource['avg_return_rate'] ?? 0,
                'defect_rate' => $this->resource['avg_defect_rate'] ?? 0,
            ],
            'grade_distribution' => $this->resource['grade_distribution'] ?? [],
            'period_distribution' => $this->resource['period_distribution'] ?? [],
            'trends' => [
                'performance_trend' => $this->resource['performance_trend'] ?? [],
                'revenue_trend' => $this->resource['revenue_trend'] ?? [],
                'satisfaction_trend' => $this->resource['satisfaction_trend'] ?? [],
            ],
            'benchmarks' => [
                'top_performers' => $this->resource['top_performers'] ?? [],
                'industry_averages' => $this->resource['industry_averages'] ?? [],
                'improvement_opportunities' => $this->resource['improvement_opportunities'] ?? [],
            ],
            'alerts' => [
                'critical_alerts' => $this->resource['critical_alerts'] ?? [],
                'warning_alerts' => $this->resource['warning_alerts'] ?? [],
                'info_alerts' => $this->resource['info_alerts'] ?? [],
            ],
            'generated_at' => now()->toISOString(),
        ];
    }
}
