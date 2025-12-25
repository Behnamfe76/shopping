<?php

namespace Fereydooni\Shopping\App\Facades;

use Fereydooni\Shopping\App\DTOs\ProviderSpecializationDTO;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderSpecializationRepositoryInterface;
use Fereydooni\Shopping\App\Repositories\ProviderSpecializationRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static ProviderSpecialization|null find(int $id)
 * @method static ProviderSpecializationDTO|null findDTO(int $id)
 * @method static Collection findByProviderId(int $providerId)
 * @method static Collection findByProviderIdDTO(int $providerId)
 * @method static Collection findBySpecializationName(string $specializationName)
 * @method static Collection findByCategory(string $category)
 * @method static Collection findByProficiencyLevel(string $proficiencyLevel)
 * @method static Collection findByVerificationStatus(string $verificationStatus)
 * @method static ProviderSpecialization create(array $data)
 * @method static ProviderSpecializationDTO createAndReturnDTO(array $data)
 * @method static bool update(ProviderSpecialization $specialization, array $data)
 * @method static ProviderSpecializationDTO|null updateAndReturnDTO(ProviderSpecialization $specialization, array $data)
 * @method static bool delete(ProviderSpecialization $specialization)
 * @method static bool activate(ProviderSpecialization $specialization)
 * @method static bool deactivate(ProviderSpecialization $specialization)
 * @method static bool setPrimary(ProviderSpecialization $specialization)
 * @method static bool removePrimary(ProviderSpecialization $specialization)
 * @method static bool verify(ProviderSpecialization $specialization, int $verifiedBy)
 * @method static bool reject(ProviderSpecialization $specialization, string $reason = null)
 * @method static int getProviderSpecializationCount(int $providerId)
 * @method static ProviderSpecialization|null getProviderPrimarySpecialization(int $providerId)
 * @method static Collection getProviderActiveSpecializations(int $providerId)
 * @method static Collection getProviderVerifiedSpecializations(int $providerId)
 * @method static array getSpecializationStatistics()
 * @method static array getProviderSpecializationStatistics(int $providerId)
 * @method static Collection searchSpecializations(string $query)
 * @method static Collection getMostPopularSpecializations(int $limit = 10)
 * @method static Collection getSpecializationsByExpertise(string $proficiencyLevel = 'expert')
 */
class ProviderSpecialization extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ProviderSpecializationRepositoryInterface::class;
    }

    /**
     * Get the repository instance.
     */
    protected static function getRepository(): ProviderSpecializationRepository
    {
        return app(ProviderSpecializationRepositoryInterface::class);
    }

    /**
     * Get all specializations with optional filtering.
     */
    public static function getAll(array $filters = []): Collection
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->newQuery();

        // Apply filters
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['proficiency_level'])) {
            $query->where('proficiency_level', $filters['proficiency_level']);
        }

        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_primary'])) {
            $query->where('is_primary', $filters['is_primary']);
        }

        if (isset($filters['min_experience'])) {
            $query->where('years_experience', '>=', $filters['min_experience']);
        }

        if (isset($filters['max_experience'])) {
            $query->where('years_experience', '<=', $filters['max_experience']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->with(['provider', 'verifiedBy'])->get();
    }

    /**
     * Get specializations with advanced filtering and sorting.
     */
    public static function getFiltered(array $filters = [], array $sorting = []): Collection
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->newQuery();

        // Apply filters
        static::applyFilters($query, $filters);

        // Apply sorting
        static::applySorting($query, $sorting);

        return $query->with(['provider', 'verifiedBy'])->get();
    }

    /**
     * Get specializations with pagination and filtering.
     */
    public static function getPaginated(array $filters = [], array $sorting = [], int $perPage = 15): LengthAwarePaginator
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->newQuery();

        // Apply filters
        static::applyFilters($query, $filters);

        // Apply sorting
        static::applySorting($query, $sorting);

        return $query->with(['provider', 'verifiedBy'])->paginate($perPage);
    }

    /**
     * Create a new specialization with validation.
     */
    public static function createSpecialization(array $data): ProviderSpecialization
    {
        $repository = static::getRepository();

        // Validate data using DTO rules
        $dto = new ProviderSpecializationDTO(...$data);

        return $repository->create($data);
    }

    /**
     * Update a specialization with validation.
     */
    public static function updateSpecialization(int $id, array $data): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->update($specialization, $data);
    }

    /**
     * Delete a specialization.
     */
    public static function deleteSpecialization(int $id): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->delete($specialization);
    }

    /**
     * Activate a specialization.
     */
    public static function activateSpecialization(int $id): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->activate($specialization);
    }

    /**
     * Deactivate a specialization.
     */
    public static function deactivateSpecialization(int $id): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->deactivate($specialization);
    }

    /**
     * Set a specialization as primary.
     */
    public static function setPrimarySpecialization(int $id): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->setPrimary($specialization);
    }

    /**
     * Remove primary status from a specialization.
     */
    public static function removePrimarySpecialization(int $id): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->removePrimary($specialization);
    }

    /**
     * Verify a specialization.
     */
    public static function verifySpecialization(int $id, int $verifiedBy): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->verify($specialization, $verifiedBy);
    }

    /**
     * Reject a specialization.
     */
    public static function rejectSpecialization(int $id, ?string $reason = null): bool
    {
        $repository = static::getRepository();
        $specialization = $repository->find($id);

        if (! $specialization) {
            throw new \InvalidArgumentException("Specialization with ID {$id} not found.");
        }

        return $repository->reject($specialization, $reason);
    }

    /**
     * Get specializations by provider with optional filtering.
     */
    public static function getByProvider(int $providerId, array $filters = []): Collection
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->where('provider_id', $providerId);

        // Apply additional filters
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['proficiency_level'])) {
            $query->where('proficiency_level', $filters['proficiency_level']);
        }

        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_primary'])) {
            $query->where('is_primary', $filters['is_primary']);
        }

        return $query->with(['provider', 'verifiedBy'])->get();
    }

    /**
     * Get specializations by category.
     */
    public static function getByCategory(string $category): Collection
    {
        return static::getRepository()->findByCategory($category);
    }

    /**
     * Get specializations by proficiency level.
     */
    public static function getByProficiencyLevel(string $proficiencyLevel): Collection
    {
        return static::getRepository()->findByProficiencyLevel($proficiencyLevel);
    }

    /**
     * Get specializations by verification status.
     */
    public static function getByVerificationStatus(string $verificationStatus): Collection
    {
        return static::getRepository()->findByVerificationStatus($verificationStatus);
    }

    /**
     * Search specializations.
     */
    public static function search(string $query): Collection
    {
        return static::getRepository()->searchSpecializations($query);
    }

    /**
     * Get specialization statistics.
     */
    public static function getStatistics(): array
    {
        return static::getRepository()->getSpecializationStatistics();
    }

    /**
     * Get provider specialization statistics.
     */
    public static function getProviderStatistics(int $providerId): array
    {
        return static::getRepository()->getProviderSpecializationStatistics($providerId);
    }

    /**
     * Get most popular specializations.
     */
    public static function getMostPopular(int $limit = 10): Collection
    {
        return static::getRepository()->getMostPopularSpecializations($limit);
    }

    /**
     * Get specializations by expertise level.
     */
    public static function getByExpertise(string $proficiencyLevel = 'expert'): Collection
    {
        return static::getRepository()->getSpecializationsByExpertise($proficiencyLevel);
    }

    /**
     * Export specialization data.
     */
    public static function exportData(array $filters = []): string
    {
        return static::getRepository()->exportSpecializationData($filters);
    }

    /**
     * Import specialization data.
     */
    public static function importData(string $data): bool
    {
        return static::getRepository()->importSpecializationData($data);
    }

    /**
     * Apply filters to query.
     */
    protected static function applyFilters($query, array $filters): void
    {
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['proficiency_level'])) {
            $query->where('proficiency_level', $filters['proficiency_level']);
        }

        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_primary'])) {
            $query->where('is_primary', $filters['is_primary']);
        }

        if (isset($filters['min_experience'])) {
            $query->where('years_experience', '>=', $filters['min_experience']);
        }

        if (isset($filters['max_experience'])) {
            $query->where('years_experience', '<=', $filters['max_experience']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
    }

    /**
     * Apply sorting to query.
     */
    protected static function applySorting($query, array $sorting): void
    {
        $defaultSorting = [
            'field' => 'created_at',
            'direction' => 'desc',
        ];

        $sorting = array_merge($defaultSorting, $sorting);

        $allowedFields = [
            'id', 'provider_id', 'specialization_name', 'category',
            'years_experience', 'proficiency_level', 'verification_status',
            'is_primary', 'is_active', 'created_at', 'updated_at',
        ];

        $allowedDirections = ['asc', 'desc'];

        if (in_array($sorting['field'], $allowedFields) &&
            in_array($sorting['direction'], $allowedDirections)) {
            $query->orderBy($sorting['field'], $sorting['direction']);
        }
    }

    /**
     * Get specialization count by various criteria.
     */
    public static function getCount(array $filters = []): int
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->newQuery();

        static::applyFilters($query, $filters);

        return $query->count();
    }

    /**
     * Check if specialization exists.
     */
    public static function exists(int $id): bool
    {
        return static::getRepository()->find($id) !== null;
    }

    /**
     * Get specialization by name.
     */
    public static function getByName(string $name): ?ProviderSpecialization
    {
        $specializations = static::getRepository()->findBySpecializationName($name);

        return $specializations->first();
    }

    /**
     * Get specializations by experience range.
     */
    public static function getByExperienceRange(int $minYears, int $maxYears): Collection
    {
        return static::getRepository()->findByExperienceRange($minYears, $maxYears);
    }

    /**
     * Get specializations verified by a specific user.
     */
    public static function getVerifiedBy(int $userId): Collection
    {
        return static::getRepository()->findByVerifiedBy($userId);
    }

    /**
     * Get primary specializations.
     */
    public static function getPrimary(): Collection
    {
        return static::getRepository()->findPrimary();
    }

    /**
     * Get active specializations.
     */
    public static function getActive(): Collection
    {
        return static::getRepository()->findActive();
    }

    /**
     * Get inactive specializations.
     */
    public static function getInactive(): Collection
    {
        return static::getRepository()->findInactive();
    }

    /**
     * Get verified specializations.
     */
    public static function getVerified(): Collection
    {
        return static::getRepository()->findVerified();
    }

    /**
     * Get unverified specializations.
     */
    public static function getUnverified(): Collection
    {
        return static::getRepository()->findUnverified();
    }

    /**
     * Get pending specializations.
     */
    public static function getPending(): Collection
    {
        return static::getRepository()->findPending();
    }

    /**
     * Get rejected specializations.
     */
    public static function getRejected(): Collection
    {
        $repository = static::getRepository();
        $query = $repository->getModel()->newQuery();

        return $query->where('verification_status', 'rejected')->with(['provider', 'verifiedBy'])->get();
    }
}
