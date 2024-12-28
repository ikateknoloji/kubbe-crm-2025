<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect([
            ['name' => 'admin', 'description' => 'Sistemi yöneten kişi'],
            ['name' => 'musteri', 'description' => 'Ürün veya hizmet alan kişi'],
            ['name' => 'tasarimci', 'description' => 'Tasarım işleri ile ilgilenen kişi'],
        ]);

        $roles->each(fn($role) => Role::updateOrCreate(
            ['name' => $role['name']],
            ['description' => $role['description']]
        ));
    }
}
