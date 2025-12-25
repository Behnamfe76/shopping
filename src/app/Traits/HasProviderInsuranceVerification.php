<?php

namespace App\Traits;

use App\Services\ProviderInsuranceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

/**
 * Trait HasProviderInsuranceVerification
 *
 * Provides insurance document verification and validation functionality
 * for models that need to manage provider insurance verification.
 */
trait HasProviderInsuranceVerification
{
    /**
     * Verify provider insurance
     */
    public function verifyProviderInsurance(int $insuranceId, int $verifiedBy, ?string $notes = null): bool
    {
        return App::make(ProviderInsuranceService::class)->verify(
            $this->findProviderInsurance($insuranceId),
            $verifiedBy,
            $notes
        );
    }

    /**
     * Reject provider insurance
     */
    public function rejectProviderInsurance(int $insuranceId, int $rejectedBy, string $reason): bool
    {
        return App::make(ProviderInsuranceService::class)->reject(
            $this->findProviderInsurance($insuranceId),
            $rejectedBy,
            $reason
        );
    }

    /**
     * Get insurance pending verification
     */
    public function getInsurancePendingVerification(int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getPendingVerification($limit);
    }

    /**
     * Get insurance pending verification as DTOs
     */
    public function getInsurancePendingVerificationDTO(int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getPendingVerificationDTO($limit);
    }

    /**
     * Get insurance pending verification by provider
     */
    public function getInsurancePendingVerificationByProvider(int $providerId, int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getPendingVerificationByProvider($providerId, $limit);
    }

    /**
     * Get insurance pending verification by provider as DTOs
     */
    public function getInsurancePendingVerificationByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getPendingVerificationByProviderDTO($providerId, $limit);
    }

    /**
     * Get verified insurance count
     */
    public function getVerifiedInsuranceCount(int $providerId): int
    {
        return App::make(ProviderInsuranceService::class)->getVerifiedInsuranceCount($providerId);
    }

    /**
     * Get pending verification count
     */
    public function getPendingVerificationCount(int $providerId): int
    {
        return App::make(ProviderInsuranceService::class)->getPendingVerificationCount($providerId);
    }

    /**
     * Check if insurance is verified
     */
    public function isInsuranceVerified(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);

        return $insurance && $insurance->verification_status === 'verified';
    }

    /**
     * Check if insurance verification is pending
     */
    public function isInsuranceVerificationPending(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);

        return $insurance && $insurance->verification_status === 'pending';
    }

    /**
     * Check if insurance verification is rejected
     */
    public function isInsuranceVerificationRejected(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);

        return $insurance && $insurance->verification_status === 'rejected';
    }

    /**
     * Check if insurance verification is expired
     */
    public function isInsuranceVerificationExpired(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);

        return $insurance && $insurance->verification_status === 'expired';
    }

    /**
     * Get verification analytics by provider
     */
    public function getInsuranceVerificationAnalytics(int $providerId): array
    {
        return App::make(ProviderInsuranceService::class)->getInsuranceAnalyticsByVerificationStatus($providerId, 'verified');
    }

    /**
     * Get global verification analytics
     */
    public function getGlobalInsuranceVerificationAnalytics(): array
    {
        return App::make(ProviderInsuranceService::class)->getGlobalInsuranceAnalyticsByVerificationStatus('verified');
    }
}
