<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTagBulkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'operation' => $this->operation ?? 'unknown',
            'success' => $this->success ?? false,
            'message' => $this->message ?? '',
            'count' => $this->count ?? 0,
            'processed' => $this->processed ?? 0,
            'failed' => $this->failed ?? 0,
            'errors' => $this->errors ?? [],
            'warnings' => $this->warnings ?? [],
            'data' => $this->data ?? [],
            'timestamp' => now()->toISOString(),
            'duration' => $this->duration ?? 0,
        ];
    }
}
