<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantBulkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'] ?? false,
            'message' => $this->resource['message'] ?? '',
            'operation_type' => $this->resource['operation_type'] ?? '',
            'total_processed' => $this->resource['total_processed'] ?? 0,
            'successful_count' => $this->resource['successful_count'] ?? 0,
            'failed_count' => $this->resource['failed_count'] ?? 0,
            'errors' => $this->resource['errors'] ?? [],
            'warnings' => $this->resource['warnings'] ?? [],
            'processed_items' => $this->resource['processed_items'] ?? [],
            'timestamp' => now()->toISOString(),
        ];
    }
}
