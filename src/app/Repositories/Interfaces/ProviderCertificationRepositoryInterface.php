<?php

namespace Fereydooni\Shopping\App\Repositories\Interfaces;

use Fereydooni\Shopping\App\DTOs\ProviderCertificationDTO;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderCertificationRepositoryInterface
{
    /**
     * Get all provider certifications.
     */
    public function all(): Collection;

    /**
     * Get paginated provider certifications.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated provider certifications.
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated provider certifications.
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find provider certification by ID.
     */
    public function find(int $id): ?ProviderCertification;

    /**
     * Find provider certification by ID and return DTO.
     */
    public function findDTO(int $id): ?ProviderCertificationDTO;

    /**
     * Find provider certifications by provider ID.
     */
    public function findByProviderId(int $providerId): Collection;

    /**
     * Find provider certifications by provider ID and return DTOs.
     */
    public function findByProviderIdDTO(int $providerId): Collection;

    /**
     * Find provider certification by certification number.
     */
    public function findByCertificationNumber(string $certificationNumber): ?ProviderCertification;

    /**
     * Find provider certification by certification number and return DTO.
     */
    public function findByCertificationNumberDTO(string $certificationNumber): ?ProviderCertificationDTO;

    /**
     * Find provider certifications by certification name.
     */
    public function findByCertificationName(string $certificationName): Collection;

    /**
     * Find provider certifications by certification name and return DTOs.
     */
    public function findByCertificationNameDTO(string $certificationName): Collection;

    /**
     * Find provider certifications by issuing organization.
     */
    public function findByIssuingOrganization(string $issuingOrganization): Collection;

    /**
     * Find provider certifications by issuing organization and return DTOs.
     */
    public function findByIssuingOrganizationDTO(string $issuingOrganization): Collection;

    /**
     * Find provider certifications by category.
     */
    public function findByCategory(string $category): Collection;

    /**
     * Find provider certifications by category and return DTOs.
     */
    public function findByCategoryDTO(string $category): Collection;

    /**
     * Find provider certifications by status.
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find provider certifications by status and return DTOs.
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find provider certifications by verification status.
     */
    public function findByVerificationStatus(string $verificationStatus): Collection;

    /**
     * Find provider certifications by verification status and return DTOs.
     */
    public function findByVerificationStatusDTO(string $verificationStatus): Collection;

    /**
     * Find provider certifications by provider and category.
     */
    public function findByProviderAndCategory(int $providerId, string $category): Collection;

    /**
     * Find provider certifications by provider and category and return DTOs.
     */
    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection;

    /**
     * Find provider certifications by provider and status.
     */
    public function findByProviderAndStatus(int $providerId, string $status): Collection;

    /**
     * Find provider certifications by provider and status and return DTOs.
     */
    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection;

    /**
     * Find active provider certifications.
     */
    public function findActive(): Collection;

    /**
     * Find active provider certifications and return DTOs.
     */
    public function findActiveDTO(): Collection;

    /**
     * Find expired provider certifications.
     */
    public function findExpired(): Collection;

    /**
     * Find expired provider certifications and return DTOs.
     */
    public function findExpiredDTO(): Collection;

    /**
     * Find provider certifications expiring soon.
     */
    public function findExpiringSoon(int $days = 30): Collection;

    /**
     * Find provider certifications expiring soon and return DTOs.
     */
    public function findExpiringSoonDTO(int $days = 30): Collection;

    /**
     * Find provider certifications pending renewal.
     */
    public function findPendingRenewal(): Collection;

    /**
     * Find provider certifications pending renewal and return DTOs.
     */
    public function findPendingRenewalDTO(): Collection;

    /**
     * Find verified provider certifications.
     */
    public function findVerified(): Collection;

    /**
     * Find verified provider certifications and return DTOs.
     */
    public function findVerifiedDTO(): Collection;

    /**
     * Find unverified provider certifications.
     */
    public function findUnverified(): Collection;

    /**
     * Find unverified provider certifications and return DTOs.
     */
    public function findUnverifiedDTO(): Collection;

    /**
     * Find provider certifications pending verification.
     */
    public function findPendingVerification(): Collection;

    /**
     * Find provider certifications pending verification and return DTOs.
     */
    public function findPendingVerificationDTO(): Collection;

    /**
     * Find recurring provider certifications.
     */
    public function findRecurring(): Collection;

    /**
     * Find recurring provider certifications and return DTOs.
     */
    public function findRecurringDTO(): Collection;

    /**
     * Find provider certifications by issue date range.
     */
    public function findByIssueDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find provider certifications by issue date range and return DTOs.
     */
    public function findByIssueDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find provider certifications by expiry date range.
     */
    public function findByExpiryDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find provider certifications by expiry date range and return DTOs.
     */
    public function findByExpiryDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find provider certifications by verified by user.
     */
    public function findByVerifiedBy(int $verifiedBy): Collection;

    /**
     * Find provider certifications by verified by user and return DTOs.
     */
    public function findByVerifiedByDTO(int $verifiedBy): Collection;

    /**
     * Create a new provider certification.
     */
    public function create(array $data): ProviderCertification;

    /**
     * Create a new provider certification and return DTO.
     */
    public function createAndReturnDTO(array $data): ProviderCertificationDTO;

    /**
     * Update provider certification.
     */
    public function update(ProviderCertification $certification, array $data): bool;

    /**
     * Update provider certification and return DTO.
     */
    public function updateAndReturnDTO(ProviderCertification $certification, array $data): ?ProviderCertificationDTO;

    /**
     * Delete provider certification.
     */
    public function delete(ProviderCertification $certification): bool;

    /**
     * Activate provider certification.
     */
    public function activate(ProviderCertification $certification): bool;

    /**
     * Expire provider certification.
     */
    public function expire(ProviderCertification $certification): bool;

    /**
     * Suspend provider certification.
     */
    public function suspend(ProviderCertification $certification, ?string $reason = null): bool;

    /**
     * Revoke provider certification.
     */
    public function revoke(ProviderCertification $certification, ?string $reason = null): bool;

    /**
     * Renew provider certification.
     */
    public function renew(ProviderCertification $certification, string $newExpiryDate): bool;

    /**
     * Verify provider certification.
     */
    public function verify(ProviderCertification $certification, int $verifiedBy): bool;

    /**
     * Reject provider certification.
     */
    public function reject(ProviderCertification $certification, ?string $reason = null): bool;

    /**
     * Update provider certification status.
     */
    public function updateStatus(ProviderCertification $certification, string $newStatus): bool;

    /**
     * Get provider certification count.
     */
    public function getProviderCertificationCount(int $providerId): int;

    /**
     * Get provider certification count by category.
     */
    public function getProviderCertificationCountByCategory(int $providerId, string $category): int;

    /**
     * Get provider certification count by status.
     */
    public function getProviderCertificationCountByStatus(int $providerId, string $status): int;

    /**
     * Get provider active certifications.
     */
    public function getProviderActiveCertifications(int $providerId): Collection;

    /**
     * Get provider active certifications and return DTOs.
     */
    public function getProviderActiveCertificationsDTO(int $providerId): Collection;

    /**
     * Get provider expired certifications.
     */
    public function getProviderExpiredCertifications(int $providerId): Collection;

    /**
     * Get provider expired certifications and return DTOs.
     */
    public function getProviderExpiredCertificationsDTO(int $providerId): Collection;

    /**
     * Get provider certifications expiring soon.
     */
    public function getProviderExpiringSoonCertifications(int $providerId, int $days = 30): Collection;

    /**
     * Get provider certifications expiring soon and return DTOs.
     */
    public function getProviderExpiringSoonCertificationsDTO(int $providerId, int $days = 30): Collection;

    /**
     * Get provider verified certifications.
     */
    public function getProviderVerifiedCertifications(int $providerId): Collection;

    /**
     * Get provider verified certifications and return DTOs.
     */
    public function getProviderVerifiedCertificationsDTO(int $providerId): Collection;

    /**
     * Get total certification count.
     */
    public function getTotalCertificationCount(): int;

    /**
     * Get total certification count by category.
     */
    public function getTotalCertificationCountByCategory(string $category): int;

    /**
     * Get total certification count by status.
     */
    public function getTotalCertificationCountByStatus(string $status): int;

    /**
     * Get active certification count.
     */
    public function getActiveCertificationCount(): int;

    /**
     * Get expired certification count.
     */
    public function getExpiredCertificationCount(): int;

    /**
     * Get verified certification count.
     */
    public function getVerifiedCertificationCount(): int;

    /**
     * Get pending verification count.
     */
    public function getPendingVerificationCount(): int;

    /**
     * Get expiring soon count.
     */
    public function getExpiringSoonCount(int $days = 30): int;

    /**
     * Search certifications.
     */
    public function searchCertifications(string $query): Collection;

    /**
     * Search certifications and return DTOs.
     */
    public function searchCertificationsDTO(string $query): Collection;

    /**
     * Search certifications by provider.
     */
    public function searchCertificationsByProvider(int $providerId, string $query): Collection;

    /**
     * Search certifications by provider and return DTOs.
     */
    public function searchCertificationsByProviderDTO(int $providerId, string $query): Collection;

    /**
     * Export certification data.
     */
    public function exportCertificationData(array $filters = []): string;

    /**
     * Import certification data.
     */
    public function importCertificationData(string $data): bool;

    /**
     * Get certification statistics.
     */
    public function getCertificationStatistics(): array;

    /**
     * Get provider certification statistics.
     */
    public function getProviderCertificationStatistics(int $providerId): array;

    /**
     * Get certification trends.
     */
    public function getCertificationTrends(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get most popular certifications.
     */
    public function getMostPopularCertifications(int $limit = 10): Collection;

    /**
     * Get most popular certifications and return DTOs.
     */
    public function getMostPopularCertificationsDTO(int $limit = 10): Collection;

    /**
     * Get top issuing organizations.
     */
    public function getTopIssuingOrganizations(int $limit = 10): Collection;

    /**
     * Process renewal reminders.
     */
    public function processRenewalReminders(): int;

    /**
     * Process expiration notifications.
     */
    public function processExpirationNotifications(): int;
}
