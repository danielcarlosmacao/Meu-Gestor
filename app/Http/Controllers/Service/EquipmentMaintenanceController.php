<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceEquipmentMaintenance;
use App\Models\ServiceClient;
use App\Services\SettingService;

class EquipmentMaintenanceController extends Controller
{
    

    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $maintenances = ServiceEquipmentMaintenance::with('serviceClient')->orderBy('date_maintenance','desc')->paginate($perPage);
        return view('service.equipment_maintenance', compact('maintenances'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'service_client_id' => 'required|exists:service_clients,id',
        'assistance'        => 'required|string|max:255',
        'equipment'         => 'required|string|max:255',
        'erro'              => 'required|string|max:255',
        'date_send'         => 'nullable|date',
        'date_received'     => 'nullable|date',
        'date_maintenance'  => 'nullable|date',
        'solution'          => 'nullable|string|max:500',
        'cost_enterprise'   => 'nullable|numeric|min:0',
        'cost_client'       => 'nullable|numeric|min:0',
    ]);

    $maintenance = ServiceEquipmentMaintenance::create($validated);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'new' => $maintenance->toArray()
        ])
        ->log('Manutenção De Equipamento Criada');

    return redirect()
        ->route('service.equipment_maintenances.index')
        ->with('success', 'Manutenção criada com sucesso.');
}

public function update(Request $request, $id)
{
    $maintenance = ServiceEquipmentMaintenance::findOrFail($id);
    $oldData = $maintenance->toArray();

    $validated = $request->validate([
        'service_client_id' => 'required|exists:service_clients,id',
        'assistance'        => 'required|string|max:255',
        'equipment'         => 'required|string|max:255',
        'erro'              => 'required|string|max:255',
        'date_send'         => 'nullable|date',
        'date_received'     => 'nullable|date',
        'date_maintenance'  => 'nullable|date',
        'solution'          => 'nullable|string|max:500',
        'cost_enterprise'   => 'nullable|numeric|min:0',
        'cost_client'       => 'nullable|numeric|min:0',
    ]);

    $maintenance->update($validated);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'old' => $oldData,
            'new' => $maintenance->toArray()
        ])
        ->log('Manutenção De Equipamento Atualizada');

    return redirect()
        ->route('service.equipment_maintenances.index')
        ->with('success', 'Manutenção atualizada com sucesso.');
}

public function destroy($id)
{
    $maintenance = ServiceEquipmentMaintenance::findOrFail($id);
    $oldData = $maintenance->toArray();

    $maintenance->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($maintenance)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Manutenção de equipamento Deletada');

    return redirect()
        ->route('service.equipment_maintenances.index')
        ->with('success', 'Manutenção excluída com sucesso.');
}

}
