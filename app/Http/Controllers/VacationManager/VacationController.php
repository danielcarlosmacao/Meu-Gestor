<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacation;
use App\Models\Collaborator;

use App\Services\SettingService;


class VacationController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $vacations = Vacation::with('Collaborator')->orderBy('start_date','desc')->paginate($perPage);
        $collaborators = Collaborator::orderBy('name')->get();

        return view('vacation_manager.vacations.index', compact('vacations', 'collaborators'));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'collaborator_id' => 'required|exists:collaborators,id',
        'start_date'      => 'required|date',
        'end_date'        => 'required|date|after_or_equal:start_date',
        'information'     => 'nullable|string',
    ]);

    $vacation = Vacation::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($vacation)
        ->withProperties([
            'new' => $vacation->toArray()
        ])
        ->log('Férias Criada');

    return redirect()
        ->route('vacation_manager.vacations.index')
        ->with('success', 'Férias cadastradas com sucesso.');
}

public function update(Request $request, $id)
{
    $data = $request->validate([
        'collaborator_id' => 'required|exists:collaborators,id',
        'start_date'      => 'required|date',
        'end_date'        => 'required|date|after_or_equal:start_date',
        'information'     => 'nullable|string',
    ]);

    $vacation = Vacation::findOrFail($id);
    $oldData = $vacation->toArray();

    $vacation->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($vacation)
        ->withProperties([
            'old' => $oldData,
            'new' => $vacation->toArray()
        ])
        ->log('Férias Atualizada');

    return redirect()
        ->route('vacation_manager.vacations.index')
        ->with('success', 'Férias atualizadas com sucesso.');
}

public function destroy($id)
{
    $vacation = Vacation::findOrFail($id);
    $oldData = $vacation->toArray();

    $vacation->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($vacation)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Férias Deletada');

    return redirect()
        ->route('vacation_manager.vacations.index')
        ->with('success', 'Férias excluídas com sucesso.');
}

}
