<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperatingHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateOperatingHours', $this->route('providerLocation'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'operating_hours' => [
                'required',
                'array',
                'min:1',
            ],
            'operating_hours.*.day' => [
                'required',
                'string',
                'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            ],
            'operating_hours.*.open' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'operating_hours.*.close' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'operating_hours.*.is_closed' => [
                'boolean',
            ],
            'operating_hours.*.notes' => [
                'nullable',
                'string',
                'max:200',
            ],
            'timezone' => [
                'nullable',
                'string',
                'timezone',
            ],
            'special_hours' => [
                'nullable',
                'array',
            ],
            'special_hours.*.date' => [
                'required_with:special_hours',
                'date',
                'after:today',
            ],
            'special_hours.*.open' => [
                'required_with:special_hours',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'special_hours.*.close' => [
                'required_with:special_hours',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'special_hours.*.is_closed' => [
                'boolean',
            ],
            'special_hours.*.reason' => [
                'nullable',
                'string',
                'max:200',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'operating_hours.required' => 'Operating hours are required.',
            'operating_hours.min' => 'At least one day must be specified.',
            'operating_hours.*.day.required' => 'Day is required for each operating hour entry.',
            'operating_hours.*.day.in' => 'Invalid day of the week.',
            'operating_hours.*.open.required' => 'Opening time is required.',
            'operating_hours.*.open.regex' => 'Opening time must be in HH:MM format.',
            'operating_hours.*.close.required' => 'Closing time is required.',
            'operating_hours.*.close.regex' => 'Closing time must be in HH:MM format.',
            'operating_hours.*.notes.max' => 'Notes cannot exceed 200 characters.',
            'timezone.timezone' => 'Invalid timezone.',
            'special_hours.*.date.required_with' => 'Date is required for special hours.',
            'special_hours.*.date.date' => 'Invalid date format.',
            'special_hours.*.date.after' => 'Special hours date must be in the future.',
            'special_hours.*.open.required_with' => 'Opening time is required for special hours.',
            'special_hours.*.open.regex' => 'Opening time must be in HH:MM format.',
            'special_hours.*.close.required_with' => 'Closing time is required for special hours.',
            'special_hours.*.close.regex' => 'Closing time must be in HH:MM format.',
            'special_hours.*.reason.max' => 'Reason cannot exceed 200 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'operating_hours' => 'operating hours',
            'operating_hours.*.day' => 'day',
            'operating_hours.*.open' => 'opening time',
            'operating_hours.*.close' => 'closing time',
            'operating_hours.*.is_closed' => 'closed status',
            'operating_hours.*.notes' => 'notes',
            'timezone' => 'timezone',
            'special_hours' => 'special hours',
            'special_hours.*.date' => 'date',
            'special_hours.*.open' => 'opening time',
            'special_hours.*.close' => 'closing time',
            'special_hours.*.is_closed' => 'closed status',
            'special_hours.*.reason' => 'reason',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize day names to lowercase
        if ($this->has('operating_hours')) {
            $normalizedHours = [];
            foreach ($this->operating_hours as $hour) {
                if (isset($hour['day'])) {
                    $hour['day'] = strtolower(trim($hour['day']));
                }
                if (isset($hour['notes'])) {
                    $hour['notes'] = trim($hour['notes']);
                }
                $normalizedHours[] = $hour;
            }
            $this->merge(['operating_hours' => $normalizedHours]);
        }

        // Normalize special hours
        if ($this->has('special_hours')) {
            $normalizedSpecial = [];
            foreach ($this->special_hours as $special) {
                if (isset($special['reason'])) {
                    $special['reason'] = trim($special['reason']);
                }
                $normalizedSpecial[] = $special;
            }
            $this->merge(['special_hours' => $normalizedSpecial]);
        }

        // Set default values
        if (! $this->has('timezone')) {
            $this->merge(['timezone' => 'UTC']);
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
