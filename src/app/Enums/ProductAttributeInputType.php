<?php

namespace Fereydooni\Shopping\app\Enums;

enum ProductAttributeInputType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case URL = 'url';
    case PASSWORD = 'password';
    case TEL = 'tel';
    case DATE = 'date';
    case DATETIME_LOCAL = 'datetime-local';
    case TIME = 'time';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';
    case COLOR = 'color';
    case RANGE = 'range';
    case FILE = 'file';
    case HIDDEN = 'hidden';
    case SEARCH = 'search';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text Input',
            self::TEXTAREA => 'Text Area',
            self::NUMBER => 'Number Input',
            self::EMAIL => 'Email Input',
            self::URL => 'URL Input',
            self::PASSWORD => 'Password Input',
            self::TEL => 'Telephone Input',
            self::DATE => 'Date Picker',
            self::DATETIME_LOCAL => 'Date & Time Picker',
            self::TIME => 'Time Picker',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio Button',
            self::SELECT => 'Dropdown Select',
            self::MULTISELECT => 'Multi-Select',
            self::COLOR => 'Color Picker',
            self::RANGE => 'Range Slider',
            self::FILE => 'File Upload',
            self::HIDDEN => 'Hidden Input',
            self::SEARCH => 'Search Input',
            self::CUSTOM => 'Custom Input',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TEXT => 'Single line text input',
            self::TEXTAREA => 'Multi-line text input',
            self::NUMBER => 'Numeric input with validation',
            self::EMAIL => 'Email address input with validation',
            self::URL => 'URL input with validation',
            self::PASSWORD => 'Password input (masked)',
            self::TEL => 'Telephone number input',
            self::DATE => 'Date selection picker',
            self::DATETIME_LOCAL => 'Date and time selection picker',
            self::TIME => 'Time selection picker',
            self::CHECKBOX => 'Single checkbox (true/false)',
            self::RADIO => 'Radio button group',
            self::SELECT => 'Single selection dropdown',
            self::MULTISELECT => 'Multiple selection dropdown',
            self::COLOR => 'Color picker input',
            self::RANGE => 'Range slider with min/max values',
            self::FILE => 'File upload input',
            self::HIDDEN => 'Hidden input field',
            self::SEARCH => 'Search input with suggestions',
            self::CUSTOM => 'Custom input implementation',
        };
    }

    public function isTextInput(): bool
    {
        return in_array($this, [self::TEXT, self::TEXTAREA, self::EMAIL, self::URL, self::PASSWORD, self::TEL, self::SEARCH]);
    }

    public function isNumericInput(): bool
    {
        return in_array($this, [self::NUMBER, self::RANGE]);
    }

    public function isDateInput(): bool
    {
        return in_array($this, [self::DATE, self::DATETIME_LOCAL, self::TIME]);
    }

    public function isSelectionInput(): bool
    {
        return in_array($this, [self::CHECKBOX, self::RADIO, self::SELECT, self::MULTISELECT]);
    }

    public function isSingleSelection(): bool
    {
        return in_array($this, [self::RADIO, self::SELECT]);
    }

    public function isMultipleSelection(): bool
    {
        return in_array($this, [self::CHECKBOX, self::MULTISELECT]);
    }

    public function isFileInput(): bool
    {
        return $this === self::FILE;
    }

    public function isHiddenInput(): bool
    {
        return $this === self::HIDDEN;
    }

    public function isCustomInput(): bool
    {
        return $this === self::CUSTOM;
    }
}
