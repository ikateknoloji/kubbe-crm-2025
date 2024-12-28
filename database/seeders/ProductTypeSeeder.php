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
            ['product_type' => 'Üçgen', 'name' => 'Mikrofon Süngeri'],
            ['product_type' => 'Dörtgen', 'name' => 'Mikrofon Süngeri'],
            ['product_type' => 'Silindir', 'name' => 'Mikrofon Süngeri'],
            ['product_type' => 'Kısa Silindir', 'name' => 'Mikrofon Süngeri'],
            ['product_type' => 'Top', 'name' => 'Mikrofon Süngeri'],
            ['product_type' => 'Üçgen Sünger', 'name' => 'Reklam Küpü'],
            ['product_type' => 'Dörtgen Sünger', 'name' => 'Reklam Küpü'],
            ['product_type' => 'Plastik Üçgen', 'name' => 'Reklam Küpü'],
            ['product_type' => 'Plastik Kare', 'name' => 'Reklam Küpü'],
        ];

        foreach ($types as $type) {
            $name = ProductCategory::firstOrCreate(['name' => $type['name']]);
            ProductType::firstOrCreate([
                'product_type' => $type['product_type'],
                'product_category_id' => $name->id,
            ]);
        }

    }
}
