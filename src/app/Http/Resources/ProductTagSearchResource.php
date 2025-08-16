<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\ProductTag;

class ProductTagSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ProductTag $this */
        $query = $request->get('query', '');

        return [
            'id' => $this->id,
            'name' => $this->highlightText($this->name, $query),
            'slug' => $this->slug,
            'description' => $this->when($this->description, function () use ($query) {
                return $this->highlightText($this->description, $query);
            }),
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'usage_count' => $this->usage_count,
            'relevance_score' => $this->calculateRelevanceScore($query),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Search metadata
            'search_metadata' => [
                'matched_fields' => $this->getMatchedFields($query),
                'snippet' => $this->generateSnippet($query),
            ],

            // Links
            'links' => [
                'self' => route('shopping.product-tags.show', $this->slug),
                'edit' => route('shopping.product-tags.edit', $this->slug),
            ],
        ];
    }

    /**
     * Highlight matching text in the given content.
     */
    private function highlightText(string $text, string $query): string
    {
        if (empty($query)) {
            return $text;
        }

        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted ?: $text;
    }

    /**
     * Calculate relevance score based on query match.
     */
    private function calculateRelevanceScore(string $query): float
    {
        if (empty($query)) {
            return 0.0;
        }

        $score = 0.0;
        $query = strtolower($query);

        // Name match (highest weight)
        if (stripos($this->name, $query) !== false) {
            $score += 10.0;
        }

        // Slug match
        if (stripos($this->slug, $query) !== false) {
            $score += 8.0;
        }

        // Description match
        if ($this->description && stripos($this->description, $query) !== false) {
            $score += 5.0;
        }

        // Usage count bonus
        $score += min(2.0, $this->usage_count / 100);

        // Featured bonus
        if ($this->is_featured) {
            $score += 1.0;
        }

        return min(10.0, $score);
    }

    /**
     * Get fields that matched the search query.
     */
    private function getMatchedFields(string $query): array
    {
        if (empty($query)) {
            return [];
        }

        $matchedFields = [];
        $query = strtolower($query);

        if (stripos($this->name, $query) !== false) {
            $matchedFields[] = 'name';
        }

        if (stripos($this->slug, $query) !== false) {
            $matchedFields[] = 'slug';
        }

        if ($this->description && stripos($this->description, $query) !== false) {
            $matchedFields[] = 'description';
        }

        return $matchedFields;
    }

    /**
     * Generate a snippet from the description.
     */
    private function generateSnippet(string $query): ?string
    {
        if (empty($this->description) || empty($query)) {
            return null;
        }

        $position = stripos($this->description, $query);
        if ($position === false) {
            return null;
        }

        $start = max(0, $position - 50);
        $length = strlen($query) + 100;
        $snippet = substr($this->description, $start, $length);

        if ($start > 0) {
            $snippet = '...' . $snippet;
        }

        if (strlen($this->description) > $start + $length) {
            $snippet .= '...';
        }

        return $this->highlightText($snippet, $query);
    }
}
