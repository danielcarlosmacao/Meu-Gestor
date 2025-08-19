<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Equipment;
use App\Services\SettingService;


class EquipmentController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();

        $equipments = Equipment::withCount('equipmentProductions')->orderBy('name', 'asc')->paginate($perPage);

        return view('tower.equipment', ['equipments' => $equipments]);

    }

    public function store(Request $request)
    {

        $Equipment = new Equipment;
        $Equipment->name = $request->name;
        $Equipment->watts = $request->watts;
        $Equipment->stock = $request->stock;
        $Equipment->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($Equipment)
            ->withProperties([
                'new' => $Equipment->toArray()
            ])
            ->log('Equipmento Criado');

        return redirect()->route('equipment.index')
            ->with('success', 'Equipamento criado com sucesso!');
    }

    public function update(Request $request, $id)
    {

        // Validação básica (opcional, mas recomendada)
        $request->validate([
            'name' => 'required|string|max:255',
            'watts' => 'required|numeric',
            'stock' => 'nullable|numeric',
        ]);

        // Busca o equipamento pelo ID
        $equipment = Equipment::findOrFail($id);

        $oldData = $equipment->toArray();

        // Atualiza os campos
        $equipment->name = $request->name;
        $equipment->watts = $request->watts;
        $equipment->stock = $request->stock;
        $equipment->save();

        //Logs

        activity()
            ->causedBy(auth()->user())
            ->performedOn($equipment)
            ->withProperties([
                'old' => $oldData,
                'new' => $equipment->toArray()
            ])
            ->log('Equipamento Atualizado');

        return redirect()->route('equipment.index')
            ->with('success', 'Equipamento atualizado com sucesso!');
    }


    public function destroy($id)
    {

        $equipment = Equipment::findOrFail($id);
        $oldData = $equipment->toArray();
        $equipment->delete();
        
        //Logs
        activity()
            ->causedBy(auth()->user())
            ->performedOn($equipment)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Equipmento Deletado');

        return redirect()->route('equipment.index')
            ->with('success', 'Equipamento deletada com sucesso!');
    }
}
