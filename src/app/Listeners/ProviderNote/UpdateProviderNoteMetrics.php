<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderNote;

use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteCreated;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteUpdated;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteArchived;
use Fereydooni\Shopping\app\Events\ProviderNote\ProviderNoteDeleted;
use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateProviderNoteMetrics
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
                // Update provider note metrics
                $this->updateProviderNoteMetrics($provider);

                // Update note type metrics
                $this->updateNoteTypeMetrics($providerNote->note_type);

                // Update priority metrics
                $this->updatePriorityMetrics($providerNote->priority);

                // Update user activity metrics
                $this->updateUserActivityMetrics($providerNote->user_id);

                // Update daily metrics
                $this->updateDailyMetrics();

                Log::info('Provider note metrics updated for created note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                    'note_type' => $providerNote->note_type,
                    'priority' => $providerNote->priority
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note metrics for created note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage()
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
                // Update provider note metrics
                $this->updateProviderNoteMetrics($provider);

                // Update note type metrics if type changed
                if (isset($changes['note_type'])) {
                    $this->updateNoteTypeMetrics($changes['note_type']['new']);
                    $this->updateNoteTypeMetrics($changes['note_type']['old'], -1);
                }

                // Update priority metrics if priority changed
                if (isset($changes['priority'])) {
                    $this->updatePriorityMetrics($changes['priority']['new']);
                    $this->updatePriorityMetrics($changes['priority']['old'], -1);
                }

                // Update user activity metrics
                $this->updateUserActivityMetrics($providerNote->user_id);

                Log::info('Provider note metrics updated for updated note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id,
                    'changes' => $changes
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note metrics for updated note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage()
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
                // Update provider note metrics
                $this->updateProviderNoteMetrics($provider);

                // Update archive metrics
                $this->updateArchiveMetrics();

                Log::info('Provider note metrics updated for archived note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note metrics for archived note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage()
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
                // Update provider note metrics
                $this->updateProviderNoteMetrics($provider, -1);

                // Update note type metrics
                $this->updateNoteTypeMetrics($providerNote->note_type, -1);

                // Update priority metrics
                $this->updatePriorityMetrics($providerNote->priority, -1);

                // Update deletion metrics
                $this->updateDeletionMetrics();

                Log::info('Provider note metrics updated for deleted note', [
                    'provider_note_id' => $providerNote->id,
                    'provider_id' => $provider->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update provider note metrics for deleted note', [
                'provider_note_id' => $event->providerNote->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider note metrics
     */
    private function updateProviderNoteMetrics(Provider $provider, int $increment = 1): void
    {
        $cacheKey = "provider_{$provider->id}_note_metrics";
        $metrics = Cache::get($cacheKey, [
            'total_notes' => 0,
            'active_notes' => 0,
            'archived_notes' => 0,
            'private_notes' => 0,
            'public_notes' => 0,
            'high_priority_notes' => 0,
            'urgent_notes' => 0,
            'last_updated' => now()->toISOString()
        ]);

        $metrics['total_notes'] += $increment;
        $metrics['last_updated'] = now()->toISOString();

        Cache::put($cacheKey, $metrics, 3600);
    }

    /**
     * Update note type metrics
     */
    private function updateNoteTypeMetrics(string $noteType, int $increment = 1): void
    {
        $cacheKey = "note_type_metrics_{$noteType}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update priority metrics
     */
    private function updatePriorityMetrics(string $priority, int $increment = 1): void
    {
        $cacheKey = "priority_metrics_{$priority}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + $increment, 3600);
    }

    /**
     * Update user activity metrics
     */
    private function updateUserActivityMetrics(int $userId): void
    {
        $cacheKey = "user_{$userId}_note_activity";
        $activity = Cache::get($cacheKey, [
            'total_notes_created' => 0,
            'total_notes_updated' => 0,
            'last_activity' => null
        ]);

        $activity['total_notes_created']++;
        $activity['last_activity'] = now()->toISOString();

        Cache::put($cacheKey, $activity, 3600);
    }

    /**
     * Update daily metrics
     */
    private function updateDailyMetrics(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_note_metrics_{$today}";

        $metrics = Cache::get($cacheKey, [
            'notes_created' => 0,
            'notes_updated' => 0,
            'notes_archived' => 0,
            'notes_deleted' => 0,
            'unique_users' => [],
            'unique_providers' => []
        ]);

        $metrics['notes_created']++;

        Cache::put($cacheKey, $metrics, 86400); // 24 hours
    }

    /**
     * Update archive metrics
     */
    private function updateArchiveMetrics(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_note_metrics_{$today}";

        $metrics = Cache::get($cacheKey, [
            'notes_created' => 0,
            'notes_updated' => 0,
            'notes_archived' => 0,
            'notes_deleted' => 0,
            'unique_users' => [],
            'unique_providers' => []
        ]);

        $metrics['notes_archived']++;

        Cache::put($cacheKey, $metrics, 86400);
    }

    /**
     * Update deletion metrics
     */
    private function updateDeletionMetrics(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_note_metrics_{$today}";

        $metrics = Cache::get($cacheKey, [
            'notes_created' => 0,
            'notes_updated' => 0,
            'notes_archived' => 0,
            'notes_deleted' => 0,
            'unique_users' => [],
            'unique_providers' => []
        ]);

        $metrics['notes_deleted']++;

        Cache::put($cacheKey, $metrics, 86400);
    }
}
