<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleProductDiscountStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toggleActive', $this->route('discount'));
    }

    public function rules(): array
    {
        return [];
    }
}
