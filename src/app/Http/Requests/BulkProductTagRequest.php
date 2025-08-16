<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bulkManage', \Fereydooni\Shopping\app\Models\ProductTag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tags' => ['sometimes', 'required', 'array', 'min:1', 'max:100'],
            'tags.*.name' => ['required', 'string', 'max:255'],
            'tags.*.slug' => ['required', 'string', 'max:255'],
            'tags.*.description' => ['nullable', 'string', 'max:1000'],
            'tags.*.color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'tags.*.icon' => ['nullable', 'string', 'max:50'],
            'tags.*.is_active' => ['boolean'],
            'tags.*.is_featured' => ['boolean'],
            'tags.*.sort_order' => ['integer', 'min:0'],
            'tags.*.usage_count' => ['integer', 'min:0'],
            'tag_ids' => ['sometimes', 'required', 'array', 'min:1', 'max:100'],
            'tag_ids.*' => ['integer', 'exists:product_tags,id'],
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
            'tags.max' => 'Cannot process more than 100 tags at once.',
            'tags.*.name.required' => 'Tag name is required.',
            'tags.*.name.max' => 'Tag name cannot exceed 255 characters.',
            'tags.*.slug.required' => 'Tag slug is required.',
            'tags.*.slug.max' => 'Tag slug cannot exceed 255 characters.',
            'tags.*.description.max' => 'Tag description cannot exceed 1000 characters.',
            'tags.*.color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'tags.*.icon.max' => 'Icon name cannot exceed 50 characters.',
            'tags.*.sort_order.min' => 'Sort order must be a positive number.',
            'tags.*.usage_count.min' => 'Usage count must be a positive number.',
            'tag_ids.required' => 'Tag IDs are required.',
            'tag_ids.array' => 'Tag IDs must be an array.',
            'tag_ids.min' => 'At least one tag ID is required.',
            'tag_ids.max' => 'Cannot process more than 100 tags at once.',
            'tag_ids.*.integer' => 'Tag ID must be an integer.',
            'tag_ids.*.exists' => 'Selected tag does not exist.',
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
            'tag_ids' => 'tag IDs',
            'tag_ids.*' => 'tag ID',
        ];
    }
}
