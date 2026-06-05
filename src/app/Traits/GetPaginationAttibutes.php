<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait GetPaginationAttibutes
{
    public function getMeta($resource)
    {
        switch ($resource) {
            case $resource instanceof LengthAwarePaginator:
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
            case $resource instanceof Paginator:
                return [
                    'current_page' => $this->currentPage() ?? null,
                    'from' => $this->firstItem() ?? null,
                    'path' => $this->path() ?? null,
                    'per_page' => $this->perPage() ?? null,
                    'to' => $this->lastItem() ?? null,
                ];
            case $resource instanceof CursorPaginator:
                return [
                    'path' => $this->path(),
                    'per_page' => $this->perPage(),
                    'next_cursor' => $this->nextCursor()?->encode(),
                    'prev_cursor' => $this->previousCursor()?->encode(),
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
            case $resource instanceof LengthAwarePaginator:
                return array_merge($links, [
                    'first' => $this->url(1) ?? null,
                    'last' => $this->url($this?->lastPage()) ?? null,
                ]);
            case $resource instanceof Paginator:
                return $links;
            case $resource instanceof CursorPaginator:
                return $links;
            default:
                return null;
        }
    }
}
