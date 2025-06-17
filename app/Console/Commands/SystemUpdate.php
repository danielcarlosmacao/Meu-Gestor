<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SystemUpdate extends Command
{
    // Nome do comando chamado via Artisan
    protected $signature = 'system:update';

    protected $description = 'Atualiza o sistema via git pull, migrate e cache clear';

    public function handle()
    {
        $this->info('Atualizando sistema...');

        $projectRoot = base_path();

        exec("git config --global --add safe.directory $projectRoot");
        exec("cd $projectRoot && git stash");
        exec("cd $projectRoot && git pull origin main");

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');

        $this->info('Sistema atualizado com sucesso.');
    }
    
}
