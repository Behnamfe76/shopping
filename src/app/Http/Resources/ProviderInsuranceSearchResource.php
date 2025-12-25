<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderInsuranceSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Add search-specific fields
        $data['search_score'] = $this->resource->search_score ?? null;
        $data['search_highlights'] = $this->getSearchHighlights();
        $data['matched_fields'] = $this->getMatchedFields();

        // Add computed fields
        $data['is_expired'] = $this->isExpired();
        $data['is_expiring_soon'] = $this->isExpiringSoon();
        $data['days_until_expiry'] = $this->daysUntilExpiry();
        $data['coverage_formatted'] = $this->formatCoverageAmount();
        $data['status_label'] = $this->getStatusLabel();
        $data['verification_status_label'] = $this->getVerificationStatusLabel();
        $data['insurance_type_label'] = $this->getInsuranceTypeLabel();

        // Add conditional fields based on user permissions
        if ($request->user() && $request->user()->can('view', $this->resource)) {
            $data['notes'] = $this->resource->notes ?? null;
            $data['documents'] = $this->resource->documents ?? [];
        }

        // Add relationships if loaded
        if ($this->resource->relationLoaded('provider')) {
            $data['provider'] = [
                'id' => $this->resource->provider->id,
                'company_name' => $this->resource->provider->company_name,
                'contact_person' => $this->resource->provider->contact_person,
                'email' => $this->resource->provider->email,
                'phone' => $this->resource->provider->phone,
            ];
        }

        return $data;
    }

    /**
     * Get search highlights for matched text.
     */
    protected function getSearchHighlights(): array
    {
        $highlights = [];
        $searchQuery = request()->get('query', '');

        if (empty($searchQuery)) {
            return $highlights;
        }

        $fields = ['policy_number', 'provider_name', 'notes'];

        foreach ($fields as $field) {
            if (isset($this->resource->$field)) {
                $text = $this->resource->$field;
                if (stripos($text, $searchQuery) !== false) {
                    $highlights[$field] = $this->highlightText($text, $searchQuery);
                }
            }
        }

        return $highlights;
    }

    /**
     * Get fields that matched the search query.
     */
    protected function getMatchedFields(): array
    {
        $matchedFields = [];
        $searchQuery = request()->get('query', '');

        if (empty($searchQuery)) {
            return $matchedFields;
        }

        $fields = ['policy_number', 'provider_name', 'notes'];

        foreach ($fields as $field) {
            if (isset($this->resource->$field)) {
                $text = $this->resource->$field;
                if (stripos($text, $searchQuery) !== false) {
                    $matchedFields[] = $field;
                }
            }
        }

        return $matchedFields;
    }

    /**
     * Highlight search terms in text.
     */
    protected function highlightText(string $text, string $query): string
    {
        $highlighted = preg_replace(
            '/('.preg_quote($query, '/').')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted;
    }

    /**
     * Check if insurance is expired.
     */
    protected function isExpired(): bool
    {
        $endDate = $this->resource->end_date ?? null;
        if (! $endDate) {
            return false;
        }

        return now()->isAfter($endDate);
    }

    /**
     * Check if insurance is expiring soon (within 30 days).
     */
    protected function isExpiringSoon(int $days = 30): bool
    {
        $endDate = $this->resource->end_date ?? null;
        if (! $endDate) {
            return false;
        }

        return now()->addDays($days)->isAfter($endDate) && ! $this->isExpired();
    }

    /**
     * Get days until expiry.
     */
    protected function daysUntilExpiry(): ?int
    {
        $endDate = $this->resource->end_date ?? null;
        if (! $endDate) {
            return null;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($endDate, false);
    }

    /**
     * Format coverage amount with currency.
     */
    protected function formatCoverageAmount(): string
    {
        $amount = $this->resource->coverage_amount ?? 0;

        return '$'.number_format($amount, 2);
    }

    /**
     * Get human-readable status label.
     */
    protected function getStatusLabel(): string
    {
        $status = $this->resource->status ?? '';

        return match ($status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
            default => ucfirst($status)
        };
    }

    /**
     * Get human-readable verification status label.
     */
    protected function getVerificationStatusLabel(): string
    {
        $status = $this->resource->verification_status ?? '';

        return match ($status) {
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'expired' => 'Verification Expired',
            default => ucfirst($status)
        };
    }

    /**
     * Get human-readable insurance type label.
     */
    protected function getInsuranceTypeLabel(): string
    {
        $type = $this->resource->insurance_type ?? '';

        return match ($type) {
            'general_liability' => 'General Liability',
            'professional_liability' => 'Professional Liability',
            'product_liability' => 'Product Liability',
            'workers_compensation' => 'Workers Compensation',
            'auto_insurance' => 'Auto Insurance',
            'property_insurance' => 'Property Insurance',
            'cyber_insurance' => 'Cyber Insurance',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $type))
        };
    }
}
