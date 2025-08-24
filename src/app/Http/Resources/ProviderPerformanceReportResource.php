<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderPerformanceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'report_info' => [
                'report_type' => $this->resource['report_type'] ?? 'general',
                'generated_at' => $this->resource['generated_at'] ?? now()->toISOString(),
                'period' => [
                    'start' => $this->resource['period_start'] ?? null,
                    'end' => $this->resource['period_end'] ?? null,
                ],
                'scope' => $this->resource['scope'] ?? 'all_providers',
            ],
            'executive_summary' => [
                'overall_performance' => $this->resource['overall_performance'] ?? 'N/A',
                'key_highlights' => $this->resource['key_highlights'] ?? [],
                'critical_issues' => $this->resource['critical_issues'] ?? [],
                'recommendations' => $this->resource['recommendations'] ?? [],
            ],
            'performance_metrics' => [
                'average_score' => $this->resource['average_score'] ?? 0,
                'grade_distribution' => $this->resource['grade_distribution'] ?? [],
                'top_performers' => $this->resource['top_performers'] ?? [],
                'bottom_performers' => $this->resource['bottom_performers'] ?? [],
            ],
            'financial_metrics' => [
                'total_revenue' => $this->resource['total_revenue'] ?? 0,
                'average_revenue' => $this->resource['average_revenue'] ?? 0,
                'revenue_trend' => $this->resource['revenue_trend'] ?? [],
                'cost_efficiency' => $this->resource['cost_efficiency'] ?? [],
            ],
            'operational_metrics' => [
                'delivery_performance' => $this->resource['delivery_performance'] ?? [],
                'quality_metrics' => $this->resource['quality_metrics'] ?? [],
                'customer_satisfaction' => $this->resource['customer_satisfaction'] ?? [],
                'response_times' => $this->resource['response_times'] ?? [],
            ],
            'trend_analysis' => [
                'performance_trends' => $this->resource['performance_trends'] ?? [],
                'seasonal_patterns' => $this->resource['seasonal_patterns'] ?? [],
                'improvement_areas' => $this->resource['improvement_areas'] ?? [],
            ],
            'benchmarking' => [
                'industry_comparison' => $this->resource['industry_comparison'] ?? [],
                'best_practices' => $this->resource['best_practices'] ?? [],
                'competitive_analysis' => $this->resource['competitive_analysis'] ?? [],
            ],
            'charts_data' => [
                'performance_chart' => $this->resource['performance_chart'] ?? [],
                'revenue_chart' => $this->resource['revenue_chart'] ?? [],
                'grade_distribution_chart' => $this->resource['grade_distribution_chart'] ?? [],
                'trend_chart' => $this->resource['trend_chart'] ?? [],
            ],
            'detailed_data' => $this->resource['detailed_data'] ?? [],
        ];
    }
}
