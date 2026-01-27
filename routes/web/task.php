<?php

use App\Http\Controllers\TaskController;

Route::middleware(['auth'])->prefix('tasks')->group(function () {

    //Visualização do calendário e dias
    Route::get('/', [TaskController::class, 'index'])
        ->middleware('can:tasks.view')
        ->name('tasks.calendar');

    Route::get('/events', [TaskController::class, 'events'])
        ->middleware('can:tasks.view')
        ->name('tasks.events');

    Route::get('/day/{date}', [TaskController::class, 'day'])
        ->middleware('can:tasks.view')
        ->name('tasks.day');

    //  Criar tarefa
    Route::post('/', [TaskController::class, 'store'])
        ->middleware('can:tasks.create')
        ->name('tasks.store');

    //  Editar tarefa
    Route::put('/{task}', [TaskController::class, 'update'])
        ->middleware('can:tasks.edit')
        ->name('tasks.update');

    //  Alterar status
    Route::put('/{task}/status', [TaskController::class, 'toggleStatus'])
        ->middleware('can:tasks.edit')
        ->name('tasks.toggleStatus');

    //  Excluir tarefa
    Route::delete('/{task}', [TaskController::class, 'destroy'])
        ->middleware('can:tasks.delete')
        ->name('tasks.destroy');
});
