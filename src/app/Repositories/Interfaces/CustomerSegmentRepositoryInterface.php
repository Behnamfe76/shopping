<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\CustomerSegmentDTO;
use Fereydooni\Shopping\app\Enums\SegmentPriority;
use Fereydooni\Shopping\app\Enums\SegmentStatus;
use Fereydooni\Shopping\app\Enums\SegmentType;
use Fereydooni\Shopping\app\Models\CustomerSegment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface CustomerSegmentRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?CustomerSegment;

    public function findDTO(int $id): ?CustomerSegmentDTO;

    public function findByName(string $name): ?CustomerSegment;

    public function findByNameDTO(string $name): ?CustomerSegmentDTO;

    public function create(array $data): CustomerSegment;

    public function createAndReturnDTO(array $data): CustomerSegmentDTO;

    public function update(CustomerSegment $segment, array $data): bool;

    public function updateAndReturnDTO(CustomerSegment $segment, array $data): ?CustomerSegmentDTO;

    public function delete(CustomerSegment $segment): bool;

    // Filtering methods
    public function findByType(SegmentType $type): Collection;

    public function findByTypeDTO(SegmentType $type): Collection;

    public function findByStatus(SegmentStatus $status): Collection;

    public function findByStatusDTO(SegmentStatus $status): Collection;

    public function findByPriority(SegmentPriority $priority): Collection;

    public function findByPriorityDTO(SegmentPriority $priority): Collection;

    public function findAutomatic(): Collection;

    public function findAutomaticDTO(): Collection;

    public function findManual(): Collection;

    public function findManualDTO(): Collection;

    public function findDynamic(): Collection;

    public function findDynamicDTO(): Collection;

    public function findStatic(): Collection;

    public function findStaticDTO(): Collection;

    public function findByCustomerCount(int $minCount, ?int $maxCount = null): Collection;

    public function findByCustomerCountDTO(int $minCount, ?int $maxCount = null): Collection;

    public function findByLastCalculatedDate(string $startDate, string $endDate): Collection;

    public function findByLastCalculatedDateDTO(string $startDate, string $endDate): Collection;

    public function findNeedsRecalculation(int $daysAgo = 7): Collection;

    public function findNeedsRecalculationDTO(int $daysAgo = 7): Collection;

    public function findByTag(string $tag): Collection;

    public function findByTagDTO(string $tag): Collection;

    // Status management
    public function activate(CustomerSegment $segment): bool;

    public function deactivate(CustomerSegment $segment): bool;

    public function archive(CustomerSegment $segment): bool;

    public function makeAutomatic(CustomerSegment $segment): bool;

    public function makeManual(CustomerSegment $segment): bool;

    public function makeDynamic(CustomerSegment $segment): bool;

    public function makeStatic(CustomerSegment $segment): bool;

    public function setPriority(CustomerSegment $segment, SegmentPriority $priority): bool;

    // Calculation and customer management
    public function calculateCustomers(CustomerSegment $segment): int;

    public function recalculateAllSegments(): bool;

    public function addCustomer(CustomerSegment $segment, int $customerId, ?int $userId = null): bool;

    public function removeCustomer(CustomerSegment $segment, int $customerId, ?int $userId = null): bool;

    public function updateCriteria(CustomerSegment $segment, array $criteria): bool;

    public function updateConditions(CustomerSegment $segment, array $conditions): bool;

    public function validateCriteria(array $criteria): bool;

    public function validateConditions(array $conditions): bool;

    // Analytics and statistics
    public function getSegmentCount(): int;

    public function getSegmentCountByType(SegmentType $type): int;

    public function getSegmentCountByStatus(SegmentStatus $status): int;

    public function getSegmentCountByPriority(SegmentPriority $priority): int;

    public function getAutomaticSegmentCount(): int;

    public function getManualSegmentCount(): int;

    public function getDynamicSegmentCount(): int;

    public function getStaticSegmentCount(): int;

    public function getTotalCustomerCount(): int;

    public function getAverageCustomerCount(): float;

    public function getSegmentStats(): array;

    public function getSegmentStatsByType(): array;

    public function getSegmentStatsByStatus(): array;

    public function getSegmentStatsByPriority(): array;

    public function getSegmentGrowthStats(string $period = 'monthly'): array;

    public function getSegmentPerformanceStats(): array;

    public function getSegmentPerformanceStatsByType(SegmentType $type): array;

    public function getSegmentInsights(): array;

    public function getSegmentInsightsByType(SegmentType $type): array;

    public function getSegmentTrends(string $period = 'monthly'): array;

    public function getSegmentComparison(int $segmentId1, int $segmentId2): array;

    public function getSegmentForecast(int $segmentId): array;

    // Search functionality
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByCriteria(array $criteria): Collection;

    public function searchByCriteriaDTO(array $criteria): Collection;

    // Recent and trending
    public function getRecentSegments(int $limit = 10): Collection;

    public function getRecentSegmentsDTO(int $limit = 10): Collection;

    public function getSegmentsByCustomerCount(int $minCount, int $maxCount): Collection;

    public function getSegmentsByCustomerCountDTO(int $minCount, int $maxCount): Collection;

    public function getSegmentsByLastCalculated(int $daysAgo): Collection;

    public function getSegmentsByLastCalculatedDTO(int $daysAgo): Collection;

    // Overlap and relationships
    public function getOverlappingSegments(CustomerSegment $segment): Collection;

    public function getOverlappingSegmentsDTO(CustomerSegment $segment): Collection;

    // Import/Export and management
    public function exportSegment(CustomerSegment $segment): array;

    public function importSegment(array $data): CustomerSegment;

    public function duplicateSegment(CustomerSegment $segment, string $newName): CustomerSegment;

    public function mergeSegments(array $segmentIds, string $newName): CustomerSegment;

    public function splitSegment(CustomerSegment $segment, array $criteria): array;

    // Recommendations and insights
    public function generateRecommendations(): array;

    public function calculateInsights(): array;

    public function forecastTrends(string $period = 'monthly'): array;
}
