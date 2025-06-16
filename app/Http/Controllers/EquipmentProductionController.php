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

        EquipmentProduction::create($validated);

        // Chama a função que está no model Tower
        $tower = Tower::findOrFail($towerId);
        $tower->updateConsumptionAh();

        return redirect()->route('tower.show', $towerId)->with('success', 'Equipamento adicionado com sucesso!');
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

        $ep->update($validated);

        // Chama a função que está no model Tower
        $towerId = $ep->tower_id;
        $tower = Tower::findOrFail($towerId);
        $tower->updateConsumptionAh();

        return response()->json(['message' => 'Equipamento atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $ep = EquipmentProduction::findOrFail($id);
        $towerId = $ep->tower_id;
        $tower = Tower::find($towerId);

        $ep->delete();
        $tower->updateConsumptionAh();




        return response()->json(['message' => 'Equipamento excluído com sucesso']);
    }

}
