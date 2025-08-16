<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait HasAnalyticsOperations
{
    public function incrementViewCount(Model $model): bool
    {
        $model->increment('view_count');
        return true;
    }

    public function incrementWishlistCount(Model $model): bool
    {
        $model->increment('wishlist_count');
        return true;
    }

    public function updateAverageRating(Model $model): bool
    {
        $averageRating = $model->reviews()->avg('rating') ?? 0;
        $model->average_rating = $averageRating;
        $model->reviews_count = $model->reviews()->count();
        return $model->save();
    }

    public function getAnalytics(Model $model): array
    {
        return [
            'total_sales' => $model->total_sales ?? 0,
            'view_count' => $model->view_count ?? 0,
            'wishlist_count' => $model->wishlist_count ?? 0,
            'average_rating' => $model->average_rating ?? 0,
            'reviews_count' => $model->reviews_count ?? 0,
        ];
    }

    // Shipment-specific analytics methods
    public function getShipmentAnalytics(int $shipmentId): array
    {
        return $this->repository->getShipmentAnalytics($shipmentId);
    }

    public function getShipmentAnalyticsByOrder(int $orderId): array
    {
        return $this->repository->getShipmentAnalyticsByOrder($orderId);
    }

    public function getShipmentAnalyticsByCarrier(string $carrier): array
    {
        return $this->repository->getShipmentAnalyticsByCarrier($carrier);
    }

    public function getDeliveryPerformance(string $carrier = null): array
    {
        return $this->repository->getDeliveryPerformance($carrier);
    }

    public function getShippingCosts(string $carrier = null): array
    {
        return $this->repository->getShippingCosts($carrier);
    }

    public function getDeliveryTimes(string $carrier = null): array
    {
        return $this->repository->getDeliveryTimes($carrier);
    }

    public function getReturnRates(string $carrier = null): array
    {
        return $this->repository->getReturnRates($carrier);
    }

    public function getCarrierPerformance(): array
    {
        return $this->repository->getCarrierPerformance();
    }

    public function getShipmentTrends(string $period = 'month'): array
    {
        return $this->repository->getShipmentTrends($period);
    }

    public function getShipmentForecast(string $period = 'month'): array
    {
        return $this->repository->getShipmentForecast($period);
    }

    public function getShipmentCount(): int
    {
        return $this->repository->getShipmentCount();
    }

    public function getShipmentCountByStatus(string $status): int
    {
        return $this->repository->getShipmentCountByStatus($status);
    }

    public function getShipmentCountByCarrier(string $carrier): int
    {
        return $this->repository->getShipmentCountByCarrier($carrier);
    }

    public function getShipmentCountByOrderId(int $orderId): int
    {
        return $this->repository->getShipmentCountByOrderId($orderId);
    }

    public function getAnalyticsSummary(): array
    {
        return [
            'total_shipments' => $this->getShipmentCount(),
            'pending_shipments' => $this->getShipmentCountByStatus('pending'),
            'in_transit_shipments' => $this->getShipmentCountByStatus('in_transit'),
            'delivered_shipments' => $this->getShipmentCountByStatus('delivered'),
            'returned_shipments' => $this->getShipmentCountByStatus('returned'),
            'delivery_performance' => $this->getDeliveryPerformance(),
            'shipping_costs' => $this->getShippingCosts(),
            'carrier_performance' => $this->getCarrierPerformance(),
        ];
    }

    public function getPerformanceMetrics(): array
    {
        $deliveryPerformance = $this->getDeliveryPerformance();
        $shippingCosts = $this->getShippingCosts();
        $returnRates = $this->getReturnRates();

        return [
            'on_time_delivery_rate' => $deliveryPerformance['on_time_percentage'] ?? 0,
            'average_delivery_time' => $deliveryPerformance['average_delivery_time'] ?? 0,
            'average_shipping_cost' => $shippingCosts['average_shipping_cost'] ?? 0,
            'return_rate' => $returnRates['return_rate'] ?? 0,
            'total_shipping_cost' => $shippingCosts['total_shipping_cost'] ?? 0,
            'total_insurance_cost' => $shippingCosts['total_insurance_cost'] ?? 0,
        ];
    }
}

