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
        $collaborators = Collaborator::orderBy('name', 'asc')->paginate($perPage);
        return view('vacation_manager.collaborators.index', compact('collaborators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'admission_date' => 'required|date',
            'color' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        Collaborator::create($request->all());

        return redirect()->route('vacation_manager.collaborators.index')->with('success', 'Colaborador criado com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'admission_date' => 'required|date',
            'color' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $collaborator = Collaborator::findOrFail($id);
        $collaborator->update($request->all());

        return redirect()->route('vacation_manager.collaborators.index')->with('success', 'Colaborador atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $collaborator = Collaborator::findOrFail($id);
        $collaborator->delete();

        return redirect()->route('vacation_manager.collaborators.index')->with('success', 'Colaborador excluído com sucesso.');
    }
}
