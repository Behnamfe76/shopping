<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProviderLocation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider_id' => [
                'required',
                'integer',
                'exists:providers,id'
            ],
            'location_name' => [
                'required',
                'string',
                'max:255'
            ],
            'address' => [
                'required',
                'string',
                'max:255'
            ],
            'city' => [
                'required',
                'string',
                'max:100'
            ],
            'state' => [
                'required',
                'string',
                'max:100'
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20'
            ],
            'country' => [
                'required',
                'string',
                'size:2',
                'in:US,CA,MX,GB,DE,FR,IT,ES,NL,BE,CH,AT,SE,NO,DK,FI,PL,CZ,HU,RO,BG,HR,SI,SK,EE,LV,LT,MT,CY,GR,PT,IE,LU,AU,NZ,JP,CN,IN,BR,AR,CL,PE,CO,VE,EC,BO,PY,UY,GY,SR,GF,FK,ZA,EG,NG,KE,UG,TZ,ET,GH,CI,SN,ML,BF,NE,TD,SD,LY,TN,DZ,MA,MR,AO,CD,CG,GA,CM,CF,SS'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'is_primary' => [
                'boolean'
            ],
            'is_active' => [
                'boolean'
            ],
            'location_type' => [
                'required',
                'string',
                'in:headquarters,warehouse,store,office,factory,distribution_center,retail_outlet,service_center,other'
            ],
            'operating_hours' => [
                'nullable',
                'array'
            ],
            'operating_hours.*.day' => [
                'required_with:operating_hours',
                'string',
                'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'
            ],
            'operating_hours.*.open' => [
                'required_with:operating_hours',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'operating_hours.*.close' => [
                'required_with:operating_hours',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'timezone' => [
                'nullable',
                'string',
                'timezone'
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90'
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180'
            ],
            'contact_person' => [
                'nullable',
                'string',
                'max:100'
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/'
            ],
            'contact_email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'provider_id.required' => 'Provider is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'location_name.required' => 'Location name is required.',
            'location_name.max' => 'Location name cannot exceed 255 characters.',
            'address.required' => 'Address is required.',
            'address.max' => 'Address cannot exceed 255 characters.',
            'city.required' => 'City is required.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.required' => 'State is required.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'country.required' => 'Country is required.',
            'country.size' => 'Country must be a 2-character code.',
            'country.in' => 'Selected country is not supported.',
            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'email.email' => 'Email format is invalid.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'website.url' => 'Website URL format is invalid.',
            'website.max' => 'Website URL cannot exceed 255 characters.',
            'location_type.required' => 'Location type is required.',
            'location_type.in' => 'Selected location type is invalid.',
            'operating_hours.*.day.required_with' => 'Day is required when specifying operating hours.',
            'operating_hours.*.day.in' => 'Invalid day of the week.',
            'operating_hours.*.open.required_with' => 'Opening time is required when specifying operating hours.',
            'operating_hours.*.open.regex' => 'Opening time must be in HH:MM format.',
            'operating_hours.*.close.required_with' => 'Closing time is required when specifying operating hours.',
            'operating_hours.*.close.regex' => 'Closing time must be in HH:MM format.',
            'timezone.timezone' => 'Invalid timezone.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'contact_person.max' => 'Contact person name cannot exceed 100 characters.',
            'contact_phone.regex' => 'Contact phone number format is invalid.',
            'contact_phone.max' => 'Contact phone number cannot exceed 20 characters.',
            'contact_email.email' => 'Contact email format is invalid.',
            'contact_email.max' => 'Contact email cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'provider_id' => 'provider',
            'location_name' => 'location name',
            'address' => 'address',
            'city' => 'city',
            'state' => 'state',
            'postal_code' => 'postal code',
            'country' => 'country',
            'phone' => 'phone number',
            'email' => 'email address',
            'website' => 'website URL',
            'is_primary' => 'primary location',
            'is_active' => 'active status',
            'location_type' => 'location type',
            'operating_hours' => 'operating hours',
            'timezone' => 'timezone',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'contact_person' => 'contact person',
            'contact_phone' => 'contact phone',
            'contact_email' => 'contact email',
            'notes' => 'notes'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        $this->merge([
            'location_name' => trim($this->location_name),
            'address' => trim($this->address),
            'city' => trim($this->city),
            'state' => trim($this->state),
            'postal_code' => trim($this->postal_code),
            'phone' => trim($this->phone),
            'email' => trim($this->email),
            'website' => trim($this->website),
            'contact_person' => trim($this->contact_person),
            'contact_phone' => trim($this->contact_phone),
            'contact_email' => trim($this->contact_email),
            'notes' => trim($this->notes)
        ]);

        // Normalize email addresses
        if ($this->email) {
            $this->merge(['email' => strtolower($this->email)]);
        }

        if ($this->contact_email) {
            $this->merge(['contact_email' => strtolower($this->contact_email)]);
        }

        // Normalize website URL
        if ($this->website && !preg_match("~^(?:f|ht)tps?://~i", $this->website)) {
            $this->merge(['website' => 'https://' . $this->website]);
        }

        // Normalize country code
        if ($this->country) {
            $this->merge(['country' => strtoupper($this->country)]);
        }

        // Normalize location type
        if ($this->location_type) {
            $this->merge(['location_type' => strtolower($this->location_type)]);
        }

        // Set default values
        if (!$this->has('is_primary')) {
            $this->merge(['is_primary' => false]);
        }

        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
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
