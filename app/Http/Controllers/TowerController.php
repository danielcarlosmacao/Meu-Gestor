<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tower;
use App\Models\Battery;
use App\Models\Equipment;
use App\Models\Plate;
use App\Services\SettingService;
use App\Models\Option;
use Carbon\Carbon;

class TowerController extends Controller
{

    public function index(SettingService $settingService, Request $request)
    {
        // Se clicar para ordenar, usar perPage=100
        $perPagesystem = $settingService->getPerPage();
        $perPage = $request->get('perPage', $perPagesystem);

        // Horas de geração
        $hoursGeneration = Option::where('reference', 'hours_Generation')->value('value') ?? 5;

        // Carrega torres + relacionamentos
        $paginator = Tower::with([
            'activeBattery.battery',
            'plateProductions.plate',
            'summary',

            // ⬇️ AQUI está a correção
            'equipmentProductions' => function ($q) {
                $q->where('active', 'yes')
                    ->with('equipment');
            },
        ])
            ->withCount([
                'equipmentProductions as active_equipments_count' => function ($q) {
                    $q->where('active', 'yes');
                }
            ])
            ->orderBy('name', 'asc')
            ->paginate($perPage);


        //---------------------------------------------------------------------
        // MONTA ARRAY towerData PARA A VIEW
        //---------------------------------------------------------------------
        $towerData = $paginator->getCollection()->map(function ($tower) use ($hoursGeneration) {

            // -------------------------
            // EQUIPAMENTOS
            // -------------------------
            $equipQty = $tower->active_equipments_count ?? 0;

            // soma total de watts dos equipamentos ativos
            $wattsEquipments = 0;

            foreach ($tower->equipmentProductions as $ep) {
                if ($ep->active === 'yes' && $ep->equipment) {
                    $watts = $ep->equipment->watts ?? 0;
                    $amount = $ep->amount ?? 1;
                    $wattsEquipments += ($watts * $amount);
                }
            }

            // -------------------------
            // BATERIA
            // -------------------------
            $batteryProd = $tower->activeBattery ?? null;
            $battery = $batteryProd?->battery ?? null;

            $batteryPercentage = 0;

            if ($batteryProd && $battery) {

                $voltageRatio = $tower->voltage > 0 ? ($tower->voltage / $battery->voltage) : 0;

                $batteryAmps = $battery->amps ?? 0;
                $amount = $batteryProd->amount ?? 0;

                $totalAmp = ($voltageRatio > 0 && $batteryAmps > 0)
                    ? ($amount * $batteryAmps) / $voltageRatio
                    : 0;

                $batteryRequired = $tower->summary->battery_required ?? 0;

                $batteryPercentage = $totalAmp > 0
                    ? ($batteryRequired / $totalAmp) * 100
                    : 0;
            }

            // -------------------------
            // DATA INSTALAÇÃO + TEMPO
            // -------------------------
            $installDateFormatted = '-';
            $installDateOrd = null;
            $productionTimeYearsFloat = null;
            $productionTimeLabel = '-';

            if ($batteryProd?->installation_date) {
                try {
                    $cd = Carbon::parse($batteryProd->installation_date);

                    $installDateFormatted = $cd->format('d/m/Y');
                    $installDateOrd = $cd->format('Y-m-d');

                    $productionTimeYearsFloat = $cd->floatDiffInYears(now());

                    $anos = floor($productionTimeYearsFloat);
                    $meses = floor(($productionTimeYearsFloat - $anos) * 12);

                    $productionTimeLabel = "{$anos} anos e {$meses} meses";
                } catch (\Exception $e) {
                    // mantém padrão
                }
            }

            // -------------------------
            // PLACA
            // -------------------------
            $plateProd = $tower->plateProductions->first() ?? null;
            $plate = $plateProd?->plate ?? null;

            $summary = $tower->summary;
            $totalWattsPlaca = $summary->watts_plate ?? ($plate?->watts ?? 0);

            $consumptionAhDay = $summary->consumption_ah_day ?? 0;
            $ampsPlate = $summary->amps_plate ?? ($plate?->amps ?? 0);

            $plateRequired = $hoursGeneration > 0 ? ($consumptionAhDay / $hoursGeneration) : 0;
            $platePercentage = $ampsPlate > 0 ? ($plateRequired / $ampsPlate) * 100 : 0;

            //---------------------------------------------------------------------
            // RETORNO
            //---------------------------------------------------------------------
            return [
                'id' => $tower->id,
                'name' => $tower->name,

                'voltage' => $tower->voltage,
                'equipments' => $equipQty,
                'watts_equipments' => round($wattsEquipments, 2), // ✅ NOVO

                // bateria
                'battery' => $battery?->name ?? 'Sem bateria',
                'battery_percentage' => round($batteryPercentage, 2),

                // datas
                'battery_install_date' => $installDateFormatted,
                'battery_install_ord' => $installDateOrd,

                // produção
                'production_time' => $productionTimeLabel,
                'production_ord' => $productionTimeYearsFloat,

                // placa
                'total_watts_placa' => round($totalWattsPlaca, 2),
                'total_amps_placa' => round($ampsPlate, 2),
                'plate_percentage' => round($platePercentage, 2),
            ];
        })->toArray();

        //---------------------------------------------------------------------
        // VIEW
        //---------------------------------------------------------------------
        return view('tower.index', [
            'towerData' => $towerData,
            'pagination' => $paginator,
        ]);
    }



    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'voltage' => 'required|integer',
        ]);

        $tower = new Tower;
        $tower->name = $request->name;
        $tower->voltage = $request->voltage;

        $tower->save();

        // Criação do resumo associado à torre
        $tower->summary()->create([
            'consumption_ah_day' => '0',
            'time_ah_consumption' => '0',
            'battery_required' => '0',
            'watts_plate' => '0',
            'amps_plate' => '0',
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($tower)
            ->withProperties([
                'new' => $tower->toArray()
            ])
            ->log('Torre Criada');

        return redirect(route('tower.index'));

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'voltage' => 'required|integer',
        ]);

        $tower = Tower::findOrFail($id);

        $oldData = $tower->toArray();

        $tower->update([
            'name' => $request->name,
            'voltage' => $request->voltage,
        ]);

        // 🔥 RECALCULA CONSUMO (voltage pode ter mudado!)
        $tower->updateConsumptionAh();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($tower)
            ->withProperties([
                'old' => $oldData,
                'new' => $tower->toArray()
            ])
            ->log('Torre Atualizada');

        return redirect()->back()->with('success', 'Torre atualizada com sucesso!');
    }


    public function destroy($id)
    {

        $tower = Tower::findOrFail($id);
        $oldData = $tower->toArray();
        $tower->delete(); // Soft delete, se usar SoftDeletes


        activity()
            ->causedBy(auth()->user())
            ->performedOn($tower)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Torre Deletada');

        return redirect()->back()->with('success', 'Torre deletada com sucesso!');

    }

    public function show($id)
    {
        $tower = Tower::with([
            'batteryProductions.battery',
            'plateProductions.plate',
            'equipmentProductions.equipment',
        ])->findOrFail($id);

        $equipments = Equipment::orderBy('name', 'asc')->get();
        $batteries = Battery::orderBy('name', 'asc')->get();
        $plates = Plate::orderBy('name', 'asc')->get();
        $summary = $tower->summary;
        $hours_Generation = Option::where('reference', 'hours_Generation')->value('value') ?? 5;
        $hours_autonomy = Option::where('reference', 'hours_autonomy')->value('value') ?? 48;

        $consumptionAhDay = $tower->summary->consumption_ah_day ?? 0;
        $platerrequire = $hours_Generation > 0
            ? $consumptionAhDay / $hours_Generation
            : 0;

        return view('tower.show', compact('tower', 'equipments', 'batteries', 'plates', 'summary', 'hours_Generation', 'hours_autonomy', 'platerrequire'));
    }


    public function repairsummary()
    {
        // Busca todas as torres
        $towers = Tower::all();

        // Contador opcional para saber quantas summaries foram criadas
        $criadas = 0;

        foreach ($towers as $tower) {
            // Se a torre não tem summary associado
            if (!$tower->summary) {
                $tower->summary()->create([
                    'consumption_ah_day' => 0,
                    'time_ah_consumption' => 0,
                    'battery_required' => 0,
                    'watts_plate' => 0,
                    'amps_plate' => 0,
                ]);
                // Força recarregar relações e dados atualizados
                $tower = $tower->fresh(['summary', 'equipmentProductions', 'plateProductions']);

                $criadas++;
            }
            // Recalcula com base em dados atuais
            $tower->updateConsumptionAh();
            $tower->updatePlate();
        }


        return redirect()->back()->with('success', "Summaries verificadas. Criadas: $criadas");
    }


}
