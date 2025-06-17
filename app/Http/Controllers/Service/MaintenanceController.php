<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceMaintenance;
use App\Models\ServiceClient;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = ServiceMaintenance::with('serviceClient')->latest()->paginate(10);
        $clients = ServiceClient::where('status', 'active')->get();
        return view('service.maintenance', compact('maintenances', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_maintenance' => 'required|date',
            'service_client_id' => 'required|exists:service_clients,id',
            'maintenance' => 'required|string|max:255',
            'cost_enterprise' => 'nullable|numeric',
            'cost_client' => 'nullable|numeric',
        ]);

        ServiceMaintenance::create($request->all());
        return back()->with('success', 'Manutenção adicionada com sucesso.');
    }

    public function update(Request $request, ServiceMaintenance $maintenance)
    {
        $request->validate([
            'date_maintenance' => 'required|date',
            'service_client_id' => 'required|exists:service_clients,id',
            'maintenance' => 'required|string|max:255',
            'cost_enterprise' => 'nullable|numeric',
            'cost_client' => 'nullable|numeric',
        ]);

        $maintenance->update($request->all());
        return back()->with('success', 'Manutenção atualizada com sucesso.');
    }

    public function destroy(ServiceMaintenance $maintenance)
    {
        $maintenance->delete();
        return back()->with('success', 'Manutenção excluída com sucesso.');
    }
}
