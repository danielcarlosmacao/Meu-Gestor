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
        $services = VehicleService::orderBy('name')->paginate(10);
        return view('fleet.vehicle_services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
        ]);

        VehicleService::create($request->only('name', 'vehicle_type'));

        return redirect()->route('fleet.vehicle_services.index')->with('success', 'Serviço criado com sucesso!');
    }

    public function update(Request $request, VehicleService $vehicleService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
        ]);

        $vehicleService->update($request->only('name', 'vehicle_type'));

        return redirect()->route('fleet.vehicle_services.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(VehicleService $vehicleService)
    {
        $vehicleService->delete();
        return redirect()->route('fleet.vehicle_services.index')->with('success', 'Serviço excluído com sucesso!');
    }
}
