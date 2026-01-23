<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de permissões
        $permissions = [

            //permissoes gerais
            'administrator.user',
            'administrator.options',

            // Permissões de Torres
            'towers.view',
            'towers.manage',
            'towers.create',
            'towers.edit',
            'towers.delete',
            'towers.maintenance',

            // Permissões de Frotas
            'fleets.view',
            'fleets.create',
            'fleets.edit',
            'fleets.delete',

            // Permissões de Ferias
            'vacations.view',
            'vacations.create',
            'vacations.edit',
            'vacations.delete',
            'collaborators.view',
            'collaborators.create',
            'collaborators.edit',
            'collaborators.delete',

            // Permissões de calendario
            'vacation_manager.calendar',

            //permição serviços
            'service.view',
            'service.create',
            'service.edit',
            'service.delete',

            //extras
            'extra.view',
            'recipients.view',
            'notification.view',
            'api.nfe',

            //stock
            'stock.view',
            'stock.items.create',
            'stock.items.edit',
            'stock.items.delete',
            'stock.movements.create',

            //collaborators.courses
            'collaborators.courses.view',
            'collaborators.courses.view.pdf',
            'collaborators.courses.create',
            'collaborators.courses.edit',
            'collaborators.courses.delete',

            //tasks
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
        ];

        // Criar permissões (se não existirem)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar a role administrator (se não existir)
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        // Atribuir todas as permissões para a role administrator
        $adminRole->syncPermissions($permissions);

        $this->command->info('Permissões e role Administrator criadas com sucesso!');
    }
}
