<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProviderLocation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'limit' => [
                'integer',
                'min:1',
                'max:100',
            ],
            'provider_id' => [
                'nullable',
                'integer',
                'exists:providers,id',
            ],
            'location_type' => [
                'nullable',
                'string',
                'in:headquarters,warehouse,store,office,factory,distribution_center,retail_outlet,service_center,other',
            ],
            'country' => [
                'nullable',
                'string',
                'size:2',
            ],
            'state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'has_coordinates' => [
                'nullable',
                'boolean',
            ],
            'sort_by' => [
                'nullable',
                'string',
                'in:name,address,city,state,country,created_at,updated_at',
            ],
            'sort_order' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],
            'radius' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:1000',
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'limit.integer' => 'Limit must be a whole number.',
            'limit.min' => 'Limit must be at least 1.',
            'limit.max' => 'Limit cannot exceed 100.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'location_type.in' => 'Invalid location type.',
            'country.size' => 'Country must be a 2-character code.',
            'state.max' => 'State cannot exceed 100 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_order.in' => 'Sort order must be ascending or descending.',
            'radius.min' => 'Radius must be at least 0.1.',
            'radius.max' => 'Radius cannot exceed 1000.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'limit' => 'result limit',
            'provider_id' => 'provider',
            'location_type' => 'location type',
            'country' => 'country',
            'state' => 'state',
            'city' => 'city',
            'is_active' => 'active status',
            'has_coordinates' => 'coordinates availability',
            'sort_by' => 'sort field',
            'sort_order' => 'sort order',
            'radius' => 'search radius',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Normalize search query
        if ($this->has('query')) {
            $data['query'] = trim($this->query);
        }

        // Normalize location type
        if ($this->has('location_type')) {
            $data['location_type'] = strtolower(trim($this->location_type));
        }

        // Normalize country
        if ($this->has('country')) {
            $data['country'] = strtoupper(trim($this->country));
        }

        // Normalize state and city
        if ($this->has('state')) {
            $data['state'] = trim($this->state);
        }

        if ($this->has('city')) {
            $data['city'] = trim($this->city);
        }

        // Normalize sort order
        if ($this->has('sort_order')) {
            $data['sort_order'] = strtolower(trim($this->sort_order));
        }

        // Set default values
        if (! $this->has('limit')) {
            $data['limit'] = 20;
        }

        if (! $this->has('sort_by')) {
            $data['sort_by'] = 'created_at';
        }

        if (! $this->has('sort_order')) {
            $data['sort_order'] = 'desc';
        }

        if (! $this->has('radius')) {
            $data['radius'] = 10;
        }

        // Merge the processed data
        if (! empty($data)) {
            $this->merge($data);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
