<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Plate;
use App\Services\SettingService;

class PlateController extends Controller
{
    public function index(SettingService $settingService){

        $perPage = $settingService->getPerPage();

        $plates = plate::orderBy('name', 'asc')->paginate($perPage);

        return view('tower.plate',['plates' => $plates]);

    }

    public function store(Request $request){

        $plate = new plate;
        $plate->name = $request->name;
        $plate->amps = $request->amps;
        $plate->watts = $request->watts;

        $plate->save();

         activity()
            ->causedBy(auth()->user())
            ->performedOn($plate)
            ->withProperties([
                'new' => $plate->toArray()
            ])
            ->log('placa Criada');
        return redirect()->route('plate.index')->with('success', 'Placa criada com sucesso!');

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'watts' => 'required|numeric|min:0|max:10000',
            'amps' => 'required|numeric|min:0|max:1000',
        ]);
        
        $oldData = $request->toArray();

        $plate = Plate::findOrFail($id);

        $plate->update([
            'name' => $request->name,
            'watts' => $request->watts,
            'amps' => $request->amps,
        ]);

         activity()
            ->causedBy(auth()->user())
            ->performedOn($plate) 
            ->withProperties([
                'old' => $oldData,
                'new' => $plate->toArray() 
            ])
            ->log('Placa Atualizada');


        return redirect()->route('plate.index')->with('success', 'Placa atualizada com sucesso!');
    }

    public function destroy($id){

        $plate = plate::findOrFail($id);
        
        $oldData = $plate->toArray();
        $plate->delete(); // Soft delete, se usar SoftDeletes

          activity()
            ->causedBy(auth()->user())
            ->performedOn($plate)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Placa Deletada');

        return redirect()->route('plate.index')->with('success', 'Placa deletada com sucesso!');
    }
}
