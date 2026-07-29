<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use App\Models\FtthCableFiberBox;
use App\Models\FtthCableRoutePoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CableRouteController extends Controller
{
    public function edit(int $cableId)
    {
        $cable = FtthCableFiberBox::with([
            'inputFiberBox',
            'outputFiberBox',
            'routePoints',
        ])->findOrFail($cableId);

        return view('ftth.cables.route', compact('cable'));
    }

    public function update(Request $request, int $cableId)
    {
        $validated = $request->validate([
            'points' => [
                'required',
                'array',
                'min:2',
            ],

            'points.*.lat' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'points.*.lng' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ], [
            'points.required' => 'Desenhe o trajeto do cabo.',
            'points.min' => 'O trajeto precisa ter pelo menos dois pontos.',
            'points.*.lat.required' => 'Existe um ponto sem latitude.',
            'points.*.lng.required' => 'Existe um ponto sem longitude.',
        ]);

        $cable = FtthCableFiberBox::findOrFail($cableId);

        DB::transaction(function () use ($cable, $validated) {

            FtthCableRoutePoint::query()
                ->where('cable_fiber_box_id', $cable->id)
                ->delete();

            foreach (array_values($validated['points']) as $position => $point) {
                FtthCableRoutePoint::create([
                    'cable_fiber_box_id' => $cable->id,
                    'latitude' => round((float) $point['lat'], 7),
                    'longitude' => round((float) $point['lng'], 7),
                    'position' => $position,
                ]);
            }
        });

        return redirect()
            ->route('cable.route.edit', $cable->id)
            ->with(
                'success',
                'Trajeto salvo com sucesso. '
                    . count($validated['points'])
                    . ' pontos registrados.'
            );
    }

    public function destroy(int $cableId)
    {
        $cable = FtthCableFiberBox::findOrFail($cableId);

        FtthCableRoutePoint::query()
            ->where('cable_fiber_box_id', $cable->id)
            ->delete();

        return redirect()
            ->route('cable.route.edit', $cable->id)
            ->with('success', 'Trajeto removido com sucesso.');
    }
}
