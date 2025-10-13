<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Arr;

trait GetPaginationAttibutes
{
    public function getMeta($resource)
    {
        switch ($resource) {
            case $resource instanceof \Illuminate\Pagination\LengthAwarePaginator:
                return [
                    'last_page' => $this?->lastPage() ?? null,
                    'total' => $this->total(),
                    'links' => $this->links() ?? null,
                    'current_page' => $this->currentPage() ?? null,
                    'from' => $this->firstItem() ?? null,
                    'path' => $this->path() ?? null,
                    'per_page' => $this->perPage() ?? null,
                    'to' => $this->lastItem() ?? null,
                ];
            case $resource instanceof \Illuminate\Pagination\Paginator:
                return [
                    'current_page' => $this->currentPage() ?? null,
                    'from' => $this->firstItem() ?? null,
                    'path' => $this->path() ?? null,
                    'per_page' => $this->perPage() ?? null,
                    'to' => $this->lastItem() ?? null,
                ];
            case $resource instanceof \Illuminate\Pagination\CursorPaginator:
                return [
                    "path" => $this->path(),
                    "per_page" => $this->perPage(),
                    "next_cursor" => $this->nextCursor()?->encode(),
                    "prev_cursor" => $this->previousCursor()?->encode()
                ];
            default:
                return null;
        }
    }
    public function getLinks($resource)
    {
        $links = [
            'prev' => $this->previousPageUrl(),
            'next' => $this->nextPageUrl(),
        ];

        switch ($resource) {
            case $resource instanceof \Illuminate\Pagination\LengthAwarePaginator:
                return array_merge($links, [
                    'first' => $this->url(1) ?? null,
                    'last' => $this->url($this?->lastPage()) ?? null,
                ]);
            case $resource instanceof \Illuminate\Pagination\Paginator:
                return $links;
            case $resource instanceof \Illuminate\Pagination\CursorPaginator:
                return $links;
            default:
                return null;
        }
    }
}
