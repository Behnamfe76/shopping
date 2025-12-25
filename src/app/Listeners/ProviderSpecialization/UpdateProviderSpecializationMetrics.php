<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderSpecialization;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateProviderSpecializationMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $specialization = $event->specialization;
        $providerId = $specialization->provider_id;

        // Clear cached metrics for this provider
        $this->clearProviderMetricsCache($providerId);

        // Update global metrics cache
        $this->updateGlobalMetrics();

        // Update category-specific metrics
        $this->updateCategoryMetrics($specialization->category);

        // Update proficiency-level metrics
        $this->updateProficiencyMetrics($specialization->proficiency_level);
    }

    /**
     * Clear provider-specific metrics cache.
     */
    protected function clearProviderMetricsCache(int $providerId): void
    {
        $cacheKeys = [
            "provider_specialization_count_{$providerId}",
            "provider_active_specializations_{$providerId}",
            "provider_verified_specializations_{$providerId}",
            "provider_primary_specialization_{$providerId}",
            "provider_specialization_stats_{$providerId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update global specialization metrics.
     */
    protected function updateGlobalMetrics(): void
    {
        $cacheKeys = [
            'total_specialization_count',
            'active_specialization_count',
            'verified_specialization_count',
            'pending_specialization_count',
            'average_experience',
            'specialization_trends',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update category-specific metrics.
     */
    protected function updateCategoryMetrics(string $category): void
    {
        $cacheKeys = [
            "category_specialization_count_{$category}",
            "category_average_experience_{$category}",
            "category_proficiency_distribution_{$category}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update proficiency-level metrics.
     */
    protected function updateProficiencyMetrics(string $proficiencyLevel): void
    {
        $cacheKeys = [
            "proficiency_specialization_count_{$proficiencyLevel}",
            'proficiency_distribution',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, $exception): void
    {
        \Log::error('Failed to update provider specialization metrics', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'specialization_id' => $event->specialization->id ?? null,
        ]);
    }
}
