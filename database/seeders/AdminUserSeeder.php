<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'create_for' => 'sigpon',
        ]);

        // Crear usuario editor
        User::create([
            'name' => 'Pedro Perez',
            'email' => 'editor@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('editor123'),
            'role' => 2, // Rol de editor (Corregido)
            'active' => true,
            'create_for' => 'sigpon',
        ]);

        // Crear usuario visitante
        User::create([
            'name' => 'Luiz Muñoz',
            'email' => 'visitante@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('visitante123'),
            'role' => 3, // Rol de visitante (Corregido, asumiendo que es el rol 3)
            'active' => true,
            'create_for' => 'sigpon',
        ]);

        // Opcional: Mostrar mensaje en consola
        $this->command->info('Usuarios admin, editor y visitante creados exitosamente!');
    }
}
