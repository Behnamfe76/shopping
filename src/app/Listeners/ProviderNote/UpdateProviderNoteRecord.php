<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderNote;

use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteArchived;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteCreated;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteDeleted;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteUpdated;
use Fereydooni\Shopping\app\Models\Provider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateProviderNoteRecord
{
    /**
     * Handle provider note created event
     */
    public function handleProviderNoteCreated(ProviderNoteCreated $event): void
    {
        try {
            $providerNote = $event->providerNote;
            $provider = $providerNote->provider;

            if ($provider) {
                // Update provider's note count
                $this->updateProviderNoteCount($provider);

                // Update provider's note type count
                $this->updateProviderNoteTypeCount($provider, $providerNote->note_type);

                // Update provider's note priority count
                $this->updateProviderNotePriorityCount($provider, $providerNote->priority);

                // Update provider's last activity
                $this->updateProviderLastActivity($provider);

                // Clear related caches
                $this->clearProviderCaches($provider->id);

                Log::info('Provider note record updated for created note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                    'note_type' => $providerNote->note_type,
                    'priority' => $providerNote->priority,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note record for created note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle provider note updated event
     */
    public function handleProviderNoteUpdated(ProviderNoteUpdated $event): void
    {
        try {
            $providerNote = $event->providerNote;
            $provider = $providerNote->provider;
            $changes = $event->changes;

            if ($provider) {
                // Update provider's note type count if type changed
                if (isset($changes['note_type'])) {
                    $this->updateProviderNoteTypeCount($provider, $changes['note_type']['new']);
                    $this->updateProviderNoteTypeCount($provider, $changes['note_type']['old'], -1);
                }

                // Update provider's note priority count if priority changed
                if (isset($changes['priority'])) {
                    $this->updateProviderNotePriorityCount($provider, $changes['priority']['new']);
                    $this->updateProviderNotePriorityCount($provider, $changes['priority']['old'], -1);
                }

                // Update provider's last activity
                $this->updateProviderLastActivity($provider);

                // Clear related caches
                $this->clearProviderCaches($provider->id);

                Log::info('Provider note record updated for updated note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                    'changes' => $changes,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note record for updated note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle provider note archived event
     */
    public function handleProviderNoteArchived(ProviderNoteArchived $event): void
    {
        try {
            $providerNote = $event->providerNote;
            $provider = $providerNote->provider;

            if ($provider) {
                // Update provider's archived note count
                $this->updateProviderArchivedNoteCount($provider);

                // Update provider's active note count
                $this->updateProviderActiveNoteCount($provider, -1);

                // Clear related caches
                $this->clearProviderCaches($provider->id);

                Log::info('Provider note record updated for archived note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note record for archived note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle provider note deleted event
     */
    public function handleProviderNoteDeleted(ProviderNoteDeleted $event): void
    {
        try {
            $providerNote = $event->providerNote;
            $provider = $providerNote->provider;

            if ($provider) {
                // Update provider's note count
                $this->updateProviderNoteCount($provider, -1);

                // Update provider's note type count
                $this->updateProviderNoteTypeCount($provider, $providerNote->note_type, -1);

                // Update provider's note priority count
                $this->updateProviderNotePriorityCount($provider, $providerNote->priority, -1);

                // Update provider's archived note count if note was archived
                if ($providerNote->is_archived) {
                    $this->updateProviderArchivedNoteCount($provider, -1);
                } else {
                    $this->updateProviderActiveNoteCount($provider, -1);
                }

                // Clear related caches
                $this->clearProviderCaches($provider->id);

                Log::info('Provider note record updated for deleted note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note record for deleted note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update provider's note count
     */
    private function updateProviderNoteCount(Provider $provider, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_note_count";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update provider's note type count
     */
    private function updateProviderNoteTypeCount(Provider $provider, string $noteType, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_note_type_{$noteType}_count";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update provider's note priority count
     */
    private function updateProviderNotePriorityCount(Provider $provider, string $priority, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_note_priority_{$priority}_count";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update provider's archived note count
     */
    private function updateProviderArchivedNoteCount(Provider $provider, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_archived_note_count";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update provider's active note count
     */
    private function updateProviderActiveNoteCount(Provider $provider, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_active_note_count";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update provider's last activity
     */
    private function updateProviderLastActivity(Provider $provider): void
    {
        $provider->update(['last_activity_at' => now()]);
    }

    /**
     * Clear provider-related caches
     */
    private function clearProviderCaches(int $providerId): void
    {
        $cacheKeys = [
            "provider_{$providerId}_notes",
            "provider_{$providerId}_note_statistics",
            "provider_{$providerId}_recent_notes",
            "provider_{$providerId}_note_search_results",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
