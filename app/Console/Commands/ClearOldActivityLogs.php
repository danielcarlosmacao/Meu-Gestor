<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use App\Models\Option; // certifique-se que o model Option existe

class ClearOldActivityLogs extends Command
{
    protected $signature = 'logs:clear {days=90}';
    protected $description = 'Excluir logs antigos de atividade se habilitado na Option';

    public function handle()
    {
        // 🔎 Verifica na tabela options se está habilitado
        $option = Option::where('reference', 'clearLog')->first();

        if (!$option || strtolower($option->value) !== 'yes') {
            $this->info("⚠️ Limpeza de logs está desativada (Option clearLog != yes). Nenhuma ação realizada.");
            return self::SUCCESS;
        }

        $days = (int) $this->argument('days');
        $date = Carbon::now()->subDays($days);

        $deleted = Activity::where('created_at', '<', $date)->delete();

        $this->info("✅ {$deleted} logs excluídos com sucesso (anteriores a {$days} dias).");

        return self::SUCCESS;
    }
}
