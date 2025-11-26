<?php

namespace App\Http\Controllers;

use App\Models\BatteryProduction;
use App\Models\Battery;
use App\Models\Tower;
use App\Models\TowerSummaries;

use Illuminate\Http\Request;
use Carbon\Carbon;

class BatteryProductionController extends Controller
{
    public function store(Request $request, $towerId)
    {
        $validated = $request->validate([
            'battery_id' => 'required|exists:batterys,id',
            'info' => 'nullable|string',
            'amount' => 'required|integer',
            'installation_date' => 'nullable|date',
            'removal_date' => 'nullable|date',
            'active' => 'required|in:yes,no',
        ]);

        $validated['tower_id'] = $towerId;

        // Se installation_date vier vazio → pega a data de hoje
        if (empty($validated['installation_date'])) {
            $validated['installation_date'] = now()->toDateString();
        }

        // -------------------------------------------------------
        // 🔥 Se for ativa → desativar a bateria ativa anterior
        // -------------------------------------------------------
        if ($validated['active'] === 'yes') {

            $oldActive = BatteryProduction::where('tower_id', $towerId)
                ->where('active', 'yes')
                ->first();

            if ($oldActive) {
                $oldActive->update([
                    'active' => 'no',
                    'removal_date' => now()->toDateString(),
                ]);
            }
        }

        // Primeiro cria o registro sem percentage
        $bp = BatteryProduction::create($validated);

        // -------------------------------------------------------
        // 🔥 Calcular percentage SOMENTE se for ativa
        // -------------------------------------------------------
        if ($bp->active === 'yes') {

            $tower = $bp->tower;
            $battery = $bp->battery;
            $summary = $tower->summary;

            if ($tower && $battery && $summary) {

                $voltageRatio = $tower->voltage / 12;

                $totalAmp = $voltageRatio > 0
                    ? ($bp->amount * $battery->amps) / $voltageRatio
                    : 0;

                $percentage = $totalAmp > 0
                    ? ($summary->battery_required / $totalAmp) * 100
                    : 0;

                $bp->update([
                    'production_percentage' => round($percentage, 2)
                ]);
            }
        }

        // -------------------------------------------------------
        // LOG
        // -------------------------------------------------------
        activity()
            ->performedOn($bp)
            ->causedBy(auth()->user())
            ->withProperties([
                'tower_id' => $towerId,
                'battery_id' => $validated['battery_id'],
                'amount' => $validated['amount'],
                'active' => $validated['active'],
            ])
            ->log('Adicionou uma nova bateria à torre');

        return redirect()
            ->route('tower.show', $towerId)
            ->with('success', 'Bateria adicionada com sucesso!');
    }




    public function edit($id)
    {
        $bp = BatteryProduction::with('battery')->findOrFail($id);
        return response()->json($bp);
    }

    public function update(Request $request, $id)
    {
        $bp = BatteryProduction::findOrFail($id);

        $validated = $request->validate([
            'battery_id' => 'required|exists:batterys,id',
            'info' => 'nullable|string',
            'amount' => 'nullable|integer',
            'installation_date' => 'nullable|date',
            'removal_date' => 'nullable|date',
            'active' => 'required|in:yes,no',
            'production_percentage' => 'nullable|numeric',
        ]);

        // Se for ativar esta, desativa a anterior
        if ($validated['active'] === 'yes') {
            $oldActive = BatteryProduction::where('tower_id', $bp->tower_id)
                ->where('active', 'yes')
                ->where('id', '!=', $bp->id)
                ->first();

            if ($oldActive) {
                $oldActive->update([
                    'active' => 'no',
                    'removal_date' => now(),
                ]);
            }
        }

        // valores antigos
        $oldValues = $bp->getOriginal();

        // aplica update
        $bp->update($validated);

        // ---------------------------------------------------------
        // 🔥 NOVA REGRA: Se removal_date for apagada, zera o %
        // ---------------------------------------------------------
        if (empty($bp->removal_date)) {
            $bp->update([
                'production_percentage' => null
            ]);
        } else {
            // -----------------------------------------------------
            // Apenas recalcula % se ainda for ativa
            // -----------------------------------------------------
            if ($bp->active === 'yes') {

                $tower = $bp->tower;
                $battery = $bp->battery;
                $summary = $tower->summary;

                if ($summary) {

                    $voltageRatio = $tower->voltage / 12;

                    $totalAmp =
                        $voltageRatio > 0
                        ? ($bp->amount * $battery->amps) / $voltageRatio
                        : 0;

                    $production_percentage =
                        $totalAmp > 0
                        ? ($summary->battery_required / $totalAmp) * 100
                        : 0;

                    $bp->update([
                        'production_percentage' => round($production_percentage, 2)
                    ]);
                }
            }
        }

        // log
        activity()
            ->performedOn($bp)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $bp->getAttributes(),
            ])
            ->log('Atualizou uma bateria da torre');

        return response()->json(['message' => 'Atualizado com sucesso']);
    }


    public function destroy($id)
    {
        $bp = BatteryProduction::findOrFail($id);

        // Guardar os dados antes da exclusão para log
        $oldValues = $bp->getAttributes();

        $bp->delete();

        // Registrar log de exclusão
        activity()
            ->performedOn($bp)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues
            ])
            ->log('Removeu uma bateria da torre');

        return response()->json(['message' => 'Bateria excluída com sucesso']);
    }

    public function recalcularPercentuais($towerId)
    {
        $tower = Tower::findOrFail($towerId);

        $summary = TowerSummaries::where('tower_id', $towerId)->first();
        if (!$summary || !$summary->battery_required) {
            return redirect()
                ->route('tower.show', $towerId)
                ->with('error', 'battery_required não encontrado para esta torre.');
        }

        $baterias = BatteryProduction::where('tower_id', $towerId)->get();

        foreach ($baterias as $bp) {

            // só recalcula se removal_date estiver preenchido
            if ($bp->removal_date && is_null($bp->production_percentage)) {

                $voltageRatio = $tower->voltage / 12;

                $totalAmp =
                    $voltageRatio > 0
                    ? ($bp->amount * $bp->battery->amps) / $voltageRatio
                    : 0;

                $percentage =
                    $totalAmp > 0
                    ? ($summary->battery_required / $totalAmp) * 100
                    : 0;

                $bp->production_percentage = round($percentage, 2);
                $bp->save();
            }
        }

        return redirect()
            ->route('tower.show', $towerId)
            ->with('success', 'Percentuais recalculados com sucesso!');
    }



    public function report(Request $request, $battery_id)
    {
        $status = $request->input('status', 'todas'); // padrão: todas

        // Busca com filtro
        $query = BatteryProduction::with(['battery', 'tower'])->where('battery_id', $battery_id);

        if ($status === 'ativas') {
            $query->where('active', 'yes');
        } elseif ($status === 'inativas') {
            $query->where('active', 'no');
        }

        $productions = $query->get();

        // Processamento
        $totalDias = 0;
        $total = 0;

        foreach ($productions as $p) {
            $start = Carbon::parse($p->installation_date);
            $end = $p->removal_date ? Carbon::parse($p->removal_date) : now();
            $dias = $start->diffInDays($end);
            $anos = floor($dias / 365);
            $meses = floor(($dias % 365) / 30);

            $p->tempo_formatado = "{$anos} anos e {$meses} meses";
            $p->data_instalacao_formatada = $start->format('d-m-Y');
            $p->data_remocao_formatada = $p->removal_date ? $end->format('d-m-Y') : '';

            $totalDias += $dias;
            $total++;
        }

        $mediaAnos = $total ? number_format(($totalDias / $total) / 365, 2) : 0;

        return view('tower.reportbattery', compact('productions', 'mediaAnos', 'status'));
    }


}
