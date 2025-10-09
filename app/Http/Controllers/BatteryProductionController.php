<?php

namespace App\Http\Controllers;

use App\Models\BatteryProduction;

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

        // Verificar se já existe uma bateria ativa para esta torre
        if ($validated['active'] === 'yes') {
            $jaExiste = BatteryProduction::where('tower_id', $towerId)
                ->where('active', 'yes')
                ->exists();

            if ($jaExiste) {
                return back()
                    ->withErrors(['active' => 'Já existe uma bateria ativa para esta torre.'])
                    ->withInput();
            }
        }

        $batteryProduction = BatteryProduction::create($validated);

        // Registrar log de atividade
        activity()
            ->performedOn($batteryProduction) // o "subject" será o BatteryProduction criado
            ->causedBy(auth()->user())        // quem fez a ação
            ->withProperties([
                'tower_id' => $towerId,
                'battery_id' => $validated['battery_id'],
                'amount' => $validated['amount'],
                'active' => $validated['active'],
            ])
            ->log('Adicionou uma nova bateria à torre');

        return redirect()->route('tower.show', $towerId)->with('success', 'Bateria adicionada com sucesso!');
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

        if ($validated['active'] === 'yes') {
            $jaExiste = BatteryProduction::where('tower_id', $bp->tower_id)
                ->where('active', 'yes')
                ->where('id', '!=', $bp->id)
                ->exists();

            if ($jaExiste) {
                return response()->json(['message' => 'Já existe uma bateria ativa'], 422);
            }
        }

        // capturar valores antes 
        $oldValues = $bp->getOriginal();

        $bp->update($validated);

        // capturar valores depois
        $newValues = $bp->getAttributes();

        // registrar log
        activity()
            ->performedOn($bp)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $newValues,
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
