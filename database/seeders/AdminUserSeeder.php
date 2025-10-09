<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria o usuário admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => bcrypt('12345678'), // Senha padrão (mude se quiser)
            ]
        );

        // Garante que a role 'administrator' já exista
        $role = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        // Atribui a role ao usuário
        $admin->assignRole($role);

        $this->command->info('Usuário administrador criado: admin@admin.com / senha: 12345678');
    
    }
}
