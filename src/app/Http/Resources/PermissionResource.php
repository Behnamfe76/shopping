<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {


        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'description' => $this->getDescription(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    public function getDescription(): ?string
    {
        if (is_string($this->meta)) {
            return json_decode($this->meta)?->{app()->getLocale()}?->description ?? null;
        }
        if (is_array($this->meta)) {
            return $this->meta[app()->getLocale()]['description'] ?? null;
        }

        return null;
    }
}
