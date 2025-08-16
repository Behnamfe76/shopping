<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $query = $request->get('query', '');

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'tracking_number' => $this->when($this->tracking_number, $this->tracking_number),
            'placed_at' => $this->placed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),

            // Search metadata
            'search_relevance' => $this->calculateSearchRelevance($query),
            'matched_fields' => $this->getMatchedFields($query),

            // Highlighted fields
            'highlighted_tracking_number' => $this->when($this->tracking_number, function () use ($query) {
                return $this->highlightText($this->tracking_number, $query);
            }),
            'highlighted_notes' => $this->when($this->notes, function () use ($query) {
                return $this->highlightText($this->notes, $query);
            }),

            // User information
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }

    /**
     * Calculate search relevance score
     */
    private function calculateSearchRelevance(string $query): float
    {
        $score = 0;
        $query = strtolower($query);

        // Exact matches get higher scores
        if (str_contains(strtolower($this->tracking_number ?? ''), $query)) {
            $score += 10;
        }

        if (str_contains(strtolower($this->notes ?? ''), $query)) {
            $score += 5;
        }

        if (str_contains(strtolower($this->payment_method ?? ''), $query)) {
            $score += 3;
        }

        if (str_contains(strtolower($this->status ?? ''), $query)) {
            $score += 2;
        }

        if (str_contains(strtolower($this->payment_status ?? ''), $query)) {
            $score += 2;
        }

        return $score;
    }

    /**
     * Get fields that matched the search query
     */
    private function getMatchedFields(string $query): array
    {
        $matchedFields = [];
        $query = strtolower($query);

        if (str_contains(strtolower($this->tracking_number ?? ''), $query)) {
            $matchedFields[] = 'tracking_number';
        }

        if (str_contains(strtolower($this->notes ?? ''), $query)) {
            $matchedFields[] = 'notes';
        }

        if (str_contains(strtolower($this->payment_method ?? ''), $query)) {
            $matchedFields[] = 'payment_method';
        }

        if (str_contains(strtolower($this->status ?? ''), $query)) {
            $matchedFields[] = 'status';
        }

        if (str_contains(strtolower($this->payment_status ?? ''), $query)) {
            $matchedFields[] = 'payment_status';
        }

        return $matchedFields;
    }

    /**
     * Highlight search terms in text
     */
    private function highlightText(?string $text, string $query): ?string
    {
        if (!$text) {
            return null;
        }

        $query = preg_quote($query, '/');
        return preg_replace("/($query)/i", '<mark>$1</mark>', $text);
    }
}
