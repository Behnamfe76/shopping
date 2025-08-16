<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductVariantSearchResource extends ResourceCollection
{
    protected string $query;

    public function __construct($resource, string $query = '')
    {
        parent::__construct($resource);
        $this->query = $query;
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'query' => $this->query,
                'total_results' => $this->count(),
                'search_time' => now()->toISOString(),
                'highlighted_terms' => $this->getHighlightedTerms(),
            ],
            'suggestions' => $this->getSearchSuggestions(),
        ];
    }

    /**
     * Get highlighted search terms.
     */
    private function getHighlightedTerms(): array
    {
        $terms = [];
        $queryWords = explode(' ', strtolower($this->query));

        foreach ($this->collection as $variant) {
            $highlighted = [];

            // Highlight SKU matches
            if (stripos($variant->sku, $this->query) !== false) {
                $highlighted['sku'] = $this->highlightText($variant->sku, $this->query);
            }

            // Highlight barcode matches
            if ($variant->barcode && stripos($variant->barcode, $this->query) !== false) {
                $highlighted['barcode'] = $this->highlightText($variant->barcode, $this->query);
            }

            // Highlight product title matches
            if ($variant->product && stripos($variant->product->title, $this->query) !== false) {
                $highlighted['product_title'] = $this->highlightText($variant->product->title, $this->query);
            }

            if (!empty($highlighted)) {
                $terms[$variant->id] = $highlighted;
            }
        }

        return $terms;
    }

    /**
     * Get search suggestions.
     */
    private function getSearchSuggestions(): array
    {
        $suggestions = [];

        // SKU suggestions
        $skuSuggestions = $this->collection->pluck('sku')->filter(function ($sku) {
            return stripos($sku, $this->query) !== false;
        })->take(5)->toArray();

        if (!empty($skuSuggestions)) {
            $suggestions['skus'] = $skuSuggestions;
        }

        // Product title suggestions
        $titleSuggestions = $this->collection->pluck('product.title')->filter(function ($title) {
            return $title && stripos($title, $this->query) !== false;
        })->take(5)->toArray();

        if (!empty($titleSuggestions)) {
            $suggestions['product_titles'] = $titleSuggestions;
        }

        return $suggestions;
    }

    /**
     * Highlight text with HTML tags.
     */
    private function highlightText(string $text, string $query): string
    {
        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted;
    }
}
