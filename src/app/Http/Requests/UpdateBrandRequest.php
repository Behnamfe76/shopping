<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Validation\Rule;
use Fereydooni\Shopping\app\DTOs\BrandDTO;
use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\Enums\BrandType;
use Fereydooni\Shopping\app\Enums\BrandStatus;

class UpdateBrandRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('brands')->ignore($this->brand->id)],
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'founded_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'headquarters' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url|max:500',
            'banner_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'status' => 'nullable|in:' . implode(',', array_column(BrandStatus::cases(), 'value')),
            'type' => 'nullable|in:' . implode(',', array_column(BrandType::cases(), 'value')),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return BrandDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'brand name',
            'slug' => 'brand slug',
            'description' => 'brand description',
            'website' => 'brand website',
            'email' => 'brand email',
            'phone' => 'brand phone',
            'founded_year' => 'founded year',
            'headquarters' => 'brand headquarters',
            'logo_url' => 'logo URL',
            'banner_url' => 'banner URL',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'sort_order' => 'sort order',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name)
            ]);
        }

        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_featured' => $this->boolean('is_featured', false),
            'sort_order' => $this->integer('sort_order', 0),
        ]);
    }
}
