<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Equipment;
use App\Services\SettingService;

class EquipmentController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();

        $equipments = Equipment::orderBy('name', 'asc')->paginate($perPage);

        return view('tower.equipment', ['equipments' => $equipments]);

    }

    public function store(Request $request)
    {

        $Equipment = new Equipment;
        $Equipment->name = $request->name;
        $Equipment->watts = $request->watts;

        $Equipment->save();
        return redirect(route('equipment.index'));

    }

    public function update(Request $request, $id)
    {
        // Validação básica (opcional, mas recomendada)
        $request->validate([
            'name' => 'required|string|max:255',
            'watts' => 'required|numeric',
        ]);

        // Busca o equipamento pelo ID
        $equipment = Equipment::findOrFail($id);

        // Atualiza os campos
        $equipment->name = $request->name;
        $equipment->watts = $request->watts;

        // Salva no banco
        $equipment->save();

        // Redireciona para a lista com mensagem (opcional)
        return redirect()->route('equipment.index')
            ->with('success', 'Equipamento atualizado com sucesso!');
    }


    public function destroy($id)
    {

        $equipment = Equipment::findOrFail($id);
        $equipment->delete(); // Soft delete, se usar SoftDeletes
        return response()->json(['message' => 'Equipamento deletada com sucesso.']);
    }
}
