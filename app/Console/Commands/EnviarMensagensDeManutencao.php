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

        // Busca manutenções para hoje (duas condições)
        $manutencoes = Maintenance::where(function ($query) use ($hoje) {
            $query->where(function ($q) use ($hoje) {
                $q->whereDate('next_maintenance_date', $hoje)
                    ->where('status', 'completed');
            })->orWhere(function ($q) use ($hoje) {
                $q->whereDate('maintenance_date', $hoje)
                    ->where('status', 'pending');
            });
        })->with('tower')->get();

        if ($manutencoes->isEmpty()) {
            $this->info('Nenhuma manutenção encontrada para hoje.');
            return;
        }

        $recipients = Recipient::whereHas('references', function ($query) {
            $query->where('name', 'serviceTowe');
        })->get();


        if ($recipients->isEmpty()) {
            $this->warn('Nenhum destinatário configurado para envio.');
            return;
        }

        foreach ($manutencoes as $manutencao) {
            $towerName = optional($manutencao->tower)->name ?? 'Sem torre associada';

            foreach ($recipients as $recipient) {
                // Evita reenvio se já enviado com sucesso
                $jaEnviado = WhatsappLog::where('recipient_id', $recipient->id)
                    ->where('maintenance_id', $manutencao->id)
                    ->where('status', 'sent')
                    ->exists();

                if ($jaEnviado) {
                    $this->info("Mensagem já enviada para {$recipient->number} ({$recipient->name}) — pulando.");
                    continue;
                }

                // Monta mensagem
                $appName = config('app.name');
                $mensagem = "*$appName*: Olá {$recipient->name}! A torre {$towerName} tem uma manutenção "
                    . ($manutencao->status === 'pending' ? 'pendente' : 'agendada')
                    . " para hoje ({$hoje->format('d/m/Y')}).";

                // Cria log com status 'pending'
                $log = WhatsappLog::create([
                    'recipient_id' => $recipient->id,
                    'maintenance_id' => $manutencao->id,
                    'status' => 'pending',
                    'message' => $mensagem,
                ]);

                try {
                    $resposta = $whatsapp->sendMessage($recipient->number, $mensagem);

                    // Atualiza log com sucesso
                    $log->update([
                        'status' => 'sent',
                        'response' => $resposta,
                        'sent_at' => now(),
                    ]);

                    $this->info("Mensagem enviada para {$recipient->number} ({$recipient->name}): $resposta");
                } catch (\Exception $e) {
                    // Atualiza log com erro
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
