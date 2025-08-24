<?php

namespace App\Facades;

use App\DTOs\ProviderInsuranceDTO;
use App\Models\ProviderInsurance;
use App\Services\ProviderInsuranceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Facade;

/**
 * ProviderInsurance Facade
 *
 * Provides static access to provider insurance functionality
 * with method chaining support and performance optimization.
 */
class ProviderInsurance extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ProviderInsuranceService::class;
    }

    /**
     * Get all provider insurance records
     */
    public static function all(): Collection
    {
        return static::getFacadeRoot()->all();
    }

    /**
     * Get all provider insurance records as DTOs
     */
    public static function allDTO(): Collection
    {
        return static::getFacadeRoot()->allDTO();
    }

    /**
     * Paginate provider insurance records
     */
    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return static::getFacadeRoot()->paginate($perPage);
    }

    /**
     * Paginate provider insurance records as DTOs
     */
    public static function paginateDTO(int $perPage = 15): LengthAwarePaginator
    {
        return static::getFacadeRoot()->paginateDTO($perPage);
    }

    /**
     * Simple paginate provider insurance records
     */
    public static function simplePaginate(int $perPage = 15): Paginator
    {
        return static::getFacadeRoot()->simplePaginate($perPage);
    }

    /**
     * Cursor paginate provider insurance records
     */
    public static function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return static::getFacadeRoot()->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find provider insurance by ID
     */
    public static function find(int $id): ?ProviderInsurance
    {
        return static::getFacadeRoot()->find($id);
    }

    /**
     * Find provider insurance by ID as DTO
     */
    public static function findDTO(int $id): ?ProviderInsuranceDTO
    {
        return static::getFacadeRoot()->findDTO($id);
    }

    /**
     * Find provider insurance by provider ID
     */
    public static function findByProviderId(int $providerId): Collection
    {
        return static::getFacadeRoot()->findByProviderId($providerId);
    }

    /**
     * Find provider insurance by provider ID as DTOs
     */
    public static function findByProviderIdDTO(int $providerId): Collection
    {
        return static::getFacadeRoot()->findByProviderIdDTO($providerId);
    }

    /**
     * Find provider insurance by insurance type
     */
    public static function findByInsuranceType(string $insuranceType): Collection
    {
        return static::getFacadeRoot()->findByInsuranceType($insuranceType);
    }

    /**
     * Find provider insurance by insurance type as DTOs
     */
    public static function findByInsuranceTypeDTO(string $insuranceType): Collection
    {
        return static::getFacadeRoot()->findByInsuranceTypeDTO($insuranceType);
    }

    /**
     * Find provider insurance by status
     */
    public static function findByStatus(string $status): Collection
    {
        return static::getFacadeRoot()->findByStatus($status);
    }

    /**
     * Find provider insurance by status as DTOs
     */
    public static function findByStatusDTO(string $status): Collection
    {
        return static::getFacadeRoot()->findByStatusDTO($status);
    }

    /**
     * Find provider insurance by verification status
     */
    public static function findByVerificationStatus(string $verificationStatus): Collection
    {
        return static::getFacadeRoot()->findByVerificationStatus($verificationStatus);
    }

    /**
     * Find provider insurance by verification status as DTOs
     */
    public static function findByVerificationStatusDTO(string $verificationStatus): Collection
    {
        return static::getFacadeRoot()->findByVerificationStatusDTO($verificationStatus);
    }

    /**
     * Find provider insurance by policy number
     */
    public static function findByPolicyNumber(string $policyNumber): ?ProviderInsurance
    {
        return static::getFacadeRoot()->findByPolicyNumber($policyNumber);
    }

    /**
     * Find provider insurance by policy number as DTO
     */
    public static function findByPolicyNumberDTO(string $policyNumber): ?ProviderInsuranceDTO
    {
        return static::getFacadeRoot()->findByPolicyNumberDTO($policyNumber);
    }

    /**
     * Find provider insurance by provider name
     */
    public static function findByProviderName(string $providerName): Collection
    {
        return static::getFacadeRoot()->findByProviderName($providerName);
    }

    /**
     * Find provider insurance by provider name as DTOs
     */
    public static function findByProviderNameDTO(string $providerName): Collection
    {
        return static::getFacadeRoot()->findByProviderNameDTO($providerName);
    }

    /**
     * Find active provider insurance
     */
    public static function findActive(): Collection
    {
        return static::getFacadeRoot()->findActive();
    }

    /**
     * Find active provider insurance as DTOs
     */
    public static function findActiveDTO(): Collection
    {
        return static::getFacadeRoot()->findActiveDTO();
    }

    /**
     * Find expired provider insurance
     */
    public static function findExpired(): Collection
    {
        return static::getFacadeRoot()->findExpired();
    }

    /**
     * Find expired provider insurance as DTOs
     */
    public static function findExpiredDTO(): Collection
    {
        return static::getFacadeRoot()->findExpiredDTO();
    }

    /**
     * Find provider insurance expiring soon
     */
    public static function findExpiringSoon(int $days = 30): Collection
    {
        return static::getFacadeRoot()->findExpiringSoon($days);
    }

    /**
     * Find provider insurance expiring soon as DTOs
     */
    public static function findExpiringSoonDTO(int $days = 30): Collection
    {
        return static::getFacadeRoot()->findExpiringSoonDTO($days);
    }

    /**
     * Find verified provider insurance
     */
    public static function findVerified(): Collection
    {
        return static::getFacadeRoot()->findVerified();
    }

    /**
     * Find verified provider insurance as DTOs
     */
    public static function findVerifiedDTO(): Collection
    {
        return static::getFacadeRoot()->findVerifiedDTO();
    }

    /**
     * Find pending verification provider insurance
     */
    public static function findPendingVerification(): Collection
    {
        return static::getFacadeRoot()->findPendingVerification();
    }

    /**
     * Find pending verification provider insurance as DTOs
     */
    public static function findPendingVerificationDTO(): Collection
    {
        return static::getFacadeRoot()->findPendingVerificationDTO();
    }

    /**
     * Find provider insurance by provider and type
     */
    public static function findByProviderAndType(int $providerId, string $insuranceType): ?ProviderInsurance
    {
        return static::getFacadeRoot()->findByProviderAndType($providerId, $insuranceType);
    }

    /**
     * Find provider insurance by provider and type as DTO
     */
    public static function findByProviderAndTypeDTO(int $providerId, string $insuranceType): ?ProviderInsuranceDTO
    {
        return static::getFacadeRoot()->findByProviderAndTypeDTO($providerId, $insuranceType);
    }

    /**
     * Find provider insurance by provider and status
     */
    public static function findByProviderAndStatus(int $providerId, string $status): Collection
    {
        return static::getFacadeRoot()->findByProviderAndStatus($providerId, $status);
    }

    /**
     * Find provider insurance by provider and status as DTOs
     */
    public static function findByProviderAndStatusDTO(int $providerId, string $status): Collection
    {
        return static::getFacadeRoot()->findByProviderAndStatusDTO($providerId, $status);
    }

    /**
     * Find provider insurance by date range
     */
    public static function findByDateRange(string $startDate, string $endDate): Collection
    {
        return static::getFacadeRoot()->findByDateRange($startDate, $endDate);
    }

    /**
     * Find provider insurance by date range as DTOs
     */
    public static function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return static::getFacadeRoot()->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Find provider insurance by coverage amount range
     */
    public static function findByCoverageAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return static::getFacadeRoot()->findByCoverageAmountRange($minAmount, $maxAmount);
    }

    /**
     * Find provider insurance by coverage amount range as DTOs
     */
    public static function findByCoverageAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return static::getFacadeRoot()->findByCoverageAmountRangeDTO($minAmount, $maxAmount);
    }

    /**
     * Create provider insurance
     */
    public static function create(array $data): ProviderInsurance
    {
        return static::getFacadeRoot()->create($data);
    }

    /**
     * Create provider insurance and return DTO
     */
    public static function createAndReturnDTO(array $data): ProviderInsuranceDTO
    {
        return static::getFacadeRoot()->createAndReturnDTO($data);
    }

    /**
     * Update provider insurance
     */
    public static function update(ProviderInsurance $providerInsurance, array $data): bool
    {
        return static::getFacadeRoot()->update($providerInsurance, $data);
    }

    /**
     * Update provider insurance and return DTO
     */
    public static function updateAndReturnDTO(ProviderInsurance $providerInsurance, array $data): ?ProviderInsuranceDTO
    {
        return static::getFacadeRoot()->updateAndReturnDTO($providerInsurance, $data);
    }

    /**
     * Delete provider insurance
     */
    public static function delete(ProviderInsurance $providerInsurance): bool
    {
        return static::getFacadeRoot()->delete($providerInsurance);
    }

    /**
     * Activate provider insurance
     */
    public static function activate(ProviderInsurance $providerInsurance): bool
    {
        return static::getFacadeRoot()->activate($providerInsurance);
    }

    /**
     * Deactivate provider insurance
     */
    public static function deactivate(ProviderInsurance $providerInsurance): bool
    {
        return static::getFacadeRoot()->deactivate($providerInsurance);
    }

    /**
     * Expire provider insurance
     */
    public static function expire(ProviderInsurance $providerInsurance): bool
    {
        return static::getFacadeRoot()->expire($providerInsurance);
    }

    /**
     * Cancel provider insurance
     */
    public static function cancel(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        return static::getFacadeRoot()->cancel($providerInsurance, $reason);
    }

    /**
     * Suspend provider insurance
     */
    public static function suspend(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        return static::getFacadeRoot()->suspend($providerInsurance, $reason);
    }

    /**
     * Verify provider insurance
     */
    public static function verify(ProviderInsurance $providerInsurance, int $verifiedBy, string $notes = null): bool
    {
        return static::getFacadeRoot()->verify($providerInsurance, $verifiedBy, $notes);
    }

    /**
     * Reject provider insurance
     */
    public static function reject(ProviderInsurance $providerInsurance, int $rejectedBy, string $reason): bool
    {
        return static::getFacadeRoot()->reject($providerInsurance, $rejectedBy, $reason);
    }

    /**
     * Renew provider insurance
     */
    public static function renew(ProviderInsurance $providerInsurance, array $renewalData): bool
    {
        return static::getFacadeRoot()->renew($providerInsurance, $renewalData);
    }

    /**
     * Add document to provider insurance
     */
    public static function addDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        return static::getFacadeRoot()->addDocument($providerInsurance, $documentPath);
    }

    /**
     * Remove document from provider insurance
     */
    public static function removeDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        return static::getFacadeRoot()->removeDocument($providerInsurance, $documentPath);
    }

    /**
     * Search provider insurance
     */
    public static function search(string $query): Collection
    {
        return static::getFacadeRoot()->searchInsurance($query);
    }

    /**
     * Search provider insurance as DTOs
     */
    public static function searchDTO(string $query): Collection
    {
        return static::getFacadeRoot()->searchInsuranceDTO($query);
    }

    /**
     * Search provider insurance by provider
     */
    public static function searchByProvider(int $providerId, string $query): Collection
    {
        return static::getFacadeRoot()->searchInsuranceByProvider($providerId, $query);
    }

    /**
     * Search provider insurance by provider as DTOs
     */
    public static function searchByProviderDTO(int $providerId, string $query): Collection
    {
        return static::getFacadeRoot()->searchInsuranceByProviderDTO($providerId, $query);
    }

    /**
     * Get insurance count by provider
     */
    public static function getCountByProvider(int $providerId): int
    {
        return static::getFacadeRoot()->getInsuranceCount($providerId);
    }

    /**
     * Get active insurance count by provider
     */
    public static function getActiveCountByProvider(int $providerId): int
    {
        return static::getFacadeRoot()->getActiveInsuranceCount($providerId);
    }

    /**
     * Get expired insurance count by provider
     */
    public static function getExpiredCountByProvider(int $providerId): int
    {
        return static::getFacadeRoot()->getExpiredInsuranceCount($providerId);
    }

    /**
     * Get expiring soon count by provider
     */
    public static function getExpiringSoonCountByProvider(int $providerId, int $days = 30): int
    {
        return static::getFacadeRoot()->getExpiringSoonCount($providerId, $days);
    }

    /**
     * Get verified insurance count by provider
     */
    public static function getVerifiedCountByProvider(int $providerId): int
    {
        return static::getFacadeRoot()->getVerifiedInsuranceCount($providerId);
    }

    /**
     * Get pending verification count by provider
     */
    public static function getPendingVerificationCountByProvider(int $providerId): int
    {
        return static::getFacadeRoot()->getPendingVerificationCount($providerId);
    }

    /**
     * Get total coverage amount by provider
     */
    public static function getTotalCoverageAmountByProvider(int $providerId): float
    {
        return static::getFacadeRoot()->getTotalCoverageAmountByProvider($providerId);
    }

    /**
     * Get average coverage amount by provider
     */
    public static function getAverageCoverageAmountByProvider(int $providerId): float
    {
        return static::getFacadeRoot()->getAverageCoverageAmountByProvider($providerId);
    }

    /**
     * Get expiring insurance
     */
    public static function getExpiringInsurance(int $limit = 10): Collection
    {
        return static::getFacadeRoot()->getExpiringInsurance($limit);
    }

    /**
     * Get expiring insurance as DTOs
     */
    public static function getExpiringInsuranceDTO(int $limit = 10): Collection
    {
        return static::getFacadeRoot()->getExpiringInsuranceDTO($limit);
    }

    /**
     * Get pending verification
     */
    public static function getPendingVerification(int $limit = 10): Collection
    {
        return static::getFacadeRoot()->getPendingVerification($limit);
    }

    /**
     * Get pending verification as DTOs
     */
    public static function getPendingVerificationDTO(int $limit = 10): Collection
    {
        return static::getFacadeRoot()->getPendingVerificationDTO($limit);
    }

    /**
     * Get insurance analytics by provider
     */
    public static function getAnalyticsByProvider(int $providerId): array
    {
        return static::getFacadeRoot()->getInsuranceAnalytics($providerId);
    }

    /**
     * Get global insurance analytics
     */
    public static function getGlobalAnalytics(): array
    {
        return static::getFacadeRoot()->getGlobalInsuranceAnalytics();
    }
}
