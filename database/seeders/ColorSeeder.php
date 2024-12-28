<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['color_name' => 'Kırmızı', 'color_hex' => '#FF0000'],
            ['color_name' => 'Yeşil', 'color_hex' => '#00FF00'],
            ['color_name' => 'Mavi', 'color_hex' => '#0000FF'],
            ['color_name' => 'Sarı', 'color_hex' => '#FFFF00'],
            ['color_name' => 'Turuncu', 'color_hex' => '#FFA500'],
            ['color_name' => 'Pembe', 'color_hex' => '#FFC0CB'],
            ['color_name' => 'Mor', 'color_hex' => '#800080'],
            ['color_name' => 'Kahverengi', 'color_hex' => '#A52A2A'],
            ['color_name' => 'Gri', 'color_hex' => '#808080'],
            ['color_name' => 'Siyah', 'color_hex' => '#000000'],
            ['color_name' => 'Beyaz', 'color_hex' => '#FFFFFF'],
            ['color_name' => 'Açık Mavi', 'color_hex' => '#ADD8E6'],
            ['color_name' => 'Lacivert', 'color_hex' => '#000080'],
            ['color_name' => 'Turkuaz', 'color_hex' => '#40E0D0'],
            ['color_name' => 'Altın', 'color_hex' => '#FFD700'],
            ['color_name' => 'Gümüş', 'color_hex' => '#C0C0C0'],
            ['color_name' => 'Zeytin Yeşili', 'color_hex' => '#808000'],
            ['color_name' => 'Bordo', 'color_hex' => '#800000'],
            ['color_name' => 'Açık Yeşil', 'color_hex' => '#90EE90'],
            ['color_name' => 'Açık Pembe', 'color_hex' => '#FFB6C1'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate($color);
        }
    }
}
