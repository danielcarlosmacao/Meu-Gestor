<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\VehicleService;
use App\Services\SettingService;

class VehicleServiceController extends Controller
{
       public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $services = VehicleService::orderBy('name')->paginate($perPage);
        return view('fleet.vehicle_services.index', compact('services'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
    ]);

    $service = VehicleService::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($service)
        ->withProperties([
            'new' => $service->toArray()
        ])
        ->log('Serviço de Veículo Criado');

    return redirect()
        ->route('fleet.vehicle_services.index')
        ->with('success', 'Serviço criado com sucesso!');
}

public function update(Request $request, VehicleService $vehicleService)
{
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
    ]);

    $oldData = $vehicleService->toArray();

    $vehicleService->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($vehicleService)
        ->withProperties([
            'old' => $oldData,
            'new' => $vehicleService->toArray()
        ])
        ->log('Serviço de Veículo Atualizado');

    return redirect()
        ->route('fleet.vehicle_services.index')
        ->with('success', 'Serviço atualizado com sucesso!');
}

public function destroy(VehicleService $vehicleService)
{
    $oldData = $vehicleService->toArray();

    $vehicleService->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($vehicleService)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Serviço de Veículo Deletado');

    return redirect()
        ->route('fleet.vehicle_services.index')
        ->with('success', 'Serviço excluído com sucesso!');
}

}
