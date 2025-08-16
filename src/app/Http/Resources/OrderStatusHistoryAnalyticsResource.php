<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'period' => [
                'start_date' => $this['period']['start_date'] ?? null,
                'end_date' => $this['period']['end_date'] ?? null,
                'duration_days' => $this['period']['duration_days'] ?? null,
            ],
            'summary' => [
                'total_changes' => $this['summary']['total_changes'] ?? 0,
                'system_changes' => $this['summary']['system_changes'] ?? 0,
                'user_changes' => $this['summary']['user_changes'] ?? 0,
                'system_change_percentage' => $this['summary']['system_change_percentage'] ?? 0,
                'user_change_percentage' => $this['summary']['user_change_percentage'] ?? 0,
                'average_changes_per_day' => $this['summary']['average_changes_per_day'] ?? 0,
                'peak_change_day' => $this['summary']['peak_change_day'] ?? null,
                'peak_change_count' => $this['summary']['peak_change_count'] ?? 0,
            ],
            'by_status' => $this['by_status'] ?? [],
            'by_change_type' => $this['by_change_type'] ?? [],
            'by_change_category' => $this['by_change_category'] ?? [],
            'by_user' => $this['by_user'] ?? [],
            'trends' => [
                'daily_trend' => $this['trends']['daily_trend'] ?? [],
                'weekly_trend' => $this['trends']['weekly_trend'] ?? [],
                'monthly_trend' => $this['trends']['monthly_trend'] ?? [],
            ],
            'comparison' => [
                'previous_period' => $this['comparison']['previous_period'] ?? null,
                'percentage_change' => $this['comparison']['percentage_change'] ?? 0,
                'trend_direction' => $this['comparison']['trend_direction'] ?? 'stable',
            ],
            'insights' => [
                'most_active_users' => $this['insights']['most_active_users'] ?? [],
                'most_changed_statuses' => $this['insights']['most_changed_statuses'] ?? [],
                'peak_activity_hours' => $this['insights']['peak_activity_hours'] ?? [],
                'common_change_patterns' => $this['insights']['common_change_patterns'] ?? [],
            ],
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'data_points' => $this['metadata']['data_points'] ?? 0,
                'confidence_level' => $this['metadata']['confidence_level'] ?? 'high',
            ],
        ];
    }
}
