<?php

namespace App\Traits;

use App\DTOs\ProviderCommunicationDTO;
use App\Models\ProviderCommunication;
use App\Services\ProviderCommunicationService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasProviderCommunicationOperations
{
    protected $communicationService;

    /**
     * Get all communications
     */
    public function getAllCommunications(): Collection
    {
        return $this->getCommunicationService()->getAllCommunications();
    }

    /**
     * Paginate communications
     */
    public function paginateCommunications(int $perPage = 15): LengthAwarePaginator
    {
        return $this->getCommunicationService()->paginateCommunications($perPage);
    }

    /**
     * Find communication by ID
     */
    public function findCommunication(int $id): ?ProviderCommunication
    {
        return $this->getCommunicationService()->findCommunication($id);
    }

    /**
     * Find communication by ID and return DTO
     */
    public function findCommunicationDTO(int $id): ?ProviderCommunicationDTO
    {
        return $this->getCommunicationService()->findCommunicationDTO($id);
    }

    /**
     * Create new communication
     */
    public function createCommunication(array $data): ProviderCommunication
    {
        return $this->getCommunicationService()->createCommunication($data);
    }

    /**
     * Create new communication and return DTO
     */
    public function createCommunicationDTO(array $data): ProviderCommunicationDTO
    {
        return $this->getCommunicationService()->createCommunicationDTO($data);
    }

    /**
     * Update communication
     */
    public function updateCommunication(ProviderCommunication $communication, array $data): bool
    {
        return $this->getCommunicationService()->updateCommunication($communication, $data);
    }

    /**
     * Update communication and return DTO
     */
    public function updateCommunicationDTO(ProviderCommunication $communication, array $data): ?ProviderCommunicationDTO
    {
        return $this->getCommunicationService()->updateCommunicationDTO($communication, $data);
    }

    /**
     * Delete communication
     */
    public function deleteCommunication(ProviderCommunication $communication): bool
    {
        return $this->getCommunicationService()->deleteCommunication($communication);
    }

    /**
     * Get communications by provider ID
     */
    public function getCommunicationsByProvider(int $providerId): Collection
    {
        return $this->getCommunicationService()->getCommunicationsByProvider($providerId);
    }

    /**
     * Get communications by provider ID and return DTOs
     */
    public function getCommunicationsByProviderDTO(int $providerId): Collection
    {
        return $this->getCommunicationService()->getCommunicationsByProviderDTO($providerId);
    }

    /**
     * Get communications by user ID
     */
    public function getCommunicationsByUser(int $userId): Collection
    {
        return $this->getCommunicationService()->getCommunicationsByUser($userId);
    }

    /**
     * Get communications by user ID and return DTOs
     */
    public function getCommunicationsByUserDTO(int $userId): Collection
    {
        return $this->getCommunicationService()->getCommunicationsByUserDTO($userId);
    }

    /**
     * Search communications
     */
    public function searchCommunications(string $query): Collection
    {
        return $this->getCommunicationService()->searchCommunications($query);
    }

    /**
     * Search communications and return DTOs
     */
    public function searchCommunicationsDTO(string $query): Collection
    {
        return $this->getCommunicationService()->searchCommunicationsDTO($query);
    }

    /**
     * Get urgent communications
     */
    public function getUrgentCommunications(int $limit = 10): Collection
    {
        return $this->getCommunicationService()->getUrgentCommunications($limit);
    }

    /**
     * Get unread communications
     */
    public function getUnreadCommunications(int $limit = 10): Collection
    {
        return $this->getCommunicationService()->getUnreadCommunications($limit);
    }

    /**
     * Get unreplied communications
     */
    public function getUnrepliedCommunications(int $limit = 10): Collection
    {
        return $this->getCommunicationService()->getUnrepliedCommunications($limit);
    }

    /**
     * Get conversation between provider and user
     */
    public function getConversation(int $providerId, int $userId, int $limit = 50): Collection
    {
        return $this->getCommunicationService()->getConversation($providerId, $userId, $limit);
    }

    /**
     * Get communication thread
     */
    public function getThread(string $threadId): Collection
    {
        return $this->getCommunicationService()->getThread($threadId);
    }

    /**
     * Get communication count for provider
     */
    public function getCommunicationCount(int $providerId): int
    {
        return $this->getCommunicationService()->getCommunicationCount($providerId);
    }

    /**
     * Get unread count for provider
     */
    public function getUnreadCount(int $providerId): int
    {
        return $this->getCommunicationService()->getUnreadCount($providerId);
    }

    /**
     * Get urgent count for provider
     */
    public function getUrgentCount(int $providerId): int
    {
        return $this->getCommunicationService()->getUrgentCount($providerId);
    }

    /**
     * Send communication to provider
     */
    public function sendCommunicationToProvider(int $providerId, array $data): ProviderCommunication
    {
        $data['provider_id'] = $providerId;
        $data['direction'] = 'outbound';
        $data['status'] = 'sent';

        return $this->createCommunication($data);
    }

    /**
     * Send communication to user
     */
    public function sendCommunicationToUser(int $userId, array $data): ProviderCommunication
    {
        $data['user_id'] = $userId;
        $data['direction'] = 'outbound';
        $data['status'] = 'sent';

        return $this->createCommunication($data);
    }

    /**
     * Reply to existing communication
     */
    public function replyToCommunication(ProviderCommunication $parentCommunication, array $data): ProviderCommunication
    {
        $data['parent_id'] = $parentCommunication->id;
        $data['thread_id'] = $parentCommunication->thread_id;
        $data['provider_id'] = $parentCommunication->provider_id;
        $data['user_id'] = $parentCommunication->user_id;
        $data['direction'] = 'outbound';
        $data['status'] = 'sent';

        return $this->createCommunication($data);
    }

    /**
     * Bulk create communications
     */
    public function bulkCreateCommunications(array $communicationsData): Collection
    {
        $createdCommunications = collect();

        foreach ($communicationsData as $data) {
            try {
                $communication = $this->createCommunication($data);
                $createdCommunications->push($communication);
            } catch (Exception $e) {
                // Log error and continue with next communication
                // In production, you might want to handle this differently
                continue;
            }
        }

        return $createdCommunications;
    }

    /**
     * Bulk update communications
     */
    public function bulkUpdateCommunications(array $communicationsData): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($communicationsData as $id => $data) {
            try {
                $communication = $this->findCommunication($id);
                if ($communication) {
                    $this->updateCommunication($communication, $data);
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Communication with ID {$id} not found";
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to update communication {$id}: ".$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Bulk delete communications
     */
    public function bulkDeleteCommunications(array $communicationIds): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($communicationIds as $id) {
            try {
                $communication = $this->findCommunication($id);
                if ($communication) {
                    $this->deleteCommunication($communication);
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Communication with ID {$id} not found";
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to delete communication {$id}: ".$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get communication service instance
     */
    protected function getCommunicationService(): ProviderCommunicationService
    {
        if (! $this->communicationService) {
            $this->communicationService = app(ProviderCommunicationService::class);
        }

        return $this->communicationService;
    }

    /**
     * Set communication service instance
     */
    public function setCommunicationService(ProviderCommunicationService $service): void
    {
        $this->communicationService = $service;
    }
}
