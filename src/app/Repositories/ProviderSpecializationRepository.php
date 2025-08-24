<?php

namespace App\Repositories;

use App\Models\ProviderSpecialization;
use App\DTOs\ProviderSpecializationDTO;
use App\Repositories\Interfaces\ProviderSpecializationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ProviderSpecializationRepository implements ProviderSpecializationRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'provider_specialization_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderSpecialization $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->orderBy('id', 'asc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderSpecialization
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['provider', 'verifiedByUser'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderSpecializationDTO
    {
        $specialization = $this->find($id);
        return $specialization ? ProviderSpecializationDTO::fromModel($specialization) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('provider_id', $providerId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $specializations = $this->findByProviderId($providerId);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findBySpecializationName(string $specializationName): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('specialization_name', 'like', '%' . $specializationName . '%')
            ->get();
    }

    public function findBySpecializationNameDTO(string $specializationName): Collection
    {
        $specializations = $this->findBySpecializationName($specializationName);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByCategory(string $category): Collection
    {
        return Cache::remember($this->cachePrefix . 'category_' . $category, $this->cacheTtl, function () use ($category) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('category', $category)
                ->get();
        });
    }

    public function findByCategoryDTO(string $category): Collection
    {
        $specializations = $this->findByCategory($category);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByProficiencyLevel(string $proficiencyLevel): Collection
    {
        return Cache::remember($this->cachePrefix . 'proficiency_' . $proficiencyLevel, $this->cacheTtl, function () use ($proficiencyLevel) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('proficiency_level', $proficiencyLevel)
                ->get();
        });
    }

    public function findByProficiencyLevelDTO(string $proficiencyLevel): Collection
    {
        $specializations = $this->findByProficiencyLevel($proficiencyLevel);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByVerificationStatus(string $verificationStatus): Collection
    {
        return Cache::remember($this->cachePrefix . 'status_' . $verificationStatus, $this->cacheTtl, function () use ($verificationStatus) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('verification_status', $verificationStatus)
                ->get();
        });
    }

    public function findByVerificationStatusDTO(string $verificationStatus): Collection
    {
        $specializations = $this->findByVerificationStatus($verificationStatus);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByProviderAndCategory(int $providerId, string $category): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('provider_id', $providerId)
            ->where('category', $category)
            ->get();
    }

    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection
    {
        $specializations = $this->findByProviderAndCategory($providerId, $category);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByProviderAndProficiency(int $providerId, string $proficiencyLevel): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('provider_id', $providerId)
            ->where('proficiency_level', $proficiencyLevel)
            ->get();
    }

    public function findByProviderAndProficiencyDTO(int $providerId, string $proficiencyLevel): Collection
    {
        $specializations = $this->findByProviderAndProficiency($providerId, $proficiencyLevel);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findPrimary(): Collection
    {
        return Cache::remember($this->cachePrefix . 'primary', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('is_primary', true)
                ->get();
        });
    }

    public function findPrimaryDTO(): Collection
    {
        $specializations = $this->findPrimary();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findActive(): Collection
    {
        return Cache::remember($this->cachePrefix . 'active', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('is_active', true)
                ->get();
        });
    }

    public function findActiveDTO(): Collection
    {
        $specializations = $this->findActive();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findInactive(): Collection
    {
        return Cache::remember($this->cachePrefix . 'inactive', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('is_active', false)
                ->get();
        });
    }

    public function findInactiveDTO(): Collection
    {
        $specializations = $this->findInactive();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findVerified(): Collection
    {
        return Cache::remember($this->cachePrefix . 'verified', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('verification_status', 'verified')
                ->get();
        });
    }

    public function findVerifiedDTO(): Collection
    {
        $specializations = $this->findVerified();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findUnverified(): Collection
    {
        return Cache::remember($this->cachePrefix . 'unverified', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('verification_status', 'unverified')
                ->get();
        });
    }

    public function findUnverifiedDTO(): Collection
    {
        $specializations = $this->findUnverified();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findPending(): Collection
    {
        return Cache::remember($this->cachePrefix . 'pending', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('verification_status', 'pending')
                ->get();
        });
    }

    public function findPendingDTO(): Collection
    {
        $specializations = $this->findPending();
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByExperienceRange(int $minYears, int $maxYears): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->whereBetween('years_experience', [$minYears, $maxYears])
            ->get();
    }

    public function findByExperienceRangeDTO(int $minYears, int $maxYears): Collection
    {
        $specializations = $this->findByExperienceRange($minYears, $maxYears);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function findByVerifiedBy(int $verifiedBy): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('verified_by', $verifiedBy)
            ->get();
    }

    public function findByVerifiedByDTO(int $verifiedBy): Collection
    {
        $specializations = $this->findByVerifiedBy($verifiedBy);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function create(array $data): ProviderSpecialization
    {
        try {
            DB::beginTransaction();

            $specialization = $this->model->create($data);

            // If this is set as primary, remove primary from other specializations
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->removePrimaryFromOthers($specialization->provider_id, $specialization->id);
            }

            DB::commit();

            // Clear relevant caches
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();

            return $specialization->load(['provider', 'verifiedByUser']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider specialization: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderSpecializationDTO
    {
        $specialization = $this->create($data);
        return ProviderSpecializationDTO::fromModel($specialization);
    }

    public function update(ProviderSpecialization $specialization, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $specialization->update($data);

            // If this is set as primary, remove primary from other specializations
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->removePrimaryFromOthers($specialization->provider_id, $specialization->id);
            }

            DB::commit();

            // Clear relevant caches
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider specialization: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderSpecialization $specialization, array $data): ?ProviderSpecializationDTO
    {
        $result = $this->update($specialization, $data);
        return $result ? ProviderSpecializationDTO::fromModel($specialization->fresh()) : null;
    }

    public function delete(ProviderSpecialization $specialization): bool
    {
        try {
            DB::beginTransaction();

            $providerId = $specialization->provider_id;
            $result = $specialization->delete();

            DB::commit();

            // Clear relevant caches
            $this->clearProviderCache($providerId);
            $this->clearCache();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider specialization: ' . $e->getMessage());
            throw $e;
        }
    }

    public function activate(ProviderSpecialization $specialization): bool
    {
        $result = $specialization->update(['is_active' => true]);
        if ($result) {
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();
        }
        return $result;
    }

    public function deactivate(ProviderSpecialization $specialization): bool
    {
        $result = $specialization->update(['is_active' => false]);
        if ($result) {
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();
        }
        return $result;
    }

    public function setPrimary(ProviderSpecialization $specialization): bool
    {
        try {
            DB::beginTransaction();

            // Remove primary from other specializations
            $this->removePrimaryFromOthers($specialization->provider_id, $specialization->id);

            // Set this as primary
            $result = $specialization->update(['is_primary' => true]);

            DB::commit();

            if ($result) {
                $this->clearProviderCache($specialization->provider_id);
                $this->clearCache();
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to set primary specialization: ' . $e->getMessage());
            throw $e;
        }
    }

    public function removePrimary(ProviderSpecialization $specialization): bool
    {
        $result = $specialization->update(['is_primary' => false]);
        if ($result) {
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();
        }
        return $result;
    }

    public function verify(ProviderSpecialization $specialization, int $verifiedBy): bool
    {
        $result = $specialization->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy
        ]);

        if ($result) {
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();
        }

        return $result;
    }

    public function reject(ProviderSpecialization $specialization, string $reason = null): bool
    {
        $data = [
            'verification_status' => 'rejected',
            'notes' => $reason ? ($specialization->notes . "\nRejection reason: " . $reason) : $specialization->notes
        ];

        $result = $specialization->update($data);

        if ($result) {
            $this->clearProviderCache($specialization->provider_id);
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Remove primary status from other specializations of the same provider
     */
    protected function removePrimaryFromOthers(int $providerId, int $excludeId): void
    {
        $this->model->where('provider_id', $providerId)
            ->where('id', '!=', $excludeId)
            ->update(['is_primary' => false]);
    }

    /**
     * Clear cache for a specific provider
     */
    protected function clearProviderCache(int $providerId): void
    {
        Cache::forget($this->cachePrefix . 'provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'active_provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'verified_provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'primary_provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'count_provider_' . $providerId);
        Cache::forget($this->cachePrefix . 'provider_stats_' . $providerId);
    }

    /**
     * Clear all specialization caches
     */
    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'all');
        Cache::forget($this->cachePrefix . 'primary');
        Cache::forget($this->cachePrefix . 'active');
        Cache::forget($this->cachePrefix . 'inactive');
        Cache::forget($this->cachePrefix . 'verified');
        Cache::forget($this->cachePrefix . 'unverified');
        Cache::forget($this->cachePrefix . 'pending');
        Cache::forget($this->cachePrefix . 'total_count');
        Cache::forget($this->cachePrefix . 'active_count');
        Cache::forget($this->cachePrefix . 'verified_count');
        Cache::forget($this->cachePrefix . 'pending_count');
        Cache::forget($this->cachePrefix . 'avg_experience');
        Cache::forget($this->cachePrefix . 'statistics');
    }

    // Missing methods from interface

    public function getProviderSpecializationCount(int $providerId): int
    {
        return Cache::remember($this->cachePrefix . 'count_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)->count();
        });
    }

    public function getProviderSpecializationCountByCategory(int $providerId, string $category): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('category', $category)
            ->count();
    }

    public function getProviderSpecializationCountByProficiency(int $providerId, string $proficiencyLevel): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('proficiency_level', $proficiencyLevel)
            ->count();
    }

    public function getProviderPrimarySpecialization(int $providerId): ?ProviderSpecialization
    {
        return Cache::remember($this->cachePrefix . 'primary_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('provider_id', $providerId)
                ->where('is_primary', true)
                ->first();
        });
    }

    public function getProviderPrimarySpecializationDTO(int $providerId): ?ProviderSpecializationDTO
    {
        $specialization = $this->getProviderPrimarySpecialization($providerId);
        return $specialization ? ProviderSpecializationDTO::fromModel($specialization) : null;
    }

    public function getProviderActiveSpecializations(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'active_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('provider_id', $providerId)
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getProviderActiveSpecializationsDTO(int $providerId): Collection
    {
        $specializations = $this->getProviderActiveSpecializations($providerId);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function getProviderVerifiedSpecializations(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'verified_provider_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifiedByUser'])
                ->where('provider_id', $providerId)
                ->where('verification_status', 'verified')
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getProviderVerifiedSpecializationsDTO(int $providerId): Collection
    {
        $specializations = $this->getProviderVerifiedSpecializations($providerId);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function getTotalSpecializationCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_count', $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalSpecializationCountByCategory(string $category): int
    {
        return Cache::remember($this->cachePrefix . 'total_count_category_' . $category, $this->cacheTtl, function () use ($category) {
            return $this->model->where('category', $category)->count();
        });
    }

    public function getTotalSpecializationCountByProficiency(string $proficiencyLevel): int
    {
        return Cache::remember($this->cachePrefix . 'total_count_proficiency_' . $proficiencyLevel, $this->cacheTtl, function () use ($proficiencyLevel) {
            return $this->model->where('proficiency_level', $proficiencyLevel)->count();
        });
    }

    public function getActiveSpecializationCount(): int
    {
        return Cache::remember($this->cachePrefix . 'active_count', $this->cacheTtl, function () {
            return $this->model->where('is_active', true)->count();
        });
    }

    public function getVerifiedSpecializationCount(): int
    {
        return Cache::remember($this->cachePrefix . 'verified_count', $this->cacheTtl, function () {
            return $this->model->where('verification_status', 'verified')->count();
        });
    }

    public function getPendingSpecializationCount(): int
    {
        return Cache::remember($this->cachePrefix . 'pending_count', $this->cacheTtl, function () {
            return $this->model->where('verification_status', 'pending')->count();
        });
    }

    public function getAverageExperience(): float
    {
        return Cache::remember($this->cachePrefix . 'avg_experience', $this->cacheTtl, function () {
            return $this->model->avg('years_experience') ?? 0.0;
        });
    }

    public function getAverageExperienceByCategory(string $category): float
    {
        return Cache::remember($this->cachePrefix . 'avg_experience_category_' . $category, $this->cacheTtl, function () use ($category) {
            return $this->model->where('category', $category)->avg('years_experience') ?? 0.0;
        });
    }

    public function searchSpecializations(string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->search($query)
            ->get();
    }

    public function searchSpecializationsDTO(string $query): Collection
    {
        $specializations = $this->searchSpecializations($query);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function searchSpecializationsByProvider(int $providerId, string $query): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('provider_id', $providerId)
            ->search($query)
            ->get();
    }

    public function searchSpecializationsByProviderDTO(int $providerId, string $query): Collection
    {
        $specializations = $this->searchSpecializationsByProvider($providerId, $query);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    public function exportSpecializationData(array $filters = []): string
    {
        $query = $this->model->with(['provider', 'verifiedByUser']);

        // Apply filters
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $specializations = $query->get();

        // Convert to CSV format
        $csv = "ID,Provider ID,Specialization Name,Category,Description,Years Experience,Proficiency Level,Verification Status,Is Primary,Is Active,Created At\n";

        foreach ($specializations as $specialization) {
            $csv .= implode(',', [
                $specialization->id,
                $specialization->provider_id,
                '"' . str_replace('"', '""', $specialization->specialization_name) . '"',
                $specialization->category->value,
                '"' . str_replace('"', '""', $specialization->description ?? '') . '"',
                $specialization->years_experience,
                $specialization->proficiency_level->value,
                $specialization->verification_status->value,
                $specialization->is_primary ? 'Yes' : 'No',
                $specialization->is_active ? 'Yes' : 'No',
                $specialization->created_at
            ]) . "\n";
        }

        return $csv;
    }

    public function importSpecializationData(string $data): bool
    {
        try {
            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = str_getcsv($line);
                if (count($row) !== count($headers)) continue;

                $data = array_combine($headers, $row);

                // Clean and validate data
                $specializationData = [
                    'provider_id' => (int) $data['Provider ID'],
                    'specialization_name' => trim($data['Specialization Name'], '"'),
                    'category' => $data['Category'],
                    'description' => trim($data['Description'] ?? '', '"'),
                    'years_experience' => (int) $data['Years Experience'],
                    'proficiency_level' => $data['Proficiency Level'],
                    'verification_status' => $data['Verification Status'],
                    'is_primary' => $data['Is Primary'] === 'Yes',
                    'is_active' => $data['Is Active'] === 'Yes',
                ];

                // Create or update specialization
                $this->model->updateOrCreate(
                    ['id' => (int) $data['ID']],
                    $specializationData
                );
            }

            return true;
        } catch (Exception $e) {
            Log::error('Failed to import specialization data: ' . $e->getMessage());
            return false;
        }
    }

    public function getSpecializationStatistics(): array
    {
        return Cache::remember($this->cachePrefix . 'statistics', $this->cacheTtl, function () {
            $total = $this->getTotalSpecializationCount();
            $active = $this->getActiveSpecializationCount();
            $verified = $this->getVerifiedSpecializationCount();
            $pending = $this->getPendingSpecializationCount();
            $primary = $this->model->where('is_primary', true)->count();

            return [
                'total' => $total,
                'active' => $active,
                'verified' => $verified,
                'pending' => $pending,
                'primary' => $primary,
                'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
                'active_rate' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'pending_rate' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
                'average_experience' => $this->getAverageExperience(),
            ];
        });
    }

    public function getProviderSpecializationStatistics(int $providerId): array
    {
        return Cache::remember($this->cachePrefix . 'provider_stats_' . $providerId, $this->cacheTtl, function () use ($providerId) {
            $total = $this->getProviderSpecializationCount($providerId);
            $active = $this->model->where('provider_id', $providerId)->where('is_active', true)->count();
            $verified = $this->model->where('provider_id', $providerId)->where('verification_status', 'verified')->count();
            $pending = $this->model->where('provider_id', $providerId)->where('verification_status', 'pending')->count();
            $primary = $this->model->where('provider_id', $providerId)->where('is_primary', true)->count();
            $avgExperience = $this->model->where('provider_id', $providerId)->avg('years_experience') ?? 0;

            return [
                'total' => $total,
                'active' => $active,
                'verified' => $verified,
                'pending' => $pending,
                'primary' => $primary,
                'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
                'active_rate' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'pending_rate' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
                'average_experience' => round($avgExperience, 1),
            ];
        });
    }

    public function getSpecializationTrends(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?: now()->subMonths(6)->format('Y-m-d');
        $endDate = $endDate ?: now()->format('Y-m-d');

        $trends = [];
        $currentDate = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($currentDate <= $end) {
            $date = $currentDate->format('Y-m-d');
            $startOfDay = $currentDate->copy()->startOfDay();
            $endOfDay = $currentDate->copy()->endOfDay();

            $created = $this->model->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            $verified = $this->model->whereBetween('verified_at', [$startOfDay, $endOfDay])->count();

            $trends[] = [
                'date' => $date,
                'created' => $created,
                'verified' => $verified,
                'verification_rate' => $created > 0 ? round(($verified / $created) * 100, 2) : 0,
            ];

            $currentDate->addDay();
        }

        return $trends;
    }

    public function getMostPopularSpecializations(int $limit = 10): Collection
    {
        return $this->model->select('specialization_name', 'category', DB::raw('count(*) as count'))
            ->groupBy('specialization_name', 'category')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMostPopularSpecializationsDTO(int $limit = 10): Collection
    {
        $specializations = $this->getMostPopularSpecializations($limit);
        return $specializations->map(function ($specialization) {
            return [
                'specialization_name' => $specialization->specialization_name,
                'category' => $specialization->category,
                'count' => $specialization->count,
            ];
        });
    }

    public function getSpecializationsByExpertise(string $proficiencyLevel = 'expert'): Collection
    {
        return $this->model->with(['provider', 'verifiedByUser'])
            ->where('proficiency_level', $proficiencyLevel)
            ->where('is_active', true)
            ->orderBy('years_experience', 'desc')
            ->get();
    }

    public function getSpecializationsByExpertiseDTO(string $proficiencyLevel = 'expert'): Collection
    {
        $specializations = $this->getSpecializationsByExpertise($proficiencyLevel);
        return $specializations->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }
}
