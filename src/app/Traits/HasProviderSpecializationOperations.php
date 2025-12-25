<?php

namespace Fereydooni\Shopping\App\Traits;

use Exception;
use Fereydooni\Shopping\App\DTOs\ProviderSpecializationDTO;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasProviderSpecializationOperations
{
    /**
     * Get all specializations for the provider.
     */
    public function specializations(): Collection
    {
        return $this->hasMany(ProviderSpecialization::class, 'provider_id');
    }

    /**
     * Get active specializations for the provider.
     */
    public function activeSpecializations(): Collection
    {
        return $this->specializations()->active()->get();
    }

    /**
     * Get inactive specializations for the provider.
     */
    public function inactiveSpecializations(): Collection
    {
        return $this->specializations()->inactive()->get();
    }

    /**
     * Get verified specializations for the provider.
     */
    public function verifiedSpecializations(): Collection
    {
        return $this->specializations()->verified()->get();
    }

    /**
     * Get unverified specializations for the provider.
     */
    public function unverifiedSpecializations(): Collection
    {
        return $this->specializations()->unverified()->get();
    }

    /**
     * Get pending specializations for the provider.
     */
    public function pendingSpecializations(): Collection
    {
        return $this->specializations()->pending()->get();
    }

    /**
     * Get rejected specializations for the provider.
     */
    public function rejectedSpecializations(): Collection
    {
        return $this->specializations()->rejected()->get();
    }

    /**
     * Get primary specialization for the provider.
     */
    public function primarySpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->primary()->first();
    }

    /**
     * Get specializations by category.
     */
    public function specializationsByCategory(string $category): Collection
    {
        return $this->specializations()->byCategory($category)->get();
    }

    /**
     * Get specializations by proficiency level.
     */
    public function specializationsByProficiency(string $proficiencyLevel): Collection
    {
        return $this->specializations()->byProficiencyLevel($proficiencyLevel)->get();
    }

    /**
     * Get specializations by experience range.
     */
    public function specializationsByExperience(int $minYears, int $maxYears): Collection
    {
        return $this->specializations()->byExperienceRange($minYears, $maxYears)->get();
    }

    /**
     * Add a new specialization to the provider.
     */
    public function addSpecialization(array $data): ProviderSpecialization
    {
        try {
            DB::beginTransaction();

            // Ensure only one primary specialization
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->specializations()->update(['is_primary' => false]);
            }

            $specialization = $this->specializations()->create($data);

            DB::commit();

            // Fire event
            if (method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.added', $specialization);
            }

            return $specialization;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to add specialization to provider {$this->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a specialization.
     */
    public function updateSpecialization(int $specializationId, array $data): bool
    {
        try {
            DB::beginTransaction();

            $specialization = $this->specializations()->findOrFail($specializationId);

            // Ensure only one primary specialization
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->specializations()
                    ->where('id', '!=', $specializationId)
                    ->update(['is_primary' => false]);
            }

            $result = $specialization->update($data);

            DB::commit();

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.updated', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update specialization {$specializationId} for provider {$this->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove a specialization from the provider.
     */
    public function removeSpecialization(int $specializationId): bool
    {
        try {
            DB::beginTransaction();

            $specialization = $this->specializations()->findOrFail($specializationId);
            $result = $specialization->delete();

            DB::commit();

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.removed', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to remove specialization {$specializationId} from provider {$this->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Activate a specialization.
     */
    public function activateSpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->findOrFail($specializationId);
        $result = $specialization->activate();

        if ($result && method_exists($this, 'fireEvent')) {
            $this->fireEvent('specialization.activated', $specialization);
        }

        return $result;
    }

    /**
     * Deactivate a specialization.
     */
    public function deactivateSpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->findOrFail($specializationId);
        $result = $specialization->deactivate();

        if ($result && method_exists($this, 'fireEvent')) {
            $this->fireEvent('specialization.deactivated', $specialization);
        }

        return $result;
    }

    /**
     * Set a specialization as primary.
     */
    public function setPrimarySpecialization(int $specializationId): bool
    {
        try {
            DB::beginTransaction();

            // Remove primary from all other specializations
            $this->specializations()->update(['is_primary' => false]);

            // Set the specified specialization as primary
            $specialization = $this->specializations()->findOrFail($specializationId);
            $result = $specialization->setPrimary();

            DB::commit();

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.primary_changed', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to set primary specialization {$specializationId} for provider {$this->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove primary status from a specialization.
     */
    public function removePrimarySpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->findOrFail($specializationId);
        $result = $specialization->removePrimary();

        if ($result && method_exists($this, 'fireEvent')) {
            $this->fireEvent('specialization.primary_removed', $specialization);
        }

        return $result;
    }

    /**
     * Get specialization count for the provider.
     */
    public function getSpecializationCount(): int
    {
        return $this->specializations()->count();
    }

    /**
     * Get active specialization count for the provider.
     */
    public function getActiveSpecializationCount(): int
    {
        return $this->specializations()->active()->count();
    }

    /**
     * Get verified specialization count for the provider.
     */
    public function getVerifiedSpecializationCount(): int
    {
        return $this->specializations()->verified()->count();
    }

    /**
     * Get pending specialization count for the provider.
     */
    public function getPendingSpecializationCount(): int
    {
        return $this->specializations()->pending()->count();
    }

    /**
     * Get specialization count by category for the provider.
     */
    public function getSpecializationCountByCategory(string $category): int
    {
        return $this->specializations()->byCategory($category)->count();
    }

    /**
     * Get specialization count by proficiency level for the provider.
     */
    public function getSpecializationCountByProficiency(string $proficiencyLevel): int
    {
        return $this->specializations()->byProficiencyLevel($proficiencyLevel)->count();
    }

    /**
     * Check if provider has a specific specialization.
     */
    public function hasSpecialization(string $specializationName): bool
    {
        return $this->specializations()
            ->where('specialization_name', $specializationName)
            ->exists();
    }

    /**
     * Check if provider has a specialization in a specific category.
     */
    public function hasSpecializationInCategory(string $category): bool
    {
        return $this->specializations()->byCategory($category)->exists();
    }

    /**
     * Check if provider has a specialization at a specific proficiency level.
     */
    public function hasSpecializationAtProficiency(string $proficiencyLevel): bool
    {
        return $this->specializations()->byProficiencyLevel($proficiencyLevel)->exists();
    }

    /**
     * Check if provider has any verified specializations.
     */
    public function hasVerifiedSpecializations(): bool
    {
        return $this->specializations()->verified()->exists();
    }

    /**
     * Check if provider has a primary specialization.
     */
    public function hasPrimarySpecialization(): bool
    {
        return $this->specializations()->primary()->exists();
    }

    /**
     * Get specializations as DTOs.
     */
    public function getSpecializationsAsDTOs(): Collection
    {
        return $this->specializations()->get()->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    /**
     * Get active specializations as DTOs.
     */
    public function getActiveSpecializationsAsDTOs(): Collection
    {
        return $this->activeSpecializations()->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    /**
     * Get verified specializations as DTOs.
     */
    public function getVerifiedSpecializationsAsDTOs(): Collection
    {
        return $this->verifiedSpecializations()->map(function ($specialization) {
            return ProviderSpecializationDTO::fromModel($specialization);
        });
    }

    /**
     * Search specializations by query.
     */
    public function searchSpecializations(string $query): Collection
    {
        return $this->specializations()->search($query)->get();
    }

    /**
     * Get specializations with pagination.
     */
    public function getSpecializationsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->specializations()->paginate($perPage);
    }

    /**
     * Get specializations ordered by creation date.
     */
    public function getSpecializationsByDate(): Collection
    {
        return $this->specializations()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get specializations ordered by experience.
     */
    public function getSpecializationsByExperience(): Collection
    {
        return $this->specializations()->orderBy('years_experience', 'desc')->get();
    }

    /**
     * Get specializations ordered by proficiency level.
     */
    public function getSpecializationsByProficiency(): Collection
    {
        return $this->specializations()->orderBy('proficiency_level', 'desc')->get();
    }

    /**
     * Get the most recent specialization.
     */
    public function getLatestSpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->latest()->first();
    }

    /**
     * Get the oldest specialization.
     */
    public function getOldestSpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->oldest()->first();
    }

    /**
     * Get the specialization with the most experience.
     */
    public function getMostExperiencedSpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->orderBy('years_experience', 'desc')->first();
    }

    /**
     * Get the specialization with the highest proficiency level.
     */
    public function getHighestProficiencySpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->orderBy('proficiency_level', 'desc')->first();
    }
}
