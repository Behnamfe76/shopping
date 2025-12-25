<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasShippingOperations
{
    /**
     * Mark item as shipped
     */
    public function markAsShipped(object $item, ?int $shippedQuantity = null): bool
    {
        try {
            DB::beginTransaction();

            $shippedQuantity = $shippedQuantity ?? $item->quantity;

            if ($shippedQuantity > $item->quantity) {
                DB::rollBack();

                return false;
            }

            $data = [
                'is_shipped' => $shippedQuantity >= $item->quantity,
                'shipped_quantity' => $shippedQuantity,
            ];

            $updated = $this->repository->update($item, $data);

            if ($updated) {
                // Log shipping action
                $this->logShippingAction($item, 'shipped', $shippedQuantity);

                // Update inventory if needed
                $this->updateInventoryForShipping($item, $shippedQuantity);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking item as shipped', [
                'item_id' => $item->id,
                'shipped_quantity' => $shippedQuantity,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark item as shipped and return DTO
     */
    public function markAsShippedDTO(object $item, ?int $shippedQuantity = null): ?object
    {
        $updated = $this->markAsShipped($item, $shippedQuantity);

        return $updated ? $this->repository->findDTO($item->id) : null;
    }

    /**
     * Mark item as returned
     */
    public function markAsReturned(object $item, ?int $returnedQuantity = null): bool
    {
        try {
            DB::beginTransaction();

            $returnedQuantity = $returnedQuantity ?? $item->shipped_quantity;

            if ($returnedQuantity > $item->shipped_quantity) {
                DB::rollBack();

                return false;
            }

            $data = [
                'returned_quantity' => $returnedQuantity,
            ];

            $updated = $this->repository->update($item, $data);

            if ($updated) {
                // Log return action
                $this->logShippingAction($item, 'returned', $returnedQuantity);

                // Update inventory for return
                $this->updateInventoryForReturn($item, $returnedQuantity);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking item as returned', [
                'item_id' => $item->id,
                'returned_quantity' => $returnedQuantity,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark item as returned and return DTO
     */
    public function markAsReturnedDTO(object $item, ?int $returnedQuantity = null): ?object
    {
        $updated = $this->markAsReturned($item, $returnedQuantity);

        return $updated ? $this->repository->findDTO($item->id) : null;
    }

    /**
     * Process refund for item
     */
    public function processRefund(object $item, ?float $refundAmount = null): bool
    {
        try {
            DB::beginTransaction();

            $refundAmount = $refundAmount ?? $item->total_amount;

            if ($refundAmount > $item->total_amount) {
                DB::rollBack();

                return false;
            }

            $data = [
                'refunded_amount' => $refundAmount,
            ];

            $updated = $this->repository->update($item, $data);

            if ($updated) {
                // Log refund action
                $this->logShippingAction($item, 'refunded', null, $refundAmount);

                // Process payment refund
                $this->processPaymentRefund($item, $refundAmount);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing refund', [
                'item_id' => $item->id,
                'refund_amount' => $refundAmount,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Process refund and return DTO
     */
    public function processRefundDTO(object $item, ?float $refundAmount = null): ?object
    {
        $updated = $this->processRefund($item, $refundAmount);

        return $updated ? $this->repository->findDTO($item->id) : null;
    }

    /**
     * Get shipped items
     */
    public function getShippedItems(): Collection
    {
        return $this->repository->findShipped();
    }

    /**
     * Get shipped items as DTOs
     */
    public function getShippedItemsDTO(): Collection
    {
        return $this->repository->findShippedDTO();
    }

    /**
     * Get unshipped items
     */
    public function getUnshippedItems(): Collection
    {
        return $this->repository->findUnshipped();
    }

    /**
     * Get unshipped items as DTOs
     */
    public function getUnshippedItemsDTO(): Collection
    {
        return $this->repository->findUnshippedDTO();
    }

    /**
     * Calculate shipping costs
     */
    public function calculateShippingCosts(object $item): float
    {
        try {
            // This would typically calculate based on weight, dimensions, destination, etc.
            $baseCost = 5.00; // Base shipping cost
            $weightCost = ($item->weight ?? 0) * 0.50; // $0.50 per unit of weight

            return $baseCost + $weightCost;
        } catch (\Exception $e) {
            Log::error('Error calculating shipping costs', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get shipping status
     */
    public function getShippingStatus(object $item): string
    {
        if ($item->is_shipped && $item->shipped_quantity >= $item->quantity) {
            return 'fully_shipped';
        } elseif ($item->shipped_quantity > 0) {
            return 'partially_shipped';
        } else {
            return 'not_shipped';
        }
    }

    /**
     * Get return status
     */
    public function getReturnStatus(object $item): string
    {
        if ($item->returned_quantity >= $item->shipped_quantity) {
            return 'fully_returned';
        } elseif ($item->returned_quantity > 0) {
            return 'partially_returned';
        } else {
            return 'not_returned';
        }
    }

    /**
     * Get refund status
     */
    public function getRefundStatus(object $item): string
    {
        if ($item->refunded_amount >= $item->total_amount) {
            return 'fully_refunded';
        } elseif ($item->refunded_amount > 0) {
            return 'partially_refunded';
        } else {
            return 'not_refunded';
        }
    }

    /**
     * Log shipping action
     */
    protected function logShippingAction(object $item, string $action, ?int $quantity = null, ?float $amount = null): void
    {
        try {
            Log::info('Shipping action logged', [
                'item_id' => $item->id,
                'action' => $action,
                'quantity' => $quantity,
                'amount' => $amount,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging shipping action', [
                'item_id' => $item->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update inventory for shipping
     */
    protected function updateInventoryForShipping(object $item, int $shippedQuantity): void
    {
        try {
            if (method_exists($this, 'updateInventory')) {
                $this->updateInventory($item->product_id, $item->variant_id, $shippedQuantity, 'decrease');
            }
        } catch (\Exception $e) {
            Log::error('Error updating inventory for shipping', [
                'item_id' => $item->id,
                'shipped_quantity' => $shippedQuantity,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update inventory for return
     */
    protected function updateInventoryForReturn(object $item, int $returnedQuantity): void
    {
        try {
            if (method_exists($this, 'updateInventory')) {
                $this->updateInventory($item->product_id, $item->variant_id, $returnedQuantity, 'increase');
            }
        } catch (\Exception $e) {
            Log::error('Error updating inventory for return', [
                'item_id' => $item->id,
                'returned_quantity' => $returnedQuantity,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process payment refund
     */
    protected function processPaymentRefund(object $item, float $refundAmount): void
    {
        try {
            // This would typically integrate with a payment gateway
            Log::info('Payment refund processed', [
                'item_id' => $item->id,
                'refund_amount' => $refundAmount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing payment refund', [
                'item_id' => $item->id,
                'refund_amount' => $refundAmount,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get shipping history
     */
    public function getShippingHistory(object $item): Collection
    {
        try {
            // This would typically query a shipping history table
            return new Collection;
        } catch (\Exception $e) {
            Log::error('Error getting shipping history', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return new Collection;
        }
    }
}
