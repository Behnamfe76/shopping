<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\app\Models\CustomerSegment;
use Fereydooni\Shopping\app\DTOs\CustomerSegmentDTO;
use Fereydooni\Shopping\app\Enums\SegmentType;
use Fereydooni\Shopping\app\Enums\SegmentStatus;
use Fereydooni\Shopping\app\Enums\SegmentPriority;

trait HasCustomerSegmentOperations
{
    // Segment-specific find methods
    public function findByName(string $name): ?CustomerSegment
    {
        return $this->model::where('name', $name)->first();
    }

    public function findByNameDTO(string $name): ?CustomerSegmentDTO
    {
        $segment = $this->findByName($name);
        return $segment ? $this->dtoClass::fromModel($segment) : null;
    }

    public function findByType(SegmentType $type): Collection
    {
        return $this->model::byType($type)->get();
    }

    public function findByTypeDTO(SegmentType $type): Collection
    {
        $segments = $this->findByType($type);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findByStatus(SegmentStatus $status): Collection
    {
        return $this->model::where('status', $status)->get();
    }

    public function findByStatusDTO(SegmentStatus $status): Collection
    {
        $segments = $this->findByStatus($status);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findByPriority(SegmentPriority $priority): Collection
    {
        return $this->model::byPriority($priority)->get();
    }

    public function findByPriorityDTO(SegmentPriority $priority): Collection
    {
        $segments = $this->findByPriority($priority);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findAutomatic(): Collection
    {
        return $this->model::automatic()->get();
    }

    public function findAutomaticDTO(): Collection
    {
        $segments = $this->findAutomatic();
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findManual(): Collection
    {
        return $this->model::manual()->get();
    }

    public function findManualDTO(): Collection
    {
        $segments = $this->findManual();
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findDynamic(): Collection
    {
        return $this->model::dynamic()->get();
    }

    public function findDynamicDTO(): Collection
    {
        $segments = $this->findDynamic();
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findStatic(): Collection
    {
        return $this->model::static()->get();
    }

    public function findStaticDTO(): Collection
    {
        $segments = $this->findStatic();
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findByCustomerCount(int $minCount, int $maxCount = null): Collection
    {
        return $this->model::byCustomerCount($minCount, $maxCount)->get();
    }

    public function findByCustomerCountDTO(int $minCount, int $maxCount = null): Collection
    {
        $segments = $this->findByCustomerCount($minCount, $maxCount);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findByLastCalculatedDate(string $startDate, string $endDate): Collection
    {
        return $this->model::whereBetween('last_calculated_at', [$startDate, $endDate])->get();
    }

    public function findByLastCalculatedDateDTO(string $startDate, string $endDate): Collection
    {
        $segments = $this->findByLastCalculatedDate($startDate, $endDate);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findNeedsRecalculation(int $daysAgo = 7): Collection
    {
        return $this->model::needsRecalculation($daysAgo)->get();
    }

    public function findNeedsRecalculationDTO(int $daysAgo = 7): Collection
    {
        $segments = $this->findNeedsRecalculation($daysAgo);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function findByTag(string $tag): Collection
    {
        return $this->model::withTag($tag)->get();
    }

    public function findByTagDTO(string $tag): Collection
    {
        $segments = $this->findByTag($tag);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    // Segment analytics and statistics
    public function getSegmentCount(): int
    {
        return $this->model::count();
    }

    public function getSegmentCountByType(SegmentType $type): int
    {
        return $this->model::byType($type)->count();
    }

    public function getSegmentCountByStatus(SegmentStatus $status): int
    {
        return $this->model::where('status', $status)->count();
    }

    public function getSegmentCountByPriority(SegmentPriority $priority): int
    {
        return $this->model::byPriority($priority)->count();
    }

    public function getAutomaticSegmentCount(): int
    {
        return $this->model::automatic()->count();
    }

    public function getManualSegmentCount(): int
    {
        return $this->model::manual()->count();
    }

    public function getDynamicSegmentCount(): int
    {
        return $this->model::dynamic()->count();
    }

    public function getStaticSegmentCount(): int
    {
        return $this->model::static()->count();
    }

    public function getTotalCustomerCount(): int
    {
        return $this->model::sum('customer_count');
    }

    public function getAverageCustomerCount(): float
    {
        return $this->model::avg('customer_count') ?? 0.0;
    }

    public function getSegmentStats(): array
    {
        return [
            'total_segments' => $this->getSegmentCount(),
            'active_segments' => $this->getSegmentCountByStatus(SegmentStatus::ACTIVE),
            'draft_segments' => $this->getSegmentCountByStatus(SegmentStatus::DRAFT),
            'archived_segments' => $this->getSegmentCountByStatus(SegmentStatus::ARCHIVED),
            'automatic_segments' => $this->getAutomaticSegmentCount(),
            'manual_segments' => $this->getManualSegmentCount(),
            'dynamic_segments' => $this->getDynamicSegmentCount(),
            'static_segments' => $this->getStaticSegmentCount(),
            'total_customers' => $this->getTotalCustomerCount(),
            'average_customers_per_segment' => $this->getAverageCustomerCount(),
        ];
    }

    public function getSegmentStatsByType(): array
    {
        $stats = [];
        foreach (SegmentType::cases() as $type) {
            $stats[$type->value] = [
                'count' => $this->getSegmentCountByType($type),
                'label' => $type->label(),
                'color' => $type->color(),
            ];
        }
        return $stats;
    }

    public function getSegmentStatsByStatus(): array
    {
        $stats = [];
        foreach (SegmentStatus::cases() as $status) {
            $stats[$status->value] = [
                'count' => $this->getSegmentCountByStatus($status),
                'label' => $status->label(),
                'color' => $status->color(),
            ];
        }
        return $stats;
    }

    public function getSegmentStatsByPriority(): array
    {
        $stats = [];
        foreach (SegmentPriority::cases() as $priority) {
            $stats[$priority->value] = [
                'count' => $this->getSegmentCountByPriority($priority),
                'label' => $priority->label(),
                'color' => $priority->color(),
            ];
        }
        return $stats;
    }

    // Recent and trending segments
    public function getRecentSegments(int $limit = 10): Collection
    {
        return $this->model::latest()->limit($limit)->get();
    }

    public function getRecentSegmentsDTO(int $limit = 10): Collection
    {
        $segments = $this->getRecentSegments($limit);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function getSegmentsByCustomerCount(int $minCount, int $maxCount): Collection
    {
        return $this->model::byCustomerCount($minCount, $maxCount)->get();
    }

    public function getSegmentsByCustomerCountDTO(int $minCount, int $maxCount): Collection
    {
        $segments = $this->getSegmentsByCustomerCount($minCount, $maxCount);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    public function getSegmentsByLastCalculated(int $daysAgo): Collection
    {
        return $this->model::where('last_calculated_at', '>=', now()->subDays($daysAgo))->get();
    }

    public function getSegmentsByLastCalculatedDTO(int $daysAgo): Collection
    {
        $segments = $this->getSegmentsByLastCalculated($daysAgo);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    // Overlap detection
    public function getOverlappingSegments(CustomerSegment $segment): Collection
    {
        // This is a simplified overlap detection
        // In a real implementation, you would compare criteria and conditions
        return $this->model::where('id', '!=', $segment->id)
            ->where('type', $segment->type)
            ->where('status', SegmentStatus::ACTIVE)
            ->get();
    }

    public function getOverlappingSegmentsDTO(CustomerSegment $segment): Collection
    {
        $segments = $this->getOverlappingSegments($segment);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    // Search by criteria
    public function searchByCriteria(array $criteria): Collection
    {
        $query = $this->model::query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    public function searchByCriteriaDTO(array $criteria): Collection
    {
        $segments = $this->searchByCriteria($criteria);
        return $segments->map(fn($segment) => $this->dtoClass::fromModel($segment));
    }

    // Utility methods
    protected function validateSegmentCriteria(array $criteria): bool
    {
        // Basic validation - in a real implementation, you would have more complex validation
        return is_array($criteria) && !empty($criteria);
    }

    protected function validateSegmentConditions(array $conditions): bool
    {
        // Basic validation - in a real implementation, you would have more complex validation
        return is_array($conditions);
    }

    protected function calculateSegmentOverlap(CustomerSegment $segment1, CustomerSegment $segment2): float
    {
        // Simplified overlap calculation
        // In a real implementation, you would compare actual customer lists
        $customers1 = $segment1->customers->pluck('id')->toArray();
        $customers2 = $segment2->customers->pluck('id')->toArray();
        
        $intersection = array_intersect($customers1, $customers2);
        $union = array_unique(array_merge($customers1, $customers2));
        
        return count($union) > 0 ? count($intersection) / count($union) : 0.0;
    }
}
