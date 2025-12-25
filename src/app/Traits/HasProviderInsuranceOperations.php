<?php

namespace App\Traits;

use App\DTOs\ProviderInsuranceDTO;
use App\Models\ProviderInsurance;
use App\Services\ProviderInsuranceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;

/**
 * Trait HasProviderInsuranceOperations
 *
 * Provides core provider insurance CRUD operations and basic functionality
 * for models that need to manage provider insurance.
 */
trait HasProviderInsuranceOperations
{
    /**
     * Get all provider insurance records
     */
    public function getAllProviderInsurance(): Collection
    {
        return App::make(ProviderInsuranceService::class)->all();
    }

    /**
     * Get all provider insurance records as DTOs
     */
    public function getAllProviderInsuranceDTO(): Collection
    {
        return App::make(ProviderInsuranceService::class)->allDTO();
    }

    /**
     * Paginate provider insurance records
     */
    public function paginateProviderInsurance(int $perPage = 15): LengthAwarePaginator
    {
        return App::make(ProviderInsuranceService::class)->paginate($perPage);
    }

    /**
     * Paginate provider insurance records as DTOs
     */
    public function paginateProviderInsuranceDTO(int $perPage = 15): LengthAwarePaginator
    {
        return App::make(ProviderInsuranceService::class)->paginateDTO($perPage);
    }

    /**
     * Simple paginate provider insurance records
     */
    public function simplePaginateProviderInsurance(int $perPage = 15): Paginator
    {
        return App::make(ProviderInsuranceService::class)->simplePaginate($perPage);
    }

    /**
     * Cursor paginate provider insurance records
     */
    public function cursorPaginateProviderInsurance(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return App::make(ProviderInsuranceService::class)->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find provider insurance by ID
     */
    public function findProviderInsurance(int $id): ?ProviderInsurance
    {
        return app(ProviderInsuranceService::class)->find($id);
    }

    /**
     * Find provider insurance by ID as DTO
     */
    public function findProviderInsuranceDTO(int $id): ?ProviderInsuranceDTO
    {
        return app(ProviderInsuranceService::class)->findDTO($id);
    }

    /**
     * Find provider insurance by provider ID
     */
    public function findProviderInsuranceByProviderId(int $providerId): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderId($providerId);
    }

    /**
     * Find provider insurance by provider ID as DTOs
     */
    public function findProviderInsuranceByProviderIdDTO(int $providerId): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderIdDTO($providerId);
    }

    /**
     * Find provider insurance by insurance type
     */
    public function findProviderInsuranceByType(string $insuranceType): Collection
    {
        return app(ProviderInsuranceService::class)->findByInsuranceType($insuranceType);
    }

    /**
     * Find provider insurance by insurance type as DTOs
     */
    public function findProviderInsuranceByTypeDTO(string $insuranceType): Collection
    {
        return app(ProviderInsuranceService::class)->findByInsuranceTypeDTO($insuranceType);
    }

    /**
     * Find provider insurance by status
     */
    public function findProviderInsuranceByStatus(string $status): Collection
    {
        return app(ProviderInsuranceService::class)->findByStatus($status);
    }

    /**
     * Find provider insurance by status as DTOs
     */
    public function findProviderInsuranceByStatusDTO(string $status): Collection
    {
        return app(ProviderInsuranceService::class)->findByStatusDTO($status);
    }

    /**
     * Find provider insurance by verification status
     */
    public function findProviderInsuranceByVerificationStatus(string $verificationStatus): Collection
    {
        return app(ProviderInsuranceService::class)->findByVerificationStatus($verificationStatus);
    }

    /**
     * Find provider insurance by verification status as DTOs
     */
    public function findProviderInsuranceByVerificationStatusDTO(string $verificationStatus): Collection
    {
        return app(ProviderInsuranceService::class)->findByVerificationStatusDTO($verificationStatus);
    }

    /**
     * Find provider insurance by policy number
     */
    public function findProviderInsuranceByPolicyNumber(string $policyNumber): ?ProviderInsurance
    {
        return app(ProviderInsuranceService::class)->findByPolicyNumber($policyNumber);
    }

    /**
     * Find provider insurance by policy number as DTO
     */
    public function findProviderInsuranceByPolicyNumberDTO(string $policyNumber): ?ProviderInsuranceDTO
    {
        return app(ProviderInsuranceService::class)->findByPolicyNumberDTO($policyNumber);
    }

    /**
     * Find provider insurance by provider name
     */
    public function findProviderInsuranceByProviderName(string $providerName): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderName($providerName);
    }

    /**
     * Find provider insurance by provider name as DTOs
     */
    public function findProviderInsuranceByProviderNameDTO(string $providerName): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderNameDTO($providerName);
    }

    /**
     * Find active provider insurance
     */
    public function findActiveProviderInsurance(): Collection
    {
        return app(ProviderInsuranceService::class)->findActive();
    }

    /**
     * Find active provider insurance as DTOs
     */
    public function findActiveProviderInsuranceDTO(): Collection
    {
        return app(ProviderInsuranceService::class)->findActiveDTO();
    }

    /**
     * Find expired provider insurance
     */
    public function findExpiredProviderInsurance(): Collection
    {
        return app(ProviderInsuranceService::class)->findExpired();
    }

    /**
     * Find expired provider insurance as DTOs
     */
    public function findExpiredProviderInsuranceDTO(): Collection
    {
        return app(ProviderInsuranceService::class)->findExpiredDTO();
    }

    /**
     * Find provider insurance expiring soon
     */
    public function findProviderInsuranceExpiringSoon(int $days = 30): Collection
    {
        return app(ProviderInsuranceService::class)->findExpiringSoon($days);
    }

    /**
     * Find provider insurance expiring soon as DTOs
     */
    public function findProviderInsuranceExpiringSoonDTO(int $days = 30): Collection
    {
        return app(ProviderInsuranceService::class)->findExpiringSoonDTO($days);
    }

    /**
     * Find verified provider insurance
     */
    public function findVerifiedProviderInsurance(): Collection
    {
        return app(ProviderInsuranceService::class)->findVerified();
    }

    /**
     * Find verified provider insurance as DTOs
     */
    public function findVerifiedProviderInsuranceDTO(): Collection
    {
        return app(ProviderInsuranceService::class)->findVerifiedDTO();
    }

    /**
     * Find pending verification provider insurance
     */
    public function findPendingVerificationProviderInsurance(): Collection
    {
        return app(ProviderInsuranceService::class)->findPendingVerification();
    }

    /**
     * Find pending verification provider insurance as DTOs
     */
    public function findPendingVerificationProviderInsuranceDTO(): Collection
    {
        return app(ProviderInsuranceService::class)->findPendingVerificationDTO();
    }

    /**
     * Find provider insurance by provider and type
     */
    public function findProviderInsuranceByProviderAndType(int $providerId, string $insuranceType): ?ProviderInsurance
    {
        return app(ProviderInsuranceService::class)->findByProviderAndType($providerId, $insuranceType);
    }

    /**
     * Find provider insurance by provider and type as DTO
     */
    public function findProviderInsuranceByProviderAndTypeDTO(int $providerId, string $insuranceType): ?ProviderInsuranceDTO
    {
        return app(ProviderInsuranceService::class)->findByProviderAndTypeDTO($providerId, $insuranceType);
    }

    /**
     * Find provider insurance by provider and status
     */
    public function findProviderInsuranceByProviderAndStatus(int $providerId, string $status): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderAndStatus($providerId, $status);
    }

    /**
     * Find provider insurance by provider and status as DTOs
     */
    public function findProviderInsuranceByProviderAndStatusDTO(int $providerId, string $status): Collection
    {
        return app(ProviderInsuranceService::class)->findByProviderAndStatusDTO($providerId, $status);
    }

    /**
     * Find provider insurance by date range
     */
    public function findProviderInsuranceByDateRange(string $startDate, string $endDate): Collection
    {
        return app(ProviderInsuranceService::class)->findByDateRange($startDate, $endDate);
    }

    /**
     * Find provider insurance by date range as DTOs
     */
    public function findProviderInsuranceByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return app(ProviderInsuranceService::class)->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Find provider insurance by coverage amount range
     */
    public function findProviderInsuranceByCoverageAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return app(ProviderInsuranceService::class)->findByCoverageAmountRange($minAmount, $maxAmount);
    }

    /**
     * Find provider insurance by coverage amount range as DTOs
     */
    public function findProviderInsuranceByCoverageAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return app(ProviderInsuranceService::class)->findByCoverageAmountRangeDTO($minAmount, $maxAmount);
    }

    /**
     * Create provider insurance
     */
    public function createProviderInsurance(array $data): ProviderInsurance
    {
        return app(ProviderInsuranceService::class)->create($data);
    }

    /**
     * Create provider insurance and return DTO
     */
    public function createProviderInsuranceAndReturnDTO(array $data): ProviderInsuranceDTO
    {
        return app(ProviderInsuranceService::class)->createAndReturnDTO($data);
    }

    /**
     * Update provider insurance
     */
    public function updateProviderInsurance(ProviderInsurance $providerInsurance, array $data): bool
    {
        return app(ProviderInsuranceService::class)->update($providerInsurance, $data);
    }

    /**
     * Update provider insurance and return DTO
     */
    public function updateProviderInsuranceAndReturnDTO(ProviderInsurance $providerInsurance, array $data): ?ProviderInsuranceDTO
    {
        return app(ProviderInsuranceService::class)->updateAndReturnDTO($providerInsurance, $data);
    }

    /**
     * Delete provider insurance
     */
    public function deleteProviderInsurance(ProviderInsurance $providerInsurance): bool
    {
        return app(ProviderInsuranceService::class)->delete($providerInsurance);
    }

    /**
     * Search provider insurance
     */
    public function searchProviderInsurance(string $query): Collection
    {
        return app(ProviderInsuranceService::class)->searchInsurance($query);
    }

    /**
     * Search provider insurance as DTOs
     */
    public function searchProviderInsuranceDTO(string $query): Collection
    {
        return app(ProviderInsuranceService::class)->searchInsuranceDTO($query);
    }

    /**
     * Search provider insurance by provider
     */
    public function searchProviderInsuranceByProvider(int $providerId, string $query): Collection
    {
        return app(ProviderInsuranceService::class)->searchInsuranceByProvider($providerId, $query);
    }

    /**
     * Search provider insurance by provider as DTOs
     */
    public function searchProviderInsuranceByProviderDTO(int $providerId, string $query): Collection
    {
        return app(ProviderInsuranceService::class)->searchInsuranceByProviderDTO($providerId, $query);
    }
}
