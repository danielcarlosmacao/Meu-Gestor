<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemUpdateHook
{
    public static function run(): void
    {
        Log::info('===> Executando SystemUpdateHook...');

        try {

            $newPermissions = [
                // 'reports.export',
                'administrator.vpn',
            ];

            if (!empty($newPermissions)) {
                // Cria permissões se não existirem
                foreach ($newPermissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }

                // Atribui ao administrador
                $adminRole = Role::where('name', 'administrator')->first();
                if ($adminRole) {
                    $adminRole->givePermissionTo($newPermissions);
                }

                Log::info('[SystemUpdateHook] Novas permissões adicionadas.');
            } else {
                Log::info('[SystemUpdateHook] Nenhuma nova permissão a ser criada.');
            }



        } catch (\Throwable $e) {
            Log::error('[SystemUpdateHook] Erro ao executar hook', [
                'erro' => $e->getMessage(),
            ]);
        }

        Log::info('===> SystemUpdateHook finalizado.');
    }
}
