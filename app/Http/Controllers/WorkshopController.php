<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Services\SettingService;

class WorkshopController extends Controller
{
     public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $services = Workshop::orderBy('vehicle_type','asc')->orderBy('name', 'asc')->paginate($perPage);
        return view('fleet.vehicle_workshop.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
        ]);

        Workshop::create($request->only('name', 'info', 'vehicle_type'));

        return redirect()->route('fleet.vehicle_workshop.index')->with('success', 'Serviço criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
        ]);

        $service = Workshop::findOrFail($id);
        $service->update($request->only('name', 'info', 'vehicle_type'));

        return redirect()->route('fleet.vehicle_workshop.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $service = Workshop::findOrFail($id);
        $service->delete();

        return redirect()->route('fleet.vehicle_workshop.index')->with('success', 'Serviço excluído com sucesso!');
    }
}
