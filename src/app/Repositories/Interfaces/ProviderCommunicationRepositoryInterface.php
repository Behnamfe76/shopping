<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use App\Models\ProviderCommunication;
use App\DTOs\ProviderCommunicationDTO;

interface ProviderCommunicationRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?ProviderCommunication;
    public function findDTO(int $id): ?ProviderCommunicationDTO;

    // Find by specific criteria
    public function findByProviderId(int $providerId): Collection;
    public function findByProviderIdDTO(int $providerId): Collection;
    public function findByUserId(int $userId): Collection;
    public function findByUserIdDTO(int $userId): Collection;
    public function findByCommunicationType(string $communicationType): Collection;
    public function findByCommunicationTypeDTO(string $communicationType): Collection;
    public function findByDirection(string $direction): Collection;
    public function findByDirectionDTO(string $direction): Collection;
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByPriority(string $priority): Collection;
    public function findByPriorityDTO(string $priority): Collection;
    public function findByThreadId(string $threadId): Collection;
    public function findByThreadIdDTO(string $threadId): Collection;
    public function findByParentId(int $parentId): Collection;
    public function findByParentIdDTO(int $parentId): Collection;

    // Special queries
    public function findUrgent(): Collection;
    public function findUrgentDTO(): Collection;
    public function findArchived(): Collection;
    public function findArchivedDTO(): Collection;
    public function findUnread(): Collection;
    public function findUnreadDTO(): Collection;
    public function findUnreplied(): Collection;
    public function findUnrepliedDTO(): Collection;

    // Combined criteria queries
    public function findByProviderAndType(int $providerId, string $communicationType): Collection;
    public function findByProviderAndTypeDTO(int $providerId, string $communicationType): Collection;
    public function findByProviderAndStatus(int $providerId, string $status): Collection;
    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection;
    public function findByProviderAndDirection(int $providerId, string $direction): Collection;
    public function findByProviderAndDirectionDTO(int $providerId, string $direction): Collection;
    public function findByProviderAndPriority(int $providerId, string $priority): Collection;
    public function findByProviderAndPriorityDTO(int $providerId, string $priority): Collection;

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByProviderAndDateRange(int $providerId, string $startDate, string $endDate): Collection;
    public function findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate): Collection;

    // Rating and response time queries
    public function findBySatisfactionRange(float $minRating, float $maxRating): Collection;
    public function findBySatisfactionRangeDTO(float $minRating, float $maxRating): Collection;
    public function findByResponseTimeRange(int $minMinutes, int $maxMinutes): Collection;
    public function findByResponseTimeRangeDTO(int $minMinutes, int $maxMinutes): Collection;

    // Conversation and thread management
    public function findConversation(int $providerId, int $userId, int $limit = 50): Collection;
    public function findConversationDTO(int $providerId, int $userId, int $limit = 50): Collection;
    public function findThread(string $threadId): Collection;
    public function findThreadDTO(string $threadId): Collection;

    // Create and update operations
    public function create(array $data): ProviderCommunication;
    public function createAndReturnDTO(array $data): ProviderCommunicationDTO;
    public function update(ProviderCommunication $providerCommunication, array $data): bool;
    public function updateAndReturnDTO(ProviderCommunication $providerCommunication, array $data): ?ProviderCommunicationDTO;

    // Delete operations
    public function delete(ProviderCommunication $providerCommunication): bool;

    // Status management
    public function markAsRead(ProviderCommunication $providerCommunication): bool;
    public function markAsReplied(ProviderCommunication $providerCommunication): bool;
    public function markAsClosed(ProviderCommunication $providerCommunication): bool;
    public function archive(ProviderCommunication $providerCommunication): bool;
    public function unarchive(ProviderCommunication $providerCommunication): bool;
    public function setUrgent(ProviderCommunication $providerCommunication): bool;
    public function unsetUrgent(ProviderCommunication $providerCommunication): bool;

    // Attachment and tag management
    public function addAttachment(ProviderCommunication $providerCommunication, string $attachmentPath): bool;
    public function removeAttachment(ProviderCommunication $providerCommunication, string $attachmentPath): bool;
    public function addTags(ProviderCommunication $providerCommunication, array $tags): bool;
    public function removeTags(ProviderCommunication $providerCommunication, array $tags): bool;
    public function clearTags(ProviderCommunication $providerCommunication): bool;

    // Rating and metrics
    public function updateSatisfactionRating(ProviderCommunication $providerCommunication, float $rating): bool;
    public function calculateResponseTime(ProviderCommunication $providerCommunication): bool;

    // Count operations
    public function getCommunicationCount(int $providerId): int;
    public function getCommunicationCountByType(int $providerId, string $communicationType): int;
    public function getCommunicationCountByStatus(int $providerId, string $status): int;
    public function getCommunicationCountByDirection(int $providerId, string $direction): int;
    public function getCommunicationCountByPriority(int $providerId, string $priority): int;
    public function getUnreadCount(int $providerId): int;
    public function getUnrepliedCount(int $providerId): int;
    public function getUrgentCount(int $providerId): int;
    public function getArchivedCount(int $providerId): int;

    // Global count operations
    public function getTotalCommunicationCount(): int;
    public function getTotalCommunicationCountByType(string $communicationType): int;
    public function getTotalCommunicationCountByStatus(string $status): int;
    public function getTotalCommunicationCountByDirection(string $direction): int;
    public function getTotalCommunicationCountByPriority(string $priority): int;
    public function getTotalUnreadCount(): int;
    public function getTotalUnrepliedCount(): int;
    public function getTotalUrgentCount(): int;
    public function getTotalArchivedCount(): int;

    // Average calculations
    public function getAverageResponseTime(int $providerId): float;
    public function getAverageResponseTimeByType(int $providerId, string $communicationType): float;
    public function getAverageResponseTimeByPriority(int $providerId, string $priority): float;
    public function getAverageSatisfactionRating(int $providerId): float;
    public function getAverageSatisfactionRatingByType(int $providerId, string $communicationType): float;
    public function getAverageSatisfactionRatingByPriority(int $providerId, string $priority): float;

    // Global average calculations
    public function getTotalAverageResponseTime(): float;
    public function getTotalAverageResponseTimeByType(string $communicationType): float;
    public function getTotalAverageResponseTimeByPriority(string $priority): float;
    public function getTotalAverageSatisfactionRating(): float;
    public function getTotalAverageSatisfactionRatingByType(string $communicationType): float;
    public function getTotalAverageSatisfactionRatingByPriority(string $priority): float;

    // Recent communications
    public function getRecentCommunications(int $limit = 10): Collection;
    public function getRecentCommunicationsDTO(int $limit = 10): Collection;
    public function getRecentCommunicationsByProvider(int $providerId, int $limit = 10): Collection;
    public function getRecentCommunicationsByProviderDTO(int $providerId, int $limit = 10): Collection;

    // Urgent communications
    public function getUrgentCommunications(int $limit = 10): Collection;
    public function getUrgentCommunicationsDTO(int $limit = 10): Collection;
    public function getUrgentCommunicationsByProvider(int $providerId, int $limit = 10): Collection;
    public function getUrgentCommunicationsByProviderDTO(int $providerId, int $limit = 10): Collection;

    // Unreplied communications
    public function getUnrepliedCommunications(int $limit = 10): Collection;
    public function getUnrepliedCommunicationsDTO(int $limit = 10): Collection;
    public function getUnrepliedCommunicationsByProvider(int $providerId, int $limit = 10): Collection;
    public function getUnrepliedCommunicationsByProviderDTO(int $providerId, int $limit = 10): Collection;

    // Search operations
    public function searchCommunications(string $query): Collection;
    public function searchCommunicationsDTO(string $query): Collection;
    public function searchCommunicationsByProvider(int $providerId, string $query): Collection;
    public function searchCommunicationsByProviderDTO(int $providerId, string $query): Collection;

    // Analytics operations
    public function getCommunicationAnalytics(int $providerId): array;
    public function getCommunicationAnalyticsByType(int $providerId, string $communicationType): array;
    public function getCommunicationAnalyticsByStatus(int $providerId, string $status): array;
    public function getCommunicationAnalyticsByDirection(int $providerId, string $direction): array;
    public function getCommunicationAnalyticsByPriority(int $providerId, string $priority): array;

    // Global analytics
    public function getGlobalCommunicationAnalytics(): array;
    public function getGlobalCommunicationAnalyticsByType(string $communicationType): array;
    public function getGlobalCommunicationAnalyticsByStatus(string $status): array;
    public function getGlobalCommunicationAnalyticsByDirection(string $direction): array;
    public function getGlobalCommunicationAnalyticsByPriority(string $priority): array;

    // Distribution and timeline
    public function getCommunicationDistribution(int $providerId): array;
    public function getGlobalCommunicationDistribution(): array;
    public function getCommunicationTimeline(int $providerId): array;
    public function getGlobalCommunicationTimeline(): array;

    // Heatmap and visualization
    public function getCommunicationHeatmap(int $providerId): array;
    public function getGlobalCommunicationHeatmap(): array;
}
