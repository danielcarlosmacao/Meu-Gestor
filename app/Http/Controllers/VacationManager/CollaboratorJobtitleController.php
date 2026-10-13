<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CollaboratorJobtitle;

class CollaboratorJobtitleController extends Controller
{
    public function index()
    {
        $jobtitles = CollaboratorJobtitle::all();
        return view('vacation_manager.collaborators_jobtitles.index', compact('jobtitles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        CollaboratorJobtitle::create($request->all());

        return redirect()->route('vacation_manager.collaborators.jobtitles.index')
            ->with('success', 'Cargo criado com sucesso.');
    }

    public function update(Request $request, CollaboratorJobtitle $jobtitle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $jobtitle->update($request->all());

        return redirect()->route('vacation_manager.collaborators.jobtitles.index')
            ->with('success', 'Cargo atualizado com sucesso.');
    }

   
}
