<?php

namespace App\Traits;

use App\Services\ProviderInsuranceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

/**
 * Trait HasProviderInsuranceRenewalManagement
 *
 * Provides insurance renewal and expiration management functionality
 * for models that need to manage provider insurance renewals.
 */
trait HasProviderInsuranceRenewalManagement
{
    /**
     * Renew provider insurance
     */
    public function renewProviderInsurance(int $insuranceId, array $renewalData): bool
    {
        return App::make(ProviderInsuranceService::class)->renew(
            $this->findProviderInsurance($insuranceId),
            $renewalData
        );
    }

    /**
     * Get expiring insurance
     */
    public function getExpiringInsurance(int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getExpiringInsurance($limit);
    }

    /**
     * Get expiring insurance as DTOs
     */
    public function getExpiringInsuranceDTO(int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getExpiringInsuranceDTO($limit);
    }

    /**
     * Get expiring insurance by provider
     */
    public function getExpiringInsuranceByProvider(int $providerId, int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getExpiringInsuranceByProvider($providerId, $limit);
    }

    /**
     * Get expiring insurance by provider as DTOs
     */
    public function getExpiringInsuranceByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        return App::make(ProviderInsuranceService::class)->getExpiringInsuranceByProviderDTO($providerId, $limit);
    }

    /**
     * Get expiring soon count
     */
    public function getExpiringSoonCount(int $providerId, int $days = 30): int
    {
        return App::make(ProviderInsuranceService::class)->getExpiringSoonCount($providerId, $days);
    }

    /**
     * Get expired insurance count
     */
    public function getExpiredInsuranceCount(int $providerId): int
    {
        return App::make(ProviderInsuranceService::class)->getExpiredInsuranceCount($providerId);
    }

    /**
     * Get active insurance count
     */
    public function getActiveInsuranceCount(int $providerId): int
    {
        return App::make(ProviderInsuranceService::class)->getActiveInsuranceCount($providerId);
    }

    /**
     * Check if insurance is expiring soon
     */
    public function isInsuranceExpiringSoon(int $insuranceId, int $days = 30): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);
        if (! $insurance) {
            return false;
        }

        $expirationDate = \Carbon\Carbon::parse($insurance->end_date);
        $now = \Carbon\Carbon::now();

        return $expirationDate->diffInDays($now) <= $days && $expirationDate->isFuture();
    }

    /**
     * Check if insurance is expired
     */
    public function isInsuranceExpired(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);
        if (! $insurance) {
            return false;
        }

        $expirationDate = \Carbon\Carbon::parse($insurance->end_date);

        return $expirationDate->isPast();
    }

    /**
     * Check if insurance is active
     */
    public function isInsuranceActive(int $insuranceId): bool
    {
        $insurance = $this->findProviderInsurance($insuranceId);

        return $insurance && $insurance->status === 'active';
    }

    /**
     * Get insurance by date range
     */
    public function getInsuranceByDateRange(string $startDate, string $endDate): Collection
    {
        return App::make(ProviderInsuranceService::class)->findByDateRange($startDate, $endDate);
    }

    /**
     * Get insurance by date range as DTOs
     */
    public function getInsuranceByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return App::make(ProviderInsuranceService::class)->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Get renewal analytics by provider
     */
    public function getInsuranceRenewalAnalytics(int $providerId): array
    {
        return App::make(ProviderInsuranceService::class)->getInsuranceAnalytics($providerId);
    }

    /**
     * Get global renewal analytics
     */
    public function getGlobalInsuranceRenewalAnalytics(): array
    {
        return App::make(ProviderInsuranceService::class)->getGlobalInsuranceAnalytics();
    }
}
