<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:permissions,name|max:64',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ];
    }
}
