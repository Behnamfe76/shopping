<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\Team::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return TeamDTO::rules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return TeamDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'team name',
            'code' => 'team code',
            'description' => 'team description',
            'department_id' => 'department',
            'location' => 'team location',
            'member_limit' => 'member limit',
            'is_active' => 'active status',
            'status' => 'team status',
            'metadata' => 'metadata',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'member_limit' => $this->integer('member_limit', null),
        ]);
    }
}
