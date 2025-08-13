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
        return redirect()->route('plate.index')->with('success', 'Placa criada com sucesso!');

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'watts' => 'required|numeric|min:0|max:10000',
            'amps' => 'required|numeric|min:0|max:1000',
        ]);

        $plate = Plate::findOrFail($id);
        $plate->update([
            'name' => $request->name,
            'watts' => $request->watts,
            'amps' => $request->amps,
        ]);

        return redirect()->route('plate.index')->with('success', 'Placa atualizada com sucesso!');
    }

    public function destroy($id){

        $plate = plate::findOrFail($id);
        $plate->delete(); // Soft delete, se usar SoftDeletes
        return redirect()->route('plate.index')->with('success', 'Placa deletada com sucesso!');
    }
}
