<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderSpecializationDTO;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderSpecializationRepositoryInterface
{
    /**
     * Get all specializations.
     */
    public function all(): Collection;

    /**
     * Get paginated specializations.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated specializations.
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated specializations.
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find specialization by ID.
     */
    public function find(int $id): ?ProviderSpecialization;

    /**
     * Find specialization by ID and return DTO.
     */
    public function findDTO(int $id): ?ProviderSpecializationDTO;

    /**
     * Find specializations by provider ID.
     */
    public function findByProviderId(int $providerId): Collection;

    /**
     * Find specializations by provider ID and return DTOs.
     */
    public function findByProviderIdDTO(int $providerId): Collection;

    /**
     * Find specializations by specialization name.
     */
    public function findBySpecializationName(string $specializationName): Collection;

    /**
     * Find specializations by specialization name and return DTOs.
     */
    public function findBySpecializationNameDTO(string $specializationName): Collection;

    /**
     * Find specializations by category.
     */
    public function findByCategory(string $category): Collection;

    /**
     * Find specializations by category and return DTOs.
     */
    public function findByCategoryDTO(string $category): Collection;

    /**
     * Find specializations by proficiency level.
     */
    public function findByProficiencyLevel(string $proficiencyLevel): Collection;

    /**
     * Find specializations by proficiency level and return DTOs.
     */
    public function findByProficiencyLevelDTO(string $proficiencyLevel): Collection;

    /**
     * Find specializations by verification status.
     */
    public function findByVerificationStatus(string $verificationStatus): Collection;

    /**
     * Find specializations by verification status and return DTOs.
     */
    public function findByVerificationStatusDTO(string $verificationStatus): Collection;

    /**
     * Find specializations by provider and category.
     */
    public function findByProviderAndCategory(int $providerId, string $category): Collection;

    /**
     * Find specializations by provider and category and return DTOs.
     */
    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection;

    /**
     * Find specializations by provider and proficiency level.
     */
    public function findByProviderAndProficiency(int $providerId, string $proficiencyLevel): Collection;

    /**
     * Find specializations by provider and proficiency level and return DTOs.
     */
    public function findByProviderAndProficiencyDTO(int $providerId, string $proficiencyLevel): Collection;

    /**
     * Find primary specializations.
     */
    public function findPrimary(): Collection;

    /**
     * Find primary specializations and return DTOs.
     */
    public function findPrimaryDTO(): Collection;

    /**
     * Find active specializations.
     */
    public function findActive(): Collection;

    /**
     * Find active specializations and return DTOs.
     */
    public function findActiveDTO(): Collection;

    /**
     * Find inactive specializations.
     */
    public function findInactive(): Collection;

    /**
     * Find inactive specializations and return DTOs.
     */
    public function findInactiveDTO(): Collection;

    /**
     * Find verified specializations.
     */
    public function findVerified(): Collection;

    /**
     * Find verified specializations and return DTOs.
     */
    public function findVerifiedDTO(): Collection;

    /**
     * Find unverified specializations.
     */
    public function findUnverified(): Collection;

    /**
     * Find unverified specializations and return DTOs.
     */
    public function findUnverifiedDTO(): Collection;

    /**
     * Find pending specializations.
     */
    public function findPending(): Collection;

    /**
     * Find pending specializations and return DTOs.
     */
    public function findPendingDTO(): Collection;

    /**
     * Find specializations by experience range.
     */
    public function findByExperienceRange(int $minYears, int $maxYears): Collection;

    /**
     * Find specializations by experience range and return DTOs.
     */
    public function findByExperienceRangeDTO(int $minYears, int $maxYears): Collection;

    /**
     * Find specializations by verified by user.
     */
    public function findByVerifiedBy(int $verifiedBy): Collection;

    /**
     * Find specializations by verified by user and return DTOs.
     */
    public function findByVerifiedByDTO(int $verifiedBy): Collection;

    /**
     * Create a new specialization.
     */
    public function create(array $data): ProviderSpecialization;

    /**
     * Create a new specialization and return DTO.
     */
    public function createAndReturnDTO(array $data): ProviderSpecializationDTO;

    /**
     * Update a specialization.
     */
    public function update(ProviderSpecialization $specialization, array $data): bool;

    /**
     * Update a specialization and return DTO.
     */
    public function updateAndReturnDTO(ProviderSpecialization $specialization, array $data): ?ProviderSpecializationDTO;

    /**
     * Delete a specialization.
     */
    public function delete(ProviderSpecialization $specialization): bool;

    /**
     * Activate a specialization.
     */
    public function activate(ProviderSpecialization $specialization): bool;

    /**
     * Deactivate a specialization.
     */
    public function deactivate(ProviderSpecialization $specialization): bool;

    /**
     * Set specialization as primary.
     */
    public function setPrimary(ProviderSpecialization $specialization): bool;

    /**
     * Remove primary status from specialization.
     */
    public function removePrimary(ProviderSpecialization $specialization): bool;

    /**
     * Verify a specialization.
     */
    public function verify(ProviderSpecialization $specialization, int $verifiedBy): bool;

    /**
     * Reject a specialization.
     */
    public function reject(ProviderSpecialization $specialization, ?string $reason = null): bool;

    /**
     * Get specialization count for a provider.
     */
    public function getProviderSpecializationCount(int $providerId): int;

    /**
     * Get specialization count by category for a provider.
     */
    public function getProviderSpecializationCountByCategory(int $providerId, string $category): int;

    /**
     * Get specialization count by proficiency level for a provider.
     */
    public function getProviderSpecializationCountByProficiency(int $providerId, string $proficiencyLevel): int;

    /**
     * Get primary specialization for a provider.
     */
    public function getProviderPrimarySpecialization(int $providerId): ?ProviderSpecialization;

    /**
     * Get primary specialization DTO for a provider.
     */
    public function getProviderPrimarySpecializationDTO(int $providerId): ?ProviderSpecializationDTO;

    /**
     * Get active specializations for a provider.
     */
    public function getProviderActiveSpecializations(int $providerId): Collection;

    /**
     * Get active specializations DTOs for a provider.
     */
    public function getProviderActiveSpecializationsDTO(int $providerId): Collection;

    /**
     * Get verified specializations for a provider.
     */
    public function getProviderVerifiedSpecializations(int $providerId): Collection;

    /**
     * Get verified specializations DTOs for a provider.
     */
    public function getProviderVerifiedSpecializationsDTO(int $providerId): Collection;

    /**
     * Get total specialization count.
     */
    public function getTotalSpecializationCount(): int;

    /**
     * Get total specialization count by category.
     */
    public function getTotalSpecializationCountByCategory(string $category): int;

    /**
     * Get total specialization count by proficiency level.
     */
    public function getTotalSpecializationCountByProficiency(string $proficiencyLevel): int;

    /**
     * Get active specialization count.
     */
    public function getActiveSpecializationCount(): int;

    /**
     * Get verified specialization count.
     */
    public function getVerifiedSpecializationCount(): int;

    /**
     * Get pending specialization count.
     */
    public function getPendingSpecializationCount(): int;

    /**
     * Get average experience across all specializations.
     */
    public function getAverageExperience(): float;

    /**
     * Get average experience by category.
     */
    public function getAverageExperienceByCategory(string $category): float;

    /**
     * Search specializations.
     */
    public function searchSpecializations(string $query): Collection;

    /**
     * Search specializations and return DTOs.
     */
    public function searchSpecializationsDTO(string $query): Collection;

    /**
     * Search specializations by provider.
     */
    public function searchSpecializationsByProvider(int $providerId, string $query): Collection;

    /**
     * Search specializations by provider and return DTOs.
     */
    public function searchSpecializationsByProviderDTO(int $providerId, string $query): Collection;

    /**
     * Export specialization data.
     */
    public function exportSpecializationData(array $filters = []): string;

    /**
     * Import specialization data.
     */
    public function importSpecializationData(string $data): bool;

    /**
     * Get specialization statistics.
     */
    public function getSpecializationStatistics(): array;

    /**
     * Get provider specialization statistics.
     */
    public function getProviderSpecializationStatistics(int $providerId): array;

    /**
     * Get specialization trends.
     */
    public function getSpecializationTrends(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get most popular specializations.
     */
    public function getMostPopularSpecializations(int $limit = 10): Collection;

    /**
     * Get most popular specializations and return DTOs.
     */
    public function getMostPopularSpecializationsDTO(int $limit = 10): Collection;

    /**
     * Get specializations by expertise level.
     */
    public function getSpecializationsByExpertise(string $proficiencyLevel = 'expert'): Collection;

    /**
     * Get specializations by expertise level and return DTOs.
     */
    public function getSpecializationsByExpertiseDTO(string $proficiencyLevel = 'expert'): Collection;
}
