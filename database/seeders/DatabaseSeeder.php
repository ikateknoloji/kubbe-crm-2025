<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        $this->call([
            RolesTableSeeder::class,
            ProductCategorySeeder::class,
            ProductTypeSeeder::class,
            ColorSeeder::class,
            StockSeeder::class,
                    ]);
         
     
         Stock::query()->update(['quantity' => DB::raw('quantity + 100')]);

    }
}
