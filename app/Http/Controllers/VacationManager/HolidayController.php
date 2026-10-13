<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index()
    {
        // Ordena por mês e dia para facilitar a visualização anual
        $holidays = DB::table('holidays')
            ->orderByRaw('MONTH(date) ASC, DAY(date) ASC')
            ->get();

        return view('vacation_manager.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:appellant,unique',
        ]);

        DB::table('holidays')->insert([
            'name' => $request->input('name'), 
            'date' => $request->input('date'),
            'type' => $request->input('type'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('vacation_manager.holidays.index')
            ->with('success', 'Feriado cadastrado com sucesso!');
    }

    public function destroy($id)
    {
        DB::table('holidays')->where('id', $id)->delete();

        return redirect()->route('vacation_manager.holidays.index')
            ->with('success', 'Feriado excluído com sucesso!');
    }
}
