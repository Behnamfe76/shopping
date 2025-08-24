<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ProviderCommunicationRepositoryInterface;
use App\Models\ProviderCommunication;
use App\DTOs\ProviderCommunicationDTO;
use App\Enums\Status;
use App\Enums\Priority;
use App\Enums\Direction;
use App\Events\ProviderCommunicationCreated;
use App\Events\CommunicationSent;
use App\Events\CommunicationRead;
use App\Events\CommunicationReplied;
use App\Events\CommunicationArchived;
use Exception;

class ProviderCommunicationService
{
    protected $repository;

    public function __construct(ProviderCommunicationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Basic CRUD operations
    public function getAllCommunications(): Collection
    {
        return $this->repository->all();
    }

    public function paginateCommunications(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findCommunication(int $id): ?ProviderCommunication
    {
        return $this->repository->find($id);
    }

    public function findCommunicationDTO(int $id): ?ProviderCommunicationDTO
    {
        return $this->repository->findDTO($id);
    }

    public function createCommunication(array $data): ProviderCommunication
    {
        try {
            // Validate business rules
            $this->validateCommunicationData($data);

            // Set default values
            $data = $this->setDefaultValues($data);

            // Create communication
            $communication = $this->repository->create($data);

            // Dispatch events
            Event::dispatch(new ProviderCommunicationCreated($communication));

            // Send notifications if needed
            $this->sendCommunicationNotifications($communication);

            Log::info('Provider communication created successfully', [
                'id' => $communication->id,
                'provider_id' => $communication->provider_id,
                'user_id' => $communication->user_id
            ]);

            return $communication;
        } catch (Exception $e) {
            Log::error('Failed to create provider communication', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function createCommunicationDTO(array $data): ProviderCommunicationDTO
    {
        $communication = $this->createCommunication($data);
        return ProviderCommunicationDTO::fromModel($communication);
    }

    public function updateCommunication(ProviderCommunication $communication, array $data): bool
    {
        try {
            // Validate update data
            $this->validateUpdateData($data);

            // Update communication
            $result = $this->repository->update($communication, $data);

            if ($result) {
                Log::info('Provider communication updated successfully', [
                    'id' => $communication->id,
                    'changes' => $data
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to update provider communication', [
                'id' => $communication->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateCommunicationDTO(ProviderCommunication $communication, array $data): ?ProviderCommunicationDTO
    {
        $result = $this->updateCommunication($communication, $data);
        return $result ? ProviderCommunicationDTO::fromModel($communication->fresh()) : null;
    }

    public function deleteCommunication(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->delete($communication);

            if ($result) {
                Log::info('Provider communication deleted successfully', [
                    'id' => $communication->id
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to delete provider communication', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Provider-specific operations
    public function getCommunicationsByProvider(int $providerId): Collection
    {
        return $this->repository->findByProviderId($providerId);
    }

    public function getCommunicationsByProviderDTO(int $providerId): Collection
    {
        return $this->repository->findByProviderIdDTO($providerId);
    }

    public function getCommunicationsByUser(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    public function getCommunicationsByUserDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    // Status management
    public function markAsRead(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->markAsRead($communication);

            if ($result) {
                Event::dispatch(new CommunicationRead($communication));
                Log::info('Communication marked as read', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to mark communication as read', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function markAsReplied(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->markAsReplied($communication);

            if ($result) {
                Event::dispatch(new CommunicationReplied($communication));
                Log::info('Communication marked as replied', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to mark communication as replied', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function markAsClosed(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->markAsClosed($communication);

            if ($result) {
                Log::info('Communication marked as closed', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to mark communication as closed', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function archiveCommunication(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->archive($communication);

            if ($result) {
                Event::dispatch(new CommunicationArchived($communication));
                Log::info('Communication archived', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to archive communication', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function unarchiveCommunication(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->unarchive($communication);

            if ($result) {
                Log::info('Communication unarchived', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to unarchive communication', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function setUrgent(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->setUrgent($communication);

            if ($result) {
                Log::info('Communication marked as urgent', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to mark communication as urgent', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function unsetUrgent(ProviderCommunication $communication): bool
    {
        try {
            $result = $this->repository->unsetUrgent($communication);

            if ($result) {
                Log::info('Communication unmarked as urgent', ['id' => $communication->id]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to unmark communication as urgent', [
                'id' => $communication->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Search and filtering
    public function searchCommunications(string $query): Collection
    {
        return $this->repository->searchCommunications($query);
    }

    public function searchCommunicationsDTO(string $query): Collection
    {
        return $this->repository->searchCommunicationsDTO($query);
    }

    public function getUrgentCommunications(int $limit = 10): Collection
    {
        return $this->repository->getUrgentCommunications($limit);
    }

    public function getUnreadCommunications(int $limit = 10): Collection
    {
        return $this->repository->getUnreadCommunications($limit);
    }

    public function getUnrepliedCommunications(int $limit = 10): Collection
    {
        return $this->repository->getUnrepliedCommunications($limit);
    }

    // Thread management
    public function getConversation(int $providerId, int $userId, int $limit = 50): Collection
    {
        return $this->repository->findConversation($providerId, $userId, $limit);
    }

    public function getThread(string $threadId): Collection
    {
        return $this->repository->findThread($threadId);
    }

    // Analytics and reporting
    public function getCommunicationCount(int $providerId): int
    {
        return $this->repository->getCommunicationCount($providerId);
    }

    public function getUnreadCount(int $providerId): int
    {
        return $this->repository->getUnreadCount($providerId);
    }

    public function getUrgentCount(int $providerId): int
    {
        return $this->repository->getUrgentCount($providerId);
    }

    // Business logic methods
    protected function validateCommunicationData(array $data): void
    {
        // Validate required fields
        if (empty($data['provider_id']) || empty($data['user_id']) ||
            empty($data['subject']) || empty($data['message'])) {
            throw new Exception('Required fields are missing');
        }

        // Validate communication type
        if (!in_array($data['communication_type'], ['email', 'phone', 'chat', 'sms', 'video_call', 'in_person', 'support_ticket', 'complaint', 'inquiry', 'order_update', 'payment_notification', 'quality_issue', 'delivery_update', 'contract_discussion', 'general'])) {
            throw new Exception('Invalid communication type');
        }

        // Validate direction
        if (!in_array($data['direction'], ['inbound', 'outbound'])) {
            throw new Exception('Invalid direction');
        }

        // Validate priority
        if (!in_array($data['priority'], ['low', 'normal', 'high', 'urgent'])) {
            throw new Exception('Invalid priority');
        }
    }

    protected function validateUpdateData(array $data): void
    {
        // Validate status if provided
        if (isset($data['status']) && !in_array($data['status'], ['draft', 'sent', 'delivered', 'read', 'replied', 'closed', 'archived', 'failed'])) {
            throw new Exception('Invalid status');
        }

        // Validate priority if provided
        if (isset($data['priority']) && !in_array($data['priority'], ['low', 'normal', 'high', 'urgent'])) {
            throw new Exception('Invalid priority');
        }
    }

    protected function setDefaultValues(array $data): array
    {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        // Set default priority if not provided
        if (!isset($data['priority'])) {
            $data['priority'] = 'normal';
        }

        // Set default direction if not provided
        if (!isset($data['direction'])) {
            $data['direction'] = 'outbound';
        }

        // Set default values for boolean fields
        $data['is_urgent'] = $data['is_urgent'] ?? false;
        $data['is_archived'] = $data['is_archived'] ?? false;

        return $data;
    }

    protected function sendCommunicationNotifications(ProviderCommunication $communication): void
    {
        try {
            // Send email notification if communication type is email
            if ($communication->communication_type === 'email') {
                // Email notification logic would go here
                Log::info('Email notification sent for communication', ['id' => $communication->id]);
            }

            // Send SMS notification if communication type is sms
            if ($communication->communication_type === 'sms') {
                // SMS notification logic would go here
                Log::info('SMS notification sent for communication', ['id' => $communication->id]);
            }

            // Send in-app notification
            // In-app notification logic would go here
            Log::info('In-app notification sent for communication', ['id' => $communication->id]);
        } catch (Exception $e) {
            Log::error('Failed to send communication notifications', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
