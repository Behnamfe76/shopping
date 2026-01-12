<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use App\Models\User;
use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|integer|exists:roles,id',
            'phone' => 'required|string|unique:users,phone',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
