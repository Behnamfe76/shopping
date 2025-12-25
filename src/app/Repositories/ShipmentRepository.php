<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\ShipmentDTO;
use Fereydooni\Shopping\app\Enums\ShipmentStatus;
use Fereydooni\Shopping\app\Models\Shipment;
use Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function all(): Collection
    {
        return Cache::remember('shipments.all', 3600, function () {
            return Shipment::with(['order', 'items'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Shipment::with(['order', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?Shipment
    {
        return Cache::remember("shipments.{$id}", 3600, function () use ($id) {
            return Shipment::with(['order', 'items'])->find($id);
        });
    }

    public function findDTO(int $id): ?ShipmentDTO
    {
        $shipment = $this->find($id);

        return $shipment ? ShipmentDTO::fromModel($shipment) : null;
    }

    public function create(array $data): Shipment
    {
        $shipment = Shipment::create($data);
        $this->clearCache();

        return $shipment->load(['order', 'items']);
    }

    public function createAndReturnDTO(array $data): ShipmentDTO
    {
        $shipment = $this->create($data);

        return ShipmentDTO::fromModel($shipment);
    }

    public function update(Shipment $shipment, array $data): bool
    {
        $updated = $shipment->update($data);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function delete(Shipment $shipment): bool
    {
        $deleted = $shipment->delete();
        if ($deleted) {
            $this->clearCache();
        }

        return $deleted;
    }

    public function findByOrderId(int $orderId): Collection
    {
        return Cache::remember("shipments.order.{$orderId}", 3600, function () use ($orderId) {
            return Shipment::with(['order', 'items'])
                ->where('order_id', $orderId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getShipmentCountByOrderId(int $orderId): int
    {
        return Cache::remember("shipments.count.order.{$orderId}", 3600, function () use ($orderId) {
            return Shipment::where('order_id', $orderId)->count();
        });
    }

    public function findByCarrier(string $carrier): Collection
    {
        return Cache::remember("shipments.carrier.{$carrier}", 3600, function () use ($carrier) {
            return Shipment::with(['order', 'items'])
                ->where('carrier', $carrier)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getShipmentCountByCarrier(string $carrier): int
    {
        return Cache::remember("shipments.count.carrier.{$carrier}", 3600, function () use ($carrier) {
            return Shipment::where('carrier', $carrier)->count();
        });
    }

    public function getCarriers(): Collection
    {
        return Cache::remember('shipments.carriers', 3600, function () {
            return Shipment::select('carrier')
                ->distinct()
                ->pluck('carrier');
        });
    }

    public function findByStatus(string $status): Collection
    {
        return Cache::remember("shipments.status.{$status}", 3600, function () use ($status) {
            return Shipment::with(['order', 'items'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getShipmentCountByStatus(string $status): int
    {
        return Cache::remember("shipments.count.status.{$status}", 3600, function () use ($status) {
            return Shipment::where('status', $status)->count();
        });
    }

    public function findPending(): Collection
    {
        return $this->findByStatus(ShipmentStatus::PENDING->value);
    }

    public function findInTransit(): Collection
    {
        return $this->findByStatus(ShipmentStatus::IN_TRANSIT->value);
    }

    public function findDelivered(): Collection
    {
        return $this->findByStatus(ShipmentStatus::DELIVERED->value);
    }

    public function findReturned(): Collection
    {
        return $this->findByStatus(ShipmentStatus::RETURNED->value);
    }

    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        return Cache::remember("shipments.tracking.{$trackingNumber}", 3600, function () use ($trackingNumber) {
            return Shipment::with(['order', 'items'])
                ->where('tracking_number', $trackingNumber)
                ->first();
        });
    }

    public function getTrackingNumbers(): Collection
    {
        return Cache::remember('shipments.tracking_numbers', 3600, function () {
            return Shipment::select('tracking_number')
                ->whereNotNull('tracking_number')
                ->pluck('tracking_number');
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return Shipment::with(['order', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByDeliveryDate(string $deliveryDate): Collection
    {
        return Shipment::with(['order', 'items'])
            ->whereDate('estimated_delivery', $deliveryDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByShippedDate(string $shippedDate): Collection
    {
        return Shipment::with(['order', 'items'])
            ->whereDate('shipped_at', $shippedDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findOverdue(): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where('status', '!=', ShipmentStatus::DELIVERED->value)
            ->where('status', '!=', ShipmentStatus::RETURNED->value)
            ->where('estimated_delivery', '<', now())
            ->orderBy('estimated_delivery', 'asc')
            ->get();
    }

    public function findDelayed(): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where('status', ShipmentStatus::IN_TRANSIT->value)
            ->where('estimated_delivery', '<', now())
            ->orderBy('estimated_delivery', 'asc')
            ->get();
    }

    public function findOnTime(): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where(function ($query) {
                $query->where('estimated_delivery', '>=', now())
                    ->orWhere(function ($q) {
                        $q->where('status', ShipmentStatus::DELIVERED->value)
                            ->where('actual_delivery', '<=', DB::raw('estimated_delivery'));
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where(function ($q) use ($query) {
                $q->where('tracking_number', 'like', "%{$query}%")
                    ->orWhere('carrier', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%")
                    ->orWhereHas('order', function ($orderQuery) use ($query) {
                        $orderQuery->where('id', 'like', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchByOrderId(int $orderId, string $query): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where('order_id', $orderId)
            ->where(function ($q) use ($query) {
                $q->where('tracking_number', 'like', "%{$query}%")
                    ->orWhere('carrier', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchByCarrier(string $carrier, string $query): Collection
    {
        return Shipment::with(['order', 'items'])
            ->where('carrier', $carrier)
            ->where(function ($q) use ($query) {
                $q->where('tracking_number', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function ship(Shipment $shipment): bool
    {
        $updated = $shipment->update([
            'status' => ShipmentStatus::IN_TRANSIT->value,
            'shipped_at' => now(),
        ]);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function deliver(Shipment $shipment): bool
    {
        $updated = $shipment->update([
            'status' => ShipmentStatus::DELIVERED->value,
            'actual_delivery' => now(),
        ]);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function return(Shipment $shipment, ?string $reason = null): bool
    {
        $data = ['status' => ShipmentStatus::RETURNED->value];
        if ($reason) {
            $data['notes'] = $shipment->notes ? $shipment->notes."\nReturn reason: ".$reason : 'Return reason: '.$reason;
        }

        $updated = $shipment->update($data);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function updateTracking(Shipment $shipment, string $trackingNumber): bool
    {
        $updated = $shipment->update(['tracking_number' => $trackingNumber]);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function updateStatus(Shipment $shipment, string $status): bool
    {
        $updated = $shipment->update(['status' => $status]);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function updateDeliveryDate(Shipment $shipment, string $deliveryDate): bool
    {
        $updated = $shipment->update(['estimated_delivery' => $deliveryDate]);
        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function getShipmentCount(): int
    {
        return Cache::remember('shipments.count', 3600, function () {
            return Shipment::count();
        });
    }

    public function getShipmentAnalytics(int $shipmentId): array
    {
        $shipment = $this->find($shipmentId);
        if (! $shipment) {
            return [];
        }

        return [
            'id' => $shipment->id,
            'status' => $shipment->status->value,
            'carrier' => $shipment->carrier,
            'shipping_cost' => $shipment->shipping_cost,
            'weight' => $shipment->weight,
            'package_count' => $shipment->package_count,
            'is_insured' => $shipment->is_insured,
            'insurance_amount' => $shipment->insurance_amount,
            'total_value' => $shipment->shipping_cost + $shipment->insurance_amount,
            'delivery_time' => $shipment->shipped_at && $shipment->actual_delivery
                ? $shipment->shipped_at->diffInDays($shipment->actual_delivery)
                : null,
            'estimated_delivery_time' => $shipment->shipped_at && $shipment->estimated_delivery
                ? $shipment->shipped_at->diffInDays($shipment->estimated_delivery)
                : null,
            'is_overdue' => $shipment->estimated_delivery && $shipment->estimated_delivery->isPast() && $shipment->status !== ShipmentStatus::DELIVERED,
            'is_delayed' => $shipment->estimated_delivery && $shipment->estimated_delivery->isPast() && $shipment->status === ShipmentStatus::IN_TRANSIT,
            'is_on_time' => $shipment->estimated_delivery && ($shipment->estimated_delivery->isFuture() || ($shipment->actual_delivery && $shipment->actual_delivery->lte($shipment->estimated_delivery))),
        ];
    }

    public function getShipmentAnalyticsByOrder(int $orderId): array
    {
        $shipments = $this->findByOrderId($orderId);

        return [
            'order_id' => $orderId,
            'total_shipments' => $shipments->count(),
            'total_shipping_cost' => $shipments->sum('shipping_cost'),
            'total_weight' => $shipments->sum('weight'),
            'total_packages' => $shipments->sum('package_count'),
            'total_insurance' => $shipments->sum('insurance_amount'),
            'status_breakdown' => $shipments->groupBy('status')->map->count(),
            'carrier_breakdown' => $shipments->groupBy('carrier')->map->count(),
            'overdue_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && $s->estimated_delivery->isPast() && $s->status !== ShipmentStatus::DELIVERED)->count(),
            'delayed_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && $s->estimated_delivery->isPast() && $s->status === ShipmentStatus::IN_TRANSIT)->count(),
            'on_time_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && ($s->estimated_delivery->isFuture() || ($s->actual_delivery && $s->actual_delivery->lte($s->estimated_delivery))))->count(),
        ];
    }

    public function getShipmentAnalyticsByCarrier(string $carrier): array
    {
        $shipments = $this->findByCarrier($carrier);

        return [
            'carrier' => $carrier,
            'total_shipments' => $shipments->count(),
            'total_shipping_cost' => $shipments->sum('shipping_cost'),
            'total_weight' => $shipments->sum('weight'),
            'total_packages' => $shipments->sum('package_count'),
            'total_insurance' => $shipments->sum('insurance_amount'),
            'status_breakdown' => $shipments->groupBy('status')->map->count(),
            'overdue_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && $s->estimated_delivery->isPast() && $s->status !== ShipmentStatus::DELIVERED)->count(),
            'delayed_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && $s->estimated_delivery->isPast() && $s->status === ShipmentStatus::IN_TRANSIT)->count(),
            'on_time_count' => $shipments->filter(fn ($s) => $s->estimated_delivery && ($s->estimated_delivery->isFuture() || ($s->actual_delivery && $s->actual_delivery->lte($s->estimated_delivery))))->count(),
            'average_delivery_time' => $shipments->filter(fn ($s) => $s->shipped_at && $s->actual_delivery)->avg(function ($s) {
                return $s->shipped_at->diffInDays($s->actual_delivery);
            }),
        ];
    }

    public function getDeliveryPerformance(?string $carrier = null): array
    {
        $query = Shipment::where('status', ShipmentStatus::DELIVERED->value);

        if ($carrier) {
            $query->where('carrier', $carrier);
        }

        $delivered = $query->get();

        return [
            'total_delivered' => $delivered->count(),
            'average_delivery_time' => $delivered->filter(fn ($s) => $s->shipped_at && $s->actual_delivery)->avg(function ($s) {
                return $s->shipped_at->diffInDays($s->actual_delivery);
            }),
            'on_time_deliveries' => $delivered->filter(fn ($s) => $s->actual_delivery && $s->estimated_delivery && $s->actual_delivery->lte($s->estimated_delivery))->count(),
            'late_deliveries' => $delivered->filter(fn ($s) => $s->actual_delivery && $s->estimated_delivery && $s->actual_delivery->gt($s->estimated_delivery))->count(),
            'on_time_percentage' => $delivered->count() > 0 ?
                ($delivered->filter(fn ($s) => $s->actual_delivery && $s->estimated_delivery && $s->actual_delivery->lte($s->estimated_delivery))->count() / $delivered->count()) * 100 : 0,
        ];
    }

    public function getShippingCosts(?string $carrier = null): array
    {
        $query = Shipment::query();

        if ($carrier) {
            $query->where('carrier', $carrier);
        }

        $shipments = $query->get();

        return [
            'total_shipping_cost' => $shipments->sum('shipping_cost'),
            'average_shipping_cost' => $shipments->avg('shipping_cost'),
            'total_insurance_cost' => $shipments->sum('insurance_amount'),
            'average_insurance_cost' => $shipments->avg('insurance_amount'),
            'total_weight' => $shipments->sum('weight'),
            'average_weight' => $shipments->avg('weight'),
            'cost_per_pound' => $shipments->sum('weight') > 0 ? $shipments->sum('shipping_cost') / $shipments->sum('weight') : 0,
        ];
    }

    public function getDeliveryTimes(?string $carrier = null): array
    {
        $query = Shipment::where('status', ShipmentStatus::DELIVERED->value)
            ->whereNotNull('shipped_at')
            ->whereNotNull('actual_delivery');

        if ($carrier) {
            $query->where('carrier', $carrier);
        }

        $delivered = $query->get();

        $deliveryTimes = $delivered->map(function ($s) {
            return $s->shipped_at->diffInDays($s->actual_delivery);
        });

        return [
            'average_delivery_time' => $deliveryTimes->avg(),
            'min_delivery_time' => $deliveryTimes->min(),
            'max_delivery_time' => $deliveryTimes->max(),
            'median_delivery_time' => $deliveryTimes->sort()->values()->get($deliveryTimes->count() / 2),
            'delivery_time_distribution' => [
                '1-2 days' => $deliveryTimes->filter(fn ($t) => $t <= 2)->count(),
                '3-5 days' => $deliveryTimes->filter(fn ($t) => $t >= 3 && $t <= 5)->count(),
                '6-10 days' => $deliveryTimes->filter(fn ($t) => $t >= 6 && $t <= 10)->count(),
                '10+ days' => $deliveryTimes->filter(fn ($t) => $t > 10)->count(),
            ],
        ];
    }

    public function getReturnRates(?string $carrier = null): array
    {
        $query = Shipment::query();

        if ($carrier) {
            $query->where('carrier', $carrier);
        }

        $allShipments = $query->get();
        $returnedShipments = $allShipments->where('status', ShipmentStatus::RETURNED->value);

        return [
            'total_shipments' => $allShipments->count(),
            'returned_shipments' => $returnedShipments->count(),
            'return_rate' => $allShipments->count() > 0 ? ($returnedShipments->count() / $allShipments->count()) * 100 : 0,
            'returned_by_carrier' => $returnedShipments->groupBy('carrier')->map->count(),
        ];
    }

    public function getCarrierPerformance(): array
    {
        $carriers = $this->getCarriers();
        $performance = [];

        foreach ($carriers as $carrier) {
            $performance[$carrier] = [
                'total_shipments' => $this->getShipmentCountByCarrier($carrier),
                'delivery_performance' => $this->getDeliveryPerformance($carrier),
                'shipping_costs' => $this->getShippingCosts($carrier),
                'return_rate' => $this->getReturnRates($carrier),
            ];
        }

        return $performance;
    }

    public function getShipmentTrends(string $period = 'month'): array
    {
        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $shipments = Shipment::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => now()->toDateString(),
            'total_shipments' => $shipments->sum('count'),
            'daily_trends' => $shipments->pluck('count', 'date'),
            'average_daily_shipments' => $shipments->avg('count'),
        ];
    }

    public function getShipmentForecast(string $period = 'month'): array
    {
        // Simple forecasting based on historical trends
        $historicalData = $this->getShipmentTrends($period);
        $averageDaily = $historicalData['average_daily_shipments'];

        $forecastDays = match ($period) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 30,
        };

        return [
            'period' => $period,
            'forecast_days' => $forecastDays,
            'predicted_total_shipments' => round($averageDaily * $forecastDays),
            'predicted_daily_average' => $averageDaily,
            'confidence_level' => 'medium', // This would be calculated based on data variance
        ];
    }

    public function validateTrackingNumber(string $trackingNumber, string $carrier): bool
    {
        // Basic validation patterns for common carriers
        $patterns = [
            'fedex' => '/^\d{12,15}$/',
            'ups' => '/^1Z[A-Z0-9]{16}$/',
            'usps' => '/^[0-9]{20,22}$/',
            'dhl' => '/^[0-9]{10,11}$/',
            'amazon' => '/^TBA[0-9]{10}$/',
        ];

        if (! isset($patterns[$carrier])) {
            return true; // Unknown carrier, accept any format
        }

        return preg_match($patterns[$carrier], $trackingNumber);
    }

    public function getTrackingInfo(string $trackingNumber, string $carrier): array
    {
        // This would integrate with actual carrier APIs
        // For now, return mock data
        return [
            'tracking_number' => $trackingNumber,
            'carrier' => $carrier,
            'status' => 'in_transit',
            'estimated_delivery' => now()->addDays(3)->toDateString(),
            'current_location' => 'Distribution Center',
            'events' => [
                [
                    'date' => now()->subDay()->toDateTimeString(),
                    'location' => 'Origin Facility',
                    'description' => 'Package picked up',
                ],
                [
                    'date' => now()->toDateTimeString(),
                    'location' => 'Distribution Center',
                    'description' => 'Package in transit',
                ],
            ],
        ];
    }

    public function calculateShippingCost(array $packageData): float
    {
        // Basic shipping cost calculation
        $baseCost = 5.00;
        $weightCost = ($packageData['weight'] ?? 0) * 0.50;
        $distanceCost = ($packageData['distance'] ?? 0) * 0.10;
        $insuranceCost = ($packageData['insurance_amount'] ?? 0) * 0.01;

        return $baseCost + $weightCost + $distanceCost + $insuranceCost;
    }

    public function estimateDeliveryDate(string $carrier, string $origin, string $destination): string
    {
        // Basic delivery estimation
        $baseDays = match ($carrier) {
            'fedex' => 3,
            'ups' => 3,
            'usps' => 5,
            'dhl' => 4,
            'amazon' => 2,
            default => 5,
        };

        return now()->addDays($baseDays)->toDateString();
    }

    public function getCarrierRates(string $carrier, array $packageData): array
    {
        // Mock carrier rates
        $rates = [
            'fedex' => [
                'ground' => $this->calculateShippingCost($packageData),
                'express' => $this->calculateShippingCost($packageData) * 2,
                'overnight' => $this->calculateShippingCost($packageData) * 4,
            ],
            'ups' => [
                'ground' => $this->calculateShippingCost($packageData),
                'next_day_air' => $this->calculateShippingCost($packageData) * 3,
                'second_day_air' => $this->calculateShippingCost($packageData) * 2,
            ],
            'usps' => [
                'priority' => $this->calculateShippingCost($packageData) * 1.5,
                'first_class' => $this->calculateShippingCost($packageData) * 0.8,
                'media_mail' => $this->calculateShippingCost($packageData) * 0.5,
            ],
        ];

        return $rates[$carrier] ?? [];
    }

    public function createShippingLabel(Shipment $shipment): string
    {
        // Mock label generation
        return 'SHIPPING_LABEL_'.$shipment->id.'_'.time().'.pdf';
    }

    public function voidShippingLabel(Shipment $shipment): bool
    {
        // Mock void operation
        return true;
    }

    public function getShippingLabel(Shipment $shipment): string
    {
        // Mock label retrieval
        return 'SHIPPING_LABEL_'.$shipment->id.'_'.time().'.pdf';
    }

    public function getReturnLabel(Shipment $shipment): string
    {
        // Mock return label generation
        return 'RETURN_LABEL_'.$shipment->id.'_'.time().'.pdf';
    }

    public function schedulePickup(Shipment $shipment): bool
    {
        // Mock pickup scheduling
        return true;
    }

    public function cancelPickup(Shipment $shipment): bool
    {
        // Mock pickup cancellation
        return true;
    }

    public function getPickupConfirmation(Shipment $shipment): array
    {
        // Mock pickup confirmation
        return [
            'pickup_id' => 'PICKUP_'.$shipment->id,
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '09:00-17:00',
            'status' => 'scheduled',
        ];
    }

    private function clearCache(): void
    {
        Cache::forget('shipments.all');
        Cache::forget('shipments.count');
        Cache::forget('shipments.carriers');
        Cache::forget('shipments.tracking_numbers');

        // Clear carrier-specific caches
        $carriers = ['fedex', 'ups', 'usps', 'dhl', 'amazon', 'other'];
        foreach ($carriers as $carrier) {
            Cache::forget("shipments.carrier.{$carrier}");
            Cache::forget("shipments.count.carrier.{$carrier}");
        }

        // Clear status-specific caches
        $statuses = ['pending', 'in_transit', 'delivered', 'returned'];
        foreach ($statuses as $status) {
            Cache::forget("shipments.status.{$status}");
            Cache::forget("shipments.count.status.{$status}");
        }
    }
}
