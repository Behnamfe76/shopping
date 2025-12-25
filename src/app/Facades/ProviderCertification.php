<?php

namespace App\Facades;

use App\Models\ProviderCertification;
use App\Repositories\ProviderCertificationRepository;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static ProviderCertification|null find(int $id)
 * @method static \App\DTOs\ProviderCertificationDTO|null findDTO(int $id)
 * @method static Collection findByProviderId(int $providerId)
 * @method static Collection findByProviderIdDTO(int $providerId)
 * @method static ProviderCertification|null findByCertificationNumber(string $certificationNumber)
 * @method static \App\DTOs\ProviderCertificationDTO|null findByCertificationNumberDTO(string $certificationNumber)
 * @method static Collection findByCertificationName(string $certificationName)
 * @method static Collection findByCertificationNameDTO(string $certificationName)
 * @method static Collection findByIssuingOrganization(string $issuingOrganization)
 * @method static Collection findByIssuingOrganizationDTO(string $issuingOrganization)
 * @method static Collection findByCategory(string $category)
 * @method static Collection findByCategoryDTO(string $category)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Collection findByVerificationStatus(string $verificationStatus)
 * @method static Collection findByVerificationStatusDTO(string $verificationStatus)
 * @method static Collection findByProviderAndCategory(int $providerId, string $category)
 * @method static Collection findByProviderAndCategoryDTO(int $providerId, string $category)
 * @method static Collection findByProviderAndStatus(int $providerId, string $status)
 * @method static Collection findByProviderAndStatusDTO(int $providerId, string $status)
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findExpired()
 * @method static Collection findExpiredDTO()
 * @method static Collection findExpiringSoon(int $days = 30)
 * @method static Collection findExpiringSoonDTO(int $days = 30)
 * @method static Collection findPendingRenewal()
 * @method static Collection findPendingRenewalDTO()
 * @method static Collection findVerified()
 * @method static Collection findVerifiedDTO()
 * @method static Collection findUnverified()
 * @method static Collection findUnverifiedDTO()
 * @method static Collection findPendingVerification()
 * @method static Collection findPendingVerificationDTO()
 * @method static Collection findRecurring()
 * @method static Collection findRecurringDTO()
 * @method static Collection findByIssueDateRange(string $startDate, string $endDate)
 * @method static Collection findByIssueDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection findByExpiryDateRange(string $startDate, string $endDate)
 * @method static Collection findByExpiryDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection findByVerifiedBy(int $verifiedBy)
 * @method static Collection findByVerifiedByDTO(int $verifiedBy)
 * @method static ProviderCertification create(array $data)
 * @method static \App\DTOs\ProviderCertificationDTO createAndReturnDTO(array $data)
 * @method static bool update(ProviderCertification $certification, array $data)
 * @method static \App\DTOs\ProviderCertificationDTO|null updateAndReturnDTO(ProviderCertification $certification, array $data)
 * @method static bool delete(ProviderCertification $certification)
 * @method static bool activate(ProviderCertification $certification)
 * @method static bool expire(ProviderCertification $certification)
 * @method static bool suspend(ProviderCertification $certification, string $reason = null)
 * @method static bool revoke(ProviderCertification $certification, string $reason = null)
 * @method static bool renew(ProviderCertification $certification, string $newExpiryDate)
 * @method static bool verify(ProviderCertification $certification, int $verifiedBy)
 * @method static bool reject(ProviderCertification $certification, string $reason = null)
 * @method static bool updateStatus(ProviderCertification $certification, string $newStatus)
 * @method static int getProviderCertificationCount(int $providerId)
 * @method static int getProviderCertificationCountByCategory(int $providerId, string $category)
 * @method static int getProviderCertificationCountByStatus(int $providerId, string $status)
 * @method static Collection getProviderActiveCertifications(int $providerId)
 * @method static Collection getProviderActiveCertificationsDTO(int $providerId)
 * @method static Collection getProviderExpiredCertifications(int $providerId)
 * @method static Collection getProviderExpiredCertificationsDTO(int $providerId)
 * @method static Collection getProviderExpiringSoonCertifications(int $providerId, int $days = 30)
 * @method static Collection getProviderExpiringSoonCertificationsDTO(int $providerId, int $days = 30)
 * @method static Collection getProviderVerifiedCertifications(int $providerId)
 * @method static Collection getProviderVerifiedCertificationsDTO(int $providerId)
 * @method static int getTotalCertificationCount()
 * @method static int getTotalCertificationCountByCategory(string $category)
 * @method static int getTotalCertificationCountByStatus(string $status)
 * @method static int getActiveCertificationCount()
 * @method static int getExpiredCertificationCount()
 * @method static int getVerifiedCertificationCount()
 * @method static int getPendingVerificationCount()
 * @method static int getExpiringSoonCount(int $days = 30)
 * @method static Collection searchCertifications(string $query)
 * @method static Collection searchCertificationsDTO(string $query)
 * @method static Collection searchCertificationsByProvider(int $providerId, string $query)
 * @method static Collection searchCertificationsByProviderDTO(int $providerId, string $query)
 * @method static string exportCertificationData(array $filters = [])
 * @method static bool importCertificationData(string $data)
 * @method static array getCertificationStatistics()
 * @method static array getProviderCertificationStatistics(int $providerId)
 * @method static array getCertificationTrends(string $startDate = null, string $endDate = null)
 * @method static Collection getMostPopularCertifications(int $limit = 10)
 * @method static Collection getMostPopularCertificationsDTO(int $limit = 10)
 * @method static Collection getTopIssuingOrganizations(int $limit = 10)
 * @method static int processRenewalReminders()
 * @method static int processExpirationNotifications()
 *
 * @see \App\Repositories\ProviderCertificationRepository
 */
class ProviderCertification extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ProviderCertificationRepository::class;
    }

    /**
     * Get the repository instance.
     */
    public static function repository(): ProviderCertificationRepository
    {
        return app(ProviderCertificationRepository::class);
    }

    /**
     * Create a new certification with validation and events.
     */
    public static function createCertification(array $data): ProviderCertification
    {
        try {
            $certification = static::repository()->create($data);

            // Fire creation event
            event(new \App\Events\ProviderCertification\ProviderCertificationCreated($certification));

            return $certification;
        } catch (\Exception $e) {
            \Log::error('Failed to create provider certification', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update a certification with validation and events.
     */
    public static function updateCertification(ProviderCertification $certification, array $data): bool
    {
        try {
            $oldData = $certification->toArray();
            $success = static::repository()->update($certification, $data);

            if ($success) {
                // Fire update event
                event(new \App\Events\ProviderCertification\ProviderCertificationUpdated($certification, $oldData));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to update provider certification', [
                'certification_id' => $certification->id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a certification with events.
     */
    public static function deleteCertification(ProviderCertification $certification): bool
    {
        try {
            $success = static::repository()->delete($certification);

            if ($success) {
                // Fire deletion event
                event(new \App\Events\ProviderCertification\ProviderCertificationDeleted($certification));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to delete provider certification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify a certification with events.
     */
    public static function verifyCertification(
        ProviderCertification $certification,
        int $verifiedBy,
        ?string $notes = null,
        ?string $verificationUrl = null
    ): bool {
        try {
            $success = static::repository()->verify($certification, $verifiedBy);

            if ($success) {
                // Fire verification event
                event(new \App\Events\ProviderCertification\ProviderCertificationVerified($certification, $verifiedBy));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to verify provider certification', [
                'certification_id' => $certification->id,
                'verified_by' => $verifiedBy,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reject a certification with events.
     */
    public static function rejectCertification(
        ProviderCertification $certification,
        int $rejectedBy,
        string $reason,
        ?string $notes = null
    ): bool {
        try {
            $success = static::repository()->reject($certification, $rejectedBy, $reason);

            if ($success) {
                // Fire rejection event
                event(new \App\Events\ProviderCertification\ProviderCertificationRejected($certification, $rejectedBy, $reason));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to reject provider certification', [
                'certification_id' => $certification->id,
                'rejected_by' => $rejectedBy,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Renew a certification with events.
     */
    public static function renewCertification(
        ProviderCertification $certification,
        string $newExpiryDate,
        ?string $newCertificationNumber = null,
        ?string $notes = null
    ): bool {
        try {
            $oldExpiryDate = $certification->expiry_date;
            $success = static::repository()->renew($certification, $newExpiryDate);

            if ($success) {
                // Fire renewal event
                event(new \App\Events\ProviderCertification\ProviderCertificationRenewed($certification, $oldExpiryDate));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to renew provider certification', [
                'certification_id' => $certification->id,
                'new_expiry_date' => $newExpiryDate,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Suspend a certification with events.
     */
    public static function suspendCertification(
        ProviderCertification $certification,
        ?string $reason = null
    ): bool {
        try {
            $success = static::repository()->suspend($certification, $reason);

            if ($success) {
                // Fire suspension event
                event(new \App\Events\ProviderCertification\ProviderCertificationSuspended($certification, $reason));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to suspend provider certification', [
                'certification_id' => $certification->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Revoke a certification with events.
     */
    public static function revokeCertification(
        ProviderCertification $certification,
        ?string $reason = null
    ): bool {
        try {
            $success = static::repository()->revoke($certification, $reason);

            if ($success) {
                // Fire revocation event
                event(new \App\Events\ProviderCertification\ProviderCertificationRevoked($certification, $reason));
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to revoke provider certification', [
                'certification_id' => $certification->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get certifications for a specific provider with caching.
     */
    public static function getProviderCertifications(int $providerId, array $filters = []): Collection
    {
        $cacheKey = "provider_certifications_{$providerId}_".md5(serialize($filters));

        return \Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return static::repository()->findByProviderId($providerId);
        });
    }

    /**
     * Get active certifications for a provider.
     */
    public static function getProviderActiveCertifications(int $providerId): Collection
    {
        return static::repository()->getProviderActiveCertifications($providerId);
    }

    /**
     * Get expiring certifications for a provider.
     */
    public static function getProviderExpiringCertifications(int $providerId, int $days = 30): Collection
    {
        return static::repository()->getProviderExpiringSoonCertifications($providerId, $days);
    }

    /**
     * Get certification statistics for a provider.
     */
    public static function getProviderCertificationStats(int $providerId): array
    {
        return static::repository()->getProviderCertificationStatistics($providerId);
    }

    /**
     * Search certifications with advanced filtering.
     */
    public static function searchCertifications(string $query, array $filters = []): Collection
    {
        return static::repository()->searchCertifications($query);
    }

    /**
     * Export certification data.
     */
    public static function exportData(array $filters = []): string
    {
        return static::repository()->exportCertificationData($filters);
    }

    /**
     * Import certification data.
     */
    public static function importData(string $data): bool
    {
        return static::repository()->importCertificationData($data);
    }

    /**
     * Process all renewal reminders.
     */
    public static function processAllRenewalReminders(): int
    {
        return static::repository()->processRenewalReminders();
    }

    /**
     * Process all expiration notifications.
     */
    public static function processAllExpirationNotifications(): int
    {
        return static::repository()->processExpirationNotifications();
    }

    /**
     * Get global certification statistics.
     */
    public static function getGlobalStats(): array
    {
        return static::repository()->getCertificationStatistics();
    }

    /**
     * Get certification trends.
     */
    public static function getTrends(?string $startDate = null, ?string $endDate = null): array
    {
        return static::repository()->getCertificationTrends($startDate, $endDate);
    }

    /**
     * Get most popular certifications.
     */
    public static function getPopularCertifications(int $limit = 10): Collection
    {
        return static::repository()->getMostPopularCertifications($limit);
    }

    /**
     * Get top issuing organizations.
     */
    public static function getTopOrganizations(int $limit = 10): Collection
    {
        return static::repository()->getTopIssuingOrganizations($limit);
    }
}
