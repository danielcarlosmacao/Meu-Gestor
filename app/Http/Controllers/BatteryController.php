<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Battery;
use App\Services\SettingService;

class BatteryController extends Controller
{
    public function index(SettingService $settingService)
    {

        $perPage = $settingService->getPerPage();

        $batterys = battery::orderBy('name', 'asc')->paginate($perPage);

        return view('tower.battery', ['batterys' => $batterys]);

    }

    public function store(Request $request)
    {

        $battery = new battery;
        $battery->name = $request->name;
        $battery->mark = $request->mark;
        $battery->amps = $request->amps;
        $battery->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($battery)
            ->withProperties([
                'new' => $battery->toArray()
            ])
            ->log('Bateria Criada');

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

        $oldData = $battery->toArray();

        $battery->update($validated);


        activity()
            ->causedBy(auth()->user())
            ->performedOn($battery)
            ->withProperties([
                'old' => $oldData,
                'new' => $battery->toArray()
            ])
            ->log('Bateria Atualizada');

        return redirect()->route('battery.index')->with('success', 'Bateria atualizada com sucesso!');
    }

    public function destroy($id)
    {

        $battery = battery::findOrFail($id);

        $oldData = $battery->toArray();
        $battery->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($battery)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Bateria Deletada');

        return redirect()->route('battery.index')->with('success', 'Bateria deletada com sucesso!');

    }
}
