<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeocodeLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('geocode', \Fereydooni\Shopping\app\Models\ProviderLocation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'address' => [
                'required',
                'string',
                'min:5',
                'max:500'
            ],
            'provider_id' => [
                'nullable',
                'integer',
                'exists:providers,id'
            ],
            'location_id' => [
                'nullable',
                'integer',
                'exists:provider_locations,id'
            ],
            'geocoding_service' => [
                'nullable',
                'string',
                'in:google,openstreetmap,here,mapbox,manual'
            ],
            'prefer_exact_match' => [
                'boolean'
            ],
            'include_components' => [
                'boolean'
            ],
            'language' => [
                'nullable',
                'string',
                'size:2',
                'in:en,es,fr,de,it,pt,ru,zh,ja,ko,ar,hi'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'address.required' => 'Address is required for geocoding.',
            'address.min' => 'Address must be at least 5 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'location_id.exists' => 'Selected location does not exist.',
            'geocoding_service.in' => 'Invalid geocoding service.',
            'language.size' => 'Language must be a 2-character code.',
            'language.in' => 'Unsupported language code.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'address' => 'address',
            'provider_id' => 'provider',
            'location_id' => 'location',
            'geocoding_service' => 'geocoding service',
            'prefer_exact_match' => 'exact match preference',
            'include_components' => 'address components',
            'language' => 'language'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Normalize address
        if ($this->has('address')) {
            $data['address'] = trim($this->address);
        }

        // Normalize geocoding service
        if ($this->has('geocoding_service')) {
            $data['geocoding_service'] = strtolower(trim($this->geocoding_service));
        }

        // Normalize language
        if ($this->has('language')) {
            $data['language'] = strtolower(trim($this->language));
        }

        // Set default values
        if (!$this->has('geocoding_service')) {
            $data['geocoding_service'] = 'google';
        }

        if (!$this->has('prefer_exact_match')) {
            $data['prefer_exact_match'] = false;
        }

        if (!$this->has('include_components')) {
            $data['include_components'] = true;
        }

        if (!$this->has('language')) {
            $data['language'] = 'en';
        }

        // Merge the processed data
        if (!empty($data)) {
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
