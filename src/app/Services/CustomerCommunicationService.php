<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\CustomerCommunicationDTO;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerCommunicationAnalytics;
use Fereydooni\Shopping\app\Traits\HasCustomerCommunicationOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerCommunicationStatusManagement;
use Fereydooni\Shopping\app\Traits\HasMediaOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

class CustomerCommunicationService
{
    use HasCrudOperations,
        HasCustomerCommunicationAnalytics,
        HasCustomerCommunicationOperations,
        HasCustomerCommunicationStatusManagement,
        HasMediaOperations,
        HasSearchOperations;

    protected CustomerCommunicationRepositoryInterface $repository;

    protected CustomerCommunication $model;

    protected string $dtoClass = CustomerCommunicationDTO::class;

    public function __construct(CustomerCommunicationRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = new CustomerCommunication;
    }

    /**
     * Create a new customer communication with validation and business logic.
     */
    public function createCommunication(array $data): CustomerCommunicationDTO
    {
        // Validate communication data
        if (! $this->repository->validateCommunication($data)) {
            throw new \InvalidArgumentException('Invalid communication data provided.');
        }

        // Set default status if not provided
        if (! isset($data['status'])) {
            $data['status'] = 'draft';
        }

        // Create the communication
        $communication = $this->repository->create($data);

        // Dispatch event
        Event::dispatch('customer-communication.created', $communication);

        return CustomerCommunicationDTO::fromModel($communication);
    }

    /**
     * Update a customer communication with validation.
     */
    public function updateCommunication(int $id, array $data): ?CustomerCommunicationDTO
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return null;
        }

        // Validate communication data
        if (! $this->repository->validateCommunication($data)) {
            throw new \InvalidArgumentException('Invalid communication data provided.');
        }

        // Update the communication
        $updated = $this->repository->update($communication, $data);

        if ($updated) {
            // Dispatch event
            Event::dispatch('customer-communication.updated', $communication->fresh());

            return CustomerCommunicationDTO::fromModel($communication->fresh());
        }

        return null;
    }

    /**
     * Delete a customer communication.
     */
    public function deleteCommunication(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        $deleted = $this->repository->delete($communication);

        if ($deleted) {
            // Dispatch event
            Event::dispatch('customer-communication.deleted', $communication);
        }

        return $deleted;
    }

    /**
     * Schedule a communication with validation.
     */
    public function scheduleCommunication(int $id, string $scheduledAt): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        // Validate if communication can be scheduled
        if (! $this->canBeScheduled($communication)) {
            throw new \InvalidArgumentException('Communication cannot be scheduled in its current state.');
        }

        return $this->repository->schedule($communication, $scheduledAt);
    }

    /**
     * Send a communication with validation.
     */
    public function sendCommunication(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        // Validate if communication can be sent
        if (! $this->canBeSent($communication)) {
            throw new \InvalidArgumentException('Communication cannot be sent in its current state.');
        }

        return $this->repository->send($communication);
    }

    /**
     * Cancel a communication with validation.
     */
    public function cancelCommunication(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        // Validate if communication can be cancelled
        if (! $this->canBeCancelled($communication)) {
            throw new \InvalidArgumentException('Communication cannot be cancelled in its current state.');
        }

        return $this->repository->cancel($communication);
    }

    /**
     * Reschedule a communication with validation.
     */
    public function rescheduleCommunication(int $id, string $newScheduledAt): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        // Validate if communication can be rescheduled
        if (! $this->canBeRescheduled($communication)) {
            throw new \InvalidArgumentException('Communication cannot be rescheduled in its current state.');
        }

        return $this->repository->reschedule($communication, $newScheduledAt);
    }

    /**
     * Mark a communication as delivered.
     */
    public function markAsDelivered(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->markAsDelivered($communication);
    }

    /**
     * Mark a communication as opened.
     */
    public function markAsOpened(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->markAsOpened($communication);
    }

    /**
     * Mark a communication as clicked.
     */
    public function markAsClicked(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->markAsClicked($communication);
    }

    /**
     * Mark a communication as bounced.
     */
    public function markAsBounced(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->markAsBounced($communication);
    }

    /**
     * Mark a communication as unsubscribed.
     */
    public function markAsUnsubscribed(int $id): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->markAsUnsubscribed($communication);
    }

    /**
     * Get communication analytics for a customer.
     */
    public function getCustomerAnalytics(int $customerId): array
    {
        return [
            'communication_stats' => $this->repository->getCommunicationStatsByCustomer($customerId),
            'engagement_stats' => $this->repository->getCommunicationEngagementStatsByCustomer($customerId),
            'performance_stats' => $this->getCommunicationPerformanceStatsByCustomer($customerId),
            'recommendations' => $this->repository->getCommunicationRecommendations($customerId),
            'insights' => $this->repository->getCommunicationInsights($customerId),
            'trends' => $this->repository->getCommunicationTrends($customerId),
        ];
    }

    /**
     * Get communication performance stats for a customer.
     */
    public function getCommunicationPerformanceStatsByCustomer(int $customerId): array
    {
        $communications = $this->repository->findByCustomerId($customerId);

        if ($communications->isEmpty()) {
            return [
                'total_communications' => 0,
                'delivery_rate' => 0.0,
                'open_rate' => 0.0,
                'click_rate' => 0.0,
                'bounce_rate' => 0.0,
                'unsubscribe_rate' => 0.0,
            ];
        }

        $sentCount = $communications->where('status', 'sent')->count();
        $deliveredCount = $communications->where('status', 'delivered')->count();
        $openedCount = $communications->where('status', 'opened')->count();
        $clickedCount = $communications->where('status', 'clicked')->count();
        $bouncedCount = $communications->where('status', 'bounced')->count();
        $unsubscribedCount = $communications->where('status', 'unsubscribed')->count();

        return [
            'total_communications' => $communications->count(),
            'delivery_rate' => $sentCount > 0 ? round(($deliveredCount / $sentCount) * 100, 2) : 0.0,
            'open_rate' => $deliveredCount > 0 ? round(($openedCount / $deliveredCount) * 100, 2) : 0.0,
            'click_rate' => $openedCount > 0 ? round(($clickedCount / $openedCount) * 100, 2) : 0.0,
            'bounce_rate' => $sentCount > 0 ? round(($bouncedCount / $sentCount) * 100, 2) : 0.0,
            'unsubscribe_rate' => $sentCount > 0 ? round(($unsubscribedCount / $sentCount) * 100, 2) : 0.0,
        ];
    }

    /**
     * Export customer communications.
     */
    public function exportCustomerCommunications(int $customerId): array
    {
        return $this->repository->exportCustomerCommunications($customerId);
    }

    /**
     * Import customer communications.
     */
    public function importCustomerCommunications(int $customerId, array $communications): bool
    {
        return $this->repository->importCustomerCommunications($customerId, $communications);
    }

    /**
     * Add attachment to a communication.
     */
    public function addAttachment(int $id, $file): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->addAttachment($communication, $file);
    }

    /**
     * Remove attachment from a communication.
     */
    public function removeAttachment(int $id, int $mediaId): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->removeAttachment($communication, $mediaId);
    }

    /**
     * Get attachments for a communication.
     */
    public function getAttachments(int $id): Collection
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return collect();
        }

        return $this->repository->getAttachments($communication);
    }

    /**
     * Update tracking data for a communication.
     */
    public function updateTrackingData(int $id, array $trackingData): bool
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return false;
        }

        return $this->repository->updateTrackingData($communication, $trackingData);
    }

    /**
     * Get tracking data for a communication.
     */
    public function getTrackingData(int $id): array
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return [];
        }

        return $this->repository->getTrackingData($communication);
    }

    /**
     * Get communication status summary.
     */
    public function getStatusSummary(int $id): array
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return [];
        }

        return $this->getStatusSummary($communication);
    }

    /**
     * Get communication status history.
     */
    public function getStatusHistory(int $id): array
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return [];
        }

        return $this->getStatusHistory($communication);
    }

    /**
     * Get valid status transitions for a communication.
     */
    public function getValidStatusTransitions(int $id): array
    {
        $communication = $this->repository->find($id);

        if (! $communication) {
            return [];
        }

        return $this->getValidStatusTransitions($communication);
    }
}
