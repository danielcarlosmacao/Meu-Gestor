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

        $plateProduction = PlateProduction::create($validated);

        $tower = Tower::find($towerId);
        $totalWatts = $tower->updatePlate();

        // Registrar log
        activity()
            ->performedOn($plateProduction)
            ->causedBy(auth()->user())
            ->withProperties([
                'tower_id' => $towerId,
                'plate_id' => $validated['plate_id'],
                'installation_date' => $validated['installation_date'],
                'totalWatts' => $totalWatts,
            ])
            ->log('Adicionou uma nova placa à torre');

        return redirect()
            ->route('tower.show', $towerId)
            ->with('success', 'Placa adicionada com sucesso!');
    }

    public function destroy($id)
    {
        $plateProduction = PlateProduction::findOrFail($id);
        $towerId = $plateProduction->tower_id;
        $tower = Tower::find($towerId);

        // Guardar valores antes da exclusão
        $oldValues = $plateProduction->getAttributes();

        $plateProduction->delete();
        $totalWatts = $tower ? $tower->updatePlate() : null;

        // Registrar log
        activity()
            ->performedOn($plateProduction)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues,
                'totalWatts' => $totalWatts,
            ])
            ->log('Removeu uma placa da torre');

        return response()->json(['message' => 'Placa excluída com sucesso']);
    }

}
