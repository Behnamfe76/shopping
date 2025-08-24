<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasProviderCommunicationAnalytics
{
    /**
     * Get communication analytics for a provider
     */
    public function getCommunicationAnalytics(int $providerId): array
    {
        $cacheKey = "provider_analytics_{$providerId}";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            $communications = ProviderCommunication::where('provider_id', $providerId);

            return [
                'total_communications' => $communications->count(),
                'communications_by_type' => $this->getCommunicationsByType($providerId),
                'communications_by_status' => $this->getCommunicationsByStatus($providerId),
                'communications_by_direction' => $this->getCommunicationsByDirection($providerId),
                'communications_by_priority' => $this->getCommunicationsByPriority($providerId),
                'response_time_metrics' => $this->getResponseTimeMetrics($providerId),
                'satisfaction_metrics' => $this->getSatisfactionMetrics($providerId),
                'volume_metrics' => $this->getVolumeMetrics($providerId),
                'performance_metrics' => $this->getPerformanceMetrics($providerId),
            ];
        });
    }

    /**
     * Get global communication analytics
     */
    public function getGlobalCommunicationAnalytics(): array
    {
        return Cache::remember('global_communication_analytics', 3600, function () {
            return [
                'total_communications' => ProviderCommunication::count(),
                'communications_by_type' => $this->getGlobalCommunicationsByType(),
                'communications_by_status' => $this->getGlobalCommunicationsByStatus(),
                'communications_by_direction' => $this->getGlobalCommunicationsByDirection(),
                'communications_by_priority' => $this->getGlobalCommunicationsByPriority(),
                'response_time_metrics' => $this->getGlobalResponseTimeMetrics(),
                'satisfaction_metrics' => $this->getGlobalSatisfactionMetrics(),
                'volume_metrics' => $this->getGlobalVolumeMetrics(),
                'performance_metrics' => $this->getGlobalPerformanceMetrics(),
            ];
        });
    }

    /**
     * Get communications by type for a provider
     */
    private function getCommunicationsByType(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->select('communication_type', DB::raw('count(*) as count'))
            ->groupBy('communication_type')
            ->pluck('count', 'communication_type')
            ->toArray();
    }

    /**
     * Get communications by status for a provider
     */
    private function getCommunicationsByStatus(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get communications by direction for a provider
     */
    private function getCommunicationsByDirection(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->select('direction', DB::raw('count(*) as count'))
            ->groupBy('direction')
            ->pluck('count', 'direction')
            ->toArray();
    }

    /**
     * Get communications by priority for a provider
     */
    private function getCommunicationsByPriority(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }

    /**
     * Get response time metrics for a provider
     */
    private function getResponseTimeMetrics(int $providerId): array
    {
        $communications = ProviderCommunication::where('provider_id', $providerId)
            ->whereNotNull('response_time');

        return [
            'average_response_time' => $communications->avg('response_time'),
            'min_response_time' => $communications->min('response_time'),
            'max_response_time' => $communications->max('response_time'),
            'response_time_distribution' => $this->getResponseTimeDistribution($providerId),
        ];
    }

    /**
     * Get satisfaction metrics for a provider
     */
    private function getSatisfactionMetrics(int $providerId): array
    {
        $communications = ProviderCommunication::where('provider_id', $providerId)
            ->whereNotNull('satisfaction_rating');

        return [
            'average_satisfaction' => $communications->avg('satisfaction_rating'),
            'satisfaction_distribution' => $this->getSatisfactionDistribution($providerId),
        ];
    }

    /**
     * Get volume metrics for a provider
     */
    private function getVolumeMetrics(int $providerId): array
    {
        return [
            'daily_volume' => $this->getDailyVolume($providerId),
            'weekly_volume' => $this->getWeeklyVolume($providerId),
            'monthly_volume' => $this->getMonthlyVolume($providerId),
        ];
    }

    /**
     * Get performance metrics for a provider
     */
    private function getPerformanceMetrics(int $providerId): array
    {
        return [
            'unread_count' => $this->getUnreadCount($providerId),
            'unreplied_count' => $this->getUnrepliedCount($providerId),
            'urgent_count' => $this->getUrgentCount($providerId),
            'archived_count' => $this->getArchivedCount($providerId),
        ];
    }

    /**
     * Get response time distribution
     */
    private function getResponseTimeDistribution(int $providerId): array
    {
        return [
            'under_1_hour' => ProviderCommunication::where('provider_id', $providerId)
                ->whereNotNull('response_time')
                ->where('response_time', '<', 60)
                ->count(),
            '1_to_4_hours' => ProviderCommunication::where('provider_id', $providerId)
                ->whereNotNull('response_time')
                ->whereBetween('response_time', [60, 240])
                ->count(),
            '4_to_24_hours' => ProviderCommunication::where('provider_id', $providerId)
                ->whereNotNull('response_time')
                ->whereBetween('response_time', [240, 1440])
                ->count(),
            'over_24_hours' => ProviderCommunication::where('provider_id', $providerId)
                ->whereNotNull('response_time')
                ->where('response_time', '>', 1440)
                ->count(),
        ];
    }

    /**
     * Get satisfaction distribution
     */
    private function getSatisfactionDistribution(int $providerId): array
    {
        return [
            '1_star' => ProviderCommunication::where('provider_id', $providerId)
                ->where('satisfaction_rating', 1)
                ->count(),
            '2_star' => ProviderCommunication::where('provider_id', $providerId)
                ->where('satisfaction_rating', 2)
                ->count(),
            '3_star' => ProviderCommunication::where('provider_id', $providerId)
                ->where('satisfaction_rating', 3)
                ->count(),
            '4_star' => ProviderCommunication::where('provider_id', $providerId)
                ->where('satisfaction_rating', 4)
                ->count(),
            '5_star' => ProviderCommunication::where('provider_id', $providerId)
                ->where('satisfaction_rating', 5)
                ->count(),
        ];
    }

    /**
     * Get daily volume for a provider
     */
    private function getDailyVolume(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }

    /**
     * Get weekly volume for a provider
     */
    private function getWeeklyVolume(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->where('created_at', '>=', now()->subWeeks(12))
            ->select(DB::raw('YEARWEEK(created_at) as week'), DB::raw('count(*) as count'))
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('count', 'week')
            ->toArray();
    }

    /**
     * Get monthly volume for a provider
     */
    private function getMonthlyVolume(int $providerId): array
    {
        return ProviderCommunication::where('provider_id', $providerId)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    // Global methods (similar structure but without provider_id filter)
    private function getGlobalCommunicationsByType(): array
    {
        return ProviderCommunication::select('communication_type', DB::raw('count(*) as count'))
            ->groupBy('communication_type')
            ->pluck('count', 'communication_type')
            ->toArray();
    }

    private function getGlobalCommunicationsByStatus(): array
    {
        return ProviderCommunication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getGlobalCommunicationsByDirection(): array
    {
        return ProviderCommunication::select('direction', DB::raw('count(*) as count'))
            ->groupBy('direction')
            ->pluck('count', 'direction')
            ->toArray();
    }

    private function getGlobalCommunicationsByPriority(): array
    {
        return ProviderCommunication::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }

    private function getGlobalResponseTimeMetrics(): array
    {
        $communications = ProviderCommunication::whereNotNull('response_time');

        return [
            'average_response_time' => $communications->avg('response_time'),
            'min_response_time' => $communications->min('response_time'),
            'max_response_time' => $communications->max('response_time'),
        ];
    }

    private function getGlobalSatisfactionMetrics(): array
    {
        $communications = ProviderCommunication::whereNotNull('satisfaction_rating');

        return [
            'average_satisfaction' => $communications->avg('satisfaction_rating'),
        ];
    }

    private function getGlobalVolumeMetrics(): array
    {
        return [
            'daily_volume' => $this->getGlobalDailyVolume(),
            'weekly_volume' => $this->getGlobalWeeklyVolume(),
            'monthly_volume' => $this->getGlobalMonthlyVolume(),
        ];
    }

    private function getGlobalPerformanceMetrics(): array
    {
        return [
            'unread_count' => $this->getTotalUnreadCount(),
            'unreplied_count' => $this->getTotalUnrepliedCount(),
            'urgent_count' => $this->getTotalUrgentCount(),
            'archived_count' => $this->getTotalArchivedCount(),
        ];
    }

    private function getGlobalDailyVolume(): array
    {
        return ProviderCommunication::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }

    private function getGlobalWeeklyVolume(): array
    {
        return ProviderCommunication::where('created_at', '>=', now()->subWeeks(12))
            ->select(DB::raw('YEARWEEK(created_at) as week'), DB::raw('count(*) as count'))
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('count', 'week')
            ->toArray();
    }

    private function getGlobalMonthlyVolume(): array
    {
        return ProviderCommunication::where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }
}
