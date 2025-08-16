<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface;
use Fereydooni\Shopping\app\Models\Shipment;
use Fereydooni\Shopping\app\DTOs\ShipmentDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusManagement;
use Fereydooni\Shopping\app\Traits\HasTrackingOperations;
use Fereydooni\Shopping\app\Traits\HasDeliveryManagement;
use Fereydooni\Shopping\app\Traits\HasCarrierIntegration;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

class ShipmentService
{
    use HasCrudOperations,
        HasStatusManagement,
        HasTrackingOperations,
        HasDeliveryManagement,
        HasCarrierIntegration,
        HasAnalyticsOperations,
        HasSearchOperations;

    protected ShipmentRepositoryInterface $repository;

    public function __construct(ShipmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = Shipment::class;
        $this->dtoClass = ShipmentDTO::class;
    }

    /**
     * Get all shipments
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get all shipments as DTOs
     */
    public function allDTO(): Collection
    {
        return $this->all()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get paginated shipments
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get simple paginated shipments
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated shipments
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find shipment by ID
     */
    public function find(int $id): ?Shipment
    {
        return $this->repository->find($id);
    }

    /**
     * Find shipment by ID and return DTO
     */
    public function findDTO(int $id): ?ShipmentDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find shipments by order ID
     */
    public function findByOrderId(int $orderId): Collection
    {
        return $this->repository->findByOrderId($orderId);
    }

    /**
     * Find shipments by order ID and return DTOs
     */
    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->repository->findByOrderId($orderId)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Find shipments by carrier
     */
    public function findByCarrier(string $carrier): Collection
    {
        return $this->repository->findByCarrier($carrier);
    }

    /**
     * Find shipments by carrier and return DTOs
     */
    public function findByCarrierDTO(string $carrier): Collection
    {
        return $this->repository->findByCarrier($carrier)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Find shipments by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Find shipments by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatus($status)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Find shipment by tracking number
     */
    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        return $this->repository->findByTrackingNumber($trackingNumber);
    }

    /**
     * Find shipment by tracking number and return DTO
     */
    public function findByTrackingNumberDTO(string $trackingNumber): ?ShipmentDTO
    {
        $shipment = $this->repository->findByTrackingNumber($trackingNumber);
        return $shipment ? ShipmentDTO::fromModel($shipment) : null;
    }

    /**
     * Get pending shipments
     */
    public function getPending(): Collection
    {
        return $this->repository->findPending();
    }

    /**
     * Get pending shipments as DTOs
     */
    public function getPendingDTO(): Collection
    {
        return $this->repository->findPending()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get in-transit shipments
     */
    public function getInTransit(): Collection
    {
        return $this->repository->findInTransit();
    }

    /**
     * Get in-transit shipments as DTOs
     */
    public function getInTransitDTO(): Collection
    {
        return $this->repository->findInTransit()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get delivered shipments
     */
    public function getDelivered(): Collection
    {
        return $this->repository->findDelivered();
    }

    /**
     * Get delivered shipments as DTOs
     */
    public function getDeliveredDTO(): Collection
    {
        return $this->repository->findDelivered()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get returned shipments
     */
    public function getReturned(): Collection
    {
        return $this->repository->findReturned();
    }

    /**
     * Get returned shipments as DTOs
     */
    public function getReturnedDTO(): Collection
    {
        return $this->repository->findReturned()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get overdue shipments
     */
    public function getOverdue(): Collection
    {
        return $this->repository->findOverdue();
    }

    /**
     * Get overdue shipments as DTOs
     */
    public function getOverdueDTO(): Collection
    {
        return $this->repository->findOverdue()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get delayed shipments
     */
    public function getDelayed(): Collection
    {
        return $this->repository->findDelayed();
    }

    /**
     * Get delayed shipments as DTOs
     */
    public function getDelayedDTO(): Collection
    {
        return $this->repository->findDelayed()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get on-time shipments
     */
    public function getOnTime(): Collection
    {
        return $this->repository->findOnTime();
    }

    /**
     * Get on-time shipments as DTOs
     */
    public function getOnTimeDTO(): Collection
    {
        return $this->repository->findOnTime()->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Ship a shipment
     */
    public function ship(Shipment $shipment): bool
    {
        return $this->repository->ship($shipment);
    }

    /**
     * Deliver a shipment
     */
    public function deliver(Shipment $shipment): bool
    {
        return $this->repository->deliver($shipment);
    }

    /**
     * Return a shipment
     */
    public function return(Shipment $shipment, string $reason = null): bool
    {
        return $this->repository->return($shipment, $reason);
    }

    /**
     * Update tracking number
     */
    public function updateTracking(Shipment $shipment, string $trackingNumber): bool
    {
        return $this->repository->updateTracking($shipment, $trackingNumber);
    }

    /**
     * Update shipment status
     */
    public function updateStatus(Shipment $shipment, string $status): bool
    {
        return $this->repository->updateStatus($shipment, $status);
    }

    /**
     * Update delivery date
     */
    public function updateDeliveryDate(Shipment $shipment, string $deliveryDate): bool
    {
        return $this->repository->updateDeliveryDate($shipment, $deliveryDate);
    }

    /**
     * Search shipments
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search shipments and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->repository->search($query)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Search shipments by order ID
     */
    public function searchByOrderId(int $orderId, string $query): Collection
    {
        return $this->repository->searchByOrderId($orderId, $query);
    }

    /**
     * Search shipments by order ID and return DTOs
     */
    public function searchByOrderIdDTO(int $orderId, string $query): Collection
    {
        return $this->repository->searchByOrderId($orderId, $query)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Search shipments by carrier
     */
    public function searchByCarrier(string $carrier, string $query): Collection
    {
        return $this->repository->searchByCarrier($carrier, $query);
    }

    /**
     * Search shipments by carrier and return DTOs
     */
    public function searchByCarrierDTO(string $carrier, string $query): Collection
    {
        return $this->repository->searchByCarrier($carrier, $query)->map(fn($shipment) => ShipmentDTO::fromModel($shipment));
    }

    /**
     * Get shipment count
     */
    public function getCount(): int
    {
        return $this->repository->getShipmentCount();
    }

    /**
     * Get shipment count by order ID
     */
    public function getCountByOrderId(int $orderId): int
    {
        return $this->repository->getShipmentCountByOrderId($orderId);
    }

    /**
     * Get shipment count by carrier
     */
    public function getCountByCarrier(string $carrier): int
    {
        return $this->repository->getShipmentCountByCarrier($carrier);
    }

    /**
     * Get shipment count by status
     */
    public function getCountByStatus(string $status): int
    {
        return $this->repository->getShipmentCountByStatus($status);
    }

    /**
     * Get carriers
     */
    public function getCarriers(): Collection
    {
        return $this->repository->getCarriers();
    }

    /**
     * Get tracking numbers
     */
    public function getTrackingNumbers(): Collection
    {
        return $this->repository->getTrackingNumbers();
    }

    /**
     * Get shipment analytics
     */
    public function getAnalytics(int $shipmentId): array
    {
        return $this->repository->getShipmentAnalytics($shipmentId);
    }

    /**
     * Get shipment analytics by order
     */
    public function getAnalyticsByOrder(int $orderId): array
    {
        return $this->repository->getShipmentAnalyticsByOrder($orderId);
    }

    /**
     * Get shipment analytics by carrier
     */
    public function getAnalyticsByCarrier(string $carrier): array
    {
        return $this->repository->getShipmentAnalyticsByCarrier($carrier);
    }

    /**
     * Get delivery performance
     */
    public function getDeliveryPerformance(string $carrier = null): array
    {
        return $this->repository->getDeliveryPerformance($carrier);
    }

    /**
     * Get shipping costs
     */
    public function getShippingCosts(string $carrier = null): array
    {
        return $this->repository->getShippingCosts($carrier);
    }

    /**
     * Get delivery times
     */
    public function getDeliveryTimes(string $carrier = null): array
    {
        return $this->repository->getDeliveryTimes($carrier);
    }

    /**
     * Get return rates
     */
    public function getReturnRates(string $carrier = null): array
    {
        return $this->repository->getReturnRates($carrier);
    }

    /**
     * Get carrier performance
     */
    public function getCarrierPerformance(): array
    {
        return $this->repository->getCarrierPerformance();
    }

    /**
     * Get shipment trends
     */
    public function getTrends(string $period = 'month'): array
    {
        return $this->repository->getShipmentTrends($period);
    }

    /**
     * Get shipment forecast
     */
    public function getForecast(string $period = 'month'): array
    {
        return $this->repository->getShipmentForecast($period);
    }

    /**
     * Get tracking info
     */
    public function getTrackingInfo(string $trackingNumber, string $carrier): array
    {
        return $this->repository->getTrackingInfo($trackingNumber, $carrier);
    }

    /**
     * Create shipping label
     */
    public function createShippingLabel(Shipment $shipment): string
    {
        return $this->repository->createShippingLabel($shipment);
    }

    /**
     * Void shipping label
     */
    public function voidShippingLabel(Shipment $shipment): bool
    {
        return $this->repository->voidShippingLabel($shipment);
    }

    /**
     * Get shipping label
     */
    public function getShippingLabel(Shipment $shipment): string
    {
        return $this->repository->getShippingLabel($shipment);
    }

    /**
     * Get return label
     */
    public function getReturnLabel(Shipment $shipment): string
    {
        return $this->repository->getReturnLabel($shipment);
    }

    /**
     * Schedule pickup
     */
    public function schedulePickup(Shipment $shipment): bool
    {
        return $this->repository->schedulePickup($shipment);
    }

    /**
     * Cancel pickup
     */
    public function cancelPickup(Shipment $shipment): bool
    {
        return $this->repository->cancelPickup($shipment);
    }

    /**
     * Get pickup confirmation
     */
    public function getPickupConfirmation(Shipment $shipment): array
    {
        return $this->repository->getPickupConfirmation($shipment);
    }

    /**
     * Validate tracking number
     */
    public function validateTrackingNumber(string $trackingNumber, string $carrier): bool
    {
        return $this->repository->validateTrackingNumber($trackingNumber, $carrier);
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost(array $packageData): float
    {
        return $this->repository->calculateShippingCost($packageData);
    }

    /**
     * Estimate delivery date
     */
    public function estimateDeliveryDate(string $carrier, string $origin, string $destination): string
    {
        return $this->repository->estimateDeliveryDate($carrier, $origin, $destination);
    }

    /**
     * Get carrier rates
     */
    public function getCarrierRates(string $carrier, array $packageData): array
    {
        return $this->repository->getCarrierRates($carrier, $packageData);
    }

    /**
     * Get shipment summary
     */
    public function getSummary(): array
    {
        return [
            'total_shipments' => $this->getCount(),
            'pending_shipments' => $this->getCountByStatus('pending'),
            'in_transit_shipments' => $this->getCountByStatus('in_transit'),
            'delivered_shipments' => $this->getCountByStatus('delivered'),
            'returned_shipments' => $this->getCountByStatus('returned'),
            'overdue_shipments' => $this->getOverdue()->count(),
            'delayed_shipments' => $this->getDelayed()->count(),
            'on_time_shipments' => $this->getOnTime()->count(),
        ];
    }

    /**
     * Get shipment dashboard data
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'recent_shipments' => $this->paginate(10),
            'overdue_shipments' => $this->getOverdue(),
            'delivery_performance' => $this->getDeliveryPerformance(),
            'carrier_performance' => $this->getCarrierPerformance(),
            'trends' => $this->getTrends('week'),
        ];
    }
}
