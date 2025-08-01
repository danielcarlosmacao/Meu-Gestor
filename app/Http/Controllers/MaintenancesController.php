<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Tower;
use App\Services\SettingService;

class MaintenancesController extends Controller
{
public function index(Request $request, SettingService $settingService)
{
    $perPage = $settingService->getPerPage();
    $statusFilter = $request->input('status');

    $query = Maintenance::with('tower')->orderBy('maintenance_date', 'desc');

    if ($statusFilter && in_array($statusFilter, ['pending', 'completed', 'archived'])) {
        $query->where('status', $statusFilter);
    }

    $maintenances = $query->paginate($perPage)->withQueryString();
    $towers = Tower::orderBy('name', 'asc')->get();

    return view('tower.maintenance', compact('maintenances', 'towers', 'statusFilter'));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tower_id' => 'required|exists:towers,id',
            'info' => 'required|string',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'required|date|after_or_equal:maintenance_date',
            'status' => 'required|in:pending,completed,archived',
        ]);
    
        Maintenance::create([
            'tower_id' => $validated['tower_id'],
            'info' => $validated['info'],
            'maintenance_date' => $validated['maintenance_date'],
            'next_maintenance_date' => $validated['next_maintenance_date'],
            'status' => $validated['status'],
        ]);
    
        return redirect()->back()->with('success', 'Manutenção adicionada com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $request->validate([
            'tower_id' => 'required|exists:towers,id',
            'info' => 'required|string',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'required|date|after_or_equal:maintenance_date',
            'status' => 'required|in:pending,completed,archived',
        ]);

        $maintenance->update($request->all());

        return redirect()->back()->with('success', 'Manutenção atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

        return redirect()->back()->with('success', 'Manutenção excluída com sucesso.');
    }
}
