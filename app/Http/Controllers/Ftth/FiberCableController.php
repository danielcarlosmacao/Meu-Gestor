<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FtthFiberCable;

class FiberCableController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([
            'fiber_box_id' => 'required|exists:ftth_fiber_boxes,id'
        ]);

        if (!$request->fibers) {
            return redirect()->back();
        }

        foreach ($request->fibers as $fiber) {

            if (empty($fiber['fiber_identification'])) {
                continue;
            }

            FtthFiberCable::create([

                'fiber_identification' => $fiber['fiber_identification'],

                'optical_power' => $fiber['optical_power'] ?? null,

                'fiber_box_id' => $request->fiber_box_id,

                'cable_fiber_box_id' => $request->cable_id ?? null,

                'status' => 'unused',

                'cable_fiber_box_direction' => $request->direction ?? 'input'

            ]);

        }

        return redirect()->back()
            ->with('success', 'Fibras criadas');

    }


    public function destroy($id)
    {
        $fiber = FtthFiberCable::findOrFail($id);

        // se estiver sendo usada em splinter ou fusão, bloqueia
        if ($fiber->status == 'used') {
            return redirect()->back()
                ->with('error', 'Fibra em uso, não pode excluir');
        }

        $fiber->delete();

        return redirect()->back()
            ->with('success', 'Fibra removida');
    }

    public function update(Request $request, $id)
    {
        $fiber = FtthFiberCable::findOrFail($id);

        $request->validate([
            'optical_power' => 'required|numeric'
        ]);

        $fiber->update([
            'optical_power' => $request->optical_power
        ]);

        return back()->with('success', 'Sinal atualizado com sucesso');
    }
}