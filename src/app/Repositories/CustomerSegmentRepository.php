<?php

namespace App\Repositories;

use App\DTOs\CustomerSegmentDTO;
use App\Enums\SegmentPriority;
use App\Enums\SegmentStatus;
use App\Enums\SegmentType;
use App\Models\CustomerSegment;
use App\Repositories\Interfaces\CustomerSegmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerSegmentRepository implements CustomerSegmentRepositoryInterface
{
    public function all(): Collection
    {
        return CustomerSegment::with(['customers', 'calculatedBy'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CustomerSegment::with(['customers', 'calculatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return CustomerSegment::with(['customers', 'calculatedBy'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return CustomerSegment::with(['customers', 'calculatedBy'])
            ->orderBy('id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?CustomerSegment
    {
        return CustomerSegment::with(['customers', 'calculatedBy'])->find($id);
    }

    public function findDTO(int $id): ?CustomerSegmentDTO
    {
        $segment = $this->find($id);
        return $segment ? CustomerSegmentDTO::fromModel($segment) : null;
    }

    public function findByName(string $name): ?CustomerSegment
    {
        return CustomerSegment::where('name', $name)->first();
    }

    public function findByNameDTO(string $name): ?CustomerSegmentDTO
    {
        $segment = $this->findByName($name);
        return $segment ? CustomerSegmentDTO::fromModel($segment) : null;
    }

    public function findByType(string $type): Collection
    {
        return CustomerSegment::where('type', $type)->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        return CustomerSegment::where('type', $type)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findByStatus(string $status): Collection
    {
        return CustomerSegment::where('status', $status)->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return CustomerSegment::where('status', $status)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findByPriority(string $priority): Collection
    {
        return CustomerSegment::where('priority', $priority)->get();
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return CustomerSegment::where('priority', $priority)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findAutomatic(): Collection
    {
        return CustomerSegment::where('is_automatic', true)->get();
    }

    public function findAutomaticDTO(): Collection
    {
        return CustomerSegment::where('is_automatic', true)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findManual(): Collection
    {
        return CustomerSegment::where('is_automatic', false)->get();
    }

    public function findManualDTO(): Collection
    {
        return CustomerSegment::where('is_automatic', false)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findDynamic(): Collection
    {
        return CustomerSegment::where('is_dynamic', true)->get();
    }

    public function findDynamicDTO(): Collection
    {
        return CustomerSegment::where('is_dynamic', true)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findStatic(): Collection
    {
        return CustomerSegment::where('is_dynamic', false)->get();
    }

    public function findStaticDTO(): Collection
    {
        return CustomerSegment::where('is_dynamic', false)
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findByCustomerCount(int $minCount, int $maxCount): Collection
    {
        return CustomerSegment::whereBetween('customer_count', [$minCount, $maxCount])->get();
    }

    public function findByCustomerCountDTO(int $minCount, int $maxCount): Collection
    {
        return CustomerSegment::whereBetween('customer_count', [$minCount, $maxCount])
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function findByLastCalculatedDate(string $startDate, string $endDate): Collection
    {
        return CustomerSegment::whereBetween('last_calculated_at', [$startDate, $endDate])->get();
    }

    public function findByLastCalculatedDateDTO(string $startDate, string $endDate): Collection
    {
        return CustomerSegment::whereBetween('last_calculated_at', [$startDate, $endDate])
            ->get()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function create(array $data): CustomerSegment
    {
        return CustomerSegment::create($data);
    }

    public function createAndReturnDTO(array $data): CustomerSegmentDTO
    {
        $segment = $this->create($data);
        return CustomerSegmentDTO::fromModel($segment);
    }

    public function update(CustomerSegment $segment, array $data): bool
    {
        return $segment->update($data);
    }

    public function updateAndReturnDTO(CustomerSegment $segment, array $data): ?CustomerSegmentDTO
    {
        $updated = $this->update($segment, $data);
        return $updated ? CustomerSegmentDTO::fromModel($segment->fresh()) : null;
    }

    public function delete(CustomerSegment $segment): bool
    {
        return $segment->delete();
    }

    public function activate(CustomerSegment $segment): bool
    {
        return $segment->update(['status' => SegmentStatus::ACTIVE]);
    }

    public function deactivate(CustomerSegment $segment): bool
    {
        return $segment->update(['status' => SegmentStatus::INACTIVE]);
    }

    public function makeAutomatic(CustomerSegment $segment): bool
    {
        return $segment->update(['is_automatic' => true]);
    }

    public function makeManual(CustomerSegment $segment): bool
    {
        return $segment->update(['is_automatic' => false]);
    }

    public function makeDynamic(CustomerSegment $segment): bool
    {
        return $segment->update(['is_dynamic' => true]);
    }

    public function makeStatic(CustomerSegment $segment): bool
    {
        return $segment->update(['is_dynamic' => false]);
    }

    public function setPriority(CustomerSegment $segment, string $priority): bool
    {
        return $segment->update(['priority' => $priority]);
    }

    public function calculateCustomers(CustomerSegment $segment): int
    {
        try {
            $criteria = $segment->criteria;
            $conditions = $segment->conditions;
            
            // Build query based on criteria and conditions
            $query = DB::table('customers');
            
            // Apply criteria filters
            if (!empty($criteria)) {
                foreach ($criteria as $criterion) {
                    $this->applyCriterion($query, $criterion);
                }
            }
            
            // Apply conditions
            if (!empty($conditions)) {
                foreach ($conditions as $condition) {
                    $this->applyCondition($query, $condition);
                }
            }
            
            $count = $query->count();
            
            // Update segment with new count
            $segment->update([
                'customer_count' => $count,
                'last_calculated_at' => now(),
                'calculated_by' => auth()->id()
            ]);
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Error calculating customers for segment: ' . $e->getMessage());
            return 0;
        }
    }

    public function recalculateAllSegments(): bool
    {
        try {
            $segments = CustomerSegment::where('is_automatic', true)->get();
            
            foreach ($segments as $segment) {
                $this->calculateCustomers($segment);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error recalculating all segments: ' . $e->getMessage());
            return false;
        }
    }

    public function addCustomer(CustomerSegment $segment, int $customerId): bool
    {
        try {
            $segment->customers()->attach($customerId);
            $this->updateCustomerCount($segment);
            return true;
        } catch (\Exception $e) {
            Log::error('Error adding customer to segment: ' . $e->getMessage());
            return false;
        }
    }

    public function removeCustomer(CustomerSegment $segment, int $customerId): bool
    {
        try {
            $segment->customers()->detach($customerId);
            $this->updateCustomerCount($segment);
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing customer from segment: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCriteria(CustomerSegment $segment, array $criteria): bool
    {
        return $segment->update(['criteria' => $criteria]);
    }

    public function updateConditions(CustomerSegment $segment, array $conditions): bool
    {
        return $segment->update(['conditions' => $conditions]);
    }

    public function validateCriteria(array $criteria): bool
    {
        // Basic validation for criteria structure
        foreach ($criteria as $criterion) {
            if (!isset($criterion['field']) || !isset($criterion['operator'])) {
                return false;
            }
        }
        return true;
    }

    public function validateConditions(array $conditions): bool
    {
        // Basic validation for conditions structure
        foreach ($conditions as $condition) {
            if (!isset($condition['type']) || !isset($condition['value'])) {
                return false;
            }
        }
        return true;
    }

    public function getSegmentCount(): int
    {
        return CustomerSegment::count();
    }

    public function getSegmentCountByType(string $type): int
    {
        return CustomerSegment::where('type', $type)->count();
    }

    public function getSegmentCountByStatus(string $status): int
    {
        return CustomerSegment::where('status', $status)->count();
    }

    public function getSegmentCountByPriority(string $priority): int
    {
        return CustomerSegment::where('priority', $priority)->count();
    }

    public function getAutomaticSegmentCount(): int
    {
        return CustomerSegment::where('is_automatic', true)->count();
    }

    public function getManualSegmentCount(): int
    {
        return CustomerSegment::where('is_automatic', false)->count();
    }

    public function getDynamicSegmentCount(): int
    {
        return CustomerSegment::where('is_dynamic', true)->count();
    }

    public function getStaticSegmentCount(): int
    {
        return CustomerSegment::where('is_dynamic', false)->count();
    }

    public function getTotalCustomerCount(): int
    {
        return CustomerSegment::sum('customer_count');
    }

    public function getAverageCustomerCount(): float
    {
        return CustomerSegment::avg('customer_count') ?? 0;
    }

    public function search(string $query): Collection
    {
        return CustomerSegment::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function searchByCriteria(array $criteria): Collection
    {
        $query = CustomerSegment::query();
        
        foreach ($criteria as $criterion) {
            if (isset($criterion['field']) && isset($criterion['value'])) {
                $query->where($criterion['field'], $criterion['value']);
            }
        }
        
        return $query->get();
    }

    public function searchByCriteriaDTO(array $criteria): Collection
    {
        return $this->searchByCriteria($criteria)
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function getRecentSegments(int $limit = 10): Collection
    {
        return CustomerSegment::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function getRecentSegmentsDTO(int $limit = 10): Collection
    {
        return $this->getRecentSegments($limit)
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function getSegmentsByCustomerCount(int $minCount, int $maxCount): Collection
    {
        return $this->findByCustomerCount($minCount, $maxCount);
    }

    public function getSegmentsByCustomerCountDTO(int $minCount, int $maxCount): Collection
    {
        return $this->findByCustomerCountDTO($minCount, $maxCount);
    }

    public function getSegmentsByLastCalculated(int $daysAgo): Collection
    {
        $date = now()->subDays($daysAgo);
        return CustomerSegment::where('last_calculated_at', '<=', $date)->get();
    }

    public function getSegmentsByLastCalculatedDTO(int $daysAgo): Collection
    {
        return $this->getSegmentsByLastCalculated($daysAgo)
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function getSegmentsNeedingRecalculation(): Collection
    {
        $date = now()->subDays(7); // Recalculate segments older than 7 days
        return CustomerSegment::where('is_automatic', true)
            ->where(function($query) use ($date) {
                $query->whereNull('last_calculated_at')
                      ->orWhere('last_calculated_at', '<=', $date);
            })
            ->get();
    }

    public function getSegmentsNeedingRecalculationDTO(): Collection
    {
        return $this->getSegmentsNeedingRecalculation()
            ->map(fn($segment) => CustomerSegmentDTO::fromModel($segment));
    }

    public function getOverlappingSegments(CustomerSegment $segment): Collection
    {
        // Find segments that might overlap based on criteria
        return CustomerSegment::where('id', '!=', $segment->id)
            ->where('type', $segment->type)
            ->get()
            ->filter(function($otherSegment) use ($segment) {
                return $this->hasOverlap($segment, $otherSegment);
            });
    }

    public function getOverlappingSegmentsDTO(CustomerSegment $segment): Collection
    {
        return $this->getOverlappingSegments($segment)
            ->map(fn($otherSegment) => CustomerSegmentDTO::fromModel($otherSegment));
    }

    public function getSegmentStats(): array
    {
        return [
            'total_segments' => $this->getSegmentCount(),
            'active_segments' => $this->getSegmentCountByStatus(SegmentStatus::ACTIVE),
            'inactive_segments' => $this->getSegmentCountByStatus(SegmentStatus::INACTIVE),
            'automatic_segments' => $this->getAutomaticSegmentCount(),
            'manual_segments' => $this->getManualSegmentCount(),
            'total_customers' => $this->getTotalCustomerCount(),
            'average_customers' => $this->getAverageCustomerCount(),
        ];
    }

    public function getSegmentStatsByType(): array
    {
        $stats = [];
        foreach (SegmentType::cases() as $type) {
            $stats[$type->value] = $this->getSegmentCountByType($type->value);
        }
        return $stats;
    }

    public function getSegmentStatsByStatus(): array
    {
        $stats = [];
        foreach (SegmentStatus::cases() as $status) {
            $stats[$status->value] = $this->getSegmentCountByStatus($status->value);
        }
        return $stats;
    }

    public function getSegmentStatsByPriority(): array
    {
        $stats = [];
        foreach (SegmentPriority::cases() as $priority) {
            $stats[$priority->value] = $this->getSegmentCountByPriority($priority->value);
        }
        return $stats;
    }

    public function getSegmentGrowthStats(string $period = 'monthly'): array
    {
        $query = CustomerSegment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date');
            
        if ($period === 'weekly') {
            $query->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
                  ->groupBy('week');
        } elseif ($period === 'monthly') {
            $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                  ->groupBy('month');
        }
        
        return $query->get()->toArray();
    }

    public function getSegmentPerformanceStats(): array
    {
        return [
            'high_performing' => CustomerSegment::where('customer_count', '>', 1000)->count(),
            'medium_performing' => CustomerSegment::whereBetween('customer_count', [100, 1000])->count(),
            'low_performing' => CustomerSegment::where('customer_count', '<', 100)->count(),
        ];
    }

    public function getSegmentPerformanceStatsByType(string $type): array
    {
        return [
            'high_performing' => CustomerSegment::where('type', $type)->where('customer_count', '>', 1000)->count(),
            'medium_performing' => CustomerSegment::where('type', $type)->whereBetween('customer_count', [100, 1000])->count(),
            'low_performing' => CustomerSegment::where('type', $type)->where('customer_count', '<', 100)->count(),
        ];
    }

    public function getSegmentInsights(): array
    {
        return [
            'most_popular_type' => $this->getMostPopularSegmentType(),
            'fastest_growing_segments' => $this->getFastestGrowingSegments(),
            'segments_needing_attention' => $this->getSegmentsNeedingRecalculation()->count(),
        ];
    }

    public function getSegmentInsightsByType(string $type): array
    {
        return [
            'total_segments' => $this->getSegmentCountByType($type),
            'total_customers' => CustomerSegment::where('type', $type)->sum('customer_count'),
            'average_customers' => CustomerSegment::where('type', $type)->avg('customer_count'),
            'performance_stats' => $this->getSegmentPerformanceStatsByType($type),
        ];
    }

    public function getSegmentTrends(string $period = 'monthly'): array
    {
        return $this->getSegmentGrowthStats($period);
    }

    public function getSegmentComparison(int $segmentId1, int $segmentId2): array
    {
        $segment1 = $this->find($segmentId1);
        $segment2 = $this->find($segmentId2);
        
        if (!$segment1 || !$segment2) {
            return [];
        }
        
        return [
            'segment1' => CustomerSegmentDTO::fromModel($segment1),
            'segment2' => CustomerSegmentDTO::fromModel($segment2),
            'comparison' => [
                'customer_count_diff' => $segment1->customer_count - $segment2->customer_count,
                'creation_date_diff' => $segment1->created_at->diffInDays($segment2->created_at),
                'last_calculated_diff' => $segment1->last_calculated_at?->diffInDays($segment2->last_calculated_at),
            ]
        ];
    }

    public function getSegmentForecast(int $segmentId): array
    {
        $segment = $this->find($segmentId);
        if (!$segment) {
            return [];
        }
        
        // Simple forecasting based on historical data
        $growthRate = $this->calculateGrowthRate($segment);
        
        return [
            'current_customers' => $segment->customer_count,
            'projected_growth_rate' => $growthRate,
            'projected_customers_30_days' => $segment->customer_count * (1 + $growthRate),
            'projected_customers_90_days' => $segment->customer_count * pow(1 + $growthRate, 3),
        ];
    }

    public function exportSegment(CustomerSegment $segment): array
    {
        return [
            'segment' => CustomerSegmentDTO::fromModel($segment),
            'customers' => $segment->customers->pluck('id')->toArray(),
            'exported_at' => now()->toISOString(),
        ];
    }

    public function importSegment(array $data): CustomerSegment
    {
        $segmentData = $data['segment'] ?? $data;
        unset($segmentData['id']); // Ensure we create a new segment
        
        $segment = $this->create($segmentData);
        
        // Import customers if provided
        if (isset($data['customers']) && is_array($data['customers'])) {
            $segment->customers()->attach($data['customers']);
            $this->updateCustomerCount($segment);
        }
        
        return $segment;
    }

    public function duplicateSegment(CustomerSegment $segment, string $newName): CustomerSegment
    {
        $newSegment = $segment->replicate();
        $newSegment->name = $newName;
        $newSegment->save();
        
        // Copy customers
        $customerIds = $segment->customers->pluck('id')->toArray();
        $newSegment->customers()->attach($customerIds);
        
        return $newSegment;
    }

    public function mergeSegments(array $segmentIds, string $newName): CustomerSegment
    {
        $segments = CustomerSegment::whereIn('id', $segmentIds)->get();
        
        // Create new merged segment
        $mergedSegment = $this->create([
            'name' => $newName,
            'description' => 'Merged segment from: ' . $segments->pluck('name')->implode(', '),
            'type' => $segments->first()->type,
            'status' => SegmentStatus::ACTIVE,
            'priority' => SegmentPriority::NORMAL,
            'is_automatic' => false,
            'is_dynamic' => false,
        ]);
        
        // Merge all customers
        $allCustomerIds = [];
        foreach ($segments as $segment) {
            $allCustomerIds = array_merge($allCustomerIds, $segment->customers->pluck('id')->toArray());
        }
        
        $mergedSegment->customers()->attach(array_unique($allCustomerIds));
        $this->updateCustomerCount($mergedSegment);
        
        return $mergedSegment;
    }

    public function splitSegment(CustomerSegment $segment, array $criteria): array
    {
        $newSegments = [];
        
        foreach ($criteria as $criterion) {
            $newSegment = $this->create([
                'name' => $segment->name . ' - ' . ($criterion['name'] ?? 'Split'),
                'description' => $segment->description . ' (Split by: ' . json_encode($criterion) . ')',
                'type' => $segment->type,
                'status' => SegmentStatus::ACTIVE,
                'priority' => $segment->priority,
                'is_automatic' => false,
                'is_dynamic' => $segment->is_dynamic,
                'criteria' => [$criterion],
            ]);
            
            $newSegments[] = $newSegment;
        }
        
        return $newSegments;
    }

    public function generateRecommendations(): array
    {
        return [
            'segments_to_recalculate' => $this->getSegmentsNeedingRecalculation()->pluck('name')->toArray(),
            'potential_merges' => $this->findPotentialMerges(),
            'performance_optimizations' => $this->getPerformanceOptimizations(),
        ];
    }

    public function calculateInsights(): array
    {
        return [
            'segment_distribution' => $this->getSegmentStatsByType(),
            'performance_metrics' => $this->getSegmentPerformanceStats(),
            'growth_trends' => $this->getSegmentGrowthStats(),
        ];
    }

    public function forecastTrends(string $period = 'monthly'): array
    {
        return [
            'growth_forecast' => $this->getSegmentGrowthStats($period),
            'customer_growth_forecast' => $this->getCustomerGrowthForecast(),
        ];
    }

    // Private helper methods
    private function applyCriterion($query, array $criterion): void
    {
        $field = $criterion['field'];
        $operator = $criterion['operator'];
        $value = $criterion['value'] ?? null;
        
        switch ($operator) {
            case 'equals':
                $query->where($field, $value);
                break;
            case 'not_equals':
                $query->where($field, '!=', $value);
                break;
            case 'contains':
                $query->where($field, 'like', "%{$value}%");
                break;
            case 'greater_than':
                $query->where($field, '>', $value);
                break;
            case 'less_than':
                $query->where($field, '<', $value);
                break;
            case 'between':
                $query->whereBetween($field, $value);
                break;
        }
    }

    private function applyCondition($query, array $condition): void
    {
        $type = $condition['type'];
        $value = $condition['value'];
        
        switch ($type) {
            case 'age_range':
                $query->whereBetween('age', $value);
                break;
            case 'location':
                $query->where('city', $value);
                break;
            case 'purchase_frequency':
                $query->where('total_orders', '>=', $value);
                break;
        }
    }

    private function updateCustomerCount(CustomerSegment $segment): void
    {
        $count = $segment->customers()->count();
        $segment->update(['customer_count' => $count]);
    }

    private function hasOverlap(CustomerSegment $segment1, CustomerSegment $segment2): bool
    {
        // Simple overlap detection based on type and criteria similarity
        if ($segment1->type !== $segment2->type) {
            return false;
        }
        
        // Check if criteria overlap (simplified)
        $criteria1 = $segment1->criteria ?? [];
        $criteria2 = $segment2->criteria ?? [];
        
        foreach ($criteria1 as $c1) {
            foreach ($criteria2 as $c2) {
                if ($c1['field'] === $c2['field'] && $c1['operator'] === $c2['operator']) {
                    return true;
                }
            }
        }
        
        return false;
    }

    private function getMostPopularSegmentType(): string
    {
        $typeCounts = $this->getSegmentStatsByType();
        return array_keys($typeCounts, max($typeCounts))[0] ?? 'demographic';
    }

    private function getFastestGrowingSegments(): array
    {
        return CustomerSegment::orderBy('customer_count', 'desc')
            ->limit(5)
            ->pluck('name')
            ->toArray();
    }

    private function calculateGrowthRate(CustomerSegment $segment): float
    {
        // Simplified growth rate calculation
        return 0.05; // 5% growth rate as default
    }

    private function findPotentialMerges(): array
    {
        $potentialMerges = [];
        $segments = CustomerSegment::all();
        
        foreach ($segments as $segment1) {
            foreach ($segments as $segment2) {
                if ($segment1->id !== $segment2->id && $segment1->type === $segment2->type) {
                    $overlap = $this->hasOverlap($segment1, $segment2);
                    if ($overlap) {
                        $potentialMerges[] = [
                            'segment1' => $segment1->name,
                            'segment2' => $segment2->name,
                            'reason' => 'Similar criteria and type'
                        ];
                    }
                }
            }
        }
        
        return $potentialMerges;
    }

    private function getPerformanceOptimizations(): array
    {
        return [
            'low_performing_segments' => CustomerSegment::where('customer_count', '<', 50)->pluck('name')->toArray(),
            'segments_needing_recalculation' => $this->getSegmentsNeedingRecalculation()->pluck('name')->toArray(),
        ];
    }

    private function getCustomerGrowthForecast(): array
    {
        return [
            'total_customers_30_days' => $this->getTotalCustomerCount() * 1.05,
            'total_customers_90_days' => $this->getTotalCustomerCount() * 1.15,
        ];
    }
}
