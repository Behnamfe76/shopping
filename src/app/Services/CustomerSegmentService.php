<?php

namespace App\Services;

use App\DTOs\CustomerSegmentDTO;
use App\Enums\SegmentPriority;
use App\Enums\SegmentStatus;
use App\Enums\SegmentType;
use App\Events\CustomerSegment\CustomerSegmentActivated;
use App\Events\CustomerSegment\CustomerSegmentCalculated;
use App\Events\CustomerSegment\CustomerSegmentCreated;
use App\Events\CustomerSegment\CustomerSegmentDeactivated;
use App\Events\CustomerSegment\CustomerSegmentDeleted;
use App\Events\CustomerSegment\CustomerSegmentRecalculated;
use App\Events\CustomerSegment\CustomerSegmentUpdated;
use App\Models\CustomerSegment;
use App\Repositories\Interfaces\CustomerSegmentRepositoryInterface;
use App\Traits\HasCrudOperations;
use App\Traits\HasCustomerSegmentCalculation;
use App\Traits\HasCustomerSegmentOperations;
use App\Traits\HasCustomerSegmentStatusManagement;
use App\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CustomerSegmentService
{
    use HasCrudOperations,
        HasSearchOperations,
        HasCustomerSegmentOperations,
        HasCustomerSegmentStatusManagement,
        HasCustomerSegmentCalculation;

    protected CustomerSegmentRepositoryInterface $repository;

    public function __construct(CustomerSegmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new customer segment
     */
    public function createSegment(array $data): CustomerSegmentDTO
    {
        try {
            // Validate segment data
            $this->validateSegmentData($data);

            // Set default values
            $data = $this->setDefaultValues($data);

            // Create the segment
            $segment = $this->repository->create($data);

            // Calculate customers if automatic
            if ($segment->is_automatic) {
                $this->repository->calculateCustomers($segment);
            }

            // Dispatch event
            event(new CustomerSegmentCreated($segment));

            return CustomerSegmentDTO::fromModel($segment);
        } catch (\Exception $e) {
            Log::error('Error creating customer segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing customer segment
     */
    public function updateSegment(CustomerSegment $segment, array $data): ?CustomerSegmentDTO
    {
        try {
            // Validate segment data
            $this->validateSegmentData($data, $segment);

            // Update the segment
            $updated = $this->repository->update($segment, $data);

            if ($updated) {
                $updatedSegment = $segment->fresh();

                // Recalculate if criteria changed
                if (isset($data['criteria']) || isset($data['conditions'])) {
                    $this->repository->calculateCustomers($updatedSegment);
                }

                // Dispatch event
                event(new CustomerSegmentUpdated($updatedSegment));

                return CustomerSegmentDTO::fromModel($updatedSegment);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error updating customer segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a customer segment
     */
    public function deleteSegment(CustomerSegment $segment): bool
    {
        try {
            $deleted = $this->repository->delete($segment);

            if ($deleted) {
                // Dispatch event
                event(new CustomerSegmentDeleted($segment));
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Error deleting customer segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Activate a customer segment
     */
    public function activateSegment(CustomerSegment $segment): bool
    {
        try {
            $activated = $this->repository->activate($segment);

            if ($activated) {
                // Dispatch event
                event(new CustomerSegmentActivated($segment));
            }

            return $activated;
        } catch (\Exception $e) {
            Log::error('Error activating customer segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deactivate a customer segment
     */
    public function deactivateSegment(CustomerSegment $segment): bool
    {
        try {
            $deactivated = $this->repository->deactivate($segment);

            if ($deactivated) {
                // Dispatch event
                event(new CustomerSegmentDeactivated($segment));
            }

            return $deactivated;
        } catch (\Exception $e) {
            Log::error('Error deactivating customer segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate customers for a segment
     */
    public function calculateSegmentCustomers(CustomerSegment $segment): int
    {
        try {
            $count = $this->repository->calculateCustomers($segment);

            // Dispatch event
            event(new CustomerSegmentCalculated($segment, $count));

            return $count;
        } catch (\Exception $e) {
            Log::error('Error calculating customers for segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Recalculate all automatic segments
     */
    public function recalculateAllSegments(): bool
    {
        try {
            $success = $this->repository->recalculateAllSegments();

            if ($success) {
                // Dispatch event
                event(new CustomerSegmentRecalculated());
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Error recalculating all segments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add customer to segment
     */
    public function addCustomerToSegment(CustomerSegment $segment, int $customerId): bool
    {
        try {
            return $this->repository->addCustomer($segment, $customerId);
        } catch (\Exception $e) {
            Log::error('Error adding customer to segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove customer from segment
     */
    public function removeCustomerFromSegment(CustomerSegment $segment, int $customerId): bool
    {
        try {
            return $this->repository->removeCustomer($segment, $customerId);
        } catch (\Exception $e) {
            Log::error('Error removing customer from segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update segment criteria
     */
    public function updateSegmentCriteria(CustomerSegment $segment, array $criteria): bool
    {
        try {
            if (!$this->repository->validateCriteria($criteria)) {
                throw new \InvalidArgumentException('Invalid criteria format');
            }

            $updated = $this->repository->updateCriteria($segment, $criteria);

            if ($updated && $segment->is_automatic) {
                $this->repository->calculateCustomers($segment);
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error('Error updating segment criteria: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update segment conditions
     */
    public function updateSegmentConditions(CustomerSegment $segment, array $conditions): bool
    {
        try {
            if (!$this->repository->validateConditions($conditions)) {
                throw new \InvalidArgumentException('Invalid conditions format');
            }

            $updated = $this->repository->updateConditions($segment, $conditions);

            if ($updated && $segment->is_automatic) {
                $this->repository->calculateCustomers($segment);
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error('Error updating segment conditions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment analytics
     */
    public function getSegmentAnalytics(int $segmentId): array
    {
        try {
            $segment = $this->repository->find($segmentId);
            
            if (!$segment) {
                return [];
            }

            return [
                'segment' => CustomerSegmentDTO::fromModel($segment),
                'stats' => [
                    'customer_count' => $segment->customer_count,
                    'last_calculated' => $segment->last_calculated_at,
                    'is_automatic' => $segment->is_automatic,
                    'is_dynamic' => $segment->is_dynamic,
                ],
                'performance' => $this->getSegmentPerformance($segment),
                'growth_trends' => $this->getSegmentGrowthTrends($segment),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting segment analytics: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment comparison
     */
    public function compareSegments(int $segmentId1, int $segmentId2): array
    {
        try {
            return $this->repository->getSegmentComparison($segmentId1, $segmentId2);
        } catch (\Exception $e) {
            Log::error('Error comparing segments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment forecast
     */
    public function getSegmentForecast(int $segmentId): array
    {
        try {
            return $this->repository->getSegmentForecast($segmentId);
        } catch (\Exception $e) {
            Log::error('Error getting segment forecast: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Export segment data
     */
    public function exportSegment(CustomerSegment $segment): array
    {
        try {
            return $this->repository->exportSegment($segment);
        } catch (\Exception $e) {
            Log::error('Error exporting segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Import segment data
     */
    public function importSegment(array $data): CustomerSegment
    {
        try {
            $segment = $this->repository->importSegment($data);
            
            // Dispatch event
            event(new CustomerSegmentCreated($segment));
            
            return $segment;
        } catch (\Exception $e) {
            Log::error('Error importing segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Duplicate segment
     */
    public function duplicateSegment(CustomerSegment $segment, string $newName): CustomerSegment
    {
        try {
            $newSegment = $this->repository->duplicateSegment($segment, $newName);
            
            // Dispatch event
            event(new CustomerSegmentCreated($newSegment));
            
            return $newSegment;
        } catch (\Exception $e) {
            Log::error('Error duplicating segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Merge segments
     */
    public function mergeSegments(array $segmentIds, string $newName): CustomerSegment
    {
        try {
            $mergedSegment = $this->repository->mergeSegments($segmentIds, $newName);
            
            // Dispatch event
            event(new CustomerSegmentCreated($mergedSegment));
            
            return $mergedSegment;
        } catch (\Exception $e) {
            Log::error('Error merging segments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Split segment
     */
    public function splitSegment(CustomerSegment $segment, array $criteria): array
    {
        try {
            $newSegments = $this->repository->splitSegment($segment, $criteria);
            
            // Dispatch events for new segments
            foreach ($newSegments as $newSegment) {
                event(new CustomerSegmentCreated($newSegment));
            }
            
            return $newSegments;
        } catch (\Exception $e) {
            Log::error('Error splitting segment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment recommendations
     */
    public function getSegmentRecommendations(): array
    {
        try {
            return $this->repository->generateRecommendations();
        } catch (\Exception $e) {
            Log::error('Error getting segment recommendations: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment insights
     */
    public function getSegmentInsights(): array
    {
        try {
            return $this->repository->calculateInsights();
        } catch (\Exception $e) {
            Log::error('Error getting segment insights: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segment trends forecast
     */
    public function getSegmentTrendsForecast(string $period = 'monthly'): array
    {
        try {
            return $this->repository->forecastTrends($period);
        } catch (\Exception $e) {
            Log::error('Error getting segment trends forecast: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get segments needing recalculation
     */
    public function getSegmentsNeedingRecalculation(): Collection
    {
        return $this->repository->getSegmentsNeedingRecalculation();
    }

    /**
     * Get overlapping segments
     */
    public function getOverlappingSegments(CustomerSegment $segment): Collection
    {
        return $this->repository->getOverlappingSegments($segment);
    }

    /**
     * Get segment statistics
     */
    public function getSegmentStatistics(): array
    {
        return [
            'overview' => $this->repository->getSegmentStats(),
            'by_type' => $this->repository->getSegmentStatsByType(),
            'by_status' => $this->repository->getSegmentStatsByStatus(),
            'by_priority' => $this->repository->getSegmentStatsByPriority(),
            'performance' => $this->repository->getSegmentPerformanceStats(),
        ];
    }

    /**
     * Validate segment data
     */
    private function validateSegmentData(array $data, ?CustomerSegment $segment = null): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_column(SegmentType::cases(), 'value')),
            'status' => 'nullable|string|in:' . implode(',', array_column(SegmentStatus::cases(), 'value')),
            'priority' => 'nullable|string|in:' . implode(',', array_column(SegmentPriority::cases(), 'value')),
            'criteria' => 'nullable|array',
            'conditions' => 'nullable|array',
            'is_automatic' => 'boolean',
            'is_dynamic' => 'boolean',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
        ];

        // Add unique name validation if creating new segment
        if (!$segment) {
            $rules['name'] .= '|unique:customer_segments,name';
        } else {
            $rules['name'] .= '|unique:customer_segments,name,' . $segment->id;
        }

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    /**
     * Set default values for segment data
     */
    private function setDefaultValues(array $data): array
    {
        return array_merge([
            'status' => SegmentStatus::ACTIVE,
            'priority' => SegmentPriority::NORMAL,
            'is_automatic' => false,
            'is_dynamic' => false,
            'customer_count' => 0,
            'criteria' => [],
            'conditions' => [],
            'metadata' => [],
            'tags' => [],
        ], $data);
    }

    /**
     * Get segment performance metrics
     */
    private function getSegmentPerformance(CustomerSegment $segment): array
    {
        $totalSegments = $this->repository->getSegmentCount();
        $averageCustomers = $this->repository->getAverageCustomerCount();

        return [
            'customer_count_percentile' => $this->calculatePercentile($segment->customer_count, $averageCustomers),
            'performance_rating' => $this->getPerformanceRating($segment->customer_count),
            'growth_potential' => $this->assessGrowthPotential($segment),
        ];
    }

    /**
     * Get segment growth trends
     */
    private function getSegmentGrowthTrends(CustomerSegment $segment): array
    {
        return [
            'creation_date' => $segment->created_at,
            'last_calculated' => $segment->last_calculated_at,
            'days_since_creation' => $segment->created_at->diffInDays(now()),
            'days_since_last_calculation' => $segment->last_calculated_at ? $segment->last_calculated_at->diffInDays(now()) : null,
        ];
    }

    /**
     * Calculate percentile
     */
    private function calculatePercentile(float $value, float $average): float
    {
        if ($average == 0) return 0;
        return min(100, max(0, ($value / $average) * 100));
    }

    /**
     * Get performance rating
     */
    private function getPerformanceRating(int $customerCount): string
    {
        if ($customerCount > 1000) return 'high';
        if ($customerCount > 100) return 'medium';
        return 'low';
    }

    /**
     * Assess growth potential
     */
    private function assessGrowthPotential(CustomerSegment $segment): string
    {
        if ($segment->is_dynamic && $segment->is_automatic) return 'high';
        if ($segment->is_dynamic || $segment->is_automatic) return 'medium';
        return 'low';
    }
}
