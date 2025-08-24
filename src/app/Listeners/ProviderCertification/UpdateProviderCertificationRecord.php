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
use Illuminate\Support\Facades\DB;

class UpdateProviderCertificationRecord implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'default';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $certification = $event->certification;

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $this->handleCreated($certification);
                    break;

                case ProviderCertificationUpdated::class:
                    $this->handleUpdated($certification, $event->changes ?? []);
                    break;

                case ProviderCertificationVerified::class:
                    $this->handleVerified($certification, $event->verifiedBy);
                    break;

                case ProviderCertificationRejected::class:
                    $this->handleRejected($certification, $event->reason);
                    break;

                case ProviderCertificationExpired::class:
                    $this->handleExpired($certification);
                    break;

                case ProviderCertificationRenewed::class:
                    $this->handleRenewed($certification, $event->newExpiryDate);
                    break;

                case ProviderCertificationSuspended::class:
                    $this->handleSuspended($certification, $event->reason);
                    break;

                case ProviderCertificationRevoked::class:
                    $this->handleRevoked($certification, $event->reason);
                    break;
            }

            Log::info('Provider certification record updated successfully', [
                'event' => get_class($event),
                'certification_id' => $certification->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider certification record', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle certification created event.
     */
    private function handleCreated($certification): void
    {
        // Update provider's certification count
        $certification->provider()->increment('total_certifications');

        // Update category-specific counts if needed
        if ($certification->category) {
            $certification->provider()->increment("certifications_{$certification->category}");
        }
    }

    /**
     * Handle certification updated event.
     */
    private function handleUpdated($certification, array $changes): void
    {
        // Handle status changes
        if (isset($changes['status'])) {
            $this->updateStatusMetrics($certification, $changes['status']);
        }

        // Handle category changes
        if (isset($changes['category'])) {
            $this->updateCategoryMetrics($certification, $changes['category']);
        }
    }

    /**
     * Handle certification verified event.
     */
    private function handleVerified($certification, $verifiedBy): void
    {
        $certification->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy->id
        ]);

        // Update verification metrics
        $certification->provider()->increment('verified_certifications');
    }

    /**
     * Handle certification rejected event.
     */
    private function handleRejected($certification, $reason): void
    {
        $certification->update([
            'verification_status' => 'rejected',
            'notes' => $certification->notes . "\nRejected: " . $reason
        ]);
    }

    /**
     * Handle certification expired event.
     */
    private function handleExpired($certification): void
    {
        $certification->update([
            'status' => 'expired'
        ]);

        // Update status metrics
        $this->updateStatusMetrics($certification, 'expired');
    }

    /**
     * Handle certification renewed event.
     */
    private function handleRenewed($certification, $newExpiryDate): void
    {
        $certification->update([
            'status' => 'active',
            'expiry_date' => $newExpiryDate,
            'renewal_date' => now()
        ]);

        // Update status metrics
        $this->updateStatusMetrics($certification, 'active');
    }

    /**
     * Handle certification suspended event.
     */
    private function handleSuspended($certification, $reason): void
    {
        $certification->update([
            'status' => 'suspended',
            'notes' => $certification->notes . "\nSuspended: " . $reason
        ]);

        // Update status metrics
        $this->updateStatusMetrics($certification, 'suspended');
    }

    /**
     * Handle certification revoked event.
     */
    private function handleRevoked($certification, $reason): void
    {
        $certification->update([
            'status' => 'revoked',
            'notes' => $certification->notes . "\nRevoked: " . $reason
        ]);

        // Update status metrics
        $this->updateStatusMetrics($certification, 'revoked');
    }

    /**
     * Update status metrics for provider.
     */
    private function updateStatusMetrics($certification, $newStatus): void
    {
        $provider = $certification->provider;

        // Decrement old status count if exists
        if ($certification->getOriginal('status')) {
            $oldStatus = $certification->getOriginal('status');
            $provider->decrement("certifications_{$oldStatus}");
        }

        // Increment new status count
        $provider->increment("certifications_{$newStatus}");
    }

    /**
     * Update category metrics for provider.
     */
    private function updateCategoryMetrics($certification, $newCategory): void
    {
        $provider = $certification->provider;

        // Decrement old category count if exists
        if ($certification->getOriginal('category')) {
            $oldCategory = $certification->getOriginal('category');
            $provider->decrement("certifications_{$oldCategory}");
        }

        // Increment new category count
        $provider->increment("certifications_{$newCategory}");
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Provider certification record update job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage()
        ]);
    }
}
