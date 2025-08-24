<?php

namespace App\Listeners\ProviderCertification;

use App\Events\ProviderCertification\ProviderCertificationCreated;
use App\Events\ProviderCertification\ProviderCertificationUpdated;
use App\Events\ProviderCertification\ProviderCertificationVerified;
use App\Events\ProviderCertification\ProviderCertificationRejected;
use App\Events\ProviderCertification\ProviderCertificationExpired;
use App\Events\ProviderCertification\ProviderCertificationRenewed;
use App\Events\ProviderCertification\ProviderCertificationSuspended;
use App\Events\ProviderCertification\ProviderCertificationRevoked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateProviderCertificationMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'metrics';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $certification = $event->certification;
            $providerId = $certification->provider_id;

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $this->updateCreationMetrics($providerId, $certification);
                    break;

                case ProviderCertificationUpdated::class:
                    $this->updateModificationMetrics($providerId, $certification, $event->changes ?? []);
                    break;

                case ProviderCertificationVerified::class:
                    $this->updateVerificationMetrics($providerId, $certification);
                    break;

                case ProviderCertificationRejected::class:
                    $this->updateRejectionMetrics($providerId, $certification);
                    break;

                case ProviderCertificationExpired::class:
                    $this->updateExpirationMetrics($providerId, $certification);
                    break;

                case ProviderCertificationRenewed::class:
                    $this->updateRenewalMetrics($providerId, $certification);
                    break;

                case ProviderCertificationSuspended::class:
                    $this->updateSuspensionMetrics($providerId, $certification);
                    break;

                case ProviderCertificationRevoked::class:
                    $this->updateRevocationMetrics($providerId, $certification);
                    break;
            }

            // Clear related caches
            $this->clearProviderMetricsCache($providerId);

            Log::info('Provider certification metrics updated successfully', [
                'event' => get_class($event),
                'certification_id' => $certification->id,
                'provider_id' => $providerId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider certification metrics', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Update metrics for certification creation.
     */
    private function updateCreationMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'total_certifications');
        $this->incrementMetric($providerId, "certifications_{$certification->category}");
        $this->incrementMetric($providerId, "certifications_{$certification->status}");

        // Update issuing organization metrics
        if ($certification->issuing_organization) {
            $this->incrementMetric($providerId, "organizations_{$certification->issuing_organization}");
        }
    }

    /**
     * Update metrics for certification modification.
     */
    private function updateModificationMetrics(int $providerId, $certification, array $changes): void
    {
        // Handle status changes
        if (isset($changes['status'])) {
            $oldStatus = $certification->getOriginal('status');
            $newStatus = $changes['status'];

            if ($oldStatus) {
                $this->decrementMetric($providerId, "certifications_{$oldStatus}");
            }
            $this->incrementMetric($providerId, "certifications_{$newStatus}");
        }

        // Handle category changes
        if (isset($changes['category'])) {
            $oldCategory = $certification->getOriginal('category');
            $newCategory = $changes['category'];

            if ($oldCategory) {
                $this->decrementMetric($providerId, "certifications_{$oldCategory}");
            }
            $this->incrementMetric($providerId, "certifications_{$newCategory}");
        }
    }

    /**
     * Update metrics for certification verification.
     */
    private function updateVerificationMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'verified_certifications');
        $this->incrementMetric($providerId, 'verification_success_rate');

        // Update verification timeline metrics
        $this->updateVerificationTimelineMetrics($providerId, $certification);
    }

    /**
     * Update metrics for certification rejection.
     */
    private function updateRejectionMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'rejected_certifications');
        $this->decrementMetric($providerId, 'verification_success_rate');
    }

    /**
     * Update metrics for certification expiration.
     */
    private function updateExpirationMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'expired_certifications');
        $this->decrementMetric($providerId, 'active_certifications');

        // Update expiration timeline metrics
        $this->updateExpirationTimelineMetrics($providerId, $certification);
    }

    /**
     * Update metrics for certification renewal.
     */
    private function updateRenewalMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'renewed_certifications');
        $this->incrementMetric($providerId, 'active_certifications');
        $this->decrementMetric($providerId, 'expired_certifications');

        // Update renewal timeline metrics
        $this->updateRenewalTimelineMetrics($providerId, $certification);
    }

    /**
     * Update metrics for certification suspension.
     */
    private function updateSuspensionMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'suspended_certifications');
        $this->decrementMetric($providerId, 'active_certifications');
    }

    /**
     * Update metrics for certification revocation.
     */
    private function updateRevocationMetrics(int $providerId, $certification): void
    {
        $this->incrementMetric($providerId, 'revoked_certifications');
        $this->decrementMetric($providerId, 'active_certifications');
    }

    /**
     * Increment a metric for a provider.
     */
    private function incrementMetric(int $providerId, string $metric): void
    {
        $cacheKey = "provider_{$providerId}_metric_{$metric}";
        $currentValue = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentValue + 1, now()->addDays(30));
    }

    /**
     * Decrement a metric for a provider.
     */
    private function decrementMetric(int $providerId, string $metric): void
    {
        $cacheKey = "provider_{$providerId}_metric_{$metric}";
        $currentValue = Cache::get($cacheKey, 0);
        if ($currentValue > 0) {
            Cache::put($cacheKey, $currentValue - 1, now()->addDays(30));
        }
    }

    /**
     * Update verification timeline metrics.
     */
    private function updateVerificationTimelineMetrics(int $providerId, $certification): void
    {
        $verificationTime = now()->diffInDays($certification->created_at);
        $this->updateTimelineMetric($providerId, 'verification_time', $verificationTime);
    }

    /**
     * Update expiration timeline metrics.
     */
    private function updateExpirationTimelineMetrics(int $providerId, $certification): void
    {
        $lifespan = now()->diffInDays($certification->issue_date);
        $this->updateTimelineMetric($providerId, 'certification_lifespan', $lifespan);
    }

    /**
     * Update renewal timeline metrics.
     */
    private function updateRenewalTimelineMetrics(int $providerId, $certification): void
    {
        $renewalTime = now()->diffInDays($certification->expiry_date);
        $this->updateTimelineMetric($providerId, 'renewal_time', $renewalTime);
    }

    /**
     * Update timeline metrics with average calculation.
     */
    private function updateTimelineMetric(int $providerId, string $metric, int $value): void
    {
        $cacheKey = "provider_{$providerId}_timeline_{$metric}";
        $currentData = Cache::get($cacheKey, ['count' => 0, 'total' => 0]);

        $currentData['count']++;
        $currentData['total'] += $value;
        $currentData['average'] = $currentData['total'] / $currentData['count'];

        Cache::put($cacheKey, $currentData, now()->addDays(30));
    }

    /**
     * Clear provider metrics cache.
     */
    private function clearProviderMetricsCache(int $providerId): void
    {
        $patterns = [
            "provider_{$providerId}_metric_*",
            "provider_{$providerId}_timeline_*",
        ];

        foreach ($patterns as $pattern) {
            $keys = Cache::get($pattern);
            if ($keys) {
                Cache::forget($keys);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Provider certification metrics update job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage()
        ]);
    }
}
