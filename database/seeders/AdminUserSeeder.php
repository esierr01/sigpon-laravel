<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 1, // Rol de administrador
            'active' => true,
            'create_for' => null, // Usuario root, no fue creado por nadie
            'last_login' => null,
            'remember_token' => null,
        ]);

        // Opcional: Mostrar mensaje en consola
        $this->command->info('Usuario administrador creado exitosamente!');
        $this->command->info('Email: admin@sistema.com');
        $this->command->info('Contraseña: admin123');
    }
}
