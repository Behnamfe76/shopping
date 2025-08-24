<?php

namespace Fereydooni\Shopping\App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

trait HasProviderSpecializationPrimaryManagement
{
    /**
     * Set a specialization as the primary specialization.
     */
    public function setPrimarySpecialization(int $specializationId): bool
    {
        try {
            DB::beginTransaction();

            // Remove primary status from all other specializations
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
            Log::error("Failed to set primary specialization {$specializationId} for provider {$this->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove primary status from a specialization.
     */
    public function removePrimarySpecialization(int $specializationId): bool
    {
        try {
            $specialization = $this->specializations()->findOrFail($specializationId);

            if (!$specialization->is_primary) {
                throw new Exception('Specialization is not primary.');
            }

            $result = $specialization->removePrimary();

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.primary_removed', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Failed to remove primary specialization {$specializationId} from provider {$this->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the current primary specialization.
     */
    public function getPrimarySpecialization(): ?ProviderSpecialization
    {
        return $this->specializations()->primary()->first();
    }

    /**
     * Check if the provider has a primary specialization.
     */
    public function hasPrimarySpecialization(): bool
    {
        return $this->specializations()->primary()->exists();
    }

    /**
     * Get the primary specialization ID.
     */
    public function getPrimarySpecializationId(): ?int
    {
        $primary = $this->getPrimarySpecialization();
        return $primary ? $primary->id : null;
    }

    /**
     * Get the primary specialization name.
     */
    public function getPrimarySpecializationName(): ?string
    {
        $primary = $this->getPrimarySpecialization();
        return $primary ? $primary->specialization_name : null;
    }

    /**
     * Get the primary specialization category.
     */
    public function getPrimarySpecializationCategory(): ?string
    {
        $primary = $this->getPrimarySpecialization();
        return $primary ? $primary->category->value : null;
    }

    /**
     * Get the primary specialization proficiency level.
     */
    public function getPrimarySpecializationProficiencyLevel(): ?string
    {
        $primary = $this->getPrimarySpecialization();
        return $primary ? $primary->proficiency_level->value : null;
    }

    /**
     * Get the primary specialization experience.
     */
    public function getPrimarySpecializationExperience(): ?int
    {
        $primary = $this->getPrimarySpecialization();
        return $primary ? $primary->years_experience : null;
    }

    /**
     * Check if a specialization is the primary specialization.
     */
    public function isPrimarySpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->find($specializationId);
        return $specialization ? $specialization->is_primary : false;
    }

    /**
     * Get all non-primary specializations.
     */
    public function getNonPrimarySpecializations(): Collection
    {
        return $this->specializations()->nonPrimary()->get();
    }

    /**
     * Get the number of primary specializations.
     */
    public function getPrimarySpecializationCount(): int
    {
        return $this->specializations()->primary()->count();
    }

    /**
     * Get the number of non-primary specializations.
     */
    public function getNonPrimarySpecializationCount(): int
    {
        return $this->specializations()->nonPrimary()->count();
    }

    /**
     * Check if the provider can have a primary specialization.
     */
    public function canHavePrimarySpecialization(): bool
    {
        return $this->getSpecializationCount() > 0;
    }

    /**
     * Check if the provider should have a primary specialization.
     */
    public function shouldHavePrimarySpecialization(): bool
    {
        return $this->getSpecializationCount() > 0 && !$this->hasPrimarySpecialization();
    }

    /**
     * Automatically assign a primary specialization if none exists.
     */
    public function autoAssignPrimarySpecialization(): bool
    {
        if ($this->hasPrimarySpecialization()) {
            return true; // Already has primary
        }

        if (!$this->canHavePrimarySpecialization()) {
            return false; // No specializations to choose from
        }

        // Try to find the best candidate for primary specialization
        $candidate = $this->findBestPrimarySpecializationCandidate();

        if ($candidate) {
            return $this->setPrimarySpecialization($candidate->id);
        }

        return false;
    }

    /**
     * Find the best candidate for primary specialization.
     */
    protected function findBestPrimarySpecializationCandidate(): ?ProviderSpecialization
    {
        // Priority order: verified > active > most experienced > most recent
        $candidate = $this->specializations()
            ->verified()
            ->active()
            ->orderBy('years_experience', 'desc')
            ->orderBy('proficiency_level', 'desc')
            ->first();

        if ($candidate) {
            return $candidate;
        }

        // If no verified specializations, try active ones
        $candidate = $this->specializations()
            ->active()
            ->orderBy('years_experience', 'desc')
            ->orderBy('proficiency_level', 'desc')
            ->first();

        if ($candidate) {
            return $candidate;
        }

        // If no active specializations, try any with most experience
        $candidate = $this->specializations()
            ->orderBy('years_experience', 'desc')
            ->orderBy('proficiency_level', 'desc')
            ->first();

        return $candidate;
    }

    /**
     * Get primary specialization statistics.
     */
    public function getPrimarySpecializationStatistics(): array
    {
        $primary = $this->getPrimarySpecialization();

        if (!$primary) {
            return [
                'has_primary' => false,
                'primary_id' => null,
                'primary_name' => null,
                'primary_category' => null,
                'primary_proficiency' => null,
                'primary_experience' => null,
                'primary_verification_status' => null,
                'days_as_primary' => null,
            ];
        }

        return [
            'has_primary' => true,
            'primary_id' => $primary->id,
            'primary_name' => $primary->specialization_name,
            'primary_category' => $primary->category->value,
            'primary_proficiency' => $primary->proficiency_level->value,
            'primary_experience' => $primary->years_experience,
            'primary_verification_status' => $primary->verification_status->value,
            'days_as_primary' => $primary->created_at->diffInDays(now()),
        ];
    }

    /**
     * Get the history of primary specializations.
     */
    public function getPrimarySpecializationHistory(): array
    {
        // This would require a separate table to track primary specialization changes
        // For now, return basic information about the current primary
        $primary = $this->getPrimarySpecialization();

        if (!$primary) {
            return [];
        }

        return [
            [
                'specialization_id' => $primary->id,
                'specialization_name' => $primary->specialization_name,
                'set_as_primary_at' => $primary->created_at,
                'is_current' => true,
            ]
        ];
    }

    /**
     * Check if the primary specialization needs attention.
     */
    public function primarySpecializationNeedsAttention(): bool
    {
        $primary = $this->getPrimarySpecialization();

        if (!$primary) {
            return true; // No primary specialization
        }

        // Check if primary specialization is inactive
        if (!$primary->is_active) {
            return true;
        }

        // Check if primary specialization is unverified for too long
        if ($primary->verification_status === 'unverified' &&
            $primary->created_at->diffInDays(now()) > 30) {
            return true;
        }

        // Check if primary specialization is pending for too long
        if ($primary->verification_status === 'pending' &&
            $primary->updated_at->diffInDays(now()) > 14) {
            return true;
        }

        // Check if primary specialization is rejected
        if ($primary->verification_status === 'rejected') {
            return true;
        }

        return false;
    }

    /**
     * Get recommendations for primary specialization.
     */
    public function getPrimarySpecializationRecommendations(): array
    {
        $recommendations = [];
        $primary = $this->getPrimarySpecialization();

        if (!$primary) {
            $recommendations[] = 'Assign a primary specialization to highlight your main expertise.';
            return $recommendations;
        }

        if (!$primary->is_active) {
            $recommendations[] = 'Your primary specialization is inactive. Consider activating it or choosing a new primary.';
        }

        if ($primary->verification_status === 'unverified') {
            $recommendations[] = 'Submit your primary specialization for verification to build trust.';
        }

        if ($primary->verification_status === 'pending') {
            $recommendations[] = 'Your primary specialization is pending verification. This may take a few days.';
        }

        if ($primary->verification_status === 'rejected') {
            $recommendations[] = 'Your primary specialization was rejected. Review the feedback and resubmit.';
        }

        if ($primary->years_experience < 2) {
            $recommendations[] = 'Consider choosing a specialization with more experience as your primary.';
        }

        if ($primary->proficiency_level->value === 'beginner') {
            $recommendations[] = 'Consider choosing a specialization with higher proficiency as your primary.';
        }

        return $recommendations;
    }

    /**
     * Validate if a specialization can be set as primary.
     */
    public function canSetAsPrimary(int $specializationId): array
    {
        $specialization = $this->specializations()->find($specializationId);

        if (!$specialization) {
            return ['can_set' => false, 'reason' => 'Specialization not found.'];
        }

        if ($specialization->is_primary) {
            return ['can_set' => false, 'reason' => 'Specialization is already primary.'];
        }

        if (!$specialization->is_active) {
            return ['can_set' => false, 'reason' => 'Inactive specializations cannot be primary.'];
        }

        if ($specialization->verification_status === 'rejected') {
            return ['can_set' => false, 'reason' => 'Rejected specializations cannot be primary.'];
        }

        return ['can_set' => true, 'reason' => 'Specialization can be set as primary.'];
    }

    /**
     * Get the impact of changing primary specialization.
     */
    public function getPrimarySpecializationChangeImpact(int $newPrimaryId): array
    {
        $currentPrimary = $this->getPrimarySpecialization();
        $newPrimary = $this->specializations()->find($newPrimaryId);

        if (!$newPrimary) {
            return ['can_change' => false, 'impact' => 'New primary specialization not found.'];
        }

        $impact = [];

        if ($currentPrimary) {
            $impact[] = "Current primary '{$currentPrimary->specialization_name}' will lose primary status.";
        }

        $impact[] = "'{$newPrimary->specialization_name}' will become the new primary specialization.";

        if ($newPrimary->verification_status === 'unverified') {
            $impact[] = "New primary is unverified - consider submitting for verification.";
        }

        if ($newPrimary->verification_status === 'pending') {
            $impact[] = "New primary is pending verification.";
        }

        if ($newPrimary->verification_status === 'rejected') {
            $impact[] = "Warning: New primary was previously rejected.";
        }

        return [
            'can_change' => true,
            'impact' => $impact,
            'current_primary' => $currentPrimary ? $currentPrimary->specialization_name : null,
            'new_primary' => $newPrimary->specialization_name,
        ];
    }
}
