<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('providerLocation'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $locationId = $this->route('providerLocation')->id;

        return [
            'provider_id' => [
                'sometimes',
                'integer',
                'exists:providers,id'
            ],
            'location_name' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'address' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'city' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'state' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20'
            ],
            'country' => [
                'sometimes',
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
                'sometimes',
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
            'provider_id.exists' => 'Selected provider does not exist.',
            'location_name.max' => 'Location name cannot exceed 255 characters.',
            'address.max' => 'Address cannot exceed 255 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'country.size' => 'Country must be a 2-character code.',
            'country.in' => 'Selected country is not supported.',
            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'email.email' => 'Email format is invalid.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'website.url' => 'Website URL format is invalid.',
            'website.max' => 'Website URL cannot exceed 255 characters.',
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
        $data = [];

        // Only process fields that are present in the request
        if ($this->has('location_name')) {
            $data['location_name'] = trim($this->location_name);
        }

        if ($this->has('address')) {
            $data['address'] = trim($this->address);
        }

        if ($this->has('city')) {
            $data['city'] = trim($this->city);
        }

        if ($this->has('state')) {
            $data['state'] = trim($this->state);
        }

        if ($this->has('postal_code')) {
            $data['postal_code'] = trim($this->postal_code);
        }

        if ($this->has('phone')) {
            $data['phone'] = trim($this->phone);
        }

        if ($this->has('email')) {
            $data['email'] = strtolower(trim($this->email));
        }

        if ($this->has('website')) {
            $website = trim($this->website);
            if ($website && !preg_match("~^(?:f|ht)tps?://~i", $website)) {
                $website = 'https://' . $website;
            }
            $data['website'] = $website;
        }

        if ($this->has('contact_person')) {
            $data['contact_person'] = trim($this->contact_person);
        }

        if ($this->has('contact_phone')) {
            $data['contact_phone'] = trim($this->contact_phone);
        }

        if ($this->has('contact_email')) {
            $data['contact_email'] = strtolower(trim($this->contact_email));
        }

        if ($this->has('notes')) {
            $data['notes'] = trim($this->notes);
        }

        if ($this->has('country')) {
            $data['country'] = strtoupper(trim($this->country));
        }

        if ($this->has('location_type')) {
            $data['location_type'] = strtolower(trim($this->location_type));
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
