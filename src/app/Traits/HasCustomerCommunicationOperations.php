<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\CustomerCommunicationDTO;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Illuminate\Database\Eloquent\Collection;

trait HasCustomerCommunicationOperations
{
    protected CustomerCommunication $model;

    protected string $dtoClass = CustomerCommunicationDTO::class;

    // Customer communication-specific find methods
    public function findByCustomerId(int $customerId): Collection
    {
        return $this->repository->findByCustomerId($customerId);
    }

    public function findByCustomerIdDTO(int $customerId): Collection
    {
        return $this->repository->findByCustomerIdDTO($customerId);
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function findByCampaignId(int $campaignId): Collection
    {
        return $this->repository->findByCampaignId($campaignId);
    }

    public function findByCampaignIdDTO(int $campaignId): Collection
    {
        return $this->repository->findByCampaignIdDTO($campaignId);
    }

    public function findBySegmentId(int $segmentId): Collection
    {
        return $this->repository->findBySegmentId($segmentId);
    }

    public function findBySegmentIdDTO(int $segmentId): Collection
    {
        return $this->repository->findBySegmentIdDTO($segmentId);
    }

    public function findByTemplateId(int $templateId): Collection
    {
        return $this->repository->findByTemplateId($templateId);
    }

    public function findByTemplateIdDTO(int $templateId): Collection
    {
        return $this->repository->findByTemplateIdDTO($templateId);
    }

    // Communication type and status filtering
    public function findByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->repository->findByTypeDTO($type);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByPriority(string $priority): Collection
    {
        return $this->repository->findByPriority($priority);
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return $this->repository->findByPriorityDTO($priority);
    }

    public function findByChannel(string $channel): Collection
    {
        return $this->repository->findByChannel($channel);
    }

    public function findByChannelDTO(string $channel): Collection
    {
        return $this->repository->findByChannelDTO($channel);
    }

    // Communication status-specific methods
    public function findScheduled(): Collection
    {
        return $this->repository->findScheduled();
    }

    public function findScheduledDTO(): Collection
    {
        return $this->repository->findScheduledDTO();
    }

    public function findSent(): Collection
    {
        return $this->repository->findSent();
    }

    public function findSentDTO(): Collection
    {
        return $this->repository->findSentDTO();
    }

    public function findDelivered(): Collection
    {
        return $this->repository->findDelivered();
    }

    public function findDeliveredDTO(): Collection
    {
        return $this->repository->findDeliveredDTO();
    }

    public function findOpened(): Collection
    {
        return $this->repository->findOpened();
    }

    public function findOpenedDTO(): Collection
    {
        return $this->repository->findOpenedDTO();
    }

    public function findClicked(): Collection
    {
        return $this->repository->findClicked();
    }

    public function findClickedDTO(): Collection
    {
        return $this->repository->findClickedDTO();
    }

    public function findBounced(): Collection
    {
        return $this->repository->findBounced();
    }

    public function findBouncedDTO(): Collection
    {
        return $this->repository->findBouncedDTO();
    }

    public function findUnsubscribed(): Collection
    {
        return $this->repository->findUnsubscribed();
    }

    public function findUnsubscribedDTO(): Collection
    {
        return $this->repository->findUnsubscribedDTO();
    }

    // Date range filtering
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    public function findByScheduledDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByScheduledDateRange($startDate, $endDate);
    }

    public function findByScheduledDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByScheduledDateRangeDTO($startDate, $endDate);
    }

    // Communication search functionality
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    public function searchByCustomer(int $customerId, string $query): Collection
    {
        return $this->repository->searchByCustomer($customerId, $query);
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        return $this->repository->searchByCustomerDTO($customerId, $query);
    }

    public function searchByCampaign(int $campaignId, string $query): Collection
    {
        return $this->repository->searchByCampaign($campaignId, $query);
    }

    public function searchByCampaignDTO(int $campaignId, string $query): Collection
    {
        return $this->repository->searchByCampaignDTO($campaignId, $query);
    }

    // Recent communications
    public function getRecentCommunications(int $limit = 10): Collection
    {
        return $this->repository->getRecentCommunications($limit);
    }

    public function getRecentCommunicationsDTO(int $limit = 10): Collection
    {
        return $this->repository->getRecentCommunicationsDTO($limit);
    }

    public function getRecentCommunicationsByCustomer(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getRecentCommunicationsByCustomer($customerId, $limit);
    }

    public function getRecentCommunicationsByCustomerDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getRecentCommunicationsByCustomerDTO($customerId, $limit);
    }

    // Customer-specific communications
    public function getCommunicationsByType(int $customerId, string $type, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByType($customerId, $type, $limit);
    }

    public function getCommunicationsByTypeDTO(int $customerId, string $type, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByTypeDTO($customerId, $type, $limit);
    }

    public function getCommunicationsByStatus(int $customerId, string $status, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByStatus($customerId, $status, $limit);
    }

    public function getCommunicationsByStatusDTO(int $customerId, string $status, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByStatusDTO($customerId, $status, $limit);
    }

    public function getCommunicationsByChannel(int $customerId, string $channel, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByChannel($customerId, $channel, $limit);
    }

    public function getCommunicationsByChannelDTO(int $customerId, string $channel, int $limit = 10): Collection
    {
        return $this->repository->getCommunicationsByChannelDTO($customerId, $channel, $limit);
    }

    public function getScheduledCommunications(int $customerId): Collection
    {
        return $this->repository->getScheduledCommunications($customerId);
    }

    public function getScheduledCommunicationsDTO(int $customerId): Collection
    {
        return $this->repository->getScheduledCommunicationsDTO($customerId);
    }

    public function getUpcomingCommunications(int $customerId, int $daysAhead = 7): Collection
    {
        return $this->repository->getUpcomingCommunications($customerId, $daysAhead);
    }

    public function getUpcomingCommunicationsDTO(int $customerId, int $daysAhead = 7): Collection
    {
        return $this->repository->getUpcomingCommunicationsDTO($customerId, $daysAhead);
    }

    // Communication relationships
    public function getCustomerCommunicationHistory(int $customerId): Collection
    {
        return $this->repository->getCustomerCommunicationHistory($customerId);
    }

    public function getCustomerCommunicationHistoryDTO(int $customerId): Collection
    {
        return $this->repository->getCustomerCommunicationHistoryDTO($customerId);
    }

    public function getCustomerCommunicationSummary(int $customerId): array
    {
        return $this->repository->getCustomerCommunicationSummary($customerId);
    }

    public function getCustomerCommunicationSummaryDTO(int $customerId): array
    {
        return $this->repository->getCustomerCommunicationSummaryDTO($customerId);
    }

    // Communication import/export functionality
    public function exportCustomerCommunications(int $customerId): array
    {
        return $this->repository->exportCustomerCommunications($customerId);
    }

    public function importCustomerCommunications(int $customerId, array $communications): bool
    {
        return $this->repository->importCustomerCommunications($customerId, $communications);
    }

    // Utility methods
    public function validateCommunication(array $data): bool
    {
        return $this->repository->validateCommunication($data);
    }

    public function getCommunicationStats(): array
    {
        return $this->repository->getCommunicationStats();
    }

    public function getCommunicationStatsByCustomer(int $customerId): array
    {
        return $this->repository->getCommunicationStatsByCustomer($customerId);
    }

    public function getCommunicationStatsByType(): array
    {
        return $this->repository->getCommunicationStatsByType();
    }

    public function getCommunicationStatsByStatus(): array
    {
        return $this->repository->getCommunicationStatsByStatus();
    }

    public function getCommunicationStatsByChannel(): array
    {
        return $this->repository->getCommunicationStatsByChannel();
    }

    public function getCommunicationStatsByCampaign(int $campaignId): array
    {
        return $this->repository->getCommunicationStatsByCampaign($campaignId);
    }

    public function getCommunicationStatsBySegment(int $segmentId): array
    {
        return $this->repository->getCommunicationStatsBySegment($segmentId);
    }

    public function getCommunicationAnalytics(int $customerId): array
    {
        return $this->repository->getCommunicationAnalytics($customerId);
    }

    public function getCommunicationAnalyticsByType(string $type): array
    {
        return $this->repository->getCommunicationAnalyticsByType($type);
    }

    public function getCommunicationAnalyticsByDateRange(string $startDate, string $endDate): array
    {
        return $this->repository->getCommunicationAnalyticsByDateRange($startDate, $endDate);
    }

    public function getCommunicationRecommendations(int $customerId): array
    {
        return $this->repository->getCommunicationRecommendations($customerId);
    }

    public function getCommunicationInsights(int $customerId): array
    {
        return $this->repository->getCommunicationInsights($customerId);
    }

    public function getCommunicationTrends(int $customerId, string $period = 'monthly'): array
    {
        return $this->repository->getCommunicationTrends($customerId, $period);
    }

    public function getCommunicationComparison(int $customerId1, int $customerId2): array
    {
        return $this->repository->getCommunicationComparison($customerId1, $customerId2);
    }

    public function getCommunicationForecast(int $customerId): array
    {
        return $this->repository->getCommunicationForecast($customerId);
    }
}
