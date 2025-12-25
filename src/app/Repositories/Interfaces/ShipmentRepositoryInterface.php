<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ShipmentDTO;
use Fereydooni\Shopping\app\Models\Shipment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ShipmentRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Shipment;

    public function findDTO(int $id): ?ShipmentDTO;

    public function create(array $data): Shipment;

    public function createAndReturnDTO(array $data): ShipmentDTO;

    public function update(Shipment $shipment, array $data): bool;

    public function delete(Shipment $shipment): bool;

    // Order-specific queries
    public function findByOrderId(int $orderId): Collection;

    public function getShipmentCountByOrderId(int $orderId): int;

    // Carrier-based queries
    public function findByCarrier(string $carrier): Collection;

    public function getShipmentCountByCarrier(string $carrier): int;

    public function getCarriers(): Collection;

    // Status-based queries
    public function findByStatus(string $status): Collection;

    public function getShipmentCountByStatus(string $status): int;

    public function findPending(): Collection;

    public function findInTransit(): Collection;

    public function findDelivered(): Collection;

    public function findReturned(): Collection;

    // Tracking queries
    public function findByTrackingNumber(string $trackingNumber): ?Shipment;

    public function getTrackingNumbers(): Collection;

    // Date-based queries
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDeliveryDate(string $deliveryDate): Collection;

    public function findByShippedDate(string $shippedDate): Collection;

    // Performance queries
    public function findOverdue(): Collection;

    public function findDelayed(): Collection;

    public function findOnTime(): Collection;

    // Search functionality
    public function search(string $query): Collection;

    public function searchByOrderId(int $orderId, string $query): Collection;

    public function searchByCarrier(string $carrier, string $query): Collection;

    // Shipment status management
    public function ship(Shipment $shipment): bool;

    public function deliver(Shipment $shipment): bool;

    public function return(Shipment $shipment, ?string $reason = null): bool;

    public function updateTracking(Shipment $shipment, string $trackingNumber): bool;

    public function updateStatus(Shipment $shipment, string $status): bool;

    public function updateDeliveryDate(Shipment $shipment, string $deliveryDate): bool;

    // Analytics and reporting
    public function getShipmentCount(): int;

    public function getShipmentAnalytics(int $shipmentId): array;

    public function getShipmentAnalyticsByOrder(int $orderId): array;

    public function getShipmentAnalyticsByCarrier(string $carrier): array;

    public function getDeliveryPerformance(?string $carrier = null): array;

    public function getShippingCosts(?string $carrier = null): array;

    public function getDeliveryTimes(?string $carrier = null): array;

    public function getReturnRates(?string $carrier = null): array;

    public function getCarrierPerformance(): array;

    public function getShipmentTrends(string $period = 'month'): array;

    public function getShipmentForecast(string $period = 'month'): array;

    // Carrier integration
    public function validateTrackingNumber(string $trackingNumber, string $carrier): bool;

    public function getTrackingInfo(string $trackingNumber, string $carrier): array;

    public function calculateShippingCost(array $packageData): float;

    public function estimateDeliveryDate(string $carrier, string $origin, string $destination): string;

    public function getCarrierRates(string $carrier, array $packageData): array;

    // Shipping label operations
    public function createShippingLabel(Shipment $shipment): string;

    public function voidShippingLabel(Shipment $shipment): bool;

    public function getShippingLabel(Shipment $shipment): string;

    public function getReturnLabel(Shipment $shipment): string;

    // Pickup operations
    public function schedulePickup(Shipment $shipment): bool;

    public function cancelPickup(Shipment $shipment): bool;

    public function getPickupConfirmation(Shipment $shipment): array;
}
