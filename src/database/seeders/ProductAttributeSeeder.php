<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color',
                'slug' => 'color',
                'description' => 'Product color options for visual customization',
                'type' => ProductAttributeType::COLOR,
                'input_type' => ProductAttributeInputType::COLOR,
                'is_required' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 1,
                'group' => 'Appearance',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Product Colors',
                'meta_description' => 'Choose from available color options',
                'values' => [
                    ['value' => 'Red', 'color_code' => '#FF0000', 'sort_order' => 1],
                    ['value' => 'Blue', 'color_code' => '#0000FF', 'sort_order' => 2],
                    ['value' => 'Green', 'color_code' => '#008000', 'sort_order' => 3],
                    ['value' => 'Black', 'color_code' => '#000000', 'sort_order' => 4, 'is_default' => true],
                    ['value' => 'White', 'color_code' => '#FFFFFF', 'sort_order' => 5],
                    ['value' => 'Yellow', 'color_code' => '#FFFF00', 'sort_order' => 6],
                    ['value' => 'Purple', 'color_code' => '#800080', 'sort_order' => 7],
                    ['value' => 'Orange', 'color_code' => '#FFA500', 'sort_order' => 8],
                ],
            ],
            [
                'name' => 'Size',
                'slug' => 'size',
                'description' => 'Product size variations for clothing and accessories',
                'type' => ProductAttributeType::SIZE,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => true,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 2,
                'group' => 'Dimensions',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Product Sizes',
                'meta_description' => 'Available size options',
                'values' => [
                    ['value' => 'XS', 'description' => 'Extra Small', 'sort_order' => 1],
                    ['value' => 'S', 'description' => 'Small', 'sort_order' => 2],
                    ['value' => 'M', 'description' => 'Medium', 'sort_order' => 3, 'is_default' => true],
                    ['value' => 'L', 'description' => 'Large', 'sort_order' => 4],
                    ['value' => 'XL', 'description' => 'Extra Large', 'sort_order' => 5],
                    ['value' => 'XXL', 'description' => '2X Large', 'sort_order' => 6],
                ],
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'description' => 'Materials used in product construction',
                'type' => ProductAttributeType::MATERIAL,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 3,
                'group' => 'Construction',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Product Materials',
                'meta_description' => 'Material composition and quality',
                'values' => [
                    ['value' => 'Cotton', 'description' => '100% Natural Cotton', 'sort_order' => 1],
                    ['value' => 'Polyester', 'description' => 'Synthetic Polyester Blend', 'sort_order' => 2],
                    ['value' => 'Wool', 'description' => 'Natural Wool Fiber', 'sort_order' => 3],
                    ['value' => 'Leather', 'description' => 'Genuine Leather', 'sort_order' => 4],
                    ['value' => 'Plastic', 'description' => 'Durable Plastic Material', 'sort_order' => 5],
                    ['value' => 'Metal', 'description' => 'Metal Construction', 'sort_order' => 6],
                    ['value' => 'Wood', 'description' => 'Natural Wood Material', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Storage Capacity',
                'slug' => 'storage',
                'description' => 'Storage capacity for electronic devices',
                'type' => ProductAttributeType::TEXT,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 4,
                'unit' => 'GB',
                'group' => 'Technical',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Storage Options',
                'meta_description' => 'Available storage capacity options',
                'values' => [
                    ['value' => '32GB', 'description' => '32 Gigabytes', 'sort_order' => 1],
                    ['value' => '64GB', 'description' => '64 Gigabytes', 'sort_order' => 2],
                    ['value' => '128GB', 'description' => '128 Gigabytes', 'sort_order' => 3, 'is_default' => true],
                    ['value' => '256GB', 'description' => '256 Gigabytes', 'sort_order' => 4],
                    ['value' => '512GB', 'description' => '512 Gigabytes', 'sort_order' => 5],
                    ['value' => '1TB', 'description' => '1 Terabyte', 'sort_order' => 6],
                ],
            ],
            [
                'name' => 'Screen Size',
                'slug' => 'screen-size',
                'description' => 'Display screen size for monitors and devices',
                'type' => ProductAttributeType::DIMENSION,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 5,
                'unit' => 'inches',
                'group' => 'Display',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Screen Sizes',
                'meta_description' => 'Available display screen sizes',
                'values' => [
                    ['value' => '13"', 'description' => '13 inch display', 'sort_order' => 1],
                    ['value' => '14"', 'description' => '14 inch display', 'sort_order' => 2],
                    ['value' => '15"', 'description' => '15 inch display', 'sort_order' => 3],
                    ['value' => '16"', 'description' => '16 inch display', 'sort_order' => 4],
                    ['value' => '17"', 'description' => '17 inch display', 'sort_order' => 5],
                    ['value' => '21"', 'description' => '21 inch display', 'sort_order' => 6],
                    ['value' => '24"', 'description' => '24 inch display', 'sort_order' => 7, 'is_default' => true],
                    ['value' => '27"', 'description' => '27 inch display', 'sort_order' => 8],
                ],
            ],
            [
                'name' => 'Processor',
                'slug' => 'processor',
                'description' => 'CPU processor specifications for electronic devices',
                'type' => ProductAttributeType::TEXT,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 6,
                'group' => 'Performance',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Processor Options',
                'meta_description' => 'Available CPU processor options',
                'values' => [
                    ['value' => 'Intel i3', 'description' => 'Intel Core i3 Processor', 'sort_order' => 1],
                    ['value' => 'Intel i5', 'description' => 'Intel Core i5 Processor', 'sort_order' => 2, 'is_default' => true],
                    ['value' => 'Intel i7', 'description' => 'Intel Core i7 Processor', 'sort_order' => 3],
                    ['value' => 'Intel i9', 'description' => 'Intel Core i9 Processor', 'sort_order' => 4],
                    ['value' => 'AMD Ryzen 3', 'description' => 'AMD Ryzen 3 Processor', 'sort_order' => 5],
                    ['value' => 'AMD Ryzen 5', 'description' => 'AMD Ryzen 5 Processor', 'sort_order' => 6],
                    ['value' => 'AMD Ryzen 7', 'description' => 'AMD Ryzen 7 Processor', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Weight',
                'slug' => 'weight',
                'description' => 'Product weight specification',
                'type' => ProductAttributeType::WEIGHT,
                'input_type' => ProductAttributeInputType::NUMBER,
                'is_required' => false,
                'is_searchable' => false,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 7,
                'unit' => 'kg',
                'group' => 'Physical',
                'validation_rules' => 'numeric|min:0|max:1000',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Product Weight',
                'meta_description' => 'Weight specification in kilograms',
                'values' => [], // No predefined values for weight
            ],
            [
                'name' => 'Warranty Period',
                'slug' => 'warranty',
                'description' => 'Product warranty duration',
                'type' => ProductAttributeType::TEXT,
                'input_type' => ProductAttributeInputType::SELECT,
                'is_required' => false,
                'is_searchable' => false,
                'is_filterable' => true,
                'is_comparable' => true,
                'is_visible' => true,
                'sort_order' => 8,
                'unit' => 'months',
                'group' => 'Service',
                'is_system' => false,
                'is_active' => true,
                'meta_title' => 'Warranty Options',
                'meta_description' => 'Available warranty periods',
                'values' => [
                    ['value' => '6 months', 'description' => '6 month warranty', 'sort_order' => 1],
                    ['value' => '1 year', 'description' => '1 year warranty', 'sort_order' => 2, 'is_default' => true],
                    ['value' => '2 years', 'description' => '2 year warranty', 'sort_order' => 3],
                    ['value' => '3 years', 'description' => '3 year warranty', 'sort_order' => 4],
                    ['value' => '5 years', 'description' => '5 year warranty', 'sort_order' => 5],
                ],
            ],
        ];

        foreach ($attributes as $attributeData) {
            $values = $attributeData['values'] ?? [];
            unset($attributeData['values']);

            // Create the attribute
            $attribute = ProductAttribute::create($attributeData);

            // Create attribute values if any
            foreach ($values as $index => $valueData) {
                if (is_string($valueData)) {
                    // Handle simple string values (backward compatibility)
                    $valueData = ['value' => $valueData];
                }

                ProductAttributeValue::create(array_merge([
                    'attribute_id' => $attribute->id,
                    'sort_order' => $valueData['sort_order'] ?? ($index + 1),
                    'is_active' => $valueData['is_active'] ?? true,
                    'is_default' => $valueData['is_default'] ?? false,
                ], $valueData));
            }
        }
    }
}
