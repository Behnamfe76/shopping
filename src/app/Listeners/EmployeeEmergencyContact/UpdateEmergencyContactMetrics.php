<?php

namespace App\Listeners\EmployeeEmergencyContact;

use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactCreated;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactDeleted;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactSetPrimary;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateEmergencyContactMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $contact = $event->contact;
            $action = $this->getActionFromEvent($event);

            // Update various metrics
            $this->updateContactCountMetrics($contact, $action);
            $this->updateRelationshipMetrics($contact, $action);
            $this->updatePrimaryContactMetrics($contact, $action);
            $this->updateGeographicMetrics($contact, $action);

            // Clear relevant caches
            $this->clearMetricsCache();

            Log::info("Emergency contact metrics updated for {$action}", [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
                'action' => $action,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update emergency contact metrics', [
                'event' => get_class($event),
                'contact_id' => $event->contact->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update contact count metrics.
     */
    private function updateContactCountMetrics($contact, string $action): void
    {
        try {
            $employeeId = $contact->employee_id;
            $cacheKey = "employee_contact_count_{$employeeId}";

            // Clear the cache to force recalculation
            Cache::forget($cacheKey);

            // Update total contact count if needed
            if (in_array($action, ['created', 'deleted'])) {
                $this->updateTotalContactCount();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update contact count metrics', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
            ]);
        }
    }

    /**
     * Update relationship distribution metrics.
     */
    private function updateRelationshipMetrics($contact, string $action): void
    {
        try {
            $cacheKey = 'emergency_contact_relationship_distribution';
            Cache::forget($cacheKey);

            // Could implement more sophisticated relationship tracking here
            // For now, just clear the cache to force recalculation
        } catch (\Exception $e) {
            Log::warning('Failed to update relationship metrics', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
            ]);
        }
    }

    /**
     * Update primary contact metrics.
     */
    private function updatePrimaryContactMetrics($contact, string $action): void
    {
        try {
            if ($action === 'set as primary' || $contact->is_primary) {
                $cacheKey = 'total_primary_contacts';
                Cache::forget($cacheKey);

                // Update primary contact count
                $this->updateTotalPrimaryContacts();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update primary contact metrics', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
            ]);
        }
    }

    /**
     * Update geographic metrics.
     */
    private function updateGeographicMetrics($contact, string $action): void
    {
        try {
            if (! empty($contact->city) || ! empty($contact->state) || ! empty($contact->country)) {
                $cacheKeys = [
                    'emergency_contact_city_distribution',
                    'emergency_contact_state_distribution',
                    'emergency_contact_country_distribution',
                ];

                foreach ($cacheKeys as $key) {
                    Cache::forget($key);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update geographic metrics', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
            ]);
        }
    }

    /**
     * Update total contact count.
     */
    private function updateTotalContactCount(): void
    {
        try {
            $cacheKey = 'total_emergency_contacts';
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Failed to update total contact count', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update total primary contacts count.
     */
    private function updateTotalPrimaryContacts(): void
    {
        try {
            $cacheKey = 'total_primary_contacts';
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Failed to update total primary contacts', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear metrics cache.
     */
    private function clearMetricsCache(): void
    {
        $cacheKeys = [
            'emergency_contact_statistics',
            'emergency_contact_distribution',
            'emergency_contact_analytics',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get action description from event.
     */
    private function getActionFromEvent($event): string
    {
        return match (true) {
            $event instanceof EmployeeEmergencyContactCreated => 'created',
            $event instanceof EmployeeEmergencyContactUpdated => 'updated',
            $event instanceof EmployeeEmergencyContactSetPrimary => 'set as primary',
            $event instanceof EmployeeEmergencyContactDeleted => 'deleted',
            default => 'unknown',
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Update emergency contact metrics job failed', [
            'event' => get_class($event),
            'contact_id' => $event->contact->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
