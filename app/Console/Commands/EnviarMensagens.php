<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\NotificationLog;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Exception;

class EnviarMensagens extends Command
{
    protected $signature = 'mensagens:enviar';
    protected $description = 'Envia mensagens agendadas para os destinatários';

    public function handle(): void
    {
        $agora = Carbon::now();

        $this->info('Iniciando envio de mensagens...');

        $whatsapp = new WhatsappService();

        $notificacoes = Notification::where('sent', false)
            ->where('send_at', '<=', $agora)
            ->with('recipients')
            ->get();

        if ($notificacoes->isEmpty()) {
            $this->info('Nenhuma notificação pendente.');
            return;
        }

        foreach ($notificacoes as $notificacao) {
            $this->info("Processando notificação ID {$notificacao->id}");

            foreach ($notificacao->recipients as $recipient) {
                $jaEnviado = NotificationLog::where('notification_id', $notificacao->id)
                    ->where('recipient_id', $recipient->id)
                    ->where('status', 'sent')
                    ->exists();

                if ($jaEnviado) {
                    $this->warn("Já enviado para {$recipient->name} ({$recipient->number})");
                    continue;
                }

                $appName = config('app.name');
                $mensagem = "*$appName*: " . $notificacao->msg ;

                $log = NotificationLog::create([
                    'notification_id' => $notificacao->id,
                    'recipient_id' => $recipient->id,
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

                    $this->info("Enviado para {$recipient->number}: $resposta");
                } catch (Exception $e) {
                    $log->update([
                        'status' => 'failed',
                        'response' => $e->getMessage(),
                    ]);

                    $this->error("Erro ao enviar para {$recipient->number}: " . $e->getMessage());
                }
            }

            // Marcar notificação como enviada após tentar todos os destinatários
            $notificacao->update(['sent' => true]);
            $this->info("Notificação ID {$notificacao->id} marcada como enviada.");
        }

        $this->info('Envio de mensagens concluído.');
    }
}
