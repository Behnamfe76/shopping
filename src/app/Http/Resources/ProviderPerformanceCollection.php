<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProviderPerformanceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_providers' => $this->collection->unique('provider_id')->count(),
                'average_performance_score' => $this->collection->avg('performance_score'),
                'grade_distribution' => $this->getGradeDistribution(),
                'verification_status' => $this->getVerificationStatus(),
            ],
        ];
    }

    /**
     * Get grade distribution summary
     */
    private function getGradeDistribution(): array
    {
        $grades = $this->collection->groupBy('performance_grade');

        return [
            'A' => $grades->get('A', collect())->count(),
            'B' => $grades->get('B', collect())->count(),
            'C' => $grades->get('C', collect())->count(),
            'D' => $grades->get('D', collect())->count(),
            'F' => $grades->get('F', collect())->count(),
        ];
    }

    /**
     * Get verification status summary
     */
    private function getVerificationStatus(): array
    {
        $verified = $this->collection->where('is_verified', true)->count();
        $unverified = $this->collection->where('is_verified', false)->count();

        return [
            'verified' => $verified,
            'unverified' => $unverified,
            'verification_rate' => $this->collection->count() > 0
                ? round(($verified / $this->collection->count()) * 100, 2)
                : 0,
        ];
    }
}
