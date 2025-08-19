<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tower;
use App\Models\Battery;
use App\Models\Equipment;
use App\Models\Plate;
use App\Services\SettingService;
use App\Models\Option;

class TowerController extends Controller
{
    public function index(SettingService $settingService)
    {

        $perPage = $settingService->getPerPage();


        $towers = Tower::with([
            'activeBattery',
            'summary',
        ])
            ->withCount([
                'equipmentProductions as active_equipments_count' => function ($query) {
                    $query->where('active', 'yes');
                }
            ])
            ->orderBy('name', 'asc')
            ->paginate($perPage);

            
            $hours_Generation = Option::where('reference', 'hours_Generation')->value('value') ?? 5;
            


        return view('tower.index', compact(['towers', 'hours_Generation' ]));

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
