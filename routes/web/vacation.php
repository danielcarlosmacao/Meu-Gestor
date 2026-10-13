<?php

use App\Http\Controllers\VacationManager\CollaboratorController;
use App\Http\Controllers\VacationManager\VacationController;
use App\Http\Controllers\VacationManager\VacationCalendarController;
use App\Http\Controllers\VacationManager\CollaboratorCourseController;
use App\Http\Controllers\VacationManager\CollaboratorJobtitleController;
use App\Http\Controllers\VacationManager\WorkSchedulesController;
use App\Http\Controllers\VacationManager\HolidayController;

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


    // ROTAS PARA COLLABORATOR JOB TITLES
    Route::get('/vacation_manager/collaborators/jobtitles', [CollaboratorJobtitleController::class, 'index'])->name('vacation_manager.collaborators.jobtitles.index');
    Route::post('/vacation_manager/collaborators/jobtitles', [CollaboratorJobtitleController::class, 'store'])->name('vacation_manager.collaborators.jobtitles.store');
    Route::put('/vacation_manager/collaborators/jobtitles/{jobtitle}', [CollaboratorJobtitleController::class, 'update'])->name('vacation_manager.collaborators.jobtitles.update');

    // ROTAS PARA WORK SCHEDULES
    Route::get('/vacation_manager/work_schedules/month/{date}', [WorkSchedulesController::class, 'index'])->name('vacation_manager.work_schedules.index');
    Route::get('/vacation_manager/work_schedules/{collaborator}/{date}', [WorkSchedulesController::class, 'myscale'])->name('vacation_manager.work_schedules.myscale');
    Route::get('/vacation_manager/work_schedules/releases', [WorkSchedulesController::class, 'releases'])->name('vacation_manager.work_schedules.releases');
    Route::post('/vacation_manager/work_schedules/releases', [WorkSchedulesController::class, 'storeRelease'])->name('vacation_manager.work_schedules.releases.store');
    Route::delete('/vacation_manager/work_schedules/releases/{id}', [WorkSchedulesController::class, 'destroyRelease'])->name('vacation_manager.work_schedules.releases.destroy');
    // Rota AJAX para alternar o dia de folga (clique na célula)
    Route::post('/vacation_manager/work_schedules/toggle', [WorkSchedulesController::class, 'toggleDayOff'])->name('vacation_manager.work_schedules.toggle');

    // Rota do botão Atualizar Jornada (Recalcular)
    Route::post('/vacation_manager/work_schedules/recalculate', [WorkSchedulesController::class, 'recalculate'])->name('vacation_manager.work_schedules.recalculate');


    // ROTAS PARA HOLIDAYS
    Route::get('/vacation_manager/holidays', [HolidayController::class, 'index'])->name('vacation_manager.holidays.index');
    Route::post('/vacation_manager/holidays', [HolidayController::class, 'store'])->name('vacation_manager.holidays.store');
    Route::delete('/vacation_manager/holidays/{id}', [HolidayController::class, 'destroy'])->name('vacation_manager.holidays.destroy');
});
