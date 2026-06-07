<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Aquí estás llamando a tu seeder personalizado
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
