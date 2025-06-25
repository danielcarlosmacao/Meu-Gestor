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
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        ServiceClient::create($request->only('name', 'status'));

        return redirect()->route('service.clients.index')->with('success', 'Cliente criado com sucesso.');
    }

public function update(Request $request, $id)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    $service_client = ServiceClient::find($id);

    $service_client->update($data);
        return redirect()->route('service.clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(ServiceClient $service_client, $id)
    {
        $service_client = ServiceClient::find($id);
        $service_client->delete();

        return redirect()->route('service.clients.index')->with('success', 'Cliente excluído com sucesso.');
    }
}
