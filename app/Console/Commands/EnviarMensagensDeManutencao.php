<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsappService;
use App\Models\Maintenance;
use App\Models\Recipient;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarMensagensDeManutencao extends Command
{
    protected $signature = 'manutencao:enviar-whatsapp';
    protected $description = 'Envia mensagem de WhatsApp para manutenções com data atual.';

    public function handle(WhatsappService $whatsapp)
    {
        $hoje = Carbon::today();

        // Busca apenas colunas necessárias
        $manutencoes = Maintenance::select('id', 'status', 'next_maintenance_date', 'maintenance_date', 'tower_id')
            ->where(function ($query) use ($hoje) {
                $query->where(function ($q) use ($hoje) {
                    $q->whereDate('next_maintenance_date', $hoje)
                      ->where('status', 'completed');
                })->orWhere(function ($q) use ($hoje) {
                    $q->whereDate('maintenance_date', $hoje)
                      ->where('status', 'pending');
                });
            })
            ->with('tower:id,name')
            ->get();

        if ($manutencoes->isEmpty()) {
            $this->info('Nenhuma manutenção encontrada para hoje.');
            return;
        }

        $recipients = Recipient::select('id', 'name', 'number')
            ->whereHas('references', fn($q) => $q->where('name', 'serviceTowe'))
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('Nenhum destinatário configurado para envio.');
            return;
        }

        // Carrega logs já enviados para evitar consultas repetidas
        $logsEnviados = WhatsappLog::whereIn('maintenance_id', $manutencoes->pluck('id'))
            ->whereIn('recipient_id', $recipients->pluck('id'))
            ->where('status', 'sent')
            ->get()
            ->keyBy(fn($log) => $log->maintenance_id.'-'.$log->recipient_id);

        $appName = config('app.name');

        foreach ($manutencoes as $manutencao) {
            $towerName = $manutencao->tower->name ?? 'Sem torre associada';

            foreach ($recipients as $recipient) {
                $key = $manutencao->id.'-'.$recipient->id;
                if (isset($logsEnviados[$key])) {
                    $this->info("Mensagem já enviada para {$recipient->number} ({$recipient->name}) — pulando.");
                    continue;
                }

                $mensagem = "*$appName*: Olá {$recipient->name}! A torre {$towerName} tem uma manutenção "
                    . ($manutencao->status === 'pending' ? 'pendente' : 'agendada')
                    . " para hoje ({$hoje->format('d/m/Y')}).";

                $log = WhatsappLog::create([
                    'recipient_id' => $recipient->id,
                    'maintenance_id' => $manutencao->id,
                    'status' => 'pending',
                    'message' => $mensagem,
                ]);

                try {
                    $resposta = $whatsapp->sendMessage($recipient->number, $mensagem);

                    $log->update([
                        'status' => 'sent',
                        'response' => $resposta,
                        'sent_at' => now(),
                    ]);

                    $this->info("Mensagem enviada para {$recipient->number} ({$recipient->name}): $resposta");
                } catch (\Throwable $e) {
                    $log->update([
                        'status' => 'failed',
                        'response' => $e->getMessage(),
                    ]);

                    $this->error("Erro ao enviar para {$recipient->number}: " . $e->getMessage());

                    Log::error('Erro no envio de WhatsApp', [
                        'recipient_id' => $recipient->id,
                        'maintenance_id' => $manutencao->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}