<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleProductMetaStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $meta = $this->route('meta');
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'togglePublic':
                return $this->user()->can('togglePublic', $meta);
            case 'toggleSearchable':
                return $this->user()->can('toggleSearchable', $meta);
            case 'toggleFilterable':
                return $this->user()->can('toggleFilterable', $meta);
            default:
                return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // No additional validation needed for toggle operations
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // No custom messages needed
        ];
    }
}
