<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color',
                'slug' => 'color',
                'values' => ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Purple', 'Orange'],
            ],
            [
                'name' => 'Size',
                'slug' => 'size',
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'values' => ['Cotton', 'Polyester', 'Wool', 'Leather', 'Plastic', 'Metal', 'Wood'],
            ],
            [
                'name' => 'Storage',
                'slug' => 'storage',
                'values' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'],
            ],
            [
                'name' => 'Screen Size',
                'slug' => 'screen-size',
                'values' => ['13"', '14"', '15"', '16"', '17"', '21"', '24"', '27"'],
            ],
            [
                'name' => 'Processor',
                'slug' => 'processor',
                'values' => ['Intel i3', 'Intel i5', 'Intel i7', 'Intel i9', 'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7'],
            ],
        ];

        foreach ($attributes as $attributeData) {
            $values = $attributeData['values'];
            unset($attributeData['values']);
            
            $attribute = ProductAttribute::create($attributeData);
            
            foreach ($values as $value) {
                ProductAttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                ]);
            }
        }
    }
}
