<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Battery;
use App\Services\SettingService;

class BatteryController extends Controller
{
    public function index(SettingService $settingService){

        $perPage = $settingService->getPerPage();

        $batterys = battery::orderBy('name', 'asc')->paginate($perPage);

        return view('tower.battery', ['batterys' => $batterys]);
        
    }

    public function store(Request $request){

        $battery = new battery;
        $battery->name = $request->name;
        $battery->mark = $request->mark;
        $battery->amps = $request->amps;

        $battery->save();
        return redirect()->route('battery.index')->with('success', 'Bateria criada com sucesso!');

    }

    public function update(Request $request, $id)
    {
        $battery = Battery::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mark' => 'required|string|max:255',
            'amps' => 'required|numeric|min:0|max:1000',
        ]);

        $battery->update($validated);

        return redirect()->route('battery.index')->with('success', 'Bateria atualizada com sucesso!');
    }

    public function destroy($id){

        $battery = battery::findOrFail($id);
        $battery->delete(); 
        return redirect()->route('battery.index')->with('success', 'Bateria deletada com sucesso!');

    }
}
