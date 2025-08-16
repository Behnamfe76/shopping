<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasCarrierIntegration
{
    /**
     * Get available carriers
     */
    public function getAvailableCarriers(): array
    {
        return [
            'fedex' => 'FedEx',
            'ups' => 'UPS',
            'usps' => 'USPS',
            'dhl' => 'DHL',
            'amazon' => 'Amazon',
            'other' => 'Other',
        ];
    }

    /**
     * Get carrier rates
     */
    public function getCarrierRates(string $carrier, array $packageData): array
    {
        return $this->repository->getCarrierRates($carrier, $packageData);
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost(array $packageData): float
    {
        return $this->repository->calculateShippingCost($packageData);
    }

    /**
     * Create shipping label
     */
    public function createShippingLabel(object $item): string
    {
        $this->validateShippingLabelCreation($item);

        $label = $this->repository->createShippingLabel($item);

        $this->fireShippingLabelCreatedEvent($item, $label);

        return $label;
    }

    /**
     * Void shipping label
     */
    public function voidShippingLabel(object $item): bool
    {
        $this->validateShippingLabelVoid($item);

        $result = $this->repository->voidShippingLabel($item);

        if ($result) {
            $this->fireShippingLabelVoidedEvent($item);
        }

        return $result;
    }

    /**
     * Get shipping label
     */
    public function getShippingLabel(object $item): string
    {
        return $this->repository->getShippingLabel($item);
    }

    /**
     * Get return label
     */
    public function getReturnLabel(object $item): string
    {
        return $this->repository->getReturnLabel($item);
    }

    /**
     * Schedule pickup
     */
    public function schedulePickup(object $item, array $pickupData = []): bool
    {
        $this->validatePickupScheduling($item, $pickupData);

        $result = $this->repository->schedulePickup($item);

        if ($result) {
            $this->firePickupScheduledEvent($item, $pickupData);
        }

        return $result;
    }

    /**
     * Cancel pickup
     */
    public function cancelPickup(object $item): bool
    {
        $this->validatePickupCancellation($item);

        $result = $this->repository->cancelPickup($item);

        if ($result) {
            $this->firePickupCancelledEvent($item);
        }

        return $result;
    }

    /**
     * Get pickup confirmation
     */
    public function getPickupConfirmation(object $item): array
    {
        return $this->repository->getPickupConfirmation($item);
    }

    /**
     * Get carrier performance
     */
    public function getCarrierPerformance(): array
    {
        return $this->repository->getCarrierPerformance();
    }

    /**
     * Get shipments by carrier
     */
    public function getShipmentsByCarrier(string $carrier): Collection
    {
        return $this->repository->findByCarrier($carrier);
    }

    /**
     * Get shipment count by carrier
     */
    public function getShipmentCountByCarrier(string $carrier): int
    {
        return $this->repository->getShipmentCountByCarrier($carrier);
    }

    /**
     * Get carriers list
     */
    public function getCarriers(): Collection
    {
        return $this->repository->getCarriers();
    }

    /**
     * Validate carrier
     */
    public function validateCarrier(string $carrier): bool
    {
        $availableCarriers = array_keys($this->getAvailableCarriers());
        return in_array($carrier, $availableCarriers);
    }

    /**
     * Get carrier label
     */
    public function getCarrierLabel(string $carrier): string
    {
        $carriers = $this->getAvailableCarriers();
        return $carriers[$carrier] ?? $carrier;
    }

    /**
     * Get carrier API status
     */
    public function getCarrierApiStatus(string $carrier): array
    {
        // Mock carrier API status
        return [
            'carrier' => $carrier,
            'status' => 'active',
            'last_check' => now()->toDateTimeString(),
            'response_time' => 0.5, // seconds
            'error_rate' => 0.1, // percentage
        ];
    }

    /**
     * Test carrier connection
     */
    public function testCarrierConnection(string $carrier): bool
    {
        // Mock carrier connection test
        return true;
    }

    /**
     * Get carrier capabilities
     */
    public function getCarrierCapabilities(string $carrier): array
    {
        $capabilities = [
            'fedex' => [
                'ground' => true,
                'express' => true,
                'overnight' => true,
                'international' => true,
                'pickup' => true,
                'returns' => true,
            ],
            'ups' => [
                'ground' => true,
                'next_day_air' => true,
                'second_day_air' => true,
                'international' => true,
                'pickup' => true,
                'returns' => true,
            ],
            'usps' => [
                'priority' => true,
                'first_class' => true,
                'media_mail' => true,
                'international' => true,
                'pickup' => false,
                'returns' => true,
            ],
            'dhl' => [
                'express' => true,
                'ground' => true,
                'international' => true,
                'pickup' => true,
                'returns' => true,
            ],
            'amazon' => [
                'prime' => true,
                'standard' => true,
                'international' => false,
                'pickup' => false,
                'returns' => true,
            ],
        ];

        return $capabilities[$carrier] ?? [];
    }

    /**
     * Check if carrier supports service
     */
    public function carrierSupportsService(string $carrier, string $service): bool
    {
        $capabilities = $this->getCarrierCapabilities($carrier);
        return $capabilities[$service] ?? false;
    }

    /**
     * Get carrier service options
     */
    public function getCarrierServiceOptions(string $carrier): array
    {
        $capabilities = $this->getCarrierCapabilities($carrier);
        return array_keys(array_filter($capabilities));
    }

    /**
     * Validate shipping label creation
     */
    protected function validateShippingLabelCreation(object $item): void
    {
        $validator = Validator::make([
            'carrier' => $item->carrier,
            'tracking_number' => $item->tracking_number,
            'status' => $item->status,
        ], [
            'carrier' => 'required|string',
            'tracking_number' => 'nullable|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate shipping label void
     */
    protected function validateShippingLabelVoid(object $item): void
    {
        $validator = Validator::make([
            'status' => $item->status,
        ], [
            'status' => 'required|string|in:pending,in_transit',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate pickup scheduling
     */
    protected function validatePickupScheduling(object $item, array $pickupData): void
    {
        $validator = Validator::make($pickupData, [
            'pickup_date' => 'required|date|after:today',
            'pickup_time' => 'required|string',
            'pickup_location' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate pickup cancellation
     */
    protected function validatePickupCancellation(object $item): void
    {
        // Add validation logic for pickup cancellation
        if ($item->status === 'delivered') {
            throw new ValidationException('Cannot cancel pickup for delivered shipment');
        }
    }

    /**
     * Fire shipping label created event
     */
    protected function fireShippingLabelCreatedEvent(object $item, string $label): void
    {
        // This would fire a shipping label created event
        // event(new ShippingLabelCreated($item, $label));
    }

    /**
     * Fire shipping label voided event
     */
    protected function fireShippingLabelVoidedEvent(object $item): void
    {
        // This would fire a shipping label voided event
        // event(new ShippingLabelVoided($item));
    }

    /**
     * Fire pickup scheduled event
     */
    protected function firePickupScheduledEvent(object $item, array $pickupData): void
    {
        // This would fire a pickup scheduled event
        // event(new PickupScheduled($item, $pickupData));
    }

    /**
     * Fire pickup cancelled event
     */
    protected function firePickupCancelledEvent(object $item): void
    {
        // This would fire a pickup cancelled event
        // event(new PickupCancelled($item));
    }

    /**
     * Get carrier analytics
     */
    public function getCarrierAnalytics(string $carrier = null): array
    {
        $performance = $this->getCarrierPerformance();

        if ($carrier) {
            return $performance[$carrier] ?? [];
        }

        return $performance;
    }

    /**
     * Get carrier recommendations
     */
    public function getCarrierRecommendations(array $packageData): array
    {
        $recommendations = [];
        $carriers = $this->getAvailableCarriers();

        foreach ($carriers as $carrierKey => $carrierName) {
            if ($carrierKey === 'other') continue;

            $rates = $this->getCarrierRates($carrierKey, $packageData);
            $capabilities = $this->getCarrierCapabilities($carrierKey);
            $performance = $this->getCarrierAnalytics($carrierKey);

            $recommendations[] = [
                'carrier' => $carrierKey,
                'carrier_name' => $carrierName,
                'rates' => $rates,
                'capabilities' => $capabilities,
                'performance' => $performance,
                'recommendation_score' => $this->calculateRecommendationScore($carrierKey, $rates, $performance),
            ];
        }

        // Sort by recommendation score
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });

        return $recommendations;
    }

    /**
     * Calculate recommendation score
     */
    protected function calculateRecommendationScore(string $carrier, array $rates, array $performance): float
    {
        $score = 0;

        // Rate-based scoring
        if (!empty($rates)) {
            $lowestRate = min(array_values($rates));
            $score += (1 / $lowestRate) * 10; // Lower rates get higher scores
        }

        // Performance-based scoring
        if (!empty($performance)) {
            $onTimePercentage = $performance['delivery_performance']['on_time_percentage'] ?? 0;
            $score += $onTimePercentage * 0.5; // Higher on-time percentage gets higher scores
        }

        return round($score, 2);
    }
}
