<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\CustomerCommunicationDTO;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface CustomerCommunicationRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?CustomerCommunication;

    public function findDTO(int $id): ?CustomerCommunicationDTO;

    public function create(array $data): CustomerCommunication;

    public function createAndReturnDTO(array $data): CustomerCommunicationDTO;

    public function update(CustomerCommunication $communication, array $data): bool;

    public function updateAndReturnDTO(CustomerCommunication $communication, array $data): ?CustomerCommunicationDTO;

    public function delete(CustomerCommunication $communication): bool;

    // Find by Relationships
    public function findByCustomerId(int $customerId): Collection;

    public function findByCustomerIdDTO(int $customerId): Collection;

    public function findByUserId(int $userId): Collection;

    public function findByUserIdDTO(int $userId): Collection;

    public function findByCampaignId(int $campaignId): Collection;

    public function findByCampaignIdDTO(int $campaignId): Collection;

    public function findBySegmentId(int $segmentId): Collection;

    public function findBySegmentIdDTO(int $segmentId): Collection;

    public function findByTemplateId(int $templateId): Collection;

    public function findByTemplateIdDTO(int $templateId): Collection;

    // Find by Communication Properties
    public function findByType(string $type): Collection;

    public function findByTypeDTO(string $type): Collection;

    public function findByStatus(string $status): Collection;

    public function findByStatusDTO(string $status): Collection;

    public function findByPriority(string $priority): Collection;

    public function findByPriorityDTO(string $priority): Collection;

    public function findByChannel(string $channel): Collection;

    public function findByChannelDTO(string $channel): Collection;

    // Find by Status
    public function findScheduled(): Collection;

    public function findScheduledDTO(): Collection;

    public function findSent(): Collection;

    public function findSentDTO(): Collection;

    public function findDelivered(): Collection;

    public function findDeliveredDTO(): Collection;

    public function findOpened(): Collection;

    public function findOpenedDTO(): Collection;

    public function findClicked(): Collection;

    public function findClickedDTO(): Collection;

    public function findBounced(): Collection;

    public function findBouncedDTO(): Collection;

    public function findUnsubscribed(): Collection;

    public function findUnsubscribedDTO(): Collection;

    // Find by Date Ranges
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    public function findByScheduledDateRange(string $startDate, string $endDate): Collection;

    public function findByScheduledDateRangeDTO(string $startDate, string $endDate): Collection;

    // Status Management
    public function schedule(CustomerCommunication $communication, string $scheduledAt): bool;

    public function send(CustomerCommunication $communication): bool;

    public function markAsDelivered(CustomerCommunication $communication): bool;

    public function markAsOpened(CustomerCommunication $communication): bool;

    public function markAsClicked(CustomerCommunication $communication): bool;

    public function markAsBounced(CustomerCommunication $communication): bool;

    public function markAsUnsubscribed(CustomerCommunication $communication): bool;

    public function cancel(CustomerCommunication $communication): bool;

    public function reschedule(CustomerCommunication $communication, string $newScheduledAt): bool;

    // Count Operations
    public function getCommunicationCount(): int;

    public function getCommunicationCountByCustomer(int $customerId): int;

    public function getCommunicationCountByType(string $type): int;

    public function getCommunicationCountByStatus(string $status): int;

    public function getCommunicationCountByChannel(string $channel): int;

    public function getCommunicationCountByCampaign(int $campaignId): int;

    public function getCommunicationCountBySegment(int $segmentId): int;

    public function getScheduledCount(): int;

    public function getSentCount(): int;

    public function getDeliveredCount(): int;

    public function getOpenedCount(): int;

    public function getClickedCount(): int;

    public function getBouncedCount(): int;

    public function getUnsubscribedCount(): int;

    // Rate Calculations
    public function getDeliveryRate(): float;

    public function getDeliveryRateByCampaign(int $campaignId): float;

    public function getDeliveryRateBySegment(int $segmentId): float;

    public function getOpenRate(): float;

    public function getOpenRateByCampaign(int $campaignId): float;

    public function getOpenRateBySegment(int $segmentId): float;

    public function getClickRate(): float;

    public function getClickRateByCampaign(int $campaignId): float;

    public function getClickRateBySegment(int $segmentId): float;

    public function getBounceRate(): float;

    public function getBounceRateByCampaign(int $campaignId): float;

    public function getBounceRateBySegment(int $segmentId): float;

    public function getUnsubscribeRate(): float;

    public function getUnsubscribeRateByCampaign(int $campaignId): float;

    public function getUnsubscribeRateBySegment(int $segmentId): float;

    // Search Operations
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByCustomer(int $customerId, string $query): Collection;

    public function searchByCustomerDTO(int $customerId, string $query): Collection;

    public function searchByCampaign(int $campaignId, string $query): Collection;

    public function searchByCampaignDTO(int $campaignId, string $query): Collection;

    // Recent Communications
    public function getRecentCommunications(int $limit = 10): Collection;

    public function getRecentCommunicationsDTO(int $limit = 10): Collection;

    public function getRecentCommunicationsByCustomer(int $customerId, int $limit = 10): Collection;

    public function getRecentCommunicationsByCustomerDTO(int $customerId, int $limit = 10): Collection;

    // Customer-specific Communications
    public function getCommunicationsByType(int $customerId, string $type, int $limit = 10): Collection;

    public function getCommunicationsByTypeDTO(int $customerId, string $type, int $limit = 10): Collection;

    public function getCommunicationsByStatus(int $customerId, string $status, int $limit = 10): Collection;

    public function getCommunicationsByStatusDTO(int $customerId, string $status, int $limit = 10): Collection;

    public function getCommunicationsByChannel(int $customerId, string $channel, int $limit = 10): Collection;

    public function getCommunicationsByChannelDTO(int $customerId, string $channel, int $limit = 10): Collection;

    public function getScheduledCommunications(int $customerId): Collection;

    public function getScheduledCommunicationsDTO(int $customerId): Collection;

    public function getUpcomingCommunications(int $customerId, int $daysAhead = 7): Collection;

    public function getUpcomingCommunicationsDTO(int $customerId, int $daysAhead = 7): Collection;

    // Validation and Stats
    public function validateCommunication(array $data): bool;

    public function getCommunicationStats(): array;

    public function getCommunicationStatsByCustomer(int $customerId): array;

    public function getCommunicationStatsByType(): array;

    public function getCommunicationStatsByStatus(): array;

    public function getCommunicationStatsByChannel(): array;

    public function getCommunicationStatsByCampaign(int $campaignId): array;

    public function getCommunicationStatsBySegment(int $segmentId): array;

    // Growth Analytics
    public function getCommunicationGrowthStats(string $period = 'monthly'): array;

    public function getCommunicationGrowthStatsByCustomer(int $customerId, string $period = 'monthly'): array;

    // Performance Analytics
    public function getCommunicationPerformanceStats(): array;

    public function getCommunicationPerformanceStatsByCampaign(int $campaignId): array;

    public function getCommunicationPerformanceStatsBySegment(int $segmentId): array;

    // Engagement Analytics
    public function getCommunicationEngagementStats(): array;

    public function getCommunicationEngagementStatsByCustomer(int $customerId): array;

    // Customer Communication History
    public function getCustomerCommunicationHistory(int $customerId): Collection;

    public function getCustomerCommunicationHistoryDTO(int $customerId): Collection;

    public function getCustomerCommunicationSummary(int $customerId): array;

    public function getCustomerCommunicationSummaryDTO(int $customerId): array;

    // Import/Export
    public function exportCustomerCommunications(int $customerId): array;

    public function importCustomerCommunications(int $customerId, array $communications): bool;

    // Analytics
    public function getCommunicationAnalytics(int $customerId): array;

    public function getCommunicationAnalyticsByType(string $type): array;

    public function getCommunicationAnalyticsByDateRange(string $startDate, string $endDate): array;

    // Recommendations and Insights
    public function getCommunicationRecommendations(int $customerId): array;

    public function getCommunicationInsights(int $customerId): array;

    public function getCommunicationTrends(int $customerId, string $period = 'monthly'): array;

    public function getCommunicationComparison(int $customerId1, int $customerId2): array;

    public function getCommunicationForecast(int $customerId): array;

    // Attachment Management
    public function addAttachment(CustomerCommunication $communication, $file): bool;

    public function removeAttachment(CustomerCommunication $communication, int $mediaId): bool;

    public function getAttachments(CustomerCommunication $communication): Collection;

    // Tracking Data Management
    public function updateTrackingData(CustomerCommunication $communication, array $trackingData): bool;

    public function getTrackingData(CustomerCommunication $communication): array;
}
