<?php

namespace App\DTOs;

use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use App\Models\Provider;
use App\Models\User;
use Spatie\LaravelData\Data;

class ProviderPerformanceDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $provider_id,
        public string $period_start,
        public string $period_end,
        public PeriodType $period_type,
        public int $total_orders,
        public float $total_revenue,
        public float $average_order_value,
        public float $on_time_delivery_rate,
        public float $return_rate,
        public float $defect_rate,
        public float $customer_satisfaction_score,
        public float $response_time_avg,
        public float $quality_rating,
        public float $delivery_rating,
        public float $communication_rating,
        public float $cost_efficiency_score,
        public float $inventory_turnover_rate,
        public float $lead_time_avg,
        public float $fill_rate,
        public float $accuracy_rate,
        public float $performance_score,
        public PerformanceGrade $performance_grade,
        public bool $is_verified,
        public ?int $verified_by,
        public ?string $verified_at,
        public ?string $notes,
        public ?string $created_at,
        public ?string $updated_at,
        public ?Provider $provider,
        public ?User $verifier
    ) {}

    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'period_type' => ['required', 'string', 'in:'.implode(',', PeriodType::values())],
            'total_orders' => ['required', 'integer', 'min:0'],
            'total_revenue' => ['required', 'numeric', 'min:0'],
            'average_order_value' => ['required', 'numeric', 'min:0'],
            'on_time_delivery_rate' => ['required', 'numeric', 'between:0,100'],
            'return_rate' => ['required', 'numeric', 'between:0,100'],
            'defect_rate' => ['required', 'numeric', 'between:0,100'],
            'customer_satisfaction_score' => ['required', 'numeric', 'between:1,10'],
            'response_time_avg' => ['required', 'numeric', 'min:0'],
            'quality_rating' => ['required', 'numeric', 'between:1,10'],
            'delivery_rating' => ['required', 'numeric', 'between:1,10'],
            'communication_rating' => ['required', 'numeric', 'between:1,10'],
            'cost_efficiency_score' => ['required', 'numeric', 'between:0,100'],
            'inventory_turnover_rate' => ['required', 'numeric', 'min:0'],
            'lead_time_avg' => ['required', 'numeric', 'min:0'],
            'fill_rate' => ['required', 'numeric', 'between:0,100'],
            'accuracy_rate' => ['required', 'numeric', 'between:0,100'],
            'performance_score' => ['required', 'numeric', 'between:0,100'],
            'performance_grade' => ['required', 'string', 'in:'.implode(',', PerformanceGrade::values())],
            'is_verified' => ['boolean'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'verified_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'period_start.required' => 'Period start date is required.',
            'period_start.date' => 'Period start must be a valid date.',
            'period_end.required' => 'Period end date is required.',
            'period_end.date' => 'Period end must be a valid date.',
            'period_end.after' => 'Period end must be after period start.',
            'period_type.required' => 'Period type is required.',
            'period_type.in' => 'Invalid period type selected.',
            'total_orders.required' => 'Total orders is required.',
            'total_orders.min' => 'Total orders cannot be negative.',
            'total_revenue.required' => 'Total revenue is required.',
            'total_revenue.min' => 'Total revenue cannot be negative.',
            'average_order_value.required' => 'Average order value is required.',
            'average_order_value.min' => 'Average order value cannot be negative.',
            'on_time_delivery_rate.required' => 'On-time delivery rate is required.',
            'on_time_delivery_rate.between' => 'On-time delivery rate must be between 0 and 100.',
            'return_rate.required' => 'Return rate is required.',
            'return_rate.between' => 'Return rate must be between 0 and 100.',
            'defect_rate.required' => 'Defect rate is required.',
            'defect_rate.between' => 'Defect rate must be between 0 and 100.',
            'customer_satisfaction_score.required' => 'Customer satisfaction score is required.',
            'customer_satisfaction_score.between' => 'Customer satisfaction score must be between 1 and 10.',
            'response_time_avg.required' => 'Average response time is required.',
            'response_time_avg.min' => 'Average response time cannot be negative.',
            'quality_rating.required' => 'Quality rating is required.',
            'quality_rating.between' => 'Quality rating must be between 1 and 10.',
            'delivery_rating.required' => 'Delivery rating is required.',
            'delivery_rating.between' => 'Delivery rating must be between 1 and 10.',
            'communication_rating.required' => 'Communication rating is required.',
            'communication_rating.between' => 'Communication rating must be between 1 and 10.',
            'cost_efficiency_score.required' => 'Cost efficiency score is required.',
            'cost_efficiency_score.between' => 'Cost efficiency score must be between 0 and 100.',
            'inventory_turnover_rate.required' => 'Inventory turnover rate is required.',
            'inventory_turnover_rate.min' => 'Inventory turnover rate cannot be negative.',
            'lead_time_avg.required' => 'Average lead time is required.',
            'lead_time_avg.min' => 'Average lead time cannot be negative.',
            'fill_rate.required' => 'Fill rate is required.',
            'fill_rate.between' => 'Fill rate must be between 0 and 100.',
            'accuracy_rate.required' => 'Accuracy rate is required.',
            'accuracy_rate.between' => 'Accuracy rate must be between 0 and 100.',
            'performance_score.required' => 'Performance score is required.',
            'performance_score.between' => 'Performance score must be between 0 and 100.',
            'performance_grade.required' => 'Performance grade is required.',
            'performance_grade.in' => 'Invalid performance grade selected.',
            'verified_by.exists' => 'Selected verifier does not exist.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public static function fromModel($model): static
    {
        return new static(
            id: $model->id,
            provider_id: $model->provider_id,
            period_start: $model->period_start,
            period_end: $model->period_end,
            period_type: $model->period_type,
            total_orders: $model->total_orders,
            total_revenue: $model->total_revenue,
            average_order_value: $model->average_order_value,
            on_time_delivery_rate: $model->on_time_delivery_rate,
            return_rate: $model->return_rate,
            defect_rate: $model->defect_rate,
            customer_satisfaction_score: $model->customer_satisfaction_score,
            response_time_avg: $model->response_time_avg,
            quality_rating: $model->quality_rating,
            delivery_rating: $model->delivery_rating,
            communication_rating: $model->communication_rating,
            cost_efficiency_score: $model->cost_efficiency_score,
            inventory_turnover_rate: $model->inventory_turnover_rate,
            lead_time_avg: $model->lead_time_avg,
            fill_rate: $model->fill_rate,
            accuracy_rate: $model->accuracy_rate,
            performance_score: $model->performance_score,
            performance_grade: $model->performance_grade,
            is_verified: $model->is_verified,
            verified_by: $model->verified_by,
            verified_at: $model->verified_at,
            notes: $model->notes,
            created_at: $model->created_at?->toISOString(),
            updated_at: $model->updated_at?->toISOString(),
            provider: $model->relationLoaded('provider') ? $model->provider : null,
            verifier: $model->relationLoaded('verifier') ? $model->verifier : null,
        );
    }

    public function calculatePerformanceScore(): float
    {
        $weights = [
            'on_time_delivery_rate' => 0.20,
            'customer_satisfaction_score' => 0.25,
            'quality_rating' => 0.20,
            'delivery_rating' => 0.15,
            'communication_rating' => 0.10,
            'cost_efficiency_score' => 0.10,
        ];

        $score = 0;
        foreach ($weights as $metric => $weight) {
            $value = $this->{$metric};
            if ($metric === 'customer_satisfaction_score') {
                $score += ($value / 10) * 100 * $weight;
            } else {
                $score += $value * $weight;
            }
        }

        return round($score, 2);
    }

    public function calculatePerformanceGrade(): PerformanceGrade
    {
        $score = $this->performance_score;

        if ($score >= 90) {
            return PerformanceGrade::A;
        }
        if ($score >= 80) {
            return PerformanceGrade::B;
        }
        if ($score >= 70) {
            return PerformanceGrade::C;
        }
        if ($score >= 60) {
            return PerformanceGrade::D;
        }

        return PerformanceGrade::F;
    }

    public function getPerformanceTrend(): string
    {
        // This would typically compare with historical data
        // For now, return a basic trend based on score
        if ($this->performance_score >= 80) {
            return 'excellent';
        }
        if ($this->performance_score >= 60) {
            return 'good';
        }
        if ($this->performance_score >= 40) {
            return 'fair';
        }

        return 'poor';
    }

    public function getBenchmarkComparison(): array
    {
        // This would typically compare with industry benchmarks
        return [
            'on_time_delivery' => [
                'current' => $this->on_time_delivery_rate,
                'benchmark' => 95.0,
                'status' => $this->on_time_delivery_rate >= 95.0 ? 'above' : 'below',
            ],
            'customer_satisfaction' => [
                'current' => $this->customer_satisfaction_score,
                'benchmark' => 8.5,
                'status' => $this->customer_satisfaction_score >= 8.5 ? 'above' : 'below',
            ],
            'quality_rating' => [
                'current' => $this->quality_rating,
                'benchmark' => 8.0,
                'status' => $this->quality_rating >= 8.0 ? 'above' : 'below',
            ],
        ];
    }

    public function getPerformanceAlerts(): array
    {
        $alerts = [];

        if ($this->on_time_delivery_rate < 90) {
            $alerts[] = 'On-time delivery rate is below target (90%)';
        }

        if ($this->customer_satisfaction_score < 7.0) {
            $alerts[] = 'Customer satisfaction score is below target (7.0)';
        }

        if ($this->return_rate > 5) {
            $alerts[] = 'Return rate is above acceptable threshold (5%)';
        }

        if ($this->defect_rate > 2) {
            $alerts[] = 'Defect rate is above acceptable threshold (2%)';
        }

        return $alerts;
    }

    public function getImprovementSuggestions(): array
    {
        $suggestions = [];

        if ($this->on_time_delivery_rate < 90) {
            $suggestions[] = 'Improve logistics and delivery processes';
        }

        if ($this->customer_satisfaction_score < 7.0) {
            $suggestions[] = 'Enhance customer service and communication';
        }

        if ($this->quality_rating < 8.0) {
            $suggestions[] = 'Implement quality control measures';
        }

        if ($this->communication_rating < 7.0) {
            $suggestions[] = 'Improve communication channels and response times';
        }

        return $suggestions;
    }
}
