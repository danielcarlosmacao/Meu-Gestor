<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FtthSplinter;
use App\Models\FtthSplinterLoss;
use App\Models\FtthFiberCable;
use App\Models\FtthFiberFusion;

class SplinterController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([

            'name' => 'required',
            'type' => 'required',
            'fiber_box_id' => 'required|exists:ftth_fiber_boxes,id',
            'splinter_input' => 'required|exists:ftth_fiber_cables,id',
            'splinter' => 'required|exists:ftth_splinter_losses,id'

        ]);


        /*
        |--------------------------------------------
        | Fibra de entrada
        |--------------------------------------------
        */

        $fiberInput = FtthFiberCable::findOrFail(
            $request->splinter_input
        );


        /*
        |--------------------------------------------
        | Tipo de splitter
        |--------------------------------------------
        */

        $loss = FtthSplinterLoss::findOrFail(
            $request->splinter
        );


        /*
        |--------------------------------------------
        | Cria o splitter
        |--------------------------------------------
        */

        $splinter = FtthSplinter::create([

            'name' => $request->name,
            'type' => $request->type,
            'fiber_box_id' => $request->fiber_box_id,
            'splinter_input' => $fiberInput->id,
            'splinter' => $loss->id

        ]);


        /*
        |--------------------------------------------
        | Marca fibra de entrada como usada
        |--------------------------------------------
        */

        $fiberInput->update([

            'status' => 'used',
            'splinter_id' => $splinter->id

        ]);

        /*
        |--------------------------------------------
        | Potência da fibra de entrada
        |--------------------------------------------
        */

        $inputPower = $fiberInput->optical_power ?? 0;


        /*
        |--------------------------------------------
        | Cria fibras de saída
        |--------------------------------------------
        */
        if ($request->type == 'network') {

            for ($i = 1; $i <= $loss->derivations; $i++) {

                if ($loss->splinter_type == 'unbalanced') {
                    $lossValue = $i == 1
                        ? $loss->loss1
                        : $loss->loss2;
                } else {
                    $lossValue = $loss->loss1;
                }

                $outputPower = $inputPower - $lossValue;

                FtthFiberCable::create([

                    'fiber_identification' =>
                        $splinter->name . '-OUT-' . $i,

                    'fiber_box_id' => $request->fiber_box_id,

                    'optical_power' => $outputPower,

                    'splinter_id' => null,

                    'cable_fiber_box_id' => null,

                    'cable_fiber_box_direction' => 'output',

                    'status' => 'unused'

                ]);

            }
        }

        return redirect()->back()
            ->with('success', 'Splitter criado com sucesso');
    }

    public function destroy($id)
    {
        $splinter = FtthSplinter::findOrFail($id);

        /*
        |----------------------------------------
        | Fibra de entrada (liberar)
        |----------------------------------------
        */
        $inputFiber = FtthFiberCable::find($splinter->splinter_input);

        if ($inputFiber) {
            $inputFiber->update([
                'status' => 'unused',
                'splinter_id' => null
            ]);
        }

        /*
        |----------------------------------------
        | Buscar fibras de saída do splinter
        |----------------------------------------
        */
        $outputFibers = FtthFiberCable::where(
            'fiber_identification',
            'LIKE',
            $splinter->name . '-OUT-%'
        )->get();

        foreach ($outputFibers as $fiber) {

            /*
            |----------------------------------------
            | Remover fusões onde essa fibra participa
            |----------------------------------------
            */
            $fusions = FtthFiberFusion::where('fiber_cables_id_1', $fiber->id)
                ->orWhere('fiber_cables_id_2', $fiber->id)
                ->get();

            foreach ($fusions as $fusion) {

                // Descobre a outra fibra
                $otherFiberId = $fusion->fiber_cables_id_1 == $fiber->id
                    ? $fusion->fiber_cables_id_2
                    : $fusion->fiber_cables_id_1;

                $otherFiber = FtthFiberCable::find($otherFiberId);

                // Libera a outra fibra
                if ($otherFiber) {
                    $otherFiber->update([
                        'status' => 'unused'
                    ]);
                }

                $fusion->delete();
            }

            /*
            |----------------------------------------
            | Deleta a fibra de saída
            |----------------------------------------
            */
            $fiber->delete();
        }

        /*
        |----------------------------------------
        | Remove splinter
        |----------------------------------------
        */
        $splinter->delete();

        return redirect()->back()
            ->with('success', 'Splinter removido com sucesso');
    }
}