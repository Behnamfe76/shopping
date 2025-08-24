<?php

namespace Fereydooni\Shopping\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderCertificationRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Fereydooni\Shopping\App\DTOs\ProviderCertificationDTO;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderCertificationRepository implements ProviderCertificationRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'provider_certification';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderCertification $model)
    {
        $this->model = $model;
    }

    /**
     * Get all provider certifications.
     */
    public function all(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:all", $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedBy'])->get();
        });
    }

    /**
     * Get paginated provider certifications.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "{$this->cachePrefix}:paginate:{$perPage}:" . request()->get('page', 1);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($perPage) {
            return $this->model->with(['provider', 'verifiedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    /**
     * Get simple paginated provider certifications.
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated provider certifications.
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        $query = $this->model->with(['provider', 'verifiedBy'])
            ->orderBy('created_at', 'desc');

        if ($cursor) {
            $query->where('id', '>', $cursor);
        }

        return $query->cursorPaginate($perPage);
    }

    /**
     * Find provider certification by ID.
     */
    public function find(int $id): ?ProviderCertification
    {
        return Cache::remember("{$this->cachePrefix}:find:{$id}", $this->cacheTtl, function () use ($id) {
            return $this->model->with(['provider', 'verifiedBy'])->find($id);
        });
    }

    /**
     * Find provider certification by ID and return DTO.
     */
    public function findDTO(int $id): ?ProviderCertificationDTO
    {
        $certification = $this->find($id);

        if (!$certification) {
            return null;
        }

        return ProviderCertificationDTO::fromModel($certification);
    }

    /**
     * Find provider certifications by provider ID.
     */
    public function findByProviderId(int $providerId): Collection
    {
        $cacheKey = "{$this->cachePrefix}:provider:{$providerId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifiedBy'])
                ->where('provider_id', $providerId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Find provider certifications by provider ID and return DTOs.
     */
    public function findByProviderIdDTO(int $providerId): Collection
    {
        $certifications = $this->findByProviderId($providerId);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certification by certification number.
     */
    public function findByCertificationNumber(string $certificationNumber): ?ProviderCertification
    {
        return Cache::remember("{$this->cachePrefix}:number:{$certificationNumber}", $this->cacheTtl, function () use ($certificationNumber) {
            return $this->model->with(['provider', 'verifiedBy'])
                ->where('certification_number', $certificationNumber)
                ->first();
        });
    }

    /**
     * Find provider certification by certification number and return DTO.
     */
    public function findByCertificationNumberDTO(string $certificationNumber): ?ProviderCertificationDTO
    {
        $certification = $this->findByCertificationNumber($certificationNumber);

        if (!$certification) {
            return null;
        }

        return ProviderCertificationDTO::fromModel($certification);
    }

    /**
     * Find provider certifications by certification name.
     */
    public function findByCertificationName(string $certificationName): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('certification_name', 'like', "%{$certificationName}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by certification name and return DTOs.
     */
    public function findByCertificationNameDTO(string $certificationName): Collection
    {
        $certifications = $this->findByCertificationName($certificationName);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by issuing organization.
     */
    public function findByIssuingOrganization(string $issuingOrganization): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('issuing_organization', 'like', "%{$issuingOrganization}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by issuing organization and return DTOs.
     */
    public function findByIssuingOrganizationDTO(string $issuingOrganization): Collection
    {
        $certifications = $this->findByIssuingOrganization($issuingOrganization);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by category.
     */
    public function findByCategory(string $category): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by category and return DTOs.
     */
    public function findByCategoryDTO(string $category): Collection
    {
        $certifications = $this->findByCategory($category);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by status.
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by status and return DTOs.
     */
    public function findByStatusDTO(string $status): Collection
    {
        $certifications = $this->findByStatus($status);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by verification status.
     */
    public function findByVerificationStatus(string $verificationStatus): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('verification_status', $verificationStatus)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by verification status and return DTOs.
     */
    public function findByVerificationStatusDTO(string $verificationStatus): Collection
    {
        $certifications = $this->findByVerificationStatus($verificationStatus);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by provider and category.
     */
    public function findByProviderAndCategory(int $providerId, string $category): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by provider and category and return DTOs.
     */
    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection
    {
        $certifications = $this->findByProviderAndCategory($providerId, $category);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by provider and status.
     */
    public function findByProviderAndStatus(int $providerId, string $status): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by provider and status and return DTOs.
     */
    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection
    {
        $certifications = $this->findByProviderAndStatus($providerId, $status);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find active provider certifications.
     */
    public function findActive(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->active()->get();
    }

    /**
     * Find active provider certifications and return DTOs.
     */
    public function findActiveDTO(): Collection
    {
        $certifications = $this->findActive();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find expired provider certifications.
     */
    public function findExpired(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->expired()->get();
    }

    /**
     * Find expired provider certifications and return DTOs.
     */
    public function findExpiredDTO(): Collection
    {
        $certifications = $this->findExpired();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications expiring soon.
     */
    public function findExpiringSoon(int $days = 30): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->expiringSoon($days)->get();
    }

    /**
     * Find provider certifications expiring soon and return DTOs.
     */
    public function findExpiringSoonDTO(int $days = 30): Collection
    {
        $certifications = $this->findExpiringSoon($days);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications pending renewal.
     */
    public function findPendingRenewal(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->pendingRenewal()->get();
    }

    /**
     * Find provider certifications pending renewal and return DTOs.
     */
    public function findPendingRenewalDTO(): Collection
    {
        $certifications = $this->findPendingRenewal();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find verified provider certifications.
     */
    public function findVerified(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->verified()->get();
    }

    /**
     * Find verified provider certifications and return DTOs.
     */
    public function findVerifiedDTO(): Collection
    {
        $certifications = $this->findVerified();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find unverified provider certifications.
     */
    public function findUnverified(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->unverified()->get();
    }

    /**
     * Find unverified provider certifications and return DTOs.
     */
    public function findUnverifiedDTO(): Collection
    {
        $certifications = $this->findUnverified();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications pending verification.
     */
    public function findPendingVerification(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->pendingVerification()->get();
    }

    /**
     * Find provider certifications pending verification and return DTOs.
     */
    public function findPendingVerificationDTO(): Collection
    {
        $certifications = $this->findPendingVerification();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find recurring provider certifications.
     */
    public function findRecurring(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->recurring()->get();
    }

    /**
     * Find recurring provider certifications and return DTOs.
     */
    public function findRecurringDTO(): Collection
    {
        $certifications = $this->findRecurring();

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by issue date range.
     */
    public function findByIssueDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by issue date range and return DTOs.
     */
    public function findByIssueDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $certifications = $this->findByIssueDateRange($startDate, $endDate);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by expiry date range.
     */
    public function findByExpiryDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by expiry date range and return DTOs.
     */
    public function findByExpiryDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $certifications = $this->findByExpiryDateRange($startDate, $endDate);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Find provider certifications by verified by user.
     */
    public function findByVerifiedBy(int $verifiedBy): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('verified_by', $verifiedBy)
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Find provider certifications by verified by user and return DTOs.
     */
    public function findByVerifiedByDTO(int $verifiedBy): Collection
    {
        $certifications = $this->findByVerifiedBy($verifiedBy);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Create a new provider certification.
     */
    public function create(array $data): ProviderCertification
    {
        try {
            DB::beginTransaction();

            $certification = $this->model->create($data);

            // Clear relevant cache
            $this->clearCache();

            DB::commit();

            Log::info('Provider certification created', ['id' => $certification->id, 'provider_id' => $certification->provider_id]);

            return $certification->load(['provider', 'verifiedBy']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider certification', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Create a new provider certification and return DTO.
     */
    public function createAndReturnDTO(array $data): ProviderCertificationDTO
    {
        $certification = $this->create($data);
        return ProviderCertificationDTO::fromModel($certification);
    }

    /**
     * Update provider certification.
     */
    public function update(ProviderCertification $certification, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $certification->update($data);

            if ($updated) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Provider certification updated', ['id' => $certification->id, 'provider_id' => $certification->provider_id]);
            }

            DB::commit();

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider certification', ['error' => $e->getMessage(), 'id' => $certification->id, 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update provider certification and return DTO.
     */
    public function updateAndReturnDTO(ProviderCertification $certification, array $data): ?ProviderCertificationDTO
    {
        $updated = $this->update($certification, $data);

        if ($updated) {
            $certification->refresh();
            return ProviderCertificationDTO::fromModel($certification);
        }

        return null;
    }

    /**
     * Delete provider certification.
     */
    public function delete(ProviderCertification $certification): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $certification->delete();

            if ($deleted) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Provider certification deleted', ['id' => $certification->id, 'provider_id' => $certification->provider_id]);
            }

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider certification', ['error' => $e->getMessage(), 'id' => $certification->id]);
            throw $e;
        }
    }

    /**
     * Activate provider certification.
     */
    public function activate(ProviderCertification $certification): bool
    {
        return $this->update($certification, ['status' => CertificationStatus::ACTIVE]);
    }

    /**
     * Expire provider certification.
     */
    public function expire(ProviderCertification $certification): bool
    {
        return $this->update($certification, ['status' => CertificationStatus::EXPIRED]);
    }

    /**
     * Suspend provider certification.
     */
    public function suspend(ProviderCertification $certification, string $reason = null): bool
    {
        $data = ['status' => CertificationStatus::SUSPENDED];

        if ($reason) {
            $notes = $certification->notes ?? [];
            $notes[] = "Suspended: {$reason}";
            $data['notes'] = $notes;
        }

        return $this->update($certification, $data);
    }

    /**
     * Revoke provider certification.
     */
    public function revoke(ProviderCertification $certification, string $reason = null): bool
    {
        $data = ['status' => CertificationStatus::REVOKED];

        if ($reason) {
            $notes = $certification->notes ?? [];
            $notes[] = "Revoked: {$reason}";
            $data['notes'] = $notes;
        }

        return $this->update($certification, $data);
    }

    /**
     * Renew provider certification.
     */
    public function renew(ProviderCertification $certification, string $newExpiryDate): bool
    {
        $data = [
            'status' => CertificationStatus::ACTIVE,
            'expiry_date' => $newExpiryDate,
            'renewal_date' => now()->format('Y-m-d'),
        ];

        return $this->update($certification, $data);
    }

    /**
     * Verify provider certification.
     */
    public function verify(ProviderCertification $certification, int $verifiedBy): bool
    {
        $data = [
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ];

        return $this->update($certification, $data);
    }

    /**
     * Reject provider certification.
     */
    public function reject(ProviderCertification $certification, string $reason = null): bool
    {
        $data = ['verification_status' => VerificationStatus::REJECTED];

        if ($reason) {
            $notes = $certification->notes ?? [];
            $notes[] = "Rejected: {$reason}";
            $data['notes'] = $notes;
        }

        return $this->update($certification, $data);
    }

    /**
     * Update provider certification status.
     */
    public function updateStatus(ProviderCertification $certification, string $newStatus): bool
    {
        return $this->update($certification, ['status' => $newStatus]);
    }

    /**
     * Get provider certification count.
     */
    public function getProviderCertificationCount(int $providerId): int
    {
        $cacheKey = "{$this->cachePrefix}:count:provider:{$providerId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)->count();
        });
    }

    /**
     * Get provider certification count by category.
     */
    public function getProviderCertificationCountByCategory(int $providerId, string $category): int
    {
        $cacheKey = "{$this->cachePrefix}:count:provider:{$providerId}:category:{$category}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $category) {
            return $this->model->where('provider_id', $providerId)
                ->where('category', $category)
                ->count();
        });
    }

    /**
     * Get provider certification count by status.
     */
    public function getProviderCertificationCountByStatus(int $providerId, string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:count:provider:{$providerId}:status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $status) {
            return $this->model->where('provider_id', $providerId)
                ->where('status', $status)
                ->count();
        });
    }

    /**
     * Get provider active certifications.
     */
    public function getProviderActiveCertifications(int $providerId): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->active()
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get provider active certifications and return DTOs.
     */
    public function getProviderActiveCertificationsDTO(int $providerId): Collection
    {
        $certifications = $this->getProviderActiveCertifications($providerId);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get provider expired certifications.
     */
    public function getProviderExpiredCertifications(int $providerId): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->expired()
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    /**
     * Get provider expired certifications and return DTOs.
     */
    public function getProviderExpiredCertificationsDTO(int $providerId): Collection
    {
        $certifications = $this->getProviderExpiredCertifications($providerId);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get provider certifications expiring soon.
     */
    public function getProviderExpiringSoonCertifications(int $providerId, int $days = 30): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->expiringSoon($days)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get provider certifications expiring soon and return DTOs.
     */
    public function getProviderExpiringSoonCertificationsDTO(int $providerId, int $days = 30): Collection
    {
        $certifications = $this->getProviderExpiringSoonCertifications($providerId, $days);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get provider verified certifications.
     */
    public function getProviderVerifiedCertifications(int $providerId): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->verified()
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Get provider verified certifications and return DTOs.
     */
    public function getProviderVerifiedCertificationsDTO(int $providerId): Collection
    {
        $certifications = $this->getProviderVerifiedCertifications($providerId);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get total certification count.
     */
    public function getTotalCertificationCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:total", $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    /**
     * Get total certification count by category.
     */
    public function getTotalCertificationCountByCategory(string $category): int
    {
        $cacheKey = "{$this->cachePrefix}:count:category:{$category}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($category) {
            return $this->model->where('category', $category)->count();
        });
    }

    /**
     * Get total certification count by status.
     */
    public function getTotalCertificationCountByStatus(string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:count:status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    /**
     * Get active certification count.
     */
    public function getActiveCertificationCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:active", $this->cacheTtl, function () {
            return $this->model->active()->count();
        });
    }

    /**
     * Get expired certification count.
     */
    public function getExpiredCertificationCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:expired", $this->cacheTtl, function () {
            return $this->model->expired()->count();
        });
    }

    /**
     * Get verified certification count.
     */
    public function getVerifiedCertificationCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:verified", $this->cacheTtl, function () {
            return $this->model->verified()->count();
        });
    }

    /**
     * Get pending verification count.
     */
    public function getPendingVerificationCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:pending", $this->cacheTtl, function () {
            return $this->model->pendingVerification()->count();
        });
    }

    /**
     * Get expiring soon count.
     */
    public function getExpiringSoonCount(int $days = 30): int
    {
        $cacheKey = "{$this->cachePrefix}:count:expiring:{$days}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($days) {
            return $this->model->expiringSoon($days)->count();
        });
    }

    /**
     * Search certifications.
     */
    public function searchCertifications(string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where(function ($q) use ($query) {
                $q->where('certification_name', 'like', "%{$query}%")
                  ->orWhere('certification_number', 'like', "%{$query}%")
                  ->orWhere('issuing_organization', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search certifications and return DTOs.
     */
    public function searchCertificationsDTO(string $query): Collection
    {
        $certifications = $this->searchCertifications($query);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Search certifications by provider.
     */
    public function searchCertificationsByProvider(int $providerId, string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('certification_name', 'like', "%{$query}%")
                  ->orWhere('certification_number', 'like', "%{$query}%")
                  ->orWhere('issuing_organization', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search certifications by provider and return DTOs.
     */
    public function searchCertificationsByProviderDTO(int $providerId, string $query): Collection
    {
        $certifications = $this->searchCertificationsByProvider($providerId, $query);

        return $certifications->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Export certification data.
     */
    public function exportCertificationData(array $filters = []): string
    {
        $query = $this->model->with(['provider', 'verifiedBy']);

        // Apply filters
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        $certifications = $query->get();

        // Convert to CSV format
        $csv = "ID,Provider ID,Certification Name,Certification Number,Issuing Organization,Category,Status,Verification Status,Issue Date,Expiry Date,Credits Earned\n";

        foreach ($certifications as $certification) {
            $csv .= "{$certification->id},{$certification->provider_id},{$certification->certification_name},{$certification->certification_number},{$certification->issuing_organization},{$certification->category->value},{$certification->status->value},{$certification->verification_status->value},{$certification->issue_date},{$certification->expiry_date},{$certification->credits_earned}\n";
        }

        return $csv;
    }

    /**
     * Import certification data.
     */
    public function importCertificationData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            $imported = 0;
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = array_combine($headers, str_getcsv($line));

                if (isset($row['Provider ID']) && isset($row['Certification Name'])) {
                    $this->create([
                        'provider_id' => $row['Provider ID'],
                        'certification_name' => $row['Certification Name'],
                        'certification_number' => $row['Certification Number'] ?? uniqid(),
                        'issuing_organization' => $row['Issuing Organization'] ?? 'Unknown',
                        'category' => $row['Category'] ?? 'other',
                        'status' => $row['Status'] ?? 'active',
                        'verification_status' => $row['Verification Status'] ?? 'unverified',
                        'issue_date' => $row['Issue Date'] ?? now()->format('Y-m-d'),
                        'expiry_date' => $row['Expiry Date'] ?? null,
                        'credits_earned' => $row['Credits Earned'] ?? null,
                    ]);

                    $imported++;
                }
            }

            DB::commit();

            Log::info("Imported {$imported} provider certifications");

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import certification data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get certification statistics.
     */
    public function getCertificationStatistics(): array
    {
        $cacheKey = "{$this->cachePrefix}:statistics";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return [
                'total' => $this->getTotalCertificationCount(),
                'active' => $this->getActiveCertificationCount(),
                'expired' => $this->getExpiredCertificationCount(),
                'verified' => $this->getVerifiedCertificationCount(),
                'pending_verification' => $this->getPendingVerificationCount(),
                'expiring_soon' => $this->getExpiringSoonCount(),
                'by_category' => $this->getCategoryStatistics(),
                'by_status' => $this->getStatusStatistics(),
                'by_verification_status' => $this->getVerificationStatusStatistics(),
            ];
        });
    }

    /**
     * Get provider certification statistics.
     */
    public function getProviderCertificationStatistics(int $providerId): array
    {
        $cacheKey = "{$this->cachePrefix}:statistics:provider:{$providerId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return [
                'total' => $this->getProviderCertificationCount($providerId),
                'active' => $this->getProviderCertificationCountByStatus($providerId, CertificationStatus::ACTIVE->value),
                'expired' => $this->getProviderCertificationCountByStatus($providerId, CertificationStatus::EXPIRED->value),
                'verified' => $this->getProviderCertificationCountByStatus($providerId, VerificationStatus::VERIFIED->value),
                'by_category' => $this->getProviderCategoryStatistics($providerId),
            ];
        });
    }

    /**
     * Get certification trends.
     */
    public function getCertificationTrends(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?: now()->subYear()->format('Y-m-d');
        $endDate = $endDate ?: now()->format('Y-m-d');

        $cacheKey = "{$this->cachePrefix}:trends:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            $trends = [];

            $currentDate = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            while ($currentDate <= $end) {
                $date = $currentDate->format('Y-m-d');
                $trends[$date] = [
                    'created' => $this->model->whereDate('created_at', $date)->count(),
                    'verified' => $this->model->whereDate('verified_at', $date)->count(),
                    'expired' => $this->model->whereDate('expiry_date', $date)->count(),
                ];

                $currentDate->addDay();
            }

            return $trends;
        });
    }

    /**
     * Get most popular certifications.
     */
    public function getMostPopularCertifications(int $limit = 10): Collection
    {
        $cacheKey = "{$this->cachePrefix}:popular:{$limit}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit) {
            return $this->model->select('certification_name', 'category', 'issuing_organization', DB::raw('count(*) as count'))
                ->groupBy('certification_name', 'category', 'issuing_organization')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get most popular certifications and return DTOs.
     */
    public function getMostPopularCertificationsDTO(int $limit = 10): Collection
    {
        $certifications = $this->getMostPopularCertifications($limit);

        return $certifications->map(function ($certification) {
            return [
                'certification_name' => $certification->certification_name,
                'category' => $certification->category,
                'issuing_organization' => $certification->issuing_organization,
                'count' => $certification->count,
            ];
        });
    }

    /**
     * Get top issuing organizations.
     */
    public function getTopIssuingOrganizations(int $limit = 10): Collection
    {
        $cacheKey = "{$this->cachePrefix}:organizations:{$limit}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit) {
            return $this->model->select('issuing_organization', DB::raw('count(*) as count'))
                ->groupBy('issuing_organization')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Process renewal reminders.
     */
    public function processRenewalReminders(): int
    {
        $certifications = $this->model->where('is_recurring', true)
            ->where('status', CertificationStatus::ACTIVE)
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>', now())
            ->get();

        $processed = 0;
        foreach ($certifications as $certification) {
            // Here you would implement the actual reminder logic
            // For now, we'll just log it
            Log::info('Renewal reminder processed', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'expiry_date' => $certification->expiry_date,
            ]);

            $processed++;
        }

        return $processed;
    }

    /**
     * Process expiration notifications.
     */
    public function processExpirationNotifications(): int
    {
        $certifications = $this->model->where('status', CertificationStatus::ACTIVE)
            ->where('expiry_date', '<=', now())
            ->get();

        $processed = 0;
        foreach ($certifications as $certification) {
            // Update status to expired
            $this->expire($certification);

            // Here you would implement the actual notification logic
            Log::info('Expiration notification processed', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'expiry_date' => $certification->expiry_date,
            ]);

            $processed++;
        }

        return $processed;
    }

    /**
     * Get category statistics.
     */
    protected function getCategoryStatistics(): array
    {
        return $this->model->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get status statistics.
     */
    protected function getStatusStatistics(): array
    {
        return $this->model->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get verification status statistics.
     */
    protected function getVerificationStatusStatistics(): array
    {
        return $this->model->select('verification_status', DB::raw('count(*) as count'))
            ->groupBy('verification_status')
            ->pluck('count', 'verification_status')
            ->toArray();
    }

    /**
     * Get provider category statistics.
     */
    protected function getProviderCategoryStatistics(int $providerId): array
    {
        return $this->model->where('provider_id', $providerId)
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Clear relevant cache.
     */
    protected function clearCache(): void
    {
        Cache::forget("{$this->cachePrefix}:all");
        Cache::forget("{$this->cachePrefix}:statistics");
        Cache::forget("{$this->cachePrefix}:popular:10");
        Cache::forget("{$this->cachePrefix}:organizations:10");

        // Clear pagination cache
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("{$this->cachePrefix}:paginate:15:{$i}");
        }
    }
}
