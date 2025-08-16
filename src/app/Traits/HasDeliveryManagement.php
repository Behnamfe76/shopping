<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasDeliveryManagement
{
    /**
     * Update delivery date
     */
    public function updateDeliveryDate(object $item, string $deliveryDate): bool
    {
        $this->validateDeliveryDate($deliveryDate, $item);

        $result = $this->repository->updateDeliveryDate($item, $deliveryDate);

        if ($result) {
            $this->fireDeliveryDateUpdatedEvent($item, $deliveryDate);
        }

        return $result;
    }

    /**
     * Confirm delivery
     */
    public function confirmDelivery(object $item, string $deliveryDate = null): bool
    {
        $data = ['status' => 'delivered'];

        if ($deliveryDate) {
            $data['actual_delivery'] = $deliveryDate;
        } else {
            $data['actual_delivery'] = now();
        }

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireDeliveryConfirmedEvent($item, $data['actual_delivery']);
        }

        return $result;
    }

    /**
     * Get delivery performance
     */
    public function getDeliveryPerformance(string $carrier = null): array
    {
        return $this->repository->getDeliveryPerformance($carrier);
    }

    /**
     * Get delivery times
     */
    public function getDeliveryTimes(string $carrier = null): array
    {
        return $this->repository->getDeliveryTimes($carrier);
    }

    /**
     * Get overdue deliveries
     */
    public function getOverdueDeliveries(): Collection
    {
        return $this->repository->findOverdue();
    }

    /**
     * Get delayed deliveries
     */
    public function getDelayedDeliveries(): Collection
    {
        return $this->repository->findDelayed();
    }

    /**
     * Get on-time deliveries
     */
    public function getOnTimeDeliveries(): Collection
    {
        return $this->repository->findOnTime();
    }

    /**
     * Calculate delivery time
     */
    public function calculateDeliveryTime(object $item): ?int
    {
        if (!$item->shipped_at || !$item->actual_delivery) {
            return null;
        }

        return $item->shipped_at->diffInDays($item->actual_delivery);
    }

    /**
     * Calculate estimated delivery time
     */
    public function calculateEstimatedDeliveryTime(object $item): ?int
    {
        if (!$item->shipped_at || !$item->estimated_delivery) {
            return null;
        }

        return $item->shipped_at->diffInDays($item->estimated_delivery);
    }

    /**
     * Check if delivery is overdue
     */
    public function isDeliveryOverdue(object $item): bool
    {
        if (!$item->estimated_delivery || $item->status === 'delivered') {
            return false;
        }

        return now()->isAfter($item->estimated_delivery);
    }

    /**
     * Check if delivery is delayed
     */
    public function isDeliveryDelayed(object $item): bool
    {
        if (!$item->estimated_delivery || $item->status === 'delivered') {
            return false;
        }

        return now()->isAfter($item->estimated_delivery) && $item->status === 'in_transit';
    }

    /**
     * Check if delivery is on time
     */
    public function isDeliveryOnTime(object $item): bool
    {
        if (!$item->estimated_delivery || $item->status === 'delivered') {
            return false;
        }

        return now()->isBefore($item->estimated_delivery) ||
               ($item->actual_delivery && $item->actual_delivery->lte($item->estimated_delivery));
    }

    /**
     * Get delivery optimization suggestions
     */
    public function getDeliveryOptimizationSuggestions(object $item): array
    {
        $suggestions = [];

        if ($this->isDeliveryOverdue($item)) {
            $suggestions[] = [
                'type' => 'overdue',
                'message' => 'Consider expedited shipping for future orders',
                'priority' => 'high',
            ];
        }

        if ($item->shipping_cost > 50) {
            $suggestions[] = [
                'type' => 'cost_optimization',
                'message' => 'Consider bulk shipping or different carrier for cost savings',
                'priority' => 'medium',
            ];
        }

        if ($item->weight > 10) {
            $suggestions[] = [
                'type' => 'weight_optimization',
                'message' => 'Consider splitting large shipments for better delivery times',
                'priority' => 'low',
            ];
        }

        return $suggestions;
    }

    /**
     * Estimate delivery date
     */
    public function estimateDeliveryDate(string $carrier, string $origin, string $destination): string
    {
        return $this->repository->estimateDeliveryDate($carrier, $origin, $destination);
    }

    /**
     * Get delivery analytics
     */
    public function getDeliveryAnalytics(int $itemId): array
    {
        return $this->repository->getShipmentAnalytics($itemId);
    }

    /**
     * Get delivery trends
     */
    public function getDeliveryTrends(string $period = 'month'): array
    {
        $trends = $this->repository->getShipmentTrends($period);

        return [
            'period' => $period,
            'total_deliveries' => $trends['total_shipments'],
            'average_delivery_time' => $this->calculateAverageDeliveryTime($period),
            'on_time_percentage' => $this->calculateOnTimePercentage($period),
            'delivery_cost_trends' => $this->getDeliveryCostTrends($period),
        ];
    }

    /**
     * Validate delivery date
     */
    protected function validateDeliveryDate(string $deliveryDate, object $item): void
    {
        $validator = Validator::make([
            'delivery_date' => $deliveryDate,
        ], [
            'delivery_date' => 'required|date|after:shipped_at',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Fire delivery date updated event
     */
    protected function fireDeliveryDateUpdatedEvent(object $item, string $deliveryDate): void
    {
        // This would fire a delivery date updated event
        // event(new DeliveryDateUpdated($item, $deliveryDate));
    }

    /**
     * Fire delivery confirmed event
     */
    protected function fireDeliveryConfirmedEvent(object $item, $actualDelivery): void
    {
        // This would fire a delivery confirmed event
        // event(new DeliveryConfirmed($item, $actualDelivery));
    }

    /**
     * Calculate average delivery time
     */
    protected function calculateAverageDeliveryTime(string $period): float
    {
        // Mock calculation
        return 3.5;
    }

    /**
     * Calculate on-time percentage
     */
    protected function calculateOnTimePercentage(string $period): float
    {
        // Mock calculation
        return 92.5;
    }

    /**
     * Get delivery cost trends
     */
    protected function getDeliveryCostTrends(string $period): array
    {
        // Mock delivery cost trends
        return [
            'average_cost' => 15.50,
            'cost_trend' => 'increasing',
            'cost_change_percentage' => 5.2,
        ];
    }

    /**
     * Get delivery alerts
     */
    public function getDeliveryAlerts(object $item): array
    {
        $alerts = [];

        if ($this->isDeliveryOverdue($item)) {
            $alerts[] = [
                'type' => 'overdue',
                'message' => 'Delivery is overdue',
                'severity' => 'high',
                'days_overdue' => now()->diffInDays($item->estimated_delivery),
            ];
        }

        if ($this->isDeliveryDelayed($item)) {
            $alerts[] = [
                'type' => 'delayed',
                'message' => 'Delivery is delayed',
                'severity' => 'medium',
                'days_delayed' => now()->diffInDays($item->estimated_delivery),
            ];
        }

        if ($item->estimated_delivery && now()->diffInDays($item->estimated_delivery) <= 1) {
            $alerts[] = [
                'type' => 'upcoming',
                'message' => 'Delivery expected soon',
                'severity' => 'low',
            ];
        }

        return $alerts;
    }

    /**
     * Get delivery status summary
     */
    public function getDeliveryStatusSummary(): array
    {
        return [
            'total_deliveries' => $this->repository->getShipmentCount(),
            'overdue_deliveries' => $this->getOverdueDeliveries()->count(),
            'delayed_deliveries' => $this->getDelayedDeliveries()->count(),
            'on_time_deliveries' => $this->getOnTimeDeliveries()->count(),
            'delivered_today' => $this->getDeliveredToday()->count(),
        ];
    }

    /**
     * Get deliveries delivered today
     */
    public function getDeliveredToday(): Collection
    {
        return $this->repository->findByDeliveryDate(now()->toDateString());
    }
}
