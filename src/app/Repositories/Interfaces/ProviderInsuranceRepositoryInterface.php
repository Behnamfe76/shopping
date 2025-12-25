<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderInsuranceDTO;
use Fereydooni\Shopping\App\Models\ProviderInsurance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderInsuranceRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?ProviderInsurance;

    public function findDTO(int $id): ?ProviderInsuranceDTO;

    public function findByProviderId(int $providerId): Collection;

    public function findByProviderIdDTO(int $providerId): Collection;

    public function findByInsuranceType(string $insuranceType): Collection;

    public function findByInsuranceTypeDTO(string $insuranceType): Collection;

    public function findByStatus(string $status): Collection;

    public function findByStatusDTO(string $status): Collection;

    public function findByVerificationStatus(string $verificationStatus): Collection;

    public function findByVerificationStatusDTO(string $verificationStatus): Collection;

    public function findByPolicyNumber(string $policyNumber): ?ProviderInsurance;

    public function findByPolicyNumberDTO(string $policyNumber): ?ProviderInsuranceDTO;

    public function findByProviderName(string $providerName): Collection;

    public function findByProviderNameDTO(string $providerName): Collection;

    // Status-based queries
    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findExpired(): Collection;

    public function findExpiredDTO(): Collection;

    public function findExpiringSoon(int $days = 30): Collection;

    public function findExpiringSoonDTO(int $days = 30): Collection;

    public function findVerified(): Collection;

    public function findVerifiedDTO(): Collection;

    public function findPendingVerification(): Collection;

    public function findPendingVerificationDTO(): Collection;

    // Combined queries
    public function findByProviderAndType(int $providerId, string $insuranceType): ?ProviderInsurance;

    public function findByProviderAndTypeDTO(int $providerId, string $insuranceType): ?ProviderInsuranceDTO;

    public function findByProviderAndStatus(int $providerId, string $status): Collection;

    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection;

    // Date and amount range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    public function findByCoverageAmountRange(float $minAmount, float $maxAmount): Collection;

    public function findByCoverageAmountRangeDTO(float $minAmount, float $maxAmount): Collection;

    // Create and update operations
    public function create(array $data): ProviderInsurance;

    public function createAndReturnDTO(array $data): ProviderInsuranceDTO;

    public function update(ProviderInsurance $providerInsurance, array $data): bool;

    public function updateAndReturnDTO(ProviderInsurance $providerInsurance, array $data): ?ProviderInsuranceDTO;

    public function delete(ProviderInsurance $providerInsurance): bool;

    // Status management
    public function activate(ProviderInsurance $providerInsurance): bool;

    public function deactivate(ProviderInsurance $providerInsurance): bool;

    public function expire(ProviderInsurance $providerInsurance): bool;

    public function cancel(ProviderInsurance $providerInsurance, ?string $reason = null): bool;

    public function suspend(ProviderInsurance $providerInsurance, ?string $reason = null): bool;

    // Verification management
    public function verify(ProviderInsurance $providerInsurance, int $verifiedBy, ?string $notes = null): bool;

    public function reject(ProviderInsurance $providerInsurance, int $rejectedBy, string $reason): bool;

    // Renewal management
    public function renew(ProviderInsurance $providerInsurance, array $renewalData): bool;

    // Document management
    public function addDocument(ProviderInsurance $providerInsurance, string $documentPath): bool;

    public function removeDocument(ProviderInsurance $providerInsurance, string $documentPath): bool;

    // Count operations
    public function getInsuranceCount(int $providerId): int;

    public function getInsuranceCountByType(int $providerId, string $insuranceType): int;

    public function getInsuranceCountByStatus(int $providerId, string $status): int;

    public function getInsuranceCountByVerificationStatus(int $providerId, string $verificationStatus): int;

    public function getActiveInsuranceCount(int $providerId): int;

    public function getExpiredInsuranceCount(int $providerId): int;

    public function getExpiringSoonCount(int $providerId, int $days = 30): int;

    public function getVerifiedInsuranceCount(int $providerId): int;

    public function getPendingVerificationCount(int $providerId): int;

    // Global count operations
    public function getTotalInsuranceCount(): int;

    public function getTotalInsuranceCountByType(string $insuranceType): int;

    public function getTotalInsuranceCountByStatus(string $status): int;

    public function getTotalInsuranceCountByVerificationStatus(string $verificationStatus): int;

    public function getTotalActiveInsuranceCount(): int;

    public function getTotalExpiredInsuranceCount(): int;

    public function getTotalExpiringSoonCount(int $days = 30): int;

    public function getTotalVerifiedInsuranceCount(): int;

    public function getTotalPendingVerificationCount(): int;

    // Coverage amount operations
    public function getTotalCoverageAmount(): float;

    public function getAverageCoverageAmount(): float;

    public function getTotalCoverageAmountByProvider(int $providerId): float;

    public function getAverageCoverageAmountByProvider(int $providerId): float;

    public function getTotalCoverageAmountByType(string $insuranceType): float;

    public function getAverageCoverageAmountByType(string $insuranceType): float;

    // Expiring insurance queries
    public function getExpiringInsurance(int $limit = 10): Collection;

    public function getExpiringInsuranceDTO(int $limit = 10): Collection;

    public function getExpiringInsuranceByProvider(int $providerId, int $limit = 10): Collection;

    public function getExpiringInsuranceByProviderDTO(int $providerId, int $limit = 10): Collection;

    // Pending verification queries
    public function getPendingVerification(int $limit = 10): Collection;

    public function getPendingVerificationDTO(int $limit = 10): Collection;

    public function getPendingVerificationByProvider(int $providerId, int $limit = 10): Collection;

    public function getPendingVerificationByProviderDTO(int $providerId, int $limit = 10): Collection;

    // Search operations
    public function searchInsurance(string $query): Collection;

    public function searchInsuranceDTO(string $query): Collection;

    public function searchInsuranceByProvider(int $providerId, string $query): Collection;

    public function searchInsuranceByProviderDTO(int $providerId, string $query): Collection;

    // Analytics operations
    public function getInsuranceAnalytics(int $providerId): array;

    public function getInsuranceAnalyticsByType(int $providerId, string $insuranceType): array;

    public function getInsuranceAnalyticsByStatus(int $providerId, string $status): array;

    public function getInsuranceAnalyticsByVerificationStatus(int $providerId, string $verificationStatus): array;

    // Global analytics operations
    public function getGlobalInsuranceAnalytics(): array;

    public function getGlobalInsuranceAnalyticsByType(string $insuranceType): array;

    public function getGlobalInsuranceAnalyticsByStatus(string $status): array;

    public function getGlobalInsuranceAnalyticsByVerificationStatus(string $verificationStatus): array;
}
