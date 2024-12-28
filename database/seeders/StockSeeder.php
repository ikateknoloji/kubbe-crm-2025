<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Stock;
use App\Models\ProductType;
use App\Models\Color;
class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = ProductType::all();
        $colors = Color::all();

        foreach ($productTypes as $productType) {
            foreach ($colors as $color) {
                Stock::factory()->create([
                    'product_type_id' => $productType->id,
                    'color_id' => $color->id,
                ]);
            }
        }
    }
}
