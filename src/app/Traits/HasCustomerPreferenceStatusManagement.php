<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\CustomerPreferenceDTO;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasCustomerPreferenceStatusManagement
{
    /**
     * Activate customer preference
     */
    public function activateCustomerPreference(CustomerPreference $preference, ?string $reason = null): bool
    {
        $this->validateStatusChange($preference, 'activate');

        $data = ['is_active' => true];

        if ($reason) {
            $data['notes'] = $this->addStatusChangeNote($preference, 'activated', $reason);
        }

        $result = $this->repository->update($preference, $data);

        if ($result) {
            $this->firePreferenceStatusChangedEvent($preference, 'activated', $reason);
        }

        return $result;
    }

    /**
     * Deactivate customer preference
     */
    public function deactivateCustomerPreference(CustomerPreference $preference, ?string $reason = null): bool
    {
        $this->validateStatusChange($preference, 'deactivate');

        $data = ['is_active' => false];

        if ($reason) {
            $data['notes'] = $this->addStatusChangeNote($preference, 'deactivated', $reason);
        }

        $result = $this->repository->update($preference, $data);

        if ($result) {
            $this->firePreferenceStatusChangedEvent($preference, 'deactivated', $reason);
        }

        return $result;
    }

    /**
     * Toggle customer preference status
     */
    public function toggleCustomerPreferenceStatus(CustomerPreference $preference, ?string $reason = null): bool
    {
        if ($preference->is_active) {
            return $this->deactivateCustomerPreference($preference, $reason);
        } else {
            return $this->activateCustomerPreference($preference, $reason);
        }
    }

    /**
     * Activate customer preference by ID
     */
    public function activateCustomerPreferenceById(int $preferenceId, ?string $reason = null): bool
    {
        $preference = $this->repository->find($preferenceId);

        if (! $preference) {
            return false;
        }

        return $this->activateCustomerPreference($preference, $reason);
    }

    /**
     * Deactivate customer preference by ID
     */
    public function deactivateCustomerPreferenceById(int $preferenceId, ?string $reason = null): bool
    {
        $preference = $this->repository->find($preferenceId);

        if (! $preference) {
            return false;
        }

        return $this->deactivateCustomerPreference($preference, $reason);
    }

    /**
     * Activate customer preference by key
     */
    public function activateCustomerPreferenceByKey(int $customerId, string $key, ?string $reason = null): bool
    {
        $preference = $this->repository->findByCustomerAndKey($customerId, $key);

        if (! $preference) {
            return false;
        }

        return $this->activateCustomerPreference($preference, $reason);
    }

    /**
     * Deactivate customer preference by key
     */
    public function deactivateCustomerPreferenceByKey(int $customerId, string $key, ?string $reason = null): bool
    {
        $preference = $this->repository->findByCustomerAndKey($customerId, $key);

        if (! $preference) {
            return false;
        }

        return $this->deactivateCustomerPreference($preference, $reason);
    }

    /**
     * Get preferences by status
     */
    public function getCustomerPreferencesByStatus(int $customerId, bool $isActive): Collection
    {
        return $this->repository->findByCustomerId($customerId)
            ->filter(fn ($preference) => $preference->is_active === $isActive);
    }

    /**
     * Get preferences by status as DTOs
     */
    public function getCustomerPreferencesByStatusDTO(int $customerId, bool $isActive): Collection
    {
        $preferences = $this->getCustomerPreferencesByStatus($customerId, $isActive);

        return $preferences->map(fn ($preference) => CustomerPreferenceDTO::fromModel($preference));
    }

    /**
     * Get active preferences
     */
    public function getActiveCustomerPreferences(int $customerId): Collection
    {
        return $this->getCustomerPreferencesByStatus($customerId, true);
    }

    /**
     * Get active preferences as DTOs
     */
    public function getActiveCustomerPreferencesDTO(int $customerId): Collection
    {
        return $this->getCustomerPreferencesByStatusDTO($customerId, true);
    }

    /**
     * Get inactive preferences
     */
    public function getInactiveCustomerPreferences(int $customerId): Collection
    {
        return $this->getCustomerPreferencesByStatus($customerId, false);
    }

    /**
     * Get inactive preferences as DTOs
     */
    public function getInactiveCustomerPreferencesDTO(int $customerId): Collection
    {
        return $this->getCustomerPreferencesByStatusDTO($customerId, false);
    }

    /**
     * Activate all customer preferences
     */
    public function activateAllCustomerPreferences(int $customerId, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerId($customerId);
            $activatedCount = 0;

            foreach ($preferences as $preference) {
                if (! $preference->is_active) {
                    if ($this->activateCustomerPreference($preference, $reason)) {
                        $activatedCount++;
                    }
                }
            }

            DB::commit();

            return $activatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Deactivate all customer preferences
     */
    public function deactivateAllCustomerPreferences(int $customerId, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerId($customerId);
            $deactivatedCount = 0;

            foreach ($preferences as $preference) {
                if ($preference->is_active) {
                    if ($this->deactivateCustomerPreference($preference, $reason)) {
                        $deactivatedCount++;
                    }
                }
            }

            DB::commit();

            return $deactivatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Activate customer preferences by type
     */
    public function activateCustomerPreferencesByType(int $customerId, string $type, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerAndType($customerId, $type);
            $activatedCount = 0;

            foreach ($preferences as $preference) {
                if (! $preference->is_active) {
                    if ($this->activateCustomerPreference($preference, $reason)) {
                        $activatedCount++;
                    }
                }
            }

            DB::commit();

            return $activatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Deactivate customer preferences by type
     */
    public function deactivateCustomerPreferencesByType(int $customerId, string $type, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerAndType($customerId, $type);
            $deactivatedCount = 0;

            foreach ($preferences as $preference) {
                if ($preference->is_active) {
                    if ($this->deactivateCustomerPreference($preference, $reason)) {
                        $deactivatedCount++;
                    }
                }
            }

            DB::commit();

            return $deactivatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Activate customer preferences by category
     */
    public function activateCustomerPreferencesByCategory(int $customerId, string $category, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerId($customerId);
            $activatedCount = 0;

            foreach ($preferences as $preference) {
                if ($this->getPreferenceCategory($preference->preference_key) === $category && ! $preference->is_active) {
                    if ($this->activateCustomerPreference($preference, $reason)) {
                        $activatedCount++;
                    }
                }
            }

            DB::commit();

            return $activatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Deactivate customer preferences by category
     */
    public function deactivateCustomerPreferencesByCategory(int $customerId, string $category, ?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $preferences = $this->repository->findByCustomerId($customerId);
            $deactivatedCount = 0;

            foreach ($preferences as $preference) {
                if ($this->getPreferenceCategory($preference->preference_key) === $category && $preference->is_active) {
                    if ($this->deactivateCustomerPreference($preference, $reason)) {
                        $deactivatedCount++;
                    }
                }
            }

            DB::commit();

            return $deactivatedCount > 0;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Get preference status statistics
     */
    public function getCustomerPreferenceStatusStats(int $customerId): array
    {
        $preferences = $this->repository->findByCustomerId($customerId);

        return [
            'total_preferences' => $preferences->count(),
            'active_preferences' => $preferences->where('is_active', true)->count(),
            'inactive_preferences' => $preferences->where('is_active', false)->count(),
            'active_percentage' => $preferences->count() > 0 ?
                round(($preferences->where('is_active', true)->count() / $preferences->count()) * 100, 2) : 0,
            'inactive_percentage' => $preferences->count() > 0 ?
                round(($preferences->where('is_active', false)->count() / $preferences->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Validate status change
     */
    protected function validateStatusChange(CustomerPreference $preference, string $action): void
    {
        $rules = [
            'preference' => 'required|exists:customer_preferences,id',
            'action' => 'required|in:activate,deactivate',
        ];

        $data = [
            'preference' => $preference->id,
            'action' => $action,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Add status change note
     */
    protected function addStatusChangeNote(CustomerPreference $preference, string $status, string $reason): array
    {
        $notes = $preference->notes ?? [];

        $notes[] = [
            'type' => 'status_change',
            'status' => $status,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id() ?? null,
        ];

        return $notes;
    }

    /**
     * Fire preference status changed event
     */
    protected function firePreferenceStatusChangedEvent(CustomerPreference $preference, string $status, ?string $reason = null): void
    {
        // This would typically fire an event
        // For now, just log the status change
        \Log::info('Customer preference status changed', [
            'preference_id' => $preference->id,
            'customer_id' => $preference->customer_id,
            'preference_key' => $preference->preference_key,
            'status' => $status,
            'reason' => $reason,
        ]);
    }

    /**
     * Get preference category from key
     */
    protected function getPreferenceCategory(string $key): string
    {
        $parts = explode('.', $key);

        return $parts[0] ?? 'general';
    }
}
