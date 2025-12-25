<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoordinatesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateCoordinates', $this->route('providerLocation'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'source' => [
                'nullable',
                'string',
                'in:manual,gps,geocoding,map',
            ],
            'accuracy' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'address_verified' => [
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'latitude.required' => 'Latitude is required.',
            'latitude.numeric' => 'Latitude must be a number.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'Longitude is required.',
            'longitude.numeric' => 'Longitude must be a number.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'source.in' => 'Invalid coordinate source.',
            'accuracy.min' => 'Accuracy must be at least 0.',
            'accuracy.max' => 'Accuracy cannot exceed 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'source' => 'coordinate source',
            'accuracy' => 'accuracy',
            'address_verified' => 'address verification',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Round coordinates to 6 decimal places for consistency
        if ($this->has('latitude')) {
            $this->merge([
                'latitude' => round((float) $this->latitude, 6),
            ]);
        }

        if ($this->has('longitude')) {
            $this->merge([
                'longitude' => round((float) $this->longitude, 6),
            ]);
        }

        // Set default values
        if (! $this->has('source')) {
            $this->merge(['source' => 'manual']);
        }

        if (! $this->has('accuracy')) {
            $this->merge(['accuracy' => 100]);
        }

        if (! $this->has('address_verified')) {
            $this->merge(['address_verified' => false]);
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
