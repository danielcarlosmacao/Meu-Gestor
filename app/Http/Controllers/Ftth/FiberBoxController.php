<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FtthPon;
use App\Models\FtthFiberBox;
use App\Models\FtthCableFiberBox;
use App\Models\FtthFiberCable;
use App\Models\FtthSplinter;
use App\Models\FtthSplinterLoss;
use App\Models\FtthFiberFusion;

class FiberBoxController extends Controller
{

    public function index(Request $request)
    {
        $pon = FtthPon::find($request->pon);

        $boxes = FtthFiberBox::where('pon_id', $pon->id)->get();

        $lastnumber = FtthFiberBox::max('number');
        $nextnumbermax = $lastnumber ? $lastnumber + 1 : 1;
        /*
                $numbers = FtthFiberBox::orderBy('number')->pluck('number');

                $nextnumber = 1;

                foreach ($numbers as $num) {
                    if ($num != $nextnumber) {
                        break;
                    }
                    $nextnumber++;
                }
        */
        $existsOne = \DB::table('ftth_fiber_boxes')->where('number', 1)->exists();

        if (!$existsOne) {
            $nextnumber = 1;
        } else {
            $result = \DB::selectOne("
        SELECT MIN(t1.number + 1) AS next
        FROM ftth_fiber_boxes t1
        LEFT JOIN ftth_fiber_boxes t2
            ON t2.number = t1.number + 1
        WHERE t2.number IS NULL
    ");

            $nextnumber = $result->next;
        }

        $boxIds = $boxes->pluck('id');

        $cables = FtthCableFiberBox::with([
            'inputFiberBox',
            'outputFiberBox'
        ])
            ->where(function ($q) use ($boxIds) {
                $q->whereIn('input_fiber_box_id', $boxIds)
                    ->orWhereIn('output_fiber_box_id', $boxIds);
            })
            ->get();

        if ($request->map == "yes") {
            return view('ftth.fiber-box.map', compact(
                'boxes',
                'pon',
                'nextnumber',
                'nextnumbermax',
                'cables'
            ));
        } else {
            return view('ftth.fiber-box.index', compact(
                'boxes',
                'nextnumber',
                'nextnumbermax',
                'pon'
            ));
        }
    }
    public function ponsmap(Request $request)
    {
        $olt = $request->olt;
        $infoolt = FtthPon::where('olt', $olt)->get();

        // Boxes de todas as PONs dessa OLT
        $boxes = FtthFiberBox::with('pon')
            ->whereHas('pon', function ($q) use ($olt) {
                $q->where('olt', $olt);
            })
            ->get();

        $boxIds = $boxes->pluck('id');

        $cables = FtthCableFiberBox::with([
            'inputFiberBox',
            'outputFiberBox'
        ])
            ->where(function ($q) use ($boxIds) {
                $q->whereIn('input_fiber_box_id', $boxIds)
                    ->orWhereIn('output_fiber_box_id', $boxIds);
            })
            ->get();

        return view('ftth.ponsmap', compact(
            'boxes',
            'olt',
            'cables',
            'infoolt'
        ));
    }



    public function store(Request $request)
    {

        FtthFiberBox::create([
            'number' => $request->number,
            'info' => $request->info,
            'coordinates' => $request->coordinates,
            'pon_id' => $request->pon_id
        ]);

        return redirect()->back();
    }


    public function destroy($id)
    {
        $box = FtthFiberBox::findOrFail($id);

        $box->delete();

        return redirect()->back();
    }


    public function show($id)
    {

        $box = FtthFiberBox::findOrFail($id);

        $boxesPon = FtthFiberBox::where(
            'pon_id',
            $box->pon_id
        )->get();


        $cables = FtthCableFiberBox::where(function ($q) use ($box) {

            $q->where('input_fiber_box_id', $box->id)
                ->orWhere('output_fiber_box_id', $box->id);

        })->get();

        $lastCableForBox = FtthCableFiberBox::where('output_fiber_box_id', $box->id)
            ->latest('id') // pega o mais recente
            ->first();
        $inputCableForBox = FtthCableFiberBox::where('input_fiber_box_id', $box->id)
            ->latest('id')
            ->first();

        $colorCablePon = $lastCableForBox
            ? $lastCableForBox->color
            : ($inputCableForBox ? $inputCableForBox->color : '#2563eb'); // fallback azul


        $splinters = FtthSplinter::with('loss')
            ->where('fiber_box_id', $box->id)
            ->get();


        /*
        Fibras da caixa

        1 - fibras dos cabos
        2 - fibras dos splinters
        3 - fibras diretas da box
        */

        $fibers = FtthFiberCable::with([
            'splinter.loss',
            'fusions1.fiber2',
            'fusions2.fiber1'
        ])
            ->where(function ($q) use ($box, $cables, $splinters) {

                $q->where('fiber_box_id', $box->id)


                    ->orWhereIn('splinter_id', $splinters->pluck('id'));

            })
            ->orderBy('fiber_identification')
            ->get();

        //filtar as duplicadas
        $fibers = $fibers->filter(function ($fiber) {

            return $fiber->fusions2->isEmpty();

        });

        $allFibers = FtthFiberCable::whereIn(
            'cable_fiber_box_id',
            $cables->pluck('id')
        )->get();

        $losses = FtthSplinterLoss::orderBy('type')->get();

        $fusions = FtthFiberFusion::with(['fiber1', 'fiber2'])
            ->where('fiber_box_id', $box->id)
            ->get();


        return view('ftth.fiber-box.show', compact(

            'box',
            'boxesPon',
            'cables',
            'colorCablePon',
            'fibers',
            'allFibers',
            'splinters',
            'losses',
            'fusions'

        ));
    }


    public function recalculate($boxId)
    {
        $visited = [];

        // 🔥 pega fibras base (sem entrada de fusão nem splinter)
        $startFibers = FtthFiberCable::where('fiber_box_id', $boxId)
            ->whereNull('splinter_id')
            ->get();

        foreach ($startFibers as $fiber) {

            if ($fiber->optical_power === null)
                continue;

            $this->propagateFiber($fiber->id, $fiber->optical_power, $visited);
        }

        return back()->with('success', 'Rede recalculada com sucesso.');
    }

    private function propagateFiber($fiberId, $power, &$visited = [])
    {
        // 🔥 evita loop infinito
        if (in_array($fiberId, $visited))
            return;

        $visited[] = $fiberId;

        $fiber = FtthFiberCable::find($fiberId);
        if (!$fiber)
            return;

        /*
        |--------------------------------------------------------------------------
        | 1. ATUALIZA A PRÓPRIA FIBRA
        |--------------------------------------------------------------------------
        */
        if ($fiber->optical_power != $power) {
            $fiber->update([
                'optical_power' => $power
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. FUSÃO (fiber1 → fiber2)
        |--------------------------------------------------------------------------
        */
        $fusions = FtthFiberFusion::where('fiber_cables_id_1', $fiberId)->get();

        foreach ($fusions as $fusion) {

            $destFiber = FtthFiberCable::find($fusion->fiber_cables_id_2);
            if (!$destFiber)
                continue;

            $fusionLoss = $fusion->loss ?? 0;

            $newPower = $power - $fusionLoss;

            $this->propagateFiber($destFiber->id, $newPower, $visited);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. SPLINTER (entrada → OUTs)
        |--------------------------------------------------------------------------
        */
        $splinters = FtthSplinter::where('splinter_input', $fiberId)->get();

        foreach ($splinters as $splinter) {

            $loss = $splinter->loss;

            $outputs = FtthFiberCable::where(
                'fiber_identification',
                'LIKE',
                $splinter->name . '-OUT-%'
            )->get();

            foreach ($outputs as $outFiber) {

                preg_match('/OUT-(\d+)$/', $outFiber->fiber_identification, $match);
                $outNumber = isset($match[1]) ? (int) $match[1] : 1;

                if ($outNumber == 1) {
                    $lossValue = $loss->loss1 ?? 0;
                } elseif ($outNumber == 2) {
                    $lossValue = $loss->loss2 ?? 0;
                } else {
                    $lossValue = $loss->loss1 ?? 0;
                }

                $newPower = $power - $lossValue;

                if ($outFiber->optical_power != $newPower) {
                    $outFiber->update([
                        'optical_power' => $newPower
                    ]);
                }

                // 🔥 ESSENCIAL → continua o fluxo
                $this->propagateFiber($outFiber->id, $newPower, $visited);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. CABO (vai para outra CTO)
        |--------------------------------------------------------------------------
        */
        $cable = FtthCableFiberBox::find($fiber->cable_fiber_box_id);

        if ($cable && $cable->output_fiber_box_id) {

            $nextFiber = FtthFiberCable::where('fiber_box_id', $cable->output_fiber_box_id)
                ->where('fiber_identification', $fiber->fiber_identification)
                ->first();

            if ($nextFiber) {
                $this->propagateFiber($nextFiber->id, $power, $visited);
            }
        }
    }

}
