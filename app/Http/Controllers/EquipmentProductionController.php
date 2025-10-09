<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EquipmentProduction;
use App\Models\TowerSummaries;
use App\Models\Tower;

class EquipmentProductionController extends Controller
{
    public function store(Request $request, $towerId)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'identification' => 'nullable|string',
            'active' => 'required|in:yes,no',
        ]);

        $validated['tower_id'] = $towerId;

        $equipmentProduction = EquipmentProduction::create($validated);

        // Chama a função que está no model Tower
        $tower = Tower::findOrFail($towerId);
        $tower->updateConsumptionAh();

        // Registrar log
        activity()
            ->performedOn($equipmentProduction)
            ->causedBy(auth()->user())
            ->withProperties([
                'tower_id' => $towerId,
                'equipment_id' => $validated['equipment_id'],
                'identification' => $validated['identification'],
                'active' => $validated['active'],
            ])
            ->log('Adicionou um novo equipamento à torre');

        return redirect()
            ->route('tower.show', $towerId)
            ->with('success', 'Equipamento adicionado com sucesso!');
    }


    public function edit($id)
    {
        $ep = EquipmentProduction::with('equipment')->findOrFail($id);
        return response()->json($ep);
    }

    public function update(Request $request, $id)
    {
        $ep = EquipmentProduction::findOrFail($id);

        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'identification' => 'nullable|string',
            'active' => 'required|in:yes,no',
        ]);

        // Guardar valores antes da atualização
        $oldValues = $ep->getOriginal();

        $ep->update($validated);

        // Chama a função que está no model Tower
        $towerId = $ep->tower_id;
        $tower = Tower::findOrFail($towerId);
        $tower->updateConsumptionAh();

        // Guardar valores depois
        $newValues = $ep->getAttributes();

        // Registrar log
        activity()
            ->performedOn($ep)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $newValues,
            ])
            ->log('Atualizou um equipamento da torre');

        return response()->json(['message' => 'Equipamento atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $ep = EquipmentProduction::findOrFail($id);
        $towerId = $ep->tower_id;
        $tower = Tower::find($towerId);

        // Guardar valores antes da exclusão
        $oldValues = $ep->getAttributes();

        $ep->delete();

        if ($tower) {
            $tower->updateConsumptionAh();
        }

        // Registrar log
        activity()
            ->performedOn($ep)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues
            ])
            ->log('Removeu um equipamento da torre');

        return response()->json(['message' => 'Equipamento excluído com sucesso']);
    }


}
