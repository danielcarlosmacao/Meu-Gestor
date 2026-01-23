<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return view('task.calendar');
    }

    // Eventos para o calendário
    public function events()
    {
        return Task::all()->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->task_date,
                'color' => $task->color,
            ];
        });
    }

    // Lista tarefas do dia
    public function day($date)
    {
        $tasks = Task::whereDate('task_date', $date)->get();
        return view('task.day', compact('tasks', 'date'));
    }

    public function store(Request $request)
    {
        Task::create($request->all());
        return back()->with('success', 'Tarefa criada');
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return back()->with('success', 'Tarefa atualizada');
    }

    public function toggleStatus(Task $task)
    {
        $task->status = $task->status === 'completed'
            ? 'pending'
            : 'completed';

        $task->save();

        return back()->with('success', 'Status atualizado');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Tarefa removida');
    }

}
