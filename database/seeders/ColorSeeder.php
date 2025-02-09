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
            ['color_name' => 'Beyaz', 'color_hex' => '#D5C5B5'],
            ['color_name' => 'Siyah', 'color_hex' => '#000000'],
            ['color_name' => 'Kırmızı', 'color_hex' => '#B3293D'],
            
            ['color_name' => 'Turuncu', 'color_hex' => '#B94D0D'],
            ['color_name' => 'Sarı', 'color_hex' => '#D39D30'],
            ['color_name' => 'Limon Sarısı', 'color_hex' => '#FFD000'],

            ['color_name' => 'Turkuaz', 'color_hex' => '#40E0D0'],
            ['color_name' => 'Lacivert', 'color_hex' => '#141823'],
            ['color_name' => 'Açık Mavi', 'color_hex' => '#ADD8E6'],
            ['color_name' => 'Saks Mavisi', 'color_hex' => '#0000C8'],

            ['color_name' => 'Yeşil', 'color_hex' => '#688930'],
            ['color_name' => 'Haki Yeşili', 'color_hex' => '#1C4E35'],
            ['color_name' => 'Fıstık Yeşili', 'color_hex' => '#A8AB3A'],
            ['color_name' => 'Koyu Yeşil', 'color_hex' => '#12231E'],

            ['color_name' => 'Toz Pembe', 'color_hex' => '#DC9881'],
            ['color_name' => 'Fuşka', 'color_hex' => '#A82350'],
            ['color_name' => 'Bordo', 'color_hex' => '#800000'],
            ['color_name' => 'Mor', 'color_hex' => '#783464'],
            ['color_name' => 'Kahverengi', 'color_hex' => '#A52A2A'],
            ['color_name' => 'Gri', 'color_hex' => '#4B4746'],
            ['color_name' => 'Krem', 'color_hex' => '#BFAB86'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate($color);
        }
    }
}
