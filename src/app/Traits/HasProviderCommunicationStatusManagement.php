<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Illuminate\Support\Carbon;

trait HasProviderCommunicationStatusManagement
{
    /**
     * Mark communication as read
     */
    public function markAsRead(ProviderCommunication $communication, ?Carbon $readAt = null): bool
    {
        try {
            $communication->update([
                'status' => 'read',
                'read_at' => $readAt ?? now(),
            ]);

            // Calculate response time if this is an inbound communication
            if ($communication->direction === 'inbound') {
                $this->calculateResponseTime($communication);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mark communication as replied
     */
    public function markAsReplied(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'status' => 'replied',
                'replied_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mark communication as closed
     */
    public function markAsClosed(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'status' => 'closed',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Archive communication
     */
    public function archive(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'is_archived' => true,
                'status' => 'archived',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Unarchive communication
     */
    public function unarchive(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'is_archived' => false,
                'status' => 'read',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set communication as urgent
     */
    public function setUrgent(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'is_urgent' => true,
                'priority' => 'urgent',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Unset communication urgency
     */
    public function unsetUrgent(ProviderCommunication $communication): bool
    {
        try {
            $communication->update([
                'is_urgent' => false,
                'priority' => 'normal',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update communication status
     */
    public function updateStatus(ProviderCommunication $communication, string $status): bool
    {
        try {
            $communication->update(['status' => $status]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update communication priority
     */
    public function updatePriority(ProviderCommunication $communication, string $priority): bool
    {
        try {
            $communication->update(['priority' => $priority]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Bulk status update
     */
    public function bulkStatusUpdate(array $communicationIds, string $status): int
    {
        try {
            return ProviderCommunication::whereIn('id', $communicationIds)
                ->update(['status' => $status]);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bulk priority update
     */
    public function bulkPriorityUpdate(array $communicationIds, string $priority): int
    {
        try {
            return ProviderCommunication::whereIn('id', $communicationIds)
                ->update(['priority' => $priority]);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bulk archive
     */
    public function bulkArchive(array $communicationIds): int
    {
        try {
            return ProviderCommunication::whereIn('id', $communicationIds)
                ->update([
                    'is_archived' => true,
                    'status' => 'archived',
                ]);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Bulk unarchive
     */
    public function bulkUnarchive(array $communicationIds): int
    {
        try {
            return ProviderCommunication::whereIn('id', $communicationIds)
                ->update([
                    'is_archived' => false,
                    'status' => 'read',
                ]);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
