<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceMaintenance;
use App\Models\ServiceClient;
use App\Services\SettingService;

class MaintenanceController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $maintenances = ServiceMaintenance::with('serviceClient')->latest()->paginate($perPage);
        $clients = ServiceClient::where('status', 'active')->get();
        return view('service.maintenance', compact('maintenances', 'clients'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'date_maintenance'  => 'required|date',
        'service_client_id' => 'required|exists:service_clients,id',
        'maintenance'       => 'required|string|max:255',
        'cost_enterprise'   => 'nullable|numeric',
        'cost_client'       => 'nullable|numeric',
    ]);

    $maintenance = ServiceMaintenance::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'new' => $maintenance->toArray()
        ])
        ->log('Manutenção Criada');

    return back()->with('success', 'Manutenção adicionada com sucesso.');
}

public function update(Request $request, ServiceMaintenance $maintenance)
{
    $data = $request->validate([
        'date_maintenance'  => 'required|date',
        'service_client_id' => 'required|exists:service_clients,id',
        'maintenance'       => 'required|string|max:255',
        'cost_enterprise'   => 'nullable|numeric',
        'cost_client'       => 'nullable|numeric',
    ]);

    $oldData = $maintenance->toArray();
    $maintenance->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'old' => $oldData,
            'new' => $maintenance->toArray()
        ])
        ->log('Manutenção Atualizada');

    return back()->with('success', 'Manutenção atualizada com sucesso.');
}

public function destroy(ServiceMaintenance $maintenance)
{
    $oldData = $maintenance->toArray();
    $maintenance->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Manutenção Deletada');

    return back()->with('success', 'Manutenção excluída com sucesso.');
}

}
