<?php

namespace App\Traits;

use App\Enums\CertificationStatus;
use App\Enums\CertificationVerificationStatus;
use App\Models\ProviderCertification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasProviderCertificationVerification
{
    /**
     * Verify a provider certification
     */
    public function verifyCertification(
        ProviderCertification $certification,
        int $verifiedBy,
        ?string $notes = null,
        ?string $verificationUrl = null
    ): bool {
        try {
            DB::beginTransaction();

            $certification->update([
                'verification_status' => CertificationVerificationStatus::VERIFIED,
                'verified_at' => now(),
                'verified_by' => $verifiedBy,
                'notes' => $notes,
                'verification_url' => $verificationUrl,
            ]);

            // Update status to active if it was pending
            if ($certification->status === CertificationStatus::PENDING_RENEWAL) {
                $certification->update(['status' => CertificationStatus::ACTIVE]);
            }

            DB::commit();

            // Fire verification event
            event(new \App\Events\ProviderCertification\ProviderCertificationVerified($certification, $verifiedBy));

            Log::info('Provider certification verified', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'verified_by' => $verifiedBy,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to verify provider certification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Reject a provider certification
     */
    public function rejectCertification(
        ProviderCertification $certification,
        int $rejectedBy,
        string $reason,
        ?string $notes = null
    ): bool {
        try {
            DB::beginTransaction();

            $certification->update([
                'verification_status' => CertificationVerificationStatus::REJECTED,
                'verified_at' => now(),
                'verified_by' => $rejectedBy,
                'notes' => $notes,
                'status' => CertificationStatus::SUSPENDED,
            ]);

            DB::commit();

            // Fire rejection event
            event(new \App\Events\ProviderCertification\ProviderCertificationRejected($certification, $rejectedBy, $reason));

            Log::info('Provider certification rejected', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'rejected_by' => $rejectedBy,
                'reason' => $reason,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject provider certification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark certification as pending verification
     */
    public function markAsPendingVerification(ProviderCertification $certification): bool
    {
        try {
            $certification->update([
                'verification_status' => CertificationVerificationStatus::PENDING,
                'verified_at' => null,
                'verified_by' => null,
            ]);

            Log::info('Provider certification marked as pending verification', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark certification as pending verification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark certification as requires update
     */
    public function markAsRequiresUpdate(
        ProviderCertification $certification,
        string $reason,
        ?string $notes = null
    ): bool {
        try {
            $certification->update([
                'verification_status' => CertificationVerificationStatus::REQUIRES_UPDATE,
                'notes' => $notes,
            ]);

            Log::info('Provider certification marked as requires update', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'reason' => $reason,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark certification as requires update', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get certifications pending verification
     */
    public function getPendingVerificationCertifications(): Collection
    {
        return $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::PENDING)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get certifications requiring updates
     */
    public function getCertificationsRequiringUpdates(): Collection
    {
        return $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::REQUIRES_UPDATE)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get verified certifications
     */
    public function getVerifiedCertifications(): Collection
    {
        return $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::VERIFIED)
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Get rejected certifications
     */
    public function getRejectedCertifications(): Collection
    {
        return $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::REJECTED)
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Check if certification is verified
     */
    public function isCertificationVerified(ProviderCertification $certification): bool
    {
        return $certification->verification_status === CertificationVerificationStatus::VERIFIED;
    }

    /**
     * Check if certification is pending verification
     */
    public function isCertificationPendingVerification(ProviderCertification $certification): bool
    {
        return $certification->verification_status === CertificationVerificationStatus::PENDING;
    }

    /**
     * Check if certification is rejected
     */
    public function isCertificationRejected(ProviderCertification $certification): bool
    {
        return $certification->verification_status === CertificationVerificationStatus::REJECTED;
    }

    /**
     * Check if certification requires updates
     */
    public function isCertificationRequiringUpdates(ProviderCertification $certification): bool
    {
        return $certification->verification_status === CertificationVerificationStatus::REQUIRES_UPDATE;
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStatistics(): array
    {
        $certifications = $this->certifications();

        return [
            'total' => $certifications->count(),
            'verified' => $certifications->where('verification_status', CertificationVerificationStatus::VERIFIED)->count(),
            'pending' => $certifications->where('verification_status', CertificationVerificationStatus::PENDING)->count(),
            'rejected' => $certifications->where('verification_status', CertificationVerificationStatus::REJECTED)->count(),
            'requires_update' => $certifications->where('verification_status', CertificationVerificationStatus::REQUIRES_UPDATE)->count(),
            'unverified' => $certifications->where('verification_status', CertificationVerificationStatus::UNVERIFIED)->count(),
        ];
    }

    /**
     * Bulk verify certifications
     */
    public function bulkVerifyCertifications(array $certificationIds, int $verifiedBy, ?string $notes = null): int
    {
        $successCount = 0;

        foreach ($certificationIds as $certificationId) {
            $certification = $this->certifications()->find($certificationId);

            if ($certification && $this->verifyCertification($certification, $verifiedBy, $notes)) {
                $successCount++;
            }
        }

        Log::info('Bulk verification completed', [
            'provider_id' => $this->id,
            'total_requested' => count($certificationIds),
            'successful' => $successCount,
            'verified_by' => $verifiedBy,
        ]);

        return $successCount;
    }

    /**
     * Get verification history
     */
    public function getVerificationHistory(): Collection
    {
        return $this->certifications()
            ->whereNotNull('verified_at')
            ->with(['verifiedByUser'])
            ->orderBy('verified_at', 'desc')
            ->get();
    }
}
