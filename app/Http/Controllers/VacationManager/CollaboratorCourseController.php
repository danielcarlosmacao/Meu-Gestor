<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Collaborator;
use App\Models\CollaboratorCourse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollaboratorCourseController extends Controller
{
    public function index()
    {
        $courses = CollaboratorCourse::with('collaborator')->paginate(20);
        $collaborators = Collaborator::where('status', 'active')->orderBy('name')->get();

        return view('vacation_manager.courses.index', compact('courses', 'collaborators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        // Upload do PDF
        $path = $request->file('file')->store('courses', 'public');


        CollaboratorCourse::create([
            'collaborator_id' => $validated['collaborator_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'valid_until' => $validated['valid_until'],
            'file_path' => $path,
        ]);

        return back()->with('success', 'Curso registrado com sucesso!');
    }

    public function show(CollaboratorCourse $course)
    {
        return response()->file(storage_path('app/public/' . $course->file_path));
    }

    public function update(Request $request, CollaboratorCourse $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'collaborator_id' => 'required|exists:collaborators,id',
            'valid_until' => 'required|date',
        ]);

        $course->update($validated);

        return redirect()
            ->route('vacation_manager.collaborator.courses.index')
            ->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(CollaboratorCourse $course)
    {
        if (Storage::disk('public')->exists($course->file_path)) {
            Storage::disk('public')->delete($course->file_path);
        }

        $course->delete();

        return back()->with('success', 'Curso removido!');
    }
}
