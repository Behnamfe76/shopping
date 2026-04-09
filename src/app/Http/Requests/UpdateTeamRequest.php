<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('team'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = TeamDTO::rules();
        $team = $this->route('team');

        // Make all fields optional for updates
        $rules['name'] = 'sometimes|required|string|max:255';
        $rules['code'] = 'sometimes|required|string|max:50|unique:teams,code,'.$team->id;
        $rules['department_id'] = 'sometimes|required|integer|exists:departments,id';
        $rules['status'] = 'sometimes|required|in:'.implode(',', array_column(\Fereydooni\Shopping\app\Enums\TeamStatus::cases(), 'value'));

        return $rules;
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
}
