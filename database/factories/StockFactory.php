<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Stock;
use App\Models\ProductType;
use App\Models\Color;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    protected $model = Stock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomElement([
            0, 
            $this->faker->numberBetween(1, 4), 
            $this->faker->numberBetween(5, 50), 
        ]);

        return [
            'product_type_id' => ProductType::all()->random()->id,
            'color_id' => Color::all()->random()->id,
            'quantity' => $quantity,
        ];
    }
}
