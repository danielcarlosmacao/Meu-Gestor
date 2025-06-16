<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VacationCalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Busca todas as férias que caem em qualquer parte do ano
        $vacations = Vacation::with('Collaborator')
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->get();

        return view('vacation_manager.calendar.index', compact('vacations', 'year'));
    }


    public function interactive()
    {
        return view('vacation_manager.calendar.interactive');
    }

    /**
     * Retorna os eventos de férias em formato JSON.
     */
    public function events()
    {
        $vacations = Vacation::with('Collaborator')->whereNull('deleted_at')->get();

        $events = [];

        foreach ($vacations as $vacation) {
            if (!$vacation->collaborator) continue;

            $events[] = [
                'id'    => $vacation->id,
                'title' => $vacation->collaborator->name,
                'start' => $vacation->start_date->toDateString(),
                'end'   => Carbon::parse($vacation->end_date)->addDay()->toDateString(), // FullCalendar is exclusive on end
                'color' => $vacation->collaborator->color,
                'allDay' => true,
            ];
        }

        return response()->json($events);
    }

    /**
     * Atualiza uma férias via drag & drop.
     */
    public function updateEvent(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required|exists:vacations,id',
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $vacation = Vacation::findOrFail($validated['id']);
        $vacation->start_date = $validated['start'];
        $vacation->end_date = Carbon::parse($validated['end'])->subDay(); // Ajuste inverso
        $vacation->save();

        return response()->json(['success' => true]);
    }
}
