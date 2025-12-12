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
        $vehicles = Vehicle::orderBy('type', 'asc')->orderBy('model', 'asc')->paginate($perPage);
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
            'color' => 'required|string|max:50',
        ]);

        // Cria o veículo
        $vehicle = Vehicle::create($data);

        // Log de auditoria
        activity()
            ->causedBy(auth()->user())
            ->performedOn($vehicle)
            ->withProperties([
                'new' => $vehicle->toArray()
            ])
            ->log('Veículo Criado');

        return redirect()
            ->route('fleet.vehicles.index')
            ->with('success', 'Veículo adicionado com sucesso.');

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
            'color' => 'required|string|max:50',
        ]);

        // Pega os dados antigos antes da atualização
        $oldData = $vehicle->toArray();

        // Atualiza com os novos dados
        $vehicle->update($data);
       

        // Log de auditoria
        activity()
            ->causedBy(auth()->user())
            ->performedOn($vehicle)
            ->withProperties([
                'old' => $oldData,
                'new' => $vehicle->toArray(),
            ])
            ->log('Veículo Atualizado');

        return redirect()
            ->route('fleet.vehicles.index')
            ->with('success', 'Veículo atualizado com sucesso.');
    }

    public function destroy(Vehicle $vehicle)
    {
        
        $oldData = $vehicle->toArray();
        $vehicle->delete();

                //Logs
        activity()
            ->causedBy(auth()->user())
            ->performedOn($vehicle)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Veiculo Deletado');
        return redirect()->route('fleet.vehicles.index')->with('success', 'Veículo excluído com sucesso.');
    }
}
