<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Maintenance;
use App\Models\Recipient;
use App\Models\WhatsappLog;
use App\Services\WhatsappService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarMensagensDeManutencao extends Command
{
    protected $signature = 'manutencao:enviar-whatsapp';
    protected $description = 'Envia mensagem de WhatsApp para manutenções com data atual.';

    public function handle(WhatsappService $whatsapp)
    {
        $hoje = Carbon::today();

        // 🔎 Busca manutenções do dia
        $manutencoes = Maintenance::select(
                'id',
                'status',
                'next_maintenance_date',
                'maintenance_date',
                'tower_id'
            )
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

        // 📤 Destinatários
        $recipients = Recipient::select('id', 'name', 'number')
            ->whereHas('references', fn ($q) => $q->where('name', 'serviceTowe'))
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('Nenhum destinatário configurado para envio.');
            return;
        }

        // 🚫 Logs já enviados (polimórfico)
        $logsEnviados = WhatsappLog::where('ref_type', Maintenance::class)
            ->whereIn('ref_id', $manutencoes->pluck('id'))
            ->whereIn('recipient_id', $recipients->pluck('id'))
            ->where('status', 'sent')
            ->get()
            ->keyBy(fn ($log) => $log->ref_id . '-' . $log->recipient_id);

        $appName = config('app.name');

        foreach ($manutencoes as $manutencao) {

            $towerName = $manutencao->tower->name ?? 'Sem torre associada';

            foreach ($recipients as $recipient) {

                $key = $manutencao->id . '-' . $recipient->id;

                if (isset($logsEnviados[$key])) {
                    $this->info("Mensagem já enviada para {$recipient->number} — pulando.");
                    continue;
                }

                // 📅 Data correta conforme o tipo
                $dataManutencao = $manutencao->status === 'pending'
                    ? $manutencao->maintenance_date
                    : $manutencao->next_maintenance_date;

                $mensagem = "*{$appName}*: Olá {$recipient->name}! 👋\n"
                    . "A torre *{$towerName}* possui uma manutenção "
                    . ($manutencao->status === 'pending' ? '*pendente*' : '*agendada*')
                    . " para *{$dataManutencao->format('d/m/Y')}*.";

                // 📝 Cria log inicial
                $log = WhatsappLog::create([
                    'recipient_id'  => $recipient->id,
                    'ref_type' => Maintenance::class,
                    'ref_id'   => $manutencao->id,
                    'status'        => 'pending',
                    'message'       => $mensagem,
                ]);

                $logsEnviados[$key] = true;

                try {
                    $this->info("Enviando mensagem para {$recipient->number}...");
                    $resposta = $whatsapp->sendMessage($recipient->number, $mensagem);

                    $log->update([
                        'status'  => 'sent',
                        'response'=> $resposta,
                        'sent_at' => now(),
                    ]);

                    $this->info("Mensagem enviada com sucesso.");
                } catch (\Throwable $e) {

                    $log->update([
                        'status'   => 'failed',
                        'response' => $e->getMessage(),
                    ]);

                    Log::error('Erro no envio de WhatsApp (Maintenance)', [
                        'recipient_id' => $recipient->id,
                        'maintenance_id' => $manutencao->id,
                        'error' => $e->getMessage(),
                    ]);

                    $this->error("Erro ao enviar: " . $e->getMessage());
                }
            }
        }

        $this->info('Processo de envio de manutenções concluído.');
    }
}
