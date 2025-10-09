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
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'info'         => 'required|string|max:255',
        'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
    ]);

    $workshop = Workshop::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($workshop)
        ->withProperties([
            'new' => $workshop->toArray()
        ])
        ->log('Oficina Criada');

    return redirect()
        ->route('fleet.vehicle_workshop.index')
        ->with('success', 'Oficina criada com sucesso!');
}

public function update(Request $request, $id)
{
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'info'         => 'required|string|max:255',
        'vehicle_type' => 'required|in:car,motorcycle,truck,others,all',
    ]);

    $workshop = Workshop::findOrFail($id);

    $oldData = $workshop->toArray();

    $workshop->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($workshop)
        ->withProperties([
            'old' => $oldData,
            'new' => $workshop->toArray()
        ])
        ->log('Oficina Atualizada');

    return redirect()
        ->route('fleet.vehicle_workshop.index')
        ->with('success', 'Oficina atualizada com sucesso!');
}

public function destroy($id)
{
    $workshop = Workshop::findOrFail($id);

    $oldData = $workshop->toArray();

    $workshop->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($workshop)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Oficina Deletado');

    return redirect()
        ->route('fleet.vehicle_workshop.index')
        ->with('success', 'Oficina excluída com sucesso!');
}

}
