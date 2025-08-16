<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('import', \Fereydooni\Shopping\app\Models\ProductTag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tags' => ['required', 'array', 'min:1', 'max:1000'],
            'tags.*.name' => ['required', 'string', 'max:255'],
            'tags.*.slug' => ['required', 'string', 'max:255'],
            'tags.*.description' => ['nullable', 'string', 'max:1000'],
            'tags.*.color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'tags.*.icon' => ['nullable', 'string', 'max:50'],
            'tags.*.is_active' => ['boolean'],
            'tags.*.is_featured' => ['boolean'],
            'tags.*.sort_order' => ['integer', 'min:0'],
            'tags.*.usage_count' => ['integer', 'min:0'],
            'overwrite' => ['boolean'],
            'skip_duplicates' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tags.required' => 'Tags data is required.',
            'tags.array' => 'Tags must be an array.',
            'tags.min' => 'At least one tag is required.',
            'tags.max' => 'Cannot import more than 1000 tags at once.',
            'tags.*.name.required' => 'Tag name is required.',
            'tags.*.name.max' => 'Tag name cannot exceed 255 characters.',
            'tags.*.slug.required' => 'Tag slug is required.',
            'tags.*.slug.max' => 'Tag slug cannot exceed 255 characters.',
            'tags.*.description.max' => 'Tag description cannot exceed 1000 characters.',
            'tags.*.color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'tags.*.icon.max' => 'Icon name cannot exceed 50 characters.',
            'tags.*.sort_order.min' => 'Sort order must be a positive number.',
            'tags.*.usage_count.min' => 'Usage count must be a positive number.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tags' => 'tags',
            'tags.*.name' => 'tag name',
            'tags.*.slug' => 'tag slug',
            'tags.*.description' => 'tag description',
            'tags.*.color' => 'tag color',
            'tags.*.icon' => 'tag icon',
            'tags.*.is_active' => 'active status',
            'tags.*.is_featured' => 'featured status',
            'tags.*.sort_order' => 'sort order',
            'tags.*.usage_count' => 'usage count',
            'overwrite' => 'overwrite',
            'skip_duplicates' => 'skip duplicates',
        ];
    }
}
