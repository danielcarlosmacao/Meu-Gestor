<?php

use App\Http\Controllers\VacationManager\CollaboratorController;
use App\Http\Controllers\VacationManager\VacationController;
use App\Http\Controllers\VacationManager\VacationCalendarController;
use App\Http\Controllers\VacationManager\CollaboratorCourseController;

Route::middleware(['auth', 'permission:vacations.view'])->group(function () {
    // ROTAS PARA COLLABORATORS
    Route::get('/vacation_manager/collaborators', [CollaboratorController::class, 'index'])->name('vacation_manager.collaborators.index');
    Route::get('/vacation_manager/collaborators/create', [CollaboratorController::class, 'create'])->name('vacation_manager.collaborators.create');
    Route::post('/vacation_manager/collaborators', [CollaboratorController::class, 'store'])->name('vacation_manager.collaborators.store');
    Route::get('/vacation_manager/collaborators/{id}/edit', [CollaboratorController::class, 'edit'])->name('vacation_manager.collaborators.edit');
    Route::put('/vacation_manager/collaborators/{id}', [CollaboratorController::class, 'update'])->name('vacation_manager.collaborators.update');
    Route::delete('/vacation_manager/collaborators/{id}', [CollaboratorController::class, 'destroy'])->name('vacation_manager.collaborators.destroy');

    // ROTAS PARA VACATIONS
    Route::get('/vacation_manager/vacations', [VacationController::class, 'index'])->name('vacation_manager.vacations.index');
    Route::get('/vacation_manager/vacations/create', [VacationController::class, 'create'])->name('vacation_manager.vacations.create');
    Route::post('/vacation_manager/vacations', [VacationController::class, 'store'])->name('vacation_manager.vacations.store');
    Route::get('/vacation_manager/vacations/{id}/edit', [VacationController::class, 'edit'])->name('vacation_manager.vacations.edit');
    Route::put('/vacation_manager/vacations/{id}', [VacationController::class, 'update'])->name('vacation_manager.vacations.update');
    Route::delete('/vacation_manager/vacations/{id}', [VacationController::class, 'destroy'])->name('vacation_manager.vacations.destroy');
    // CALENDÁRIO
    Route::get('/vacation_manager/calendar', [VacationCalendarController::class, 'index'])->name('vacation_manager.calendar');

    //
    Route::get('/vacation_manager/collaborators/courses', [CollaboratorCourseController::class, 'index'])->name('vacation_manager.collaborator.courses.index');
    Route::post('/vacation_manager/collaborators/courses', [CollaboratorCourseController::class, 'store'])->name('vacation_manager.collaborator.courses.store');
    Route::put('/vacation_manager/collaborators/courses/{course:token}', [CollaboratorCourseController::class, 'update'])->name('vacation_manager.collaborator.courses.update');
    Route::get('/vacation_manager/collaborators/courses/{course:token}', [CollaboratorCourseController::class, 'show'])->name('vacation_manager.collaborator.courses.show');
    Route::delete('/vacation_manager/collaborators/courses/{course:token}', [CollaboratorCourseController::class, 'destroy'])->name('vacation_manager.collaborator.courses.destroy');
    Route::get('/vacation_manager/collaborators/courses/{course:token}/download', [CollaboratorCourseController::class, 'download'])->name('vacation_manager.collaborator.courses.download');

});

