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

class UpdateProviderProfile implements ShouldQueue
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
            $provider = $certification->provider;

            if (!$provider) {
                Log::warning('Provider not found for profile update', [
                    'certification_id' => $certification->id
                ]);
                return;
            }

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $this->updateProfileOnCreation($provider, $certification);
                    break;

                case ProviderCertificationUpdated::class:
                    $this->updateProfileOnModification($provider, $certification, $event->changes ?? []);
                    break;

                case ProviderCertificationVerified::class:
                    $this->updateProfileOnVerification($provider, $certification);
                    break;

                case ProviderCertificationRejected::class:
                    $this->updateProfileOnRejection($provider, $certification);
                    break;

                case ProviderCertificationExpired::class:
                    $this->updateProfileOnExpiration($provider, $certification);
                    break;

                case ProviderCertificationRenewed::class:
                    $this->updateProfileOnRenewal($provider, $certification);
                    break;

                case ProviderCertificationSuspended::class:
                    $this->updateProfileOnSuspension($provider, $certification);
                    break;

                case ProviderCertificationRevoked::class:
                    $this->updateProfileOnRevocation($provider, $certification);
                    break;
            }

            // Update provider's last activity timestamp
            $provider->update(['last_certification_activity' => now()]);

            Log::info('Provider profile updated successfully', [
                'event' => get_class($event),
                'certification_id' => $certification->id,
                'provider_id' => $provider->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider profile', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Update provider profile when certification is created.
     */
    private function updateProfileOnCreation($provider, $certification): void
    {
        // Update certification count
        $provider->increment('total_certifications');

        // Update category-specific counts
        if ($certification->category) {
            $provider->increment("certifications_{$certification->category}");
        }

        // Update status-specific counts
        $provider->increment("certifications_{$certification->status}");

        // Update issuing organization diversity
        $this->updateIssuingOrganizationDiversity($provider, $certification);

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is modified.
     */
    private function updateProfileOnModification($provider, $certification, array $changes): void
    {
        // Handle status changes
        if (isset($changes['status'])) {
            $oldStatus = $certification->getOriginal('status');
            $newStatus = $changes['status'];

            if ($oldStatus) {
                $provider->decrement("certifications_{$oldStatus}");
            }
            $provider->increment("certifications_{$newStatus}");
        }

        // Handle category changes
        if (isset($changes['category'])) {
            $oldCategory = $certification->getOriginal('category');
            $newCategory = $changes['category'];

            if ($oldCategory) {
                $provider->decrement("certifications_{$oldCategory}");
            }
            $provider->increment("certifications_{$newCategory}");
        }

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is verified.
     */
    private function updateProfileOnVerification($provider, $certification): void
    {
        $provider->increment('verified_certifications');
        $provider->increment('verification_success_rate');

        // Update trust score
        $this->updateTrustScore($provider);

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is rejected.
     */
    private function updateProfileOnRejection($provider, $certification): void
    {
        $provider->increment('rejected_certifications');
        $provider->decrement('verification_success_rate');

        // Update trust score
        $this->updateTrustScore($provider);
    }

    /**
     * Update provider profile when certification expires.
     */
    private function updateProfileOnExpiration($provider, $certification): void
    {
        $provider->increment('expired_certifications');
        $provider->decrement('active_certifications');

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is renewed.
     */
    private function updateProfileOnRenewal($provider, $certification): void
    {
        $provider->increment('renewed_certifications');
        $provider->increment('active_certifications');
        $provider->decrement('expired_certifications');

        // Update renewal rate
        $this->updateRenewalRate($provider);

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is suspended.
     */
    private function updateProfileOnSuspension($provider, $certification): void
    {
        $provider->increment('suspended_certifications');
        $provider->decrement('active_certifications');

        // Update trust score
        $this->updateTrustScore($provider);

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update provider profile when certification is revoked.
     */
    private function updateProfileOnRevocation($provider, $certification): void
    {
        $provider->increment('revoked_certifications');
        $provider->decrement('active_certifications');

        // Update trust score
        $this->updateTrustScore($provider);

        // Update certification score
        $this->updateCertificationScore($provider);
    }

    /**
     * Update issuing organization diversity.
     */
    private function updateIssuingOrganizationDiversity($provider, $certification): void
    {
        if ($certification->issuing_organization) {
            $currentOrganizations = $provider->certifications()
                ->distinct('issuing_organization')
                ->pluck('issuing_organization')
                ->count();

            $provider->update(['issuing_organization_diversity' => $currentOrganizations]);
        }
    }

    /**
     * Update certification score based on various factors.
     */
    private function updateCertificationScore($provider): void
    {
        $totalCertifications = $provider->total_certifications ?? 0;
        $verifiedCertifications = $provider->verified_certifications ?? 0;
        $activeCertifications = $provider->active_certifications ?? 0;
        $expiredCertifications = $provider->expired_certifications ?? 0;

        if ($totalCertifications > 0) {
            $verificationRate = ($verifiedCertifications / $totalCertifications) * 100;
            $activeRate = ($activeCertifications / $totalCertifications) * 100;
            $expiryRate = ($expiredCertifications / $totalCertifications) * 100;

            $score = ($verificationRate * 0.4) + ($activeRate * 0.4) + ((100 - $expiryRate) * 0.2);

            $provider->update(['certification_score' => round($score, 2)]);
        }
    }

    /**
     * Update trust score based on verification and compliance.
     */
    private function updateTrustScore($provider): void
    {
        $totalCertifications = $provider->total_certifications ?? 0;
        $verifiedCertifications = $provider->verified_certifications ?? 0;
        $rejectedCertifications = $provider->rejected_certifications ?? 0;
        $suspendedCertifications = $provider->suspended_certifications ?? 0;
        $revokedCertifications = $provider->revoked_certifications ?? 0;

        if ($totalCertifications > 0) {
            $verificationRate = ($verifiedCertifications / $totalCertifications) * 100;
            $complianceRate = (($totalCertifications - $rejectedCertifications - $suspendedCertifications - $revokedCertifications) / $totalCertifications) * 100;

            $trustScore = ($verificationRate * 0.6) + ($complianceRate * 0.4);

            $provider->update(['trust_score' => round($trustScore, 2)]);
        }
    }

    /**
     * Update renewal rate.
     */
    private function updateRenewalRate($provider): void
    {
        $totalCertifications = $provider->total_certifications ?? 0;
        $renewedCertifications = $provider->renewed_certifications ?? 0;

        if ($totalCertifications > 0) {
            $renewalRate = ($renewedCertifications / $totalCertifications) * 100;
            $provider->update(['renewal_rate' => round($renewalRate, 2)]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Provider profile update job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage()
        ]);
    }
}
