<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Illuminate\Database\Eloquent\Collection;

trait HasAttributeManagement
{
    /**
     * Get all attribute types
     */
    public function getAttributeTypes(): Collection
    {
        return collect(ProductAttributeType::cases())->map(function ($type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
                'description' => $type->description(),
                'is_numeric' => $type->isNumeric(),
                'is_date' => $type->isDate(),
                'is_text' => $type->isText(),
                'is_selection' => $type->isSelection(),
                'is_boolean' => $type->isBoolean(),
                'is_custom' => $type->isCustom(),
            ];
        });
    }

    /**
     * Get all input types
     */
    public function getInputTypes(): Collection
    {
        return collect(ProductAttributeInputType::cases())->map(function ($inputType) {
            return [
                'value' => $inputType->value,
                'label' => $inputType->label(),
                'description' => $inputType->description(),
                'is_text_input' => $inputType->isTextInput(),
                'is_numeric_input' => $inputType->isNumericInput(),
                'is_date_input' => $inputType->isDateInput(),
                'is_selection_input' => $inputType->isSelectionInput(),
                'is_single_selection' => $inputType->isSingleSelection(),
                'is_multiple_selection' => $inputType->isMultipleSelection(),
                'is_file_input' => $inputType->isFileInput(),
                'is_hidden_input' => $inputType->isHiddenInput(),
                'is_custom_input' => $inputType->isCustomInput(),
            ];
        });
    }

    /**
     * Get attribute groups
     */
    public function getAttributeGroups(): Collection
    {
        return $this->repository->getAttributeGroups();
    }

    /**
     * Validate attribute type and input type compatibility
     */
    public function validateAttributeTypeCompatibility(string $type, string $inputType): bool
    {
        $attributeType = ProductAttributeType::from($type);
        $inputTypeEnum = ProductAttributeInputType::from($inputType);

        // Check compatibility based on type
        if ($attributeType->isNumeric() && ! $inputTypeEnum->isNumericInput()) {
            return false;
        }

        if ($attributeType->isDate() && ! $inputTypeEnum->isDateInput()) {
            return false;
        }

        if ($attributeType->isText() && ! $inputTypeEnum->isTextInput()) {
            return false;
        }

        if ($attributeType->isSelection() && ! $inputTypeEnum->isSelectionInput()) {
            return false;
        }

        if ($attributeType->isBoolean() && ! in_array($inputType, ['checkbox', 'radio', 'select'])) {
            return false;
        }

        return true;
    }

    /**
     * Get compatible input types for a given attribute type
     */
    public function getCompatibleInputTypes(string $type): Collection
    {
        $attributeType = ProductAttributeType::from($type);
        $inputTypes = $this->getInputTypes();

        return $inputTypes->filter(function ($inputType) {
            return $this->validateAttributeTypeCompatibility($type, $inputType['value']);
        });
    }

    /**
     * Get compatible attribute types for a given input type
     */
    public function getCompatibleAttributeTypes(string $inputType): Collection
    {
        $inputTypeEnum = ProductAttributeInputType::from($inputType);
        $attributeTypes = $this->getAttributeTypes();

        return $attributeTypes->filter(function ($attributeType) use ($inputType) {
            return $this->validateAttributeTypeCompatibility($attributeType['value'], $inputType);
        });
    }

    /**
     * Get validation rules for attribute type
     */
    public function getValidationRulesForType(string $type): array
    {
        $attributeType = ProductAttributeType::from($type);

        $rules = [];

        if ($attributeType->isNumeric()) {
            $rules[] = 'numeric';
        }

        if ($attributeType->isDate()) {
            $rules[] = 'date';
        }

        if ($attributeType->value === 'email') {
            $rules[] = 'email';
        }

        if ($attributeType->value === 'url') {
            $rules[] = 'url';
        }

        if ($attributeType->value === 'phone') {
            $rules[] = 'regex:/^[\+]?[1-9][\d]{0,15}$/';
        }

        return $rules;
    }

    /**
     * Get validation rules for input type
     */
    public function getValidationRulesForInputType(string $inputType): array
    {
        $inputTypeEnum = ProductAttributeInputType::from($inputType);

        $rules = [];

        if ($inputTypeEnum->isNumericInput()) {
            $rules[] = 'numeric';
        }

        if ($inputTypeEnum->isDateInput()) {
            $rules[] = 'date';
        }

        if ($inputType === 'email') {
            $rules[] = 'email';
        }

        if ($inputType === 'url') {
            $rules[] = 'url';
        }

        if ($inputType === 'tel') {
            $rules[] = 'regex:/^[\+]?[1-9][\d]{0,15}$/';
        }

        if ($inputType === 'file') {
            $rules[] = 'file';
        }

        return $rules;
    }

    /**
     * Get default validation rules for attribute
     */
    public function getDefaultValidationRules(string $type, string $inputType): array
    {
        $typeRules = $this->getValidationRulesForType($type);
        $inputTypeRules = $this->getValidationRulesForInputType($inputType);

        return array_merge($typeRules, $inputTypeRules);
    }
}
