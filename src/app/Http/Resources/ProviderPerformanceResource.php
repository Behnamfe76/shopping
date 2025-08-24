<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;

class ProviderPerformanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'period_start' => $this->period_start?->toDateString(),
            'period_end' => $this->period_end?->toDateString(),
            'period_type' => $this->period_type?->value,
            'period_type_label' => $this->period_type?->getLabel(),
            'period_type_description' => $this->period_type?->getDescription(),
            'total_orders' => $this->total_orders,
            'total_revenue' => $this->total_revenue,
            'average_order_value' => $this->average_order_value,
            'on_time_delivery_rate' => $this->on_time_delivery_rate,
            'return_rate' => $this->return_rate,
            'defect_rate' => $this->defect_rate,
            'customer_satisfaction_score' => $this->customer_satisfaction_score,
            'response_time_avg' => $this->response_time_avg,
            'quality_rating' => $this->quality_rating,
            'delivery_rating' => $this->delivery_rating,
            'communication_rating' => $this->communication_rating,
            'cost_efficiency_score' => $this->cost_efficiency_score,
            'inventory_turnover_rate' => $this->inventory_turnover_rate,
            'lead_time_avg' => $this->lead_time_avg,
            'fill_rate' => $this->fill_rate,
            'accuracy_rate' => $this->accuracy_rate,
            'performance_score' => $this->performance_score,
            'performance_grade' => $this->performance_grade?->value,
            'performance_grade_description' => $this->performance_grade?->getDescription(),
            'performance_grade_color' => $this->performance_grade?->getColor(),
            'performance_grade_score_range' => $this->performance_grade?->getScoreRange(),
            'is_verified' => $this->is_verified,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at?->toISOString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];

        // Add calculated metrics
        $data['calculated_metrics'] = $this->getCalculatedMetrics();

        // Add performance analysis
        $data['performance_analysis'] = $this->getPerformanceAnalysis();

        // Add alerts and suggestions
        $data['alerts'] = $this->getPerformanceAlerts();
        $data['improvement_suggestions'] = $this->getImprovementSuggestions();

        // Add benchmark comparison
        $data['benchmark_comparison'] = $this->getBenchmarkComparison();

        // Add performance trend
        $data['performance_trend'] = $this->getPerformanceTrend();

        // Add efficiency score
        $data['efficiency_score'] = $this->getEfficiencyScore();

        // Add revenue per order
        $data['revenue_per_order'] = $this->getRevenuePerOrder();

        // Add period duration
        $data['period_duration_days'] = $this->getPeriodDuration();

        // Add performance status
        $data['performance_status'] = $this->getPerformanceStatus();

        // Add relationships if loaded
        if ($this->relationLoaded('provider')) {
            $data['provider'] = [
                'id' => $this->provider->id,
                'name' => $this->provider->name,
                'email' => $this->provider->email,
                'phone' => $this->provider->phone,
                'status' => $this->provider->status,
            ];
        }

        if ($this->relationLoaded('verifier')) {
            $data['verifier'] = [
                'id' => $this->verifier->id,
                'name' => $this->verifier->name,
                'email' => $this->verifier->email,
            ];
        }

        // Add conditional fields based on request parameters
        if ($request->boolean('include_metrics')) {
            $data['detailed_metrics'] = $this->getDetailedMetrics();
        }

        if ($request->boolean('include_history')) {
            $data['performance_history'] = $this->getPerformanceHistory();
        }

        if ($request->boolean('include_comparison')) {
            $data['performance_comparison'] = $this->getPerformanceComparison();
        }

        if ($request->boolean('include_forecast')) {
            $data['performance_forecast'] = $this->getPerformanceForecast();
        }

        // Add meta information
        $data['meta'] = [
            'is_high_performer' => $this->isHighPerformer(),
            'is_low_performer' => $this->isLowPerformer(),
            'needs_attention' => $this->needsAttention(),
            'can_be_verified' => !$this->is_verified,
            'can_be_unverified' => $this->is_verified,
            'can_be_recalculated' => true,
            'last_updated' => $this->updated_at?->diffForHumans(),
            'created_ago' => $this->created_at?->diffForHumans(),
        ];

        return $data;
    }

    /**
     * Get calculated metrics
     */
    protected function getCalculatedMetrics(): array
    {
        return [
            'revenue_per_order' => $this->getRevenuePerOrder(),
            'efficiency_score' => $this->getEfficiencyScore(),
            'overall_rating' => $this->calculateOverallRating(),
            'performance_trend' => $this->getPerformanceTrend(),
            'grade_improvement_potential' => $this->getGradeImprovementPotential(),
        ];
    }

    /**
     * Get performance analysis
     */
    protected function getPerformanceAnalysis(): array
    {
        return [
            'score_breakdown' => [
                'delivery_performance' => $this->on_time_delivery_rate * 0.20,
                'customer_satisfaction' => ($this->customer_satisfaction_score / 10) * 100 * 0.25,
                'quality_performance' => $this->quality_rating * 10 * 0.20,
                'delivery_quality' => $this->delivery_rating * 10 * 0.15,
                'communication_quality' => $this->communication_rating * 10 * 0.10,
                'cost_efficiency' => $this->cost_efficiency_score * 0.10,
            ],
            'strengths' => $this->getStrengths(),
            'weaknesses' => $this->getWeaknesses(),
            'opportunities' => $this->getOpportunities(),
            'threats' => $this->getThreats(),
        ];
    }

    /**
     * Get detailed metrics
     */
    protected function getDetailedMetrics(): array
    {
        return [
            'financial_metrics' => [
                'total_revenue' => $this->total_revenue,
                'average_order_value' => $this->average_order_value,
                'revenue_per_order' => $this->getRevenuePerOrder(),
                'cost_efficiency_score' => $this->cost_efficiency_score,
            ],
            'operational_metrics' => [
                'total_orders' => $this->total_orders,
                'on_time_delivery_rate' => $this->on_time_delivery_rate,
                'fill_rate' => $this->fill_rate,
                'accuracy_rate' => $this->accuracy_rate,
            ],
            'quality_metrics' => [
                'quality_rating' => $this->quality_rating,
                'defect_rate' => $this->defect_rate,
                'return_rate' => $this->return_rate,
                'customer_satisfaction_score' => $this->customer_satisfaction_score,
            ],
            'efficiency_metrics' => [
                'response_time_avg' => $this->response_time_avg,
                'lead_time_avg' => $this->lead_time_avg,
                'inventory_turnover_rate' => $this->inventory_turnover_rate,
                'efficiency_score' => $this->getEfficiencyScore(),
            ],
        ];
    }

    /**
     * Get performance history
     */
    protected function getPerformanceHistory(): array
    {
        // This would typically fetch historical data
        // For now, return basic structure
        return [
            'previous_periods' => [],
            'trend_analysis' => [],
            'seasonal_patterns' => [],
        ];
    }

    /**
     * Get performance comparison
     */
    protected function getPerformanceComparison(): array
    {
        // This would typically compare with other providers or benchmarks
        return [
            'industry_average' => [],
            'peer_comparison' => [],
            'benchmark_analysis' => [],
        ];
    }

    /**
     * Get performance forecast
     */
    protected function getPerformanceForecast(): array
    {
        // This would typically provide forecasting data
        return [
            'next_period_prediction' => [],
            'trend_forecast' => [],
            'risk_assessment' => [],
        ];
    }

    /**
     * Calculate overall rating
     */
    protected function calculateOverallRating(): float
    {
        $ratings = [
            $this->quality_rating,
            $this->delivery_rating,
            $this->communication_rating,
        ];

        return round(array_sum($ratings) / count($ratings), 2);
    }

    /**
     * Get grade improvement potential
     */
    protected function getGradeImprovementPotential(): array
    {
        $currentScore = $this->performance_score;
        $currentGrade = $this->performance_grade;

        $nextGrade = match($currentGrade) {
            PerformanceGrade::F => PerformanceGrade::D,
            PerformanceGrade::D => PerformanceGrade::C,
            PerformanceGrade::C => PerformanceGrade::B,
            PerformanceGrade::B => PerformanceGrade::A,
            PerformanceGrade::A => null,
        };

        if (!$nextGrade) {
            return [
                'can_improve' => false,
                'next_grade' => null,
                'points_needed' => 0,
                'improvement_percentage' => 0,
            ];
        }

        $nextGradeRange = $nextGrade->getScoreRange();
        $pointsNeeded = $nextGradeRange[0] - $currentScore;
        $improvementPercentage = ($pointsNeeded / $currentScore) * 100;

        return [
            'can_improve' => true,
            'next_grade' => $nextGrade->value,
            'next_grade_description' => $nextGrade->getDescription(),
            'points_needed' => max(0, $pointsNeeded),
            'improvement_percentage' => round($improvementPercentage, 2),
        ];
    }

    /**
     * Get strengths
     */
    protected function getStrengths(): array
    {
        $strengths = [];

        if ($this->on_time_delivery_rate >= 95) {
            $strengths[] = 'Excellent on-time delivery performance';
        }

        if ($this->customer_satisfaction_score >= 8.5) {
            $strengths[] = 'High customer satisfaction';
        }

        if ($this->quality_rating >= 8.5) {
            $strengths[] = 'Superior quality standards';
        }

        if ($this->return_rate <= 2) {
            $strengths[] = 'Low return rate';
        }

        if ($this->defect_rate <= 1) {
            $strengths[] = 'Minimal defect rate';
        }

        return $strengths;
    }

    /**
     * Get weaknesses
     */
    protected function getWeaknesses(): array
    {
        $weaknesses = [];

        if ($this->on_time_delivery_rate < 90) {
            $weaknesses[] = 'Below target delivery performance';
        }

        if ($this->customer_satisfaction_score < 7.0) {
            $weaknesses[] = 'Customer satisfaction needs improvement';
        }

        if ($this->quality_rating < 7.5) {
            $weaknesses[] = 'Quality standards below expectations';
        }

        if ($this->return_rate > 5) {
            $weaknesses[] = 'High return rate';
        }

        if ($this->defect_rate > 3) {
            $weaknesses[] = 'Defect rate above acceptable levels';
        }

        return $weaknesses;
    }

    /**
     * Get opportunities
     */
    protected function getOpportunities(): array
    {
        $opportunities = [];

        if ($this->on_time_delivery_rate < 95) {
            $opportunities[] = 'Improve delivery processes to achieve 95%+ on-time rate';
        }

        if ($this->customer_satisfaction_score < 8.5) {
            $opportunities[] = 'Enhance customer service to reach 8.5+ satisfaction score';
        }

        if ($this->quality_rating < 8.5) {
            $opportunities[] = 'Implement quality improvement initiatives';
        }

        if ($this->cost_efficiency_score < 85) {
            $opportunities[] = 'Optimize cost management processes';
        }

        return $opportunities;
    }

    /**
     * Get threats
     */
    protected function getThreats(): array
    {
        $threats = [];

        if ($this->performance_score < 70) {
            $threats[] = 'Risk of performance grade downgrade';
        }

        if ($this->customer_satisfaction_score < 6.0) {
            $threats[] = 'Risk of customer dissatisfaction and churn';
        }

        if ($this->return_rate > 8) {
            $threats[] = 'High return rate may impact profitability';
        }

        if ($this->defect_rate > 5) {
            $threats[] = 'High defect rate may affect reputation';
        }

        return $threats;
    }

    /**
     * Get performance status
     */
    protected function getPerformanceStatus(): array
    {
        return [
            'status' => $this->getStatusLevel(),
            'priority' => $this->getPriorityLevel(),
            'risk_level' => $this->getRiskLevel(),
            'action_required' => $this->needsAttention(),
        ];
    }

    /**
     * Get status level
     */
    protected function getStatusLevel(): string
    {
        if ($this->performance_score >= 90) return 'excellent';
        if ($this->performance_score >= 80) return 'good';
        if ($this->performance_score >= 70) return 'fair';
        if ($this->performance_score >= 60) return 'poor';
        return 'critical';
    }

    /**
     * Get priority level
     */
    protected function getPriorityLevel(): string
    {
        if ($this->performance_score < 60) return 'high';
        if ($this->performance_score < 70) return 'medium';
        if ($this->performance_score < 80) return 'low';
        return 'none';
    }

    /**
     * Get risk level
     */
    protected function getRiskLevel(): string
    {
        if ($this->performance_score < 60) return 'high';
        if ($this->performance_score < 70) return 'medium';
        if ($this->performance_score < 80) return 'low';
        return 'minimal';
    }
}
