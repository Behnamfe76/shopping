<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasStatusManagement
{
    /**
     * Change status with validation
     */
    public function changeStatus(object $item, string $newStatus, ?string $reason = null): bool
    {
        $this->validateStatusChange($item, $newStatus);

        $data = ['status' => $newStatus];

        if ($reason) {
            $data['notes'] = $this->addStatusChangeNote($item, $newStatus, $reason);
        }

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, $newStatus, $reason);
        }

        return $result;
    }

    /**
     * Cancel item
     */
    public function cancel(object $item, ?string $reason = null): bool
    {
        return $this->changeStatus($item, 'cancelled', $reason);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(object $item): bool
    {
        return $this->changeStatus($item, 'paid');
    }

    /**
     * Mark as shipped
     */
    public function markAsShipped(object $item, ?string $trackingNumber = null): bool
    {
        $data = ['status' => 'shipped'];

        if ($trackingNumber) {
            $data['tracking_number'] = $trackingNumber;
        }

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, 'shipped', null, $trackingNumber);
        }

        return $result;
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(object $item): bool
    {
        return $this->changeStatus($item, 'completed');
    }

    /**
     * Get items by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Get items by status as DTOs
     */
    public function getByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    /**
     * Get pending items
     */
    public function getPending(): Collection
    {
        return $this->repository->getPendingOrders();
    }

    /**
     * Get pending items as DTOs
     */
    public function getPendingDTO(): Collection
    {
        return $this->repository->getPendingOrdersDTO();
    }

    /**
     * Get shipped items
     */
    public function getShipped(): Collection
    {
        return $this->repository->getShippedOrders();
    }

    /**
     * Get shipped items as DTOs
     */
    public function getShippedDTO(): Collection
    {
        return $this->repository->getShippedOrdersDTO();
    }

    /**
     * Get completed items
     */
    public function getCompleted(): Collection
    {
        return $this->repository->getCompletedOrders();
    }

    /**
     * Get completed items as DTOs
     */
    public function getCompletedDTO(): Collection
    {
        return $this->repository->getCompletedOrdersDTO();
    }

    /**
     * Get cancelled items
     */
    public function getCancelled(): Collection
    {
        return $this->repository->getCancelledOrders();
    }

    /**
     * Get cancelled items as DTOs
     */
    public function getCancelledDTO(): Collection
    {
        return $this->repository->getCancelledOrdersDTO();
    }

    /**
     * Validate status change
     */
    protected function validateStatusChange(object $item, string $newStatus): void
    {
        $rules = [
            'status' => 'required|string|in:pending,paid,shipped,completed,cancelled',
        ];

        $data = ['status' => $newStatus];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Check if status transition is allowed
        $this->validateStatusTransition($item, $newStatus);
    }

    /**
     * Validate status transition
     */
    protected function validateStatusTransition(object $item, string $newStatus): void
    {
        $currentStatus = $item->status->value ?? $item->status;

        $allowedTransitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['shipped', 'cancelled'],
            'shipped' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (! isset($allowedTransitions[$currentStatus]) || ! in_array($newStatus, $allowedTransitions[$currentStatus])) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('status', "Cannot transition from {$currentStatus} to {$newStatus}")
            );
        }
    }

    /**
     * Add status change note
     */
    protected function addStatusChangeNote(object $item, string $newStatus, ?string $reason = null): string
    {
        $currentNotes = $item->notes ? json_decode($item->notes, true) : [];

        $note = "Status changed to {$newStatus}";
        if ($reason) {
            $note .= " - {$reason}";
        }

        $currentNotes[] = [
            'note' => $note,
            'type' => 'status_change',
            'created_at' => now()->toISOString(),
        ];

        return json_encode($currentNotes);
    }

    /**
     * Fire status changed event
     */
    protected function fireStatusChangedEvent(object $item, string $newStatus, ?string $reason = null, ?string $trackingNumber = null): void
    {
        // This method can be overridden in specific services to fire custom events
        // For now, we'll leave it empty as a placeholder
    }

    /**
     * Get status count
     */
    public function getStatusCount(string $status): int
    {
        return $this->repository->getOrderCountByStatus($status);
    }

    /**
     * Check if item can be cancelled
     */
    public function canBeCancelled(object $item): bool
    {
        $currentStatus = $item->status->value ?? $item->status;

        return in_array($currentStatus, ['pending', 'paid']);
    }

    /**
     * Check if item can be shipped
     */
    public function canBeShipped(object $item): bool
    {
        $currentStatus = $item->status->value ?? $item->status;

        return $currentStatus === 'paid';
    }

    /**
     * Check if item can be completed
     */
    public function canBeCompleted(object $item): bool
    {
        $currentStatus = $item->status->value ?? $item->status;

        return $currentStatus === 'shipped';
    }
}
