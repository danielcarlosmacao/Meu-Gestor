<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collaborator;
use App\Services\SettingService;

class CollaboratorController extends Controller
{
    
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        $collaborators = Collaborator::orderBy('status', 'asc')->orderBy('name', 'asc')->paginate($perPage);
        return view('vacation_manager.collaborators.index', compact('collaborators'));
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'name'           => 'required|string',
        'admission_date' => 'required|date',
        'color'          => 'required|string',
        'status'         => 'required|in:active,inactive',
    ]);

    $collaborator = Collaborator::create($data);

    // 🔹 Log de criação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($collaborator)
        ->withProperties([
            'new' => $collaborator->toArray()
        ])
        ->log('Colaborador Criado');

    return redirect()
        ->route('vacation_manager.collaborators.index')
        ->with('success', 'Colaborador criado com sucesso.');
}

public function update(Request $request, $id)
{
    $data = $request->validate([
        'name'           => 'required|string',
        'admission_date' => 'required|date',
        'color'          => 'required|string',
        'status'         => 'required|in:active,inactive',
    ]);

    $collaborator = Collaborator::findOrFail($id);
    $oldData = $collaborator->toArray();

    $collaborator->update($data);

    // 🔹 Log de atualização
    activity()
        ->causedBy(auth()->user())
        ->performedOn($collaborator)
        ->withProperties([
            'old' => $oldData,
            'new' => $collaborator->toArray()
        ])
        ->log('Colaborador Atualizado');

    return redirect()
        ->route('vacation_manager.collaborators.index')
        ->with('success', 'Colaborador atualizado com sucesso.');
}

public function destroy($id)
{
    $collaborator = Collaborator::findOrFail($id);
    $oldData = $collaborator->toArray();

    $collaborator->delete();

    // 🔹 Log de exclusão
    activity()
        ->causedBy(auth()->user())
        ->performedOn($collaborator)
        ->withProperties([
            'old' => $oldData
        ])
        ->log('Colaborador Deletado');

    return redirect()
        ->route('vacation_manager.collaborators.index')
        ->with('success', 'Colaborador excluído com sucesso.');
}

}
