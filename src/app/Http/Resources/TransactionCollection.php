<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => TransactionResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
                'transaction_count' => $this->total(),
                'total_amount' => $this->calculateTotalAmount(),
                'successful_count' => $this->calculateSuccessfulCount(),
                'failed_count' => $this->calculateFailedCount(),
                'refunded_count' => $this->calculateRefundedCount(),
                'pending_count' => $this->calculatePendingCount(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }

    /**
     * Calculate total amount of all transactions in the collection.
     */
    private function calculateTotalAmount(): float
    {
        return $this->collection->sum('amount');
    }

    /**
     * Calculate count of successful transactions.
     */
    private function calculateSuccessfulCount(): int
    {
        return $this->collection->where('status', 'success')->count();
    }

    /**
     * Calculate count of failed transactions.
     */
    private function calculateFailedCount(): int
    {
        return $this->collection->where('status', 'failed')->count();
    }

    /**
     * Calculate count of refunded transactions.
     */
    private function calculateRefundedCount(): int
    {
        return $this->collection->where('status', 'refunded')->count();
    }

    /**
     * Calculate count of pending transactions.
     */
    private function calculatePendingCount(): int
    {
        return $this->collection->where('status', 'initiated')->count();
    }
}
