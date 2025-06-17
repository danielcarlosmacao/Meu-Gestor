<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceEquipmentMaintenance;
use App\Models\ServiceClient;

class EquipmentMaintenanceController extends Controller
{
    

    public function index()
    {
        $maintenances = ServiceEquipmentMaintenance::with('serviceClient')->paginate(10);
        return view('service.equipment_maintenance', compact('maintenances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_client_id' => 'required|exists:service_clients,id',
            'assistance' => 'required|string|max:255',
            'equipment' => 'required|string|max:255',
            'erro' => 'required|string|max:255',
            'date_send' => 'nullable|date',
            'date_received' => 'nullable|date',
            'date_maintenance' => 'nullable|date',
            'solution' => 'nullable|string|max:500',
            'cost_enterprise' => 'nullable|numeric|min:0',
            'cost_client' => 'nullable|numeric|min:0',
        ]);

        ServiceEquipmentMaintenance::create($validated);

        return redirect()->route('service.equipment_maintenances.index')
            ->with('success', 'Manutenção criada com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $maintenance = ServiceEquipmentMaintenance::findOrFail($id);

        $validated = $request->validate([
            'service_client_id' => 'required|exists:service_clients,id',
            'assistance' => 'required|string|max:255',
            'equipment' => 'required|string|max:255',
            'erro' => 'required|string|max:255',
            'date_send' => 'nullable|date',
            'date_received' => 'nullable|date',
            'date_maintenance' => 'nullable|date',
            'solution' => 'nullable|string|max:500',
            'cost_enterprise' => 'nullable|numeric|min:0',
            'cost_client' => 'nullable|numeric|min:0',
        ]);

        $maintenance->update($validated);

        return redirect()->route('service.equipment_maintenances.index')
            ->with('success', 'Manutenção atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $maintenance = ServiceEquipmentMaintenance::findOrFail($id);
        $maintenance->delete();

        return redirect()->route('service.equipment_maintenances.index')
            ->with('success', 'Manutenção excluída com sucesso.');
    }
}
