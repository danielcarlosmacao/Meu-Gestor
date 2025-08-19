<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceClient;
use App\Services\SettingService;

class ClientController extends Controller
{
   public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $clients = ServiceClient::orderBy('name' ,'asc')->paginate($perPage);
        return view('service.client', compact('clients'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name'   => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    $serviceClient = ServiceClient::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($serviceClient)
        ->withProperties([
            'new' => $serviceClient->toArray()
        ])
        ->log('Cliente Criado');

    return redirect()
        ->route('service.clients.index')
        ->with('success', 'Cliente criado com sucesso.');
}

public function update(Request $request, $id)
{
    $data = $request->validate([
        'name'   => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    $serviceClient = ServiceClient::findOrFail($id);
    $oldData = $serviceClient->toArray();

    $serviceClient->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($serviceClient)
        ->withProperties([
            'old' => $oldData,
            'new' => $serviceClient->toArray()
        ])
        ->log('Cliente Atualizado');

    return redirect()
        ->route('service.clients.index')
        ->with('success', 'Cliente atualizado com sucesso.');
}

public function destroy($id)
{
    $serviceClient = ServiceClient::findOrFail($id);
    $oldData = $serviceClient->toArray();

    $serviceClient->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($serviceClient)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Cliente Deletado');

    return redirect()
        ->route('service.clients.index')
        ->with('success', 'Cliente excluído com sucesso.');
}

}
