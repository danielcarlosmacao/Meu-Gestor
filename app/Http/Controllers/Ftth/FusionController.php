<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FtthFiberFusion;
use App\Models\FtthFiberCable;
use Illuminate\Support\Facades\DB;

class FusionController extends Controller
{



    public function store(Request $request)
    {
        $data = $request->validate([
            'fiber_box_id' => 'required',
            'info'   => 'nullable|string|max:255',
            'fiber1' => 'required|exists:ftth_fiber_cables,id|different:fiber2',
            'fiber2' => 'required|exists:ftth_fiber_cables,id'
        ]);

        DB::transaction(function () use ($data) {

            // 🔎 Lock para evitar concorrência
            $fiber1 = FtthFiberCable::lockForUpdate()->findOrFail($data['fiber1']);
            $fiber2 = FtthFiberCable::lockForUpdate()->findOrFail($data['fiber2']);

            //  REGRA 1: não pode já estar em uso
            if ($fiber1->status === 'used' || $fiber2->status === 'used') {
                throw new \Exception('Uma das fibras já está em uso');
            }

            //  REGRA 2: BLOQUEAR OUT + OUT (loop de splitter)
            if (
                str_contains($fiber1->fiber_identification, '-OUT-') &&
                str_contains($fiber2->fiber_identification, '-OUT-')
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fiber1' => 'Não é permitido conectar saída de splitter com outra saída (OUT-OUT).'
                ]);
            }

            //  Cria fusão
            FtthFiberFusion::create([
                'info' => $data['info'] ?? null,
                'fiber_box_id' => $data['fiber_box_id'],
                'fiber_cables_id_1' => $fiber1->id,
                'fiber_cables_id_2' => $fiber2->id
            ]);

            // 🔄 Atualiza fibras
            $fiber1->update([
                'status' => 'used'
            ]);

            $fiber2->update([
                'status' => 'used',
                'optical_power' => $fiber1->optical_power
            ]);
        });

        return redirect()->back()->with('success', 'Fusão criada com sucesso');
    }

    public function destroy($id)
    {
        $fusion = FtthFiberFusion::findOrFail($id);

        /*
        |----------------------------------------
        | Libera fibras
        |----------------------------------------
        */
        FtthFiberCable::whereIn('id', [
            $fusion->fiber_cables_id_1,
            $fusion->fiber_cables_id_2
        ])->update([
                    'status' => 'unused'
                ]);

        /*
        |----------------------------------------
        | Remove fusão
        |----------------------------------------
        */
        $fusion->delete();

        return redirect()->back()
            ->with('success', 'Fusão removida');
    }
}