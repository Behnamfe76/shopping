<?php

namespace App\Listeners;

use App\Events\ProviderPerformanceCreated;
use App\Events\ProviderPerformanceUpdated;
use App\Events\ProviderPerformanceDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdatePerformanceAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'analytics';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle provider performance created event.
     */
    public function handleProviderPerformanceCreated(ProviderPerformanceCreated $event): void
    {
        $this->updateAnalytics($event->providerPerformance, 'created');
    }

    /**
     * Handle provider performance updated event.
     */
    public function handleProviderPerformanceUpdated(ProviderPerformanceUpdated $event): void
    {
        $this->updateAnalytics($event->providerPerformance, 'updated');
    }

    /**
     * Handle provider performance deleted event.
     */
    public function handleProviderPerformanceDeleted(ProviderPerformanceDeleted $event): void
    {
        $this->updateAnalytics($event->providerPerformance, 'deleted');
    }

    /**
     * Update analytics data
     */
    private function updateAnalytics($providerPerformance, string $action): void
    {
        try {
            Log::info('Updating performance analytics', [
                'action' => $action,
                'provider_id' => $providerPerformance->provider_id,
                'performance_id' => $providerPerformance->id
            ]);

            // Clear cached analytics data
            $this->clearAnalyticsCache($providerPerformance->provider_id);

            // Update provider-specific analytics
            $this->updateProviderAnalytics($providerPerformance->provider_id);

            // Update global analytics
            $this->updateGlobalAnalytics();

            // Update grade distribution analytics
            $this->updateGradeDistributionAnalytics();

            // Update period distribution analytics
            $this->updatePeriodDistributionAnalytics();

            Log::info('Performance analytics updated successfully', [
                'action' => $action,
                'provider_id' => $providerPerformance->provider_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update performance analytics', [
                'error' => $e->getMessage(),
                'action' => $action,
                'provider_id' => $providerPerformance->provider_id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Clear analytics cache for a specific provider
     */
    private function clearAnalyticsCache(int $providerId): void
    {
        $cacheKeys = [
            "provider_analytics_{$providerId}",
            "provider_performance_trend_{$providerId}",
            "provider_benchmarks_{$providerId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update provider-specific analytics
     */
    private function updateProviderAnalytics(int $providerId): void
    {
        // This would typically query the database and calculate analytics
        // For now, just clear the cache to force recalculation

        Cache::forget("provider_analytics_{$providerId}");
    }

    /**
     * Update global analytics
     */
    private function updateGlobalAnalytics(): void
    {
        // Clear global analytics cache
        Cache::forget('global_performance_analytics');
        Cache::forget('global_performance_benchmarks');
        Cache::forget('global_grade_distribution');
    }

    /**
     * Update grade distribution analytics
     */
    private function updateGradeDistributionAnalytics(): void
    {
        // Clear grade distribution cache
        Cache::forget('grade_distribution_analytics');
        Cache::forget('grade_performance_trends');
    }

    /**
     * Update period distribution analytics
     */
    private function updatePeriodDistributionAnalytics(): void
    {
        // Clear period distribution cache
        Cache::forget('period_distribution_analytics');
        Cache::forget('period_performance_trends');
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update performance analytics', [
            'event' => $event,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
