<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderCommunicationStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_communications' => $this->resource['total_communications'] ?? 0,
            'communications_by_type' => $this->resource['communications_by_type'] ?? [],
            'communications_by_status' => $this->resource['communications_by_status'] ?? [],
            'communications_by_direction' => $this->resource['communications_by_direction'] ?? [],
            'communications_by_priority' => $this->resource['communications_by_priority'] ?? [],
            'response_time_metrics' => [
                'average_response_time' => $this->resource['average_response_time'] ?? 0,
                'response_time_distribution' => $this->resource['response_time_distribution'] ?? [],
                'response_time_trends' => $this->resource['response_time_trends'] ?? [],
            ],
            'satisfaction_metrics' => [
                'average_satisfaction' => $this->resource['average_satisfaction'] ?? 0,
                'satisfaction_distribution' => $this->resource['satisfaction_distribution'] ?? [],
                'satisfaction_trends' => $this->resource['satisfaction_trends'] ?? [],
            ],
            'volume_metrics' => [
                'daily_volume' => $this->resource['daily_volume'] ?? [],
                'weekly_volume' => $this->resource['weekly_volume'] ?? [],
                'monthly_volume' => $this->resource['monthly_volume'] ?? [],
            ],
            'performance_metrics' => [
                'unread_count' => $this->resource['unread_count'] ?? 0,
                'unreplied_count' => $this->resource['unreplied_count'] ?? 0,
                'urgent_count' => $this->resource['urgent_count'] ?? 0,
                'archived_count' => $this->resource['archived_count'] ?? 0,
            ],
            'provider_performance' => $this->resource['provider_performance'] ?? [],
            'communication_patterns' => $this->resource['communication_patterns'] ?? [],
            'generated_at' => now()->toISOString(),
        ];
    }
}
