<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderContractDTO;
use Fereydooni\Shopping\App\Models\ProviderContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderContractRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?ProviderContract;

    public function findDTO(int $id): ?ProviderContractDTO;

    public function findByProviderId(int $providerId): Collection;

    public function findByProviderIdDTO(int $providerId): Collection;

    public function findByContractNumber(string $contractNumber): ?ProviderContract;

    public function findByContractNumberDTO(string $contractNumber): ?ProviderContractDTO;

    public function findByContractType(string $contractType): Collection;

    public function findByContractTypeDTO(string $contractType): Collection;

    public function findByStatus(string $status): Collection;

    public function findByStatusDTO(string $status): Collection;

    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    public function findByProviderAndType(int $providerId, string $contractType): Collection;

    public function findByProviderAndTypeDTO(int $providerId, string $contractType): Collection;

    // Status-based queries
    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findExpired(): Collection;

    public function findExpiredDTO(): Collection;

    public function findTerminated(): Collection;

    public function findTerminatedDTO(): Collection;

    public function findPendingRenewal(): Collection;

    public function findPendingRenewalDTO(): Collection;

    public function findExpiringSoon(int $days = 30): Collection;

    public function findExpiringSoonDTO(int $days = 30): Collection;

    // User-based queries
    public function findBySignedBy(int $signedBy): Collection;

    public function findBySignedByDTO(int $signedBy): Collection;

    public function findByRenewalDate(string $renewalDate): Collection;

    public function findByRenewalDateDTO(string $renewalDate): Collection;

    // Financial queries
    public function findByCommissionRateRange(float $minRate, float $maxRate): Collection;

    public function findByCommissionRateRangeDTO(float $minRate, float $maxRate): Collection;

    public function findByContractValueRange(float $minValue, float $maxValue): Collection;

    public function findByContractValueRangeDTO(float $minValue, float $maxValue): Collection;

    // Create and update operations
    public function create(array $data): ProviderContract;

    public function createAndReturnDTO(array $data): ProviderContractDTO;

    public function update(ProviderContract $contract, array $data): bool;

    public function updateAndReturnDTO(ProviderContract $contract, array $data): ?ProviderContractDTO;

    public function delete(ProviderContract $contract): bool;

    // Contract lifecycle management
    public function activate(ProviderContract $contract): bool;

    public function expire(ProviderContract $contract): bool;

    public function terminate(ProviderContract $contract, ?string $reason = null): bool;

    public function suspend(ProviderContract $contract, ?string $reason = null): bool;

    public function renew(ProviderContract $contract, ?string $newEndDate = null): bool;

    public function sign(ProviderContract $contract, int $signedBy): bool;

    // Contract modifications
    public function updateCommissionRate(ProviderContract $contract, float $newRate): bool;

    public function updatePaymentTerms(ProviderContract $contract, array $newTerms): bool;

    public function extendContract(ProviderContract $contract, string $newEndDate): bool;

    // Provider-specific statistics
    public function getProviderContractCount(int $providerId): int;

    public function getProviderContractCountByType(int $providerId, string $contractType): int;

    public function getProviderContractCountByStatus(int $providerId, string $status): int;

    public function getProviderActiveContracts(int $providerId): Collection;

    public function getProviderActiveContractsDTO(int $providerId): Collection;

    public function getProviderExpiredContracts(int $providerId): Collection;

    public function getProviderExpiredContractsDTO(int $providerId): Collection;

    // Global statistics
    public function getTotalContractCount(): int;

    public function getTotalContractCountByType(string $contractType): int;

    public function getTotalContractCountByStatus(string $status): int;

    public function getActiveContractCount(): int;

    public function getExpiredContractCount(): int;

    public function getTerminatedContractCount(): int;

    public function getExpiringContractCount(int $days = 30): int;

    // Financial statistics
    public function getTotalContractValue(): float;

    public function getAverageContractValue(): float;

    public function getTotalContractValueByType(string $contractType): float;

    // Search operations
    public function searchContracts(string $query): Collection;

    public function searchContractsDTO(string $query): Collection;

    public function searchContractsByProvider(int $providerId, string $query): Collection;

    public function searchContractsByProviderDTO(int $providerId, string $query): Collection;

    // Data operations
    public function exportContractData(array $filters = []): string;

    public function importContractData(string $data): bool;

    // Analytics and reporting
    public function getContractStatistics(): array;

    public function getProviderContractStatistics(int $providerId): array;

    public function getContractTrends(?string $startDate = null, ?string $endDate = null): array;

    // Utility methods
    public function generateContractNumber(): string;

    public function isContractNumberUnique(string $contractNumber): bool;
}
