<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'total_subscriptions' => $this->resource['total_subscriptions'] ?? 0,
            'active_subscriptions' => $this->resource['active_subscriptions'] ?? 0,
            'trial_subscriptions' => $this->resource['trial_subscriptions'] ?? 0,
            'expired_subscriptions' => $this->resource['expired_subscriptions'] ?? 0,
            'cancelled_subscriptions' => $this->resource['cancelled_subscriptions'] ?? 0,
            'paused_subscriptions' => $this->resource['paused_subscriptions'] ?? 0,

            // Revenue breakdown
            'total_revenue' => $this->resource['total_revenue'] ?? 0,
            'monthly_revenue' => $this->resource['monthly_revenue'] ?? 0,
            'annual_revenue' => $this->resource['annual_revenue'] ?? 0,
            'average_revenue_per_subscription' => $this->resource['average_revenue_per_subscription'] ?? 0,

            // Status distribution
            'status_distribution' => [
                'active' => [
                    'count' => $this->resource['active_subscriptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['active_subscriptions'] ?? 0, $this->resource['total_subscriptions'] ?? 1),
                ],
                'trial' => [
                    'count' => $this->resource['trial_subscriptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['trial_subscriptions'] ?? 0, $this->resource['total_subscriptions'] ?? 1),
                ],
                'expired' => [
                    'count' => $this->resource['expired_subscriptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['expired_subscriptions'] ?? 0, $this->resource['total_subscriptions'] ?? 1),
                ],
                'cancelled' => [
                    'count' => $this->resource['cancelled_subscriptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['cancelled_subscriptions'] ?? 0, $this->resource['total_subscriptions'] ?? 1),
                ],
                'paused' => [
                    'count' => $this->resource['paused_subscriptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['paused_subscriptions'] ?? 0, $this->resource['total_subscriptions'] ?? 1),
                ],
            ],

            // Analytics data
            'churn_rate' => $this->resource['churn_rate'] ?? 0,
            'retention_rate' => $this->resource['retention_rate'] ?? 0,
            'average_subscription_duration' => $this->resource['average_subscription_duration'] ?? 0,
            'trial_conversion_rate' => $this->resource['trial_conversion_rate'] ?? 0,

            // Time-based metrics
            'new_subscriptions_this_month' => $this->resource['new_subscriptions_this_month'] ?? 0,
            'cancelled_subscriptions_this_month' => $this->resource['cancelled_subscriptions_this_month'] ?? 0,
            'revenue_growth_rate' => $this->resource['revenue_growth_rate'] ?? 0,

            // Upcoming events
            'upcoming_renewals' => $this->resource['upcoming_renewals'] ?? 0,
            'expiring_trials' => $this->resource['expiring_trials'] ?? 0,
            'expiring_subscriptions' => $this->resource['expiring_subscriptions'] ?? 0,

            // Generated at
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Calculate percentage.
     */
    private function calculatePercentage(int $value, int $total): float
    {
        if ($total === 0) {
            return 0;
        }

        return round(($value / $total) * 100, 2);
    }
}
