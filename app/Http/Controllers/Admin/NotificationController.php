<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Recipient;
use App\Services\WhatsappService;
use App\Models\NotificationLog;
use App\Services\SettingService;

class NotificationController extends Controller
{
    public function index(SettingService $settingService)
{
    $perPage = $settingService->getPerPage();
    $user = auth()->user();

    // ✅ SE O USUÁRIO FOR ADMINISTRADOR → VÊ TODAS
    if ($user->hasRole('administrator')) {
        $notifications = Notification::with('recipients')
            ->latest()
            ->paginate($perPage);
    } else {
        // ✅ USUÁRIO COMUM → VÊ APENAS AS SUAS
        $notifications = Notification::whereHas('recipients', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('recipients')
            ->latest()
            ->paginate($perPage);
    }

    $recipients = Recipient::whereHas('references', function ($query) {
        $query->where('name', 'notification');
    })->get();

    return view('admin.notification.index', compact('notifications', 'recipients'));
}



    public function store(Request $request)
{
    $request->validate([
        'msg' => 'required|string',
        'recipient_ids' => 'required|array|min:1',
        'send_at' => 'nullable|date',
    ]);

    $notification = Notification::create([
        'user_id' => auth()->id(),   
        'info' => $request->input('info'),
        'msg' => $request->input('msg'),
        'send_at' => $request->input('send_at'),
    ]);

    $notification->recipients()->attach($request->input('recipient_ids'));

    return redirect()->route('admin.notification.index')->with('success', 'Notificação criada com sucesso!');
}

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'info' => 'nullable|string',
            'msg' => 'required|string',
            'send_at' => 'nullable|date',
        ]);

        $notification->update($request->only('info', 'msg', 'send_at'));
        return redirect()->back()->with('success', 'Notificação atualizada.');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notification.index')->with('success', 'Notificação excluída.');
    }

    public function send(Notification $notification)
    {
        if ($notification->sent) {
            return back()->with('warning', 'Essa notificação já foi enviada.');
        }

        $this->enviarMensagem($notification);
        return back()->with('success', 'Mensagens enviadas.');
    }

    public function resend(Notification $notification)
    {
        $this->enviarMensagem($notification, true);
        return back()->with('success', 'Mensagens reenviadas.');
    }

    protected function enviarMensagem(Notification $notification, $forcarEnvio = false)
    {
        $whatsapp = new WhatsappService();

        foreach ($notification->recipients as $recipient) {
            if (!$forcarEnvio) {
                $jaEnviado = $notification->logs()
                    ->where('recipient_id', $recipient->id)
                    ->where('status', 'sent')
                    ->exists();

                if ($jaEnviado) {
                    continue;
                }
            }

            $mensagem = $notification->msg;

            $log = $notification->logs()->create([
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
            } catch (\Exception $e) {
                $log->update([
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);
            }
        }

        $notification->update(['sent' => true]);
    }

    public function cleanSent($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            $notification->sent = 0;
            $notification->save();
            return back()->with('success', 'resetado da fila de envio.');
        }
        return back()->with('success', 'nao resetado fila de envio.');
    }

    public function logs(SettingService $settingService)
    {

        $perPage = $settingService->getPerPage();
        $logs = NotificationLog::with(['recipient', 'notification'])->paginate($perPage);
        return view('admin.notification.logs', compact('logs'));
    }

    public function logsDelete($id)
    {
        $log = NotificationLog::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.notification.logs')->with('success', 'Log excluído.');
    }
}
