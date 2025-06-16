<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlateProduction;
use App\Models\Tower;

class PlateProductionController extends Controller
{
    public function store(Request $request, $towerId)
    {
        $validated = $request->validate([
            'plate_id' => 'required|exists:plates,id',
            'installation_date' => 'nullable|date',
        ]);

        $validated['tower_id'] = $towerId;

        PlateProduction::create($validated);

        $tower = Tower::find($towerId);
        $totalWatts = $tower->updatePlate();

        return redirect()->route('tower.show', $towerId)->with('success', 'Placa adicionada com sucesso!');
    }

    public function destroy($id)
    {

        $plateProduction = PlateProduction::findOrFail($id);
        $towerId = $plateProduction->tower_id;
        $tower = Tower::find($towerId);

        $plateProduction->delete();
        $totalWatts = $tower->updatePlate();
        

        return response()->json(['message' => 'Placa excluída com sucesso']);
    }
}
