<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vacation;
use App\Models\Recipient;
use App\Models\WhatsappLog;
use App\Services\WhatsappService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarMensagensDeFerias extends Command
{
    protected $signature = 'vacations:enviar-whatsapp';
    protected $description = 'Envia mensagem de WhatsApp para colaboradores que iniciaram férias há 7 dias.';

    public function handle(WhatsappService $whatsapp)
    {
        $dataReferencia = Carbon::today()->addDays(7);

        // Férias iniciadas há 7 dias
        $vacations = Vacation::with('collaborator:id,name')
            ->whereDate('start_date', $dataReferencia)
            ->get();

        if ($vacations->isEmpty()) {
            $this->info('Nenhuma férias encontrada para a data.');
            return;
        }

        // Destinatários do setor de férias
        $recipients = Recipient::select('id', 'name', 'number')
            ->whereHas('references', fn ($q) => $q->where('name', 'vacation'))
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('Nenhum destinatário configurado para férias.');
            return;
        }

        // Logs já enviados (polimórfico)
        $logsEnviados = WhatsappLog::where('ref_type', Vacation::class)
            ->whereIn('ref_id', $vacations->pluck('id'))
            ->whereIn('recipient_id', $recipients->pluck('id'))
            ->where('status', 'sent')
            ->get()
            ->keyBy(fn ($log) => $log->ref_id . '-' . $log->recipient_id);

        $appName = config('app.name');

        foreach ($vacations as $vacation) {
            $employeeName = $vacation->collaborator->name ?? 'Colaborador';
            $dataFormatada = Carbon::parse($vacation->start_date)->format('d/m/Y');

            foreach ($recipients as $recipient) {
                $key = $vacation->id . '-' . $recipient->id;

                if (isset($logsEnviados[$key])) {
                    $this->info("Mensagem já enviada para {$recipient->number} — pulando.");
                    continue;
                }

                $mensagem = "*{$appName}*: Olá {$recipient->name}! \n"
                    . "O colaborador *{$employeeName}* inicia as férias em "
                    . "*$dataFormatada*.";

                // Cria log inicial (polimórfico)
                $log = WhatsappLog::create([
                    'recipient_id' => $recipient->id,
                    'ref_type'     => Vacation::class,
                    'ref_id'       => $vacation->id,
                    'status'       => 'pending',
                    'message'      => $mensagem,
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

                    Log::error('Erro no envio de WhatsApp (Vacation)', [
                        'recipient_id' => $recipient->id,
                        'ref_type'     => Vacation::class,
                        'ref_id'       => $vacation->id,
                        'error'        => $e->getMessage(),
                    ]);

                    $this->error("Erro ao enviar: " . $e->getMessage());
                }
            }
        }

        $this->info('Processo de envio de férias concluído.');
    }
}
