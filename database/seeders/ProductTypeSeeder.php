<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProductType;
use App\Models\ProductCategory;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['product_type' => 'Üçgen', 'category' => 'Mikrofon Süngeri'],
            ['product_type' => 'Dörtgen', 'category' => 'Mikrofon Süngeri'],
            ['product_type' => 'Silindir', 'category' => 'Mikrofon Süngeri'],
            ['product_type' => 'Kısa Silindir', 'category' => 'Mikrofon Süngeri'],
            ['product_type' => 'Top', 'category' => 'Mikrofon Süngeri'],
            ['product_type' => 'Reklam Üçgen Sünger', 'category' => 'Reklam Küpü'],
            ['product_type' => 'Reklam Dörtgen Sünger', 'category' => 'Reklam Küpü'],
            ['product_type' => 'Reklam Plastik Üçgen', 'category' => 'Reklam Küpü'],
            ['product_type' => 'Reklam Plastik Kare', 'category' => 'Reklam Küpü'],
        ];

        foreach ($types as $type) {
            $name = ProductCategory::firstOrCreate(['category' => $type['category']]);
            ProductType::firstOrCreate([
                'product_type' => $type['product_type'],
                'product_category_id' => $name->id,
            ]);
        }

    }
}
