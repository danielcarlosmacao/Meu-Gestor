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
        $vacations = Vacation::with('Collaborator')->paginate($perPage);
        $collaborators = Collaborator::orderBy('name')->get();

        return view('vacation_manager.vacations.index', compact('vacations', 'collaborators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'information' => 'nullable|string',
        ]);

        Vacation::create($request->only(['collaborator_id', 'start_date', 'end_date', 'information']));

        return redirect()->route('vacation_manager.vacations.index')->with('success', 'Férias cadastradas com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'information' => 'nullable|string',
        ]);

        $vacation = Vacation::findOrFail($id);
        $vacation->update($request->only(['collaborator_id', 'start_date', 'end_date', 'information']));

        return redirect()->route('vacation_manager.vacations.index')->with('success', 'Férias atualizadas com sucesso.');
    }

    public function destroy($id)
    {
        $vacation = Vacation::findOrFail($id);
        $vacation->delete();

        return redirect()->route('vacation_manager.vacations.index')->with('success', 'Férias excluídas com sucesso.');
    }
}
