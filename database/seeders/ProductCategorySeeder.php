<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Mikrofon Süngeri'],
            ['name' => 'Reklam Küpü'],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate($category);
        }
    }
}
