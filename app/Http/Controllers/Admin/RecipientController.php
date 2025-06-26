<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipient;
use App\Models\WhatsappLog;
use App\Services\SettingService;

class RecipientController extends Controller
{
    public function index(SettingService $settingService)
    {
         $perPage = $settingService->getPerPage();

        $recipients = Recipient::paginate($perPage);
        return view('admin.recipients.index', compact('recipients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'reference' => 'nullable|string|max:255',
        ]);

        Recipient::create($request->all());
        return redirect()->back()->with('success', 'Destinatário adicionado com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $recipient = Recipient::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'reference' => 'nullable|string|max:255',
        ]);

        $recipient->update($request->all());
        return redirect()->back()->with('success', 'Destinatário atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $recipient = Recipient::findOrFail($id);
        $recipient->delete();
        return redirect()->back()->with('success', 'Destinatário excluído com sucesso.');
    }

    public function logs(SettingService $settingService)
{
    $perPage = $settingService->getPerPage();

    $logs = WhatsappLog::with(['recipient', 'maintenance.tower'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    return view('admin.recipients.logs', compact('logs'));
}
}
