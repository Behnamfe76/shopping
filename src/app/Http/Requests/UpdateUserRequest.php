<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use App\Models\User;
use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'required|integer|exists:roles,id',
            'phone' => ['nullable', 'string', Rule::unique('users', 'phone')->ignore($this->user->id)],
            'name' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
