<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Services\SettingService;

class VehicleController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $vehicles = Vehicle::latest()->paginate($perPage);
        return view('fleet.vehicles.index', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'license_plate' => 'required|string|max:10',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'type' => 'required|in:car,motorcycle,truck,others',
            'year' => 'required|digits:4|integer',
            'fuel_type' => 'required|string|max:30',
            'status' => 'required|string|max:20',
        ]);
        

        Vehicle::create($data);
        return redirect()->route('fleet.vehicles.index')->with('success', 'Veículo adicionado com sucesso.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'license_plate' => 'required|string|max:10',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'type' => 'required|in:car,motorcycle,truck,others',
            'year' => 'required|digits:4|integer',
            'fuel_type' => 'required|string|max:30',
            'status' => 'required|string|max:20',
        ]);

        $vehicle->update($data);
        return redirect()->route('fleet.vehicles.index')->with('success', 'Veículo atualizado com sucesso.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('fleet.vehicles.index')->with('success', 'Veículo excluído com sucesso.');
    }
}
