<?php

namespace Fereydooni\Shopping\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInsuranceRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderInsurance;
use Fereydooni\Shopping\App\DTOs\ProviderInsuranceDTO;
use Fereydooni\Shopping\App\Enums\InsuranceStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderInsuranceRepository implements ProviderInsuranceRepositoryInterface
{
    public function __construct(protected ProviderInsurance $model)
    {
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'verifiedBy'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'verifiedBy'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'verifiedBy'])->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    // Find operations
    public function find(int $id): ?ProviderInsurance
    {
        return $this->model->with(['provider', 'verifiedBy'])->find($id);
    }

    public function findDTO(int $id): ?ProviderInsuranceDTO
    {
        $insurance = $this->find($id);
        return $insurance ? ProviderInsuranceDTO::fromModel($insurance) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->get();
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $insurances = $this->findByProviderId($providerId);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findByInsuranceType(string $insuranceType): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('insurance_type', $insuranceType)
            ->get();
    }

    public function findByInsuranceTypeDTO(string $insuranceType): Collection
    {
        $insurances = $this->findByInsuranceType($insuranceType);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('status', $status)
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $insurances = $this->findByStatus($status);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findByVerificationStatus(string $verificationStatus): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('verification_status', $verificationStatus)
            ->get();
    }

    public function findByVerificationStatusDTO(string $verificationStatus): Collection
    {
        $insurances = $this->findByVerificationStatus($verificationStatus);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findByPolicyNumber(string $policyNumber): ?ProviderInsurance
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('policy_number', $policyNumber)
            ->first();
    }

    public function findByPolicyNumberDTO(string $policyNumber): ?ProviderInsuranceDTO
    {
        $insurance = $this->findByPolicyNumber($policyNumber);
        return $insurance ? ProviderInsuranceDTO::fromModel($insurance) : null;
    }

    public function findByProviderName(string $providerName): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_name', 'like', "%{$providerName}%")
            ->get();
    }

    public function findByProviderNameDTO(string $providerName): Collection
    {
        $insurances = $this->findByProviderName($providerName);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->active()->get();
    }

    public function findActiveDTO(): Collection
    {
        $insurances = $this->findActive();
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findExpired(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->expired()->get();
    }

    public function findExpiredDTO(): Collection
    {
        $insurances = $this->findExpired();
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findExpiringSoon(int $days = 30): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->expiringSoon($days)->get();
    }

    public function findExpiringSoonDTO(int $days = 30): Collection
    {
        $insurances = $this->findExpiringSoon($days);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findVerified(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->verified()->get();
    }

    public function findVerifiedDTO(): Collection
    {
        $insurances = $this->findVerified();
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findPendingVerification(): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])->pendingVerification()->get();
    }

    public function findPendingVerificationDTO(): Collection
    {
        $insurances = $this->findPendingVerification();
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Combined queries
    public function findByProviderAndType(int $providerId, string $insuranceType): ?ProviderInsurance
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where('insurance_type', $insuranceType)
            ->first();
    }

    public function findByProviderAndTypeDTO(int $providerId, string $insuranceType): ?ProviderInsuranceDTO
    {
        $insurance = $this->findByProviderAndType($providerId, $insuranceType);
        return $insurance ? ProviderInsuranceDTO::fromModel($insurance) : null;
    }

    public function findByProviderAndStatus(int $providerId, string $status): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where('status', $status)
            ->get();
    }

    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection
    {
        $insurances = $this->findByProviderAndStatus($providerId, $status);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Date and amount range queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->byDateRange($startDate, $endDate)
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $insurances = $this->findByDateRange($startDate, $endDate);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function findByCoverageAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->byCoverageAmountRange($minAmount, $maxAmount)
            ->get();
    }

    public function findByCoverageAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        $insurances = $this->findByCoverageAmountRange($minAmount, $maxAmount);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Create and update operations
    public function create(array $data): ProviderInsurance
    {
        try {
            DB::beginTransaction();

            $insurance = $this->model->create($data);

            DB::commit();

            Log::info('Provider insurance created', [
                'insurance_id' => $insurance->id,
                'provider_id' => $insurance->provider_id,
                'policy_number' => $insurance->policy_number
            ]);

            return $insurance->load(['provider', 'verifiedBy']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider insurance', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderInsuranceDTO
    {
        $insurance = $this->create($data);
        return ProviderInsuranceDTO::fromModel($insurance);
    }

    public function update(ProviderInsurance $providerInsurance, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $providerInsurance->update($data);

            DB::commit();

            if ($updated) {
                Log::info('Provider insurance updated', [
                    'insurance_id' => $providerInsurance->id,
                    'provider_id' => $providerInsurance->provider_id
                ]);
            }

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider insurance', [
                'error' => $e->getMessage(),
                'insurance_id' => $providerInsurance->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderInsurance $providerInsurance, array $data): ?ProviderInsuranceDTO
    {
        $updated = $this->update($providerInsurance, $data);
        return $updated ? ProviderInsuranceDTO::fromModel($providerInsurance->fresh()) : null;
    }

    public function delete(ProviderInsurance $providerInsurance): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $providerInsurance->delete();

            DB::commit();

            if ($deleted) {
                Log::info('Provider insurance deleted', [
                    'insurance_id' => $providerInsurance->id,
                    'provider_id' => $providerInsurance->provider_id
                ]);
            }

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider insurance', [
                'error' => $e->getMessage(),
                'insurance_id' => $providerInsurance->id
            ]);
            throw $e;
        }
    }

    // Status management
    public function activate(ProviderInsurance $providerInsurance): bool
    {
        return $this->update($providerInsurance, ['status' => InsuranceStatus::ACTIVE->value]);
    }

    public function deactivate(ProviderInsurance $providerInsurance): bool
    {
        return $this->update($providerInsurance, ['status' => InsuranceStatus::SUSPENDED->value]);
    }

    public function expire(ProviderInsurance $providerInsurance): bool
    {
        return $this->update($providerInsurance, ['status' => InsuranceStatus::EXPIRED->value]);
    }

    public function cancel(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        $data = ['status' => InsuranceStatus::CANCELLED->value];
        if ($reason) {
            $data['notes'] = $reason;
        }
        return $this->update($providerInsurance, $data);
    }

    public function suspend(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        $data = ['status' => InsuranceStatus::SUSPENDED->value];
        if ($reason) {
            $data['notes'] = $reason;
        }
        return $this->update($providerInsurance, $data);
    }

    // Verification management
    public function verify(ProviderInsurance $providerInsurance, int $verifiedBy, string $notes = null): bool
    {
        $data = [
            'verification_status' => VerificationStatus::VERIFIED->value,
            'verified_by' => $verifiedBy,
            'verified_at' => now()
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($providerInsurance, $data);
    }

    public function reject(ProviderInsurance $providerInsurance, int $rejectedBy, string $reason): bool
    {
        $data = [
            'verification_status' => VerificationStatus::REJECTED->value,
            'verified_by' => $rejectedBy,
            'verified_at' => now(),
            'notes' => $reason
        ];

        return $this->update($providerInsurance, $data);
    }

    // Renewal management
    public function renew(ProviderInsurance $providerInsurance, array $renewalData): bool
    {
        $renewalData['verification_status'] = VerificationStatus::PENDING->value;
        $renewalData['verified_by'] = null;
        $renewalData['verified_at'] = null;

        return $this->update($providerInsurance, $renewalData);
    }

    // Document management
    public function addDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        return $providerInsurance->addDocument($documentPath);
    }

    public function removeDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        return $providerInsurance->removeDocument($documentPath);
    }

    // Count operations
    public function getInsuranceCount(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)->count();
    }

    public function getInsuranceCountByType(int $providerId, string $insuranceType): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('insurance_type', $insuranceType)
            ->count();
    }

    public function getInsuranceCountByStatus(int $providerId, string $status): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', $status)
            ->count();
    }

    public function getInsuranceCountByVerificationStatus(int $providerId, string $verificationStatus): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('verification_status', $verificationStatus)
            ->count();
    }

    public function getActiveInsuranceCount(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', InsuranceStatus::ACTIVE->value)
            ->count();
    }

    public function getExpiredInsuranceCount(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', InsuranceStatus::EXPIRED->value)
            ->count();
    }

    public function getExpiringSoonCount(int $providerId, int $days = 30): int
    {
        return $this->model->where('provider_id', $providerId)
            ->expiringSoon($days)
            ->count();
    }

    public function getVerifiedInsuranceCount(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('verification_status', VerificationStatus::VERIFIED->value)
            ->count();
    }

    public function getPendingVerificationCount(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('verification_status', VerificationStatus::PENDING->value)
            ->count();
    }

    // Global count operations
    public function getTotalInsuranceCount(): int
    {
        return Cache::remember('total_insurance_count', 3600, function () {
            return $this->model->count();
        });
    }

    public function getTotalInsuranceCountByType(string $insuranceType): int
    {
        return Cache::remember("insurance_count_type_{$insuranceType}", 3600, function () use ($insuranceType) {
            return $this->model->where('insurance_type', $insuranceType)->count();
        });
    }

    public function getTotalInsuranceCountByStatus(string $status): int
    {
        return Cache::remember("insurance_count_status_{$status}", 3600, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    public function getTotalInsuranceCountByVerificationStatus(string $verificationStatus): int
    {
        return Cache::remember("insurance_count_verification_{$verificationStatus}", 3600, function () use ($verificationStatus) {
            return $this->model->where('verification_status', $verificationStatus)->count();
        });
    }

    public function getTotalActiveInsuranceCount(): int
    {
        return $this->getTotalInsuranceCountByStatus(InsuranceStatus::ACTIVE->value);
    }

    public function getTotalExpiredInsuranceCount(): int
    {
        return $this->getTotalInsuranceCountByStatus(InsuranceStatus::EXPIRED->value);
    }

    public function getTotalExpiringSoonCount(int $days = 30): int
    {
        return Cache::remember("expiring_soon_count_{$days}", 3600, function () use ($days) {
            return $this->model->expiringSoon($days)->count();
        });
    }

    public function getTotalVerifiedInsuranceCount(): int
    {
        return $this->getTotalInsuranceCountByVerificationStatus(VerificationStatus::VERIFIED->value);
    }

    public function getTotalPendingVerificationCount(): int
    {
        return $this->getTotalInsuranceCountByVerificationStatus(VerificationStatus::PENDING->value);
    }

    // Coverage amount operations
    public function getTotalCoverageAmount(): float
    {
        return Cache::remember('total_coverage_amount', 3600, function () {
            return $this->model->sum('coverage_amount');
        });
    }

    public function getAverageCoverageAmount(): float
    {
        return Cache::remember('average_coverage_amount', 3600, function () {
            return $this->model->avg('coverage_amount') ?? 0;
        });
    }

    public function getTotalCoverageAmountByProvider(int $providerId): float
    {
        return $this->model->where('provider_id', $providerId)->sum('coverage_amount');
    }

    public function getAverageCoverageAmountByProvider(int $providerId): float
    {
        return $this->model->where('provider_id', $providerId)->avg('coverage_amount') ?? 0;
    }

    public function getTotalCoverageAmountByType(string $insuranceType): float
    {
        return Cache::remember("coverage_amount_type_{$insuranceType}", 3600, function () use ($insuranceType) {
            return $this->model->where('insurance_type', $insuranceType)->sum('coverage_amount');
        });
    }

    public function getAverageCoverageAmountByType(string $insuranceType): float
    {
        return Cache::remember("avg_coverage_amount_type_{$insuranceType}", 3600, function () use ($insuranceType) {
            return $this->model->where('insurance_type', $insuranceType)->avg('coverage_amount') ?? 0;
        });
    }

    // Expiring insurance queries
    public function getExpiringInsurance(int $limit = 10): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->expiringSoon()
            ->orderBy('end_date')
            ->limit($limit)
            ->get();
    }

    public function getExpiringInsuranceDTO(int $limit = 10): Collection
    {
        $insurances = $this->getExpiringInsurance($limit);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function getExpiringInsuranceByProvider(int $providerId, int $limit = 10): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->expiringSoon()
            ->orderBy('end_date')
            ->limit($limit)
            ->get();
    }

    public function getExpiringInsuranceByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        $insurances = $this->getExpiringInsuranceByProvider($providerId, $limit);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Pending verification queries
    public function getPendingVerification(int $limit = 10): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->pendingVerification()
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    public function getPendingVerificationDTO(int $limit = 10): Collection
    {
        $insurances = $this->getPendingVerification($limit);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function getPendingVerificationByProvider(int $providerId, int $limit = 10): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->pendingVerification()
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    public function getPendingVerificationByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        $insurances = $this->getPendingVerificationByProvider($providerId, $limit);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Search operations
    public function searchInsurance(string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where(function ($q) use ($query) {
                $q->where('policy_number', 'like', "%{$query}%")
                  ->orWhere('provider_name', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            })
            ->get();
    }

    public function searchInsuranceDTO(string $query): Collection
    {
        $insurances = $this->searchInsurance($query);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    public function searchInsuranceByProvider(int $providerId, string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedBy'])
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('policy_number', 'like', "%{$query}%")
                  ->orWhere('provider_name', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            })
            ->get();
    }

    public function searchInsuranceByProviderDTO(int $providerId, string $query): Collection
    {
        $insurances = $this->searchInsuranceByProvider($providerId, $query);
        return $insurances->map(fn($insurance) => ProviderInsuranceDTO::fromModel($insurance));
    }

    // Analytics operations
    public function getInsuranceAnalytics(int $providerId): array
    {
        return Cache::remember("insurance_analytics_provider_{$providerId}", 3600, function () use ($providerId) {
            $insurances = $this->model->where('provider_id', $providerId);

            return [
                'total_count' => $insurances->count(),
                'active_count' => $insurances->where('status', InsuranceStatus::ACTIVE->value)->count(),
                'expired_count' => $insurances->where('status', InsuranceStatus::EXPIRED->value)->count(),
                'pending_count' => $insurances->where('status', InsuranceStatus::PENDING->value)->count(),
                'verified_count' => $insurances->where('verification_status', VerificationStatus::VERIFIED->value)->count(),
                'pending_verification_count' => $insurances->where('verification_status', VerificationStatus::PENDING->value)->count(),
                'total_coverage_amount' => $insurances->sum('coverage_amount'),
                'average_coverage_amount' => $insurances->avg('coverage_amount') ?? 0,
                'expiring_soon_count' => $insurances->expiringSoon()->count(),
            ];
        });
    }

    public function getInsuranceAnalyticsByType(int $providerId, string $insuranceType): array
    {
        return Cache::remember("insurance_analytics_provider_{$providerId}_type_{$insuranceType}", 3600, function () use ($providerId, $insuranceType) {
            $insurances = $this->model->where('provider_id', $providerId)
                ->where('insurance_type', $insuranceType);

            return [
                'total_count' => $insurances->count(),
                'active_count' => $insurances->where('status', InsuranceStatus::ACTIVE->value)->count(),
                'expired_count' => $insurances->where('status', InsuranceStatus::EXPIRED->value)->count(),
                'verified_count' => $insurances->where('verification_status', VerificationStatus::VERIFIED->value)->count(),
                'total_coverage_amount' => $insurances->sum('coverage_amount'),
                'average_coverage_amount' => $insurances->avg('coverage_amount') ?? 0,
            ];
        });
    }

    public function getInsuranceAnalyticsByStatus(int $providerId, string $status): array
    {
        return Cache::remember("insurance_analytics_provider_{$providerId}_status_{$status}", 3600, function () use ($providerId, $status) {
            $insurances = $this->model->where('provider_id', $providerId)
                ->where('status', $status);

            return [
                'total_count' => $insurances->count(),
                'total_coverage_amount' => $insurances->sum('coverage_amount'),
                'average_coverage_amount' => $insurances->avg('coverage_amount') ?? 0,
                'verified_count' => $insurances->where('verification_status', VerificationStatus::VERIFIED->value)->count(),
            ];
        });
    }

    public function getInsuranceAnalyticsByVerificationStatus(int $providerId, string $verificationStatus): array
    {
        return Cache::remember("insurance_analytics_provider_{$providerId}_verification_{$verificationStatus}", 3600, function () use ($providerId, $verificationStatus) {
            $insurances = $this->model->where('provider_id', $providerId)
                ->where('verification_status', $verificationStatus);

            return [
                'total_count' => $insurances->count(),
                'total_coverage_amount' => $insurances->sum('coverage_amount'),
                'average_coverage_amount' => $insurances->avg('coverage_amount') ?? 0,
                'active_count' => $insurances->where('status', InsuranceStatus::ACTIVE->value)->count(),
            ];
        });
    }

    // Global analytics operations
    public function getGlobalInsuranceAnalytics(): array
    {
        return Cache::remember('global_insurance_analytics', 3600, function () {
            return [
                'total_count' => $this->getTotalInsuranceCount(),
                'active_count' => $this->getTotalActiveInsuranceCount(),
                'expired_count' => $this->getTotalExpiredInsuranceCount(),
                'verified_count' => $this->getTotalVerifiedInsuranceCount(),
                'pending_verification_count' => $this->getTotalPendingVerificationCount(),
                'total_coverage_amount' => $this->getTotalCoverageAmount(),
                'average_coverage_amount' => $this->getAverageCoverageAmount(),
                'expiring_soon_count' => $this->getTotalExpiringSoonCount(),
            ];
        });
    }

    public function getGlobalInsuranceAnalyticsByType(string $insuranceType): array
    {
        return Cache::remember("global_insurance_analytics_type_{$insuranceType}", 3600, function () use ($insuranceType) {
            return [
                'total_count' => $this->getTotalInsuranceCountByType($insuranceType),
                'total_coverage_amount' => $this->getTotalCoverageAmountByType($insuranceType),
                'average_coverage_amount' => $this->getAverageCoverageAmountByType($insuranceType),
            ];
        });
    }

    public function getGlobalInsuranceAnalyticsByStatus(string $status): array
    {
        return Cache::remember("global_insurance_analytics_status_{$status}", 3600, function () use ($status) {
            return [
                'total_count' => $this->getTotalInsuranceCountByStatus($status),
                'total_coverage_amount' => $this->model->where('status', $status)->sum('coverage_amount'),
                'average_coverage_amount' => $this->model->where('status', $status)->avg('coverage_amount') ?? 0,
            ];
        });
    }

    public function getGlobalInsuranceAnalyticsByVerificationStatus(string $verificationStatus): array
    {
        return Cache::remember("global_insurance_analytics_verification_{$verificationStatus}", 3600, function () use ($verificationStatus) {
            return [
                'total_count' => $this->getTotalInsuranceCountByVerificationStatus($verificationStatus),
                'total_coverage_amount' => $this->model->where('verification_status', $verificationStatus)->sum('coverage_amount'),
                'average_coverage_amount' => $this->model->where('verification_status', $verificationStatus)->avg('coverage_amount') ?? 0,
            ];
        });
    }
}
