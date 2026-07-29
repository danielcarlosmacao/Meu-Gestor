<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\FtthPon;
use App\Models\FtthFiberBox;
use App\Models\FtthCableFiberBox;
use App\Models\FtthFiberCable;
use App\Models\FtthSplinter;
use App\Models\FtthSplinterLoss;
use App\Models\FtthFiberFusion;

use App\Services\FtthSignalPropagationService;

class FiberBoxController extends Controller
{

    public function index(Request $request)
    {
        $pon = FtthPon::findOrFail($request->pon);

        $ponrede = FtthPon::where('olt', 'REDE')->value('id');

        $boxes = FtthFiberBox::with('pon')
            ->where(function ($query) use ($pon, $ponrede) {
                $query->where('pon_id', $pon->id);

                if ($ponrede) {
                    $query->orWhere('pon_id', $ponrede);
                }
            })
            ->get();

        $lastnumber = FtthFiberBox::query()->max('number');

        $nextnumbermax = $lastnumber
            ? $lastnumber + 1
            : 1;

        /*
    |--------------------------------------------------------------------------
    | PRÓXIMO NÚMERO DISPONÍVEL
    |--------------------------------------------------------------------------
    */

        $existsOne = FtthFiberBox::where('number', 1)->exists();

        if (!$existsOne) {
            $nextnumber = 1;
        } else {
            $result = \DB::selectOne("
            SELECT MIN(t1.number + 1) AS next
            FROM ftth_fiber_boxes t1

            LEFT JOIN ftth_fiber_boxes t2
                ON t2.number = t1.number + 1
                AND t2.deleted_at IS NULL

            WHERE t1.deleted_at IS NULL
            AND t2.number IS NULL
        ");

            $nextnumber = $result->next ?? ($lastnumber + 1);
        }

        /*
    |--------------------------------------------------------------------------
    | CABOS DAS BOXES
    |--------------------------------------------------------------------------
    */

        $boxIds = $boxes->pluck('id');

        $cables = FtthCableFiberBox::with([
            'inputFiberBox',
            'outputFiberBox',
            'routePoints',
        ])
            ->where(function ($query) use ($boxIds) {
                $query->whereIn('input_fiber_box_id', $boxIds)
                    ->orWhereIn('output_fiber_box_id', $boxIds);
            })
            ->get();

        if ($request->map === 'yes') {
            return view('ftth.fiber-box.map', compact(
                'boxes',
                'pon',
                'nextnumber',
                'nextnumbermax',
                'cables'
            ));
        }

        return view('ftth.fiber-box.index', compact(
            'boxes',
            'nextnumber',
            'nextnumbermax',
            'pon'
        ));
    }

    public function ponsmap(Request $request)
    {
        $olt = $request->olt;

        $infoolt = FtthPon::where('olt', $olt)->get();

        if ($olt === 'REDE') {
            $boxes = FtthFiberBox::with('pon')->get();
        } else {
            $boxes = FtthFiberBox::with('pon')
                ->whereHas('pon', function ($query) use ($olt) {
                    $query->where('olt', $olt)
                        ->orWhere('olt', 'REDE');
                })
                ->get();
        }

        $boxIds = $boxes->pluck('id');

        $cables = FtthCableFiberBox::with([
            'inputFiberBox',
            'outputFiberBox',
            'routePoints',
        ])
            ->where(function ($query) use ($boxIds) {
                $query->whereIn('input_fiber_box_id', $boxIds)
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
        $request->validate([
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('ftth_fiber_boxes', 'number')
                    ->whereNull('deleted_at')
            ],
            'info' => 'nullable|string',
            'coordinates' => 'required|string',
            'pon_id' => 'required|exists:ftth_pons,id',
        ], [
            'number.unique' => 'Esse número já está em uso.',
        ]);

        FtthFiberBox::create([
            'number' => $request->number,
            'info' => $request->info,
            'coordinates' => $request->coordinates,
            'pon_id' => $request->pon_id
        ]);

        return redirect()->back()->with('success', 'Caixa criada');
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

        $boxesall = FtthFiberBox::orderBy('number')->get();


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
            'boxesall',
            'cables',
            'colorCablePon',
            'fibers',
            'allFibers',
            'splinters',
            'losses',
            'fusions'

        ));
    }

    public function updatesignal(Request $request, $id)
    {
        $request->validate([
            'optical_power' => 'required|numeric',
        ]);

        $fiber = FtthFiberCable::findOrFail($id);
        $fiber->optical_power = $request->optical_power;
        $fiber->save();

        return redirect()->back()->with('success', 'Sinal atualizado com sucesso.');
    }



    public function recalculate(
        int $boxId,
        FtthSignalPropagationService $signalService
    ) {
        try {
            $processed = $signalService->recalculateBox($boxId);

            return back()->with(
                'success',
                "Rede recalculada com sucesso. {$processed} fibras processadas."
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Não foi possível recalcular a rede: '
                    . $exception->getMessage()
            );
        }
    }
}
