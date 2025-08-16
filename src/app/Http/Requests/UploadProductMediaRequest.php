<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('uploadMedia', $this->route('product'));
    }

    public function rules(): array
    {
        return [
            'media' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
            'collection' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'media.required' => 'Media file is required.',
            'media.file' => 'The media must be a valid file.',
            'media.mimes' => 'The media must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'media.max' => 'The media may not be greater than 10MB.',
        ];
    }
}
