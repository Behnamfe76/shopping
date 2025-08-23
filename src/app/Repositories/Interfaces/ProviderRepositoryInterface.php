<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\DTOs\ProviderDTO;

interface ProviderRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?Provider;
    public function findDTO(int $id): ?ProviderDTO;
    public function findByUserId(int $userId): ?Provider;
    public function findByUserIdDTO(int $userId): ?ProviderDTO;
    public function findByEmail(string $email): ?Provider;
    public function findByEmailDTO(string $email): ?ProviderDTO;
    public function findByPhone(string $phone): ?Provider;
    public function findByPhoneDTO(string $phone): ?ProviderDTO;
    public function findByProviderNumber(string $providerNumber): ?Provider;
    public function findByProviderNumberDTO(string $providerNumber): ?ProviderDTO;
    public function findByCompanyName(string $companyName): ?Provider;
    public function findByCompanyNameDTO(string $companyName): ?ProviderDTO;
    public function findByTaxId(string $taxId): ?Provider;
    public function findByTaxIdDTO(string $taxId): ?ProviderDTO;

    // Status-based queries
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByType(string $type): Collection;
    public function findByTypeDTO(string $type): Collection;
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findInactive(): Collection;
    public function findInactiveDTO(): Collection;
    public function findSuspended(): Collection;
    public function findSuspendedDTO(): Collection;

    // Create and update operations
    public function create(array $data): Provider;
    public function createAndReturnDTO(array $data): ProviderDTO;
    public function update(Provider $provider, array $data): bool;
    public function updateAndReturnDTO(Provider $provider, array $data): ?ProviderDTO;
    public function delete(Provider $provider): bool;

    // Status management
    public function activate(Provider $provider): bool;
    public function deactivate(Provider $provider): bool;
    public function suspend(Provider $provider, string $reason = null): bool;
    public function unsuspend(Provider $provider): bool;

    // Rating management
    public function updateRating(Provider $provider, float $rating): bool;
    public function updateQualityRating(Provider $provider, float $rating): bool;
    public function updateDeliveryRating(Provider $provider, float $rating): bool;
    public function updateCommunicationRating(Provider $provider, float $rating): bool;

    // Financial management
    public function updateCreditLimit(Provider $provider, float $newLimit): bool;
    public function updateCommissionRate(Provider $provider, float $newRate): bool;
    public function updateDiscountRate(Provider $provider, float $newRate): bool;

    // Contract management
    public function extendContract(Provider $provider, string $newEndDate): bool;
    public function terminateContract(Provider $provider, string $reason = null): bool;

    // Statistics and counts
    public function getProviderCount(): int;
    public function getProviderCountByStatus(string $status): int;
    public function getProviderCountByType(string $type): int;
    public function getActiveProviderCount(): int;
    public function getInactiveProviderCount(): int;
    public function getSuspendedProviderCount(): int;
    public function getTotalProviderSpending(): float;
    public function getAverageProviderSpending(): float;
    public function getAverageProviderRating(): float;
    public function getTotalCreditLimit(): float;
    public function getAverageCreditLimit(): float;
    public function getTotalCurrentBalance(): float;
    public function getAverageCurrentBalance(): float;

    // Search operations
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByCompany(string $companyName): Collection;
    public function searchByCompanyDTO(string $companyName): Collection;
    public function searchBySpecialization(string $specialization): Collection;
    public function searchBySpecializationDTO(string $specialization): Collection;

    // Top performers
    public function getTopRated(int $limit = 10): Collection;
    public function getTopRatedDTO(int $limit = 10): Collection;
    public function getTopSpenders(int $limit = 10): Collection;
    public function getTopSpendersDTO(int $limit = 10): Collection;
    public function getMostReliable(int $limit = 10): Collection;
    public function getMostReliableDTO(int $limit = 10): Collection;
    public function getNewestProviders(int $limit = 10): Collection;
    public function getNewestProvidersDTO(int $limit = 10): Collection;
    public function getLongestServing(int $limit = 10): Collection;
    public function getLongestServingDTO(int $limit = 10): Collection;

    // Validation and utilities
    public function validateProvider(array $data): bool;
    public function generateProviderNumber(): string;
    public function isProviderNumberUnique(string $providerNumber): bool;

    // Analytics and reporting
    public function getProviderStats(): array;
    public function getProviderStatsByStatus(): array;
    public function getProviderStatsByType(): array;
    public function getProviderGrowthStats(string $period = 'monthly'): array;
    public function getProviderPerformanceStats(): array;
    public function getProviderQualityStats(): array;
    public function getProviderFinancialStats(): array;
    public function getProviderContractStats(): array;

    // Provider-specific data
    public function getProviderLifetimeValue(int $providerId): float;
    public function getProviderOrderHistory(int $providerId): Collection;
    public function getProviderProducts(int $providerId): Collection;
    public function getProviderInvoices(int $providerId): Collection;
    public function getProviderPayments(int $providerId): Collection;

    // Notes and additional data
    public function addProviderNote(Provider $provider, string $note, string $type = 'general'): bool;
    public function getProviderNotes(Provider $provider): Collection;

    // Specializations and certifications
    public function updateProviderSpecializations(Provider $provider, array $specializations): bool;
    public function getProviderSpecializations(int $providerId): array;
    public function updateProviderCertifications(Provider $provider, array $certifications): bool;
    public function getProviderCertifications(int $providerId): array;

    // Insurance
    public function updateProviderInsurance(Provider $provider, array $insurance): bool;
    public function getProviderInsurance(int $providerId): array;

    // Performance metrics
    public function calculateProviderScore(int $providerId): float;
    public function getProviderPerformanceMetrics(int $providerId): array;
}
