<?php

namespace App\Events;

use App\Models\ProviderPerformance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProviderPerformanceCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerPerformance;

    public $user;

    public $timestamp;

    public $calculatedMetrics;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPerformance $providerPerformance)
    {
        $this->providerPerformance = $providerPerformance;
        $this->user = auth()->user();
        $this->timestamp = now();
        $this->calculatedMetrics = $this->getCalculatedMetrics();

        // Log the event
        Log::info('Provider performance created', [
            'provider_id' => $providerPerformance->provider_id,
            'performance_id' => $providerPerformance->id,
            'period_start' => $providerPerformance->period_start,
            'period_end' => $providerPerformance->period_end,
            'performance_score' => $providerPerformance->performance_score,
            'performance_grade' => $providerPerformance->performance_grade?->value,
            'user_id' => $this->user?->id,
            'timestamp' => $this->timestamp,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-performance.'.$this->providerPerformance->provider_id),
            new PrivateChannel('admin.provider-performance'),
            new Channel('provider-performance-updates'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerPerformance->id,
            'provider_id' => $this->providerPerformance->provider_id,
            'provider_name' => $this->providerPerformance->provider->name ?? 'Unknown Provider',
            'period_start' => $this->providerPerformance->period_start?->toDateString(),
            'period_end' => $this->providerPerformance->period_end?->toDateString(),
            'period_type' => $this->providerPerformance->period_type?->value,
            'performance_score' => $this->providerPerformance->performance_score,
            'performance_grade' => $this->providerPerformance->performance_grade?->value,
            'performance_grade_description' => $this->providerPerformance->performance_grade?->getDescription(),
            'total_orders' => $this->providerPerformance->total_orders,
            'total_revenue' => $this->providerPerformance->total_revenue,
            'on_time_delivery_rate' => $this->providerPerformance->on_time_delivery_rate,
            'customer_satisfaction_score' => $this->providerPerformance->customer_satisfaction_score,
            'quality_rating' => $this->providerPerformance->quality_rating,
            'return_rate' => $this->providerPerformance->return_rate,
            'defect_rate' => $this->providerPerformance->defect_rate,
            'is_verified' => $this->providerPerformance->is_verified,
            'created_by' => $this->user?->id,
            'created_by_name' => $this->user?->name ?? 'System',
            'timestamp' => $this->timestamp->toISOString(),
            'calculated_metrics' => $this->calculatedMetrics,
            'alerts' => $this->providerPerformance->getPerformanceAlerts(),
            'needs_attention' => $this->providerPerformance->needsAttention(),
            'event_type' => 'provider_performance_created',
        ];
    }

    /**
     * Get the event broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'provider.performance.created';
    }

    /**
     * Get calculated metrics for the event.
     */
    protected function getCalculatedMetrics(): array
    {
        return [
            'revenue_per_order' => $this->providerPerformance->getRevenuePerOrder(),
            'efficiency_score' => $this->providerPerformance->getEfficiencyScore(),
            'period_duration_days' => $this->providerPerformance->getPeriodDuration(),
            'overall_rating' => $this->calculateOverallRating(),
            'performance_trend' => $this->providerPerformance->getPerformanceTrend(),
            'benchmark_comparison' => $this->providerPerformance->getBenchmarkComparison(),
        ];
    }

    /**
     * Calculate overall rating from individual ratings.
     */
    protected function calculateOverallRating(): float
    {
        $ratings = [
            $this->providerPerformance->quality_rating,
            $this->providerPerformance->delivery_rating,
            $this->providerPerformance->communication_rating,
        ];

        return round(array_sum($ratings) / count($ratings), 2);
    }

    /**
     * Get the event queue connection.
     */
    public function viaConnection(): string
    {
        return 'redis';
    }

    /**
     * Get the event queue name.
     */
    public function viaQueue(): string
    {
        return 'events';
    }

    /**
     * Determine if the event should be queued.
     */
    public function shouldQueue(): bool
    {
        return true;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'provider_performance',
            'provider_'.$this->providerPerformance->provider_id,
            'performance_'.$this->providerPerformance->id,
            'event_created',
        ];
    }

    /**
     * Get the retry delay for the job.
     */
    public function retryAfter(): int
    {
        return 60; // 1 minute
    }

    /**
     * Get the maximum number of attempts for the job.
     */
    public function tries(): int
    {
        return 3;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Provider performance created event failed', [
            'provider_id' => $this->providerPerformance->provider_id,
            'performance_id' => $this->providerPerformance->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Get the event's unique identifier.
     */
    public function uniqueId(): string
    {
        return 'provider_performance_created_'.$this->providerPerformance->id;
    }

    /**
     * Get the event's expiration time.
     */
    public function expiresAt(): \DateTime
    {
        return now()->addDays(30);
    }

    /**
     * Get the event's priority.
     */
    public function priority(): int
    {
        return 1; // High priority
    }

    /**
     * Get the event's category.
     */
    public function category(): string
    {
        return 'provider_performance';
    }

    /**
     * Get the event's subcategory.
     */
    public function subcategory(): string
    {
        return 'created';
    }

    /**
     * Get the event's severity level.
     */
    public function severity(): string
    {
        if ($this->providerPerformance->needsAttention()) {
            return 'high';
        }

        if ($this->providerPerformance->isLowPerformer()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event's impact level.
     */
    public function impact(): string
    {
        if ($this->providerPerformance->total_revenue > 10000) {
            return 'high';
        }

        if ($this->providerPerformance->total_revenue > 5000) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event's urgency level.
     */
    public function urgency(): string
    {
        if ($this->providerPerformance->needsAttention()) {
            return 'high';
        }

        if ($this->providerPerformance->isLowPerformer()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event's business value.
     */
    public function businessValue(): string
    {
        if ($this->providerPerformance->isHighPerformer()) {
            return 'high';
        }

        if ($this->providerPerformance->performance_score >= 70) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event's risk level.
     */
    public function riskLevel(): string
    {
        if ($this->providerPerformance->performance_score < 60) {
            return 'high';
        }

        if ($this->providerPerformance->performance_score < 70) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get the event's compliance status.
     */
    public function complianceStatus(): string
    {
        if ($this->providerPerformance->is_verified) {
            return 'compliant';
        }

        if ($this->providerPerformance->needsAttention()) {
            return 'non_compliant';
        }

        return 'pending_review';
    }

    /**
     * Get the event's audit trail.
     */
    public function auditTrail(): array
    {
        return [
            'event_type' => 'provider_performance_created',
            'event_id' => $this->uniqueId(),
            'timestamp' => $this->timestamp->toISOString(),
            'user_id' => $this->user?->id,
            'user_name' => $this->user?->name ?? 'System',
            'user_email' => $this->user?->email ?? 'system@example.com',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'request_id' => request()->id() ?? uniqid(),
            'changes' => [
                'before' => null,
                'after' => $this->providerPerformance->toArray(),
            ],
            'metadata' => [
                'provider_id' => $this->providerPerformance->provider_id,
                'performance_id' => $this->providerPerformance->id,
                'period_start' => $this->providerPerformance->period_start?->toDateString(),
                'period_end' => $this->providerPerformance->period_end?->toDateString(),
                'performance_score' => $this->providerPerformance->performance_score,
                'performance_grade' => $this->providerPerformance->performance_grade?->value,
            ],
        ];
    }
}
