<?php

namespace App\Models;

use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderPerformance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'period_start',
        'period_end',
        'period_type',
        'total_orders',
        'total_revenue',
        'average_order_value',
        'on_time_delivery_rate',
        'return_rate',
        'defect_rate',
        'customer_satisfaction_score',
        'response_time_avg',
        'quality_rating',
        'delivery_rating',
        'communication_rating',
        'cost_efficiency_score',
        'inventory_turnover_rate',
        'lead_time_avg',
        'fill_rate',
        'accuracy_rate',
        'performance_score',
        'performance_grade',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'period_type' => PeriodType::class,
        'total_orders' => 'integer',
        'total_revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'return_rate' => 'decimal:2',
        'defect_rate' => 'decimal:2',
        'customer_satisfaction_score' => 'decimal:2',
        'response_time_avg' => 'decimal:2',
        'quality_rating' => 'decimal:2',
        'delivery_rating' => 'decimal:2',
        'communication_rating' => 'decimal:2',
        'cost_efficiency_score' => 'decimal:2',
        'inventory_turnover_rate' => 'decimal:2',
        'lead_time_avg' => 'decimal:2',
        'fill_rate' => 'decimal:2',
        'accuracy_rate' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'performance_grade' => PerformanceGrade::class,
        'is_verified' => 'boolean',
        'verified_by' => 'integer',
        'verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'period_start',
        'period_end',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByProvider(Builder $query, int $providerId): Builder
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeByPeriod(Builder $query, string $periodStart, string $periodEnd): Builder
    {
        return $query->whereBetween('period_start', [$periodStart, $periodEnd]);
    }

    public function scopeByPeriodType(Builder $query, string $periodType): Builder
    {
        return $query->where('period_type', $periodType);
    }

    public function scopeByPerformanceGrade(Builder $query, string $grade): Builder
    {
        return $query->where('performance_grade', $grade);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    public function scopeTopPerformers(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('performance_score', 'desc')->limit($limit);
    }

    public function scopeBottomPerformers(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('performance_score', 'asc')->limit($limit);
    }

    public function scopeByRevenueRange(Builder $query, float $minRevenue, float $maxRevenue): Builder
    {
        return $query->whereBetween('total_revenue', [$minRevenue, $maxRevenue]);
    }

    public function scopeBySatisfactionRange(Builder $query, float $minScore, float $maxScore): Builder
    {
        return $query->whereBetween('customer_satisfaction_score', [$minScore, $maxScore]);
    }

    public function scopeByDeliveryRateRange(Builder $query, float $minRate, float $maxRate): Builder
    {
        return $query->whereBetween('on_time_delivery_rate', [$minRate, $maxRate]);
    }

    public function scopeByReturnRateRange(Builder $query, float $minRate, float $maxRate): Builder
    {
        return $query->whereBetween('return_rate', [$minRate, $maxRate]);
    }

    public function scopeByDefectRateRange(Builder $query, float $minRate, float $maxRate): Builder
    {
        return $query->whereBetween('defect_rate', [$minRate, $maxRate]);
    }

    public function scopeByPerformanceScoreRange(Builder $query, float $minScore, float $maxScore): Builder
    {
        return $query->whereBetween('performance_score', [$minScore, $maxScore]);
    }

    // Methods
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

        $this->performance_score = round($score, 2);

        return $this->performance_score;
    }

    public function updatePerformanceGrade(): PerformanceGrade
    {
        $score = $this->performance_score ?? $this->calculatePerformanceScore();

        if ($score >= 90) {
            $this->performance_grade = PerformanceGrade::A;
        } elseif ($score >= 80) {
            $this->performance_grade = PerformanceGrade::B;
        } elseif ($score >= 70) {
            $this->performance_grade = PerformanceGrade::C;
        } elseif ($score >= 60) {
            $this->performance_grade = PerformanceGrade::D;
        } else {
            $this->performance_grade = PerformanceGrade::F;
        }

        return $this->performance_grade;
    }

    public function verify(int $verifiedBy, ?string $notes = null): bool
    {
        $this->is_verified = true;
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->notes = $notes;

        return $this->save();
    }

    public function unverify(): bool
    {
        $this->is_verified = false;
        $this->verified_by = null;
        $this->verified_at = null;

        return $this->save();
    }

    public function getPerformanceTrend(): string
    {
        // This would typically compare with historical data
        // For now, return a basic trend based on score
        $score = $this->performance_score;

        if ($score >= 80) {
            return 'excellent';
        }
        if ($score >= 60) {
            return 'good';
        }
        if ($score >= 40) {
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

    public function isHighPerformer(): bool
    {
        return $this->performance_grade === PerformanceGrade::A || $this->performance_grade === PerformanceGrade::B;
    }

    public function isLowPerformer(): bool
    {
        return $this->performance_grade === PerformanceGrade::D || $this->performance_grade === PerformanceGrade::F;
    }

    public function needsAttention(): bool
    {
        return $this->isLowPerformer() || ! empty($this->getPerformanceAlerts());
    }

    public function getPeriodDuration(): int
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    public function getRevenuePerOrder(): float
    {
        return $this->total_orders > 0 ? $this->total_revenue / $this->total_orders : 0;
    }

    public function getEfficiencyScore(): float
    {
        $efficiencyFactors = [
            'on_time_delivery_rate' => 0.3,
            'fill_rate' => 0.2,
            'accuracy_rate' => 0.2,
            'inventory_turnover_rate' => 0.15,
            'cost_efficiency_score' => 0.15,
        ];

        $score = 0;
        foreach ($efficiencyFactors as $metric => $weight) {
            $value = $this->{$metric};
            if ($metric === 'inventory_turnover_rate') {
                // Normalize inventory turnover (higher is better, but cap at reasonable level)
                $normalizedValue = min($value / 10, 100);
                $score += $normalizedValue * $weight;
            } else {
                $score += $value * $weight;
            }
        }

        return round($score, 2);
    }
}
