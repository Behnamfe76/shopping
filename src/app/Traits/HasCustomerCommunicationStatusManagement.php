<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\Enums\CommunicationStatus;
use Illuminate\Support\Facades\Event;

trait HasCustomerCommunicationStatusManagement
{
    /**
     * Schedule a communication.
     */
    public function schedule(CustomerCommunication $communication, string $scheduledAt): bool
    {
        $communication->update([
            'status' => CommunicationStatus::SCHEDULED,
            'scheduled_at' => $scheduledAt,
        ]);

        Event::dispatch('customer-communication.scheduled', $communication);

        return true;
    }

    /**
     * Send a communication.
     */
    public function send(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::SENT,
            'sent_at' => now(),
        ]);

        Event::dispatch('customer-communication.sent', $communication);

        return true;
    }

    /**
     * Mark a communication as delivered.
     */
    public function markAsDelivered(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::DELIVERED,
            'delivered_at' => now(),
        ]);

        Event::dispatch('customer-communication.delivered', $communication);

        return true;
    }

    /**
     * Mark a communication as opened.
     */
    public function markAsOpened(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::OPENED,
            'opened_at' => now(),
        ]);

        Event::dispatch('customer-communication.opened', $communication);

        return true;
    }

    /**
     * Mark a communication as clicked.
     */
    public function markAsClicked(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::CLICKED,
            'clicked_at' => now(),
        ]);

        Event::dispatch('customer-communication.clicked', $communication);

        return true;
    }

    /**
     * Mark a communication as bounced.
     */
    public function markAsBounced(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::BOUNCED,
            'bounced_at' => now(),
        ]);

        Event::dispatch('customer-communication.bounced', $communication);

        return true;
    }

    /**
     * Mark a communication as unsubscribed.
     */
    public function markAsUnsubscribed(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ]);

        Event::dispatch('customer-communication.unsubscribed', $communication);

        return true;
    }

    /**
     * Cancel a communication.
     */
    public function cancel(CustomerCommunication $communication): bool
    {
        $communication->update([
            'status' => CommunicationStatus::CANCELLED,
        ]);

        Event::dispatch('customer-communication.cancelled', $communication);

        return true;
    }

    /**
     * Reschedule a communication.
     */
    public function reschedule(CustomerCommunication $communication, string $newScheduledAt): bool
    {
        $communication->update([
            'status' => CommunicationStatus::SCHEDULED,
            'scheduled_at' => $newScheduledAt,
        ]);

        Event::dispatch('customer-communication.rescheduled', $communication);

        return true;
    }

    /**
     * Validate communication status change.
     */
    public function validateStatusChange(CustomerCommunication $communication, string $newStatus): bool
    {
        $currentStatus = $communication->status;
        $validTransitions = [
            CommunicationStatus::DRAFT => [
                CommunicationStatus::SCHEDULED,
                CommunicationStatus::SENT,
            ],
            CommunicationStatus::SCHEDULED => [
                CommunicationStatus::SENT,
                CommunicationStatus::CANCELLED,
            ],
            CommunicationStatus::SENT => [
                CommunicationStatus::DELIVERED,
                CommunicationStatus::BOUNCED,
                CommunicationStatus::FAILED,
            ],
            CommunicationStatus::DELIVERED => [
                CommunicationStatus::OPENED,
                CommunicationStatus::BOUNCED,
            ],
            CommunicationStatus::OPENED => [
                CommunicationStatus::CLICKED,
                CommunicationStatus::UNSUBSCRIBED,
            ],
            CommunicationStatus::CLICKED => [
                CommunicationStatus::UNSUBSCRIBED,
            ],
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }

    /**
     * Get valid status transitions for a communication.
     */
    public function getValidStatusTransitions(CustomerCommunication $communication): array
    {
        $currentStatus = $communication->status;
        $validTransitions = [
            CommunicationStatus::DRAFT => [
                CommunicationStatus::SCHEDULED,
                CommunicationStatus::SENT,
            ],
            CommunicationStatus::SCHEDULED => [
                CommunicationStatus::SENT,
                CommunicationStatus::CANCELLED,
            ],
            CommunicationStatus::SENT => [
                CommunicationStatus::DELIVERED,
                CommunicationStatus::BOUNCED,
                CommunicationStatus::FAILED,
            ],
            CommunicationStatus::DELIVERED => [
                CommunicationStatus::OPENED,
                CommunicationStatus::BOUNCED,
            ],
            CommunicationStatus::OPENED => [
                CommunicationStatus::CLICKED,
                CommunicationStatus::UNSUBSCRIBED,
            ],
            CommunicationStatus::CLICKED => [
                CommunicationStatus::UNSUBSCRIBED,
            ],
        ];

        return $validTransitions[$currentStatus] ?? [];
    }

    /**
     * Check if a communication can be scheduled.
     */
    public function canBeScheduled(CustomerCommunication $communication): bool
    {
        return in_array($communication->status, [
            CommunicationStatus::DRAFT,
        ]);
    }

    /**
     * Check if a communication can be sent.
     */
    public function canBeSent(CustomerCommunication $communication): bool
    {
        return in_array($communication->status, [
            CommunicationStatus::DRAFT,
            CommunicationStatus::SCHEDULED,
        ]);
    }

    /**
     * Check if a communication can be cancelled.
     */
    public function canBeCancelled(CustomerCommunication $communication): bool
    {
        return in_array($communication->status, [
            CommunicationStatus::SCHEDULED,
        ]);
    }

    /**
     * Check if a communication can be rescheduled.
     */
    public function canBeRescheduled(CustomerCommunication $communication): bool
    {
        return in_array($communication->status, [
            CommunicationStatus::SCHEDULED,
        ]);
    }

    /**
     * Get communication status history.
     */
    public function getStatusHistory(CustomerCommunication $communication): array
    {
        $history = [];

        if ($communication->scheduled_at) {
            $history[] = [
                'status' => CommunicationStatus::SCHEDULED,
                'timestamp' => $communication->scheduled_at,
                'description' => 'Communication scheduled',
            ];
        }

        if ($communication->sent_at) {
            $history[] = [
                'status' => CommunicationStatus::SENT,
                'timestamp' => $communication->sent_at,
                'description' => 'Communication sent',
            ];
        }

        if ($communication->delivered_at) {
            $history[] = [
                'status' => CommunicationStatus::DELIVERED,
                'timestamp' => $communication->delivered_at,
                'description' => 'Communication delivered',
            ];
        }

        if ($communication->opened_at) {
            $history[] = [
                'status' => CommunicationStatus::OPENED,
                'timestamp' => $communication->opened_at,
                'description' => 'Communication opened',
            ];
        }

        if ($communication->clicked_at) {
            $history[] = [
                'status' => CommunicationStatus::CLICKED,
                'timestamp' => $communication->clicked_at,
                'description' => 'Communication clicked',
            ];
        }

        if ($communication->bounced_at) {
            $history[] = [
                'status' => CommunicationStatus::BOUNCED,
                'timestamp' => $communication->bounced_at,
                'description' => 'Communication bounced',
            ];
        }

        if ($communication->unsubscribed_at) {
            $history[] = [
                'status' => CommunicationStatus::UNSUBSCRIBED,
                'timestamp' => $communication->unsubscribed_at,
                'description' => 'Customer unsubscribed',
            ];
        }

        return $history;
    }

    /**
     * Get communication status summary.
     */
    public function getStatusSummary(CustomerCommunication $communication): array
    {
        return [
            'current_status' => $communication->status,
            'is_scheduled' => $communication->isScheduled(),
            'is_sent' => $communication->isSent(),
            'is_delivered' => $communication->isDelivered(),
            'is_opened' => $communication->isOpened(),
            'is_clicked' => $communication->isClicked(),
            'is_bounced' => $communication->isBounced(),
            'is_unsubscribed' => $communication->isUnsubscribed(),
            'is_cancelled' => $communication->isCancelled(),
            'is_failed' => $communication->isFailed(),
            'can_be_scheduled' => $this->canBeScheduled($communication),
            'can_be_sent' => $this->canBeSent($communication),
            'can_be_cancelled' => $this->canBeCancelled($communication),
            'can_be_rescheduled' => $this->canBeRescheduled($communication),
            'valid_transitions' => $this->getValidStatusTransitions($communication),
            'status_history' => $this->getStatusHistory($communication),
        ];
    }
}
