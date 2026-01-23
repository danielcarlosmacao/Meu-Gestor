<?php

use App\Http\Controllers\TowerController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BatteryController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\EquipmentProductionController;
use App\Http\Controllers\BatteryProductionController;
use App\Http\Controllers\PlateProductionController;
use App\Http\Controllers\MaintenancesController;

// towers.view
Route::middleware(['auth', 'permission:towers.view'])->group(function () {
    Route::get('/tower', [TowerController::class, 'index'])->name('tower.index');
    Route::get('/tower/show/{id}', [TowerController::class, 'show'])->name('tower.show');
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/battery', [BatteryController::class, 'index'])->name('battery.index');
    Route::get('/plate', [PlateController::class, 'index'])->name('plate.index');
    Route::get('/batteryproduction/report/{batteryId}', [BatteryProductionController::class, 'report'])->name('batteryproduction.report');
});
//towers.create
Route::middleware(['auth', 'permission:towers.create'])->group(function () {
    Route::post('/tower', [TowerController::class, 'store'])->name('tower.store');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/battery', [BatteryController::class, 'store'])->name('battery.store');
    Route::post('/plate', [PlateController::class, 'store'])->name('plate.store');
});
// towers.update
Route::middleware(['auth', 'permission:towers.edit'])->group(function () {
    Route::put('/towers/{id}', [TowerController::class, 'update'])->name('tower.update');
    Route::put('/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::put('/battery/{id}', [BatteryController::class, 'update'])->name('battery.update');
    Route::put('/plate/{id}', [PlateController::class, 'update'])->name('plate.update');
});
//towers.destroy
Route::middleware(['auth', 'permission:towers.delete'])->group(function () {
    Route::delete('/tower/{id}', [TowerController::class, 'destroy'])->name('tower.destroy');
    Route::delete('/equipment/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    Route::delete('/battery/{id}', [BatteryController::class, 'destroy'])->name('battery.destroy');
    Route::delete('/plate/{id}', [PlateController::class, 'destroy'])->name('plate.destroy');
});
//Towers.manage
Route::middleware(['auth', 'permission:towers.manage'])->group(function () {
    //EquipmentProduction
    Route::post('/towers/{id}/equipment', [EquipmentProductionController::class, 'store'])->name('equipmentproduction.store');
    Route::get('/equipmentproduction/{id}', [EquipmentProductionController::class, 'edit'])->name('equipmentproduction.edit');
    Route::put('/equipmentproduction/{id}', [EquipmentProductionController::class, 'update'])->name('equipmentproduction.update');
    Route::delete('/equipmentproduction/{id}', [EquipmentProductionController::class, 'destroy'])->name('equipmentproduction.destroy');
    //BatteryProduction
    Route::post('/towers/{id}/battery', [BatteryProductionController::class, 'store'])->name('batteryproduction.store');
    Route::get('/batteryproduction/{id}', [BatteryProductionController::class, 'edit'])->name('batteryproduction.edit');
    Route::put('/batteryproduction/{id}', [BatteryProductionController::class, 'update'])->name('batteryproduction.update');
    Route::delete('/batteryproduction/{id}', [BatteryProductionController::class, 'destroy'])->name('batteryproduction.destroy');
    //PlateProduction
    Route::post('/towers/{id}/plate', [PlateProductionController::class, 'store']);
    Route::delete('/plateproduction/{id}', [PlateProductionController::class, 'destroy'])->name('plateproduction.destroy');

    Route::get('/tower/{id}/recalcular-baterias', [BatteryProductionController::class, 'recalcularPercentuais'])->name('tower.recalcular.baterias');

});
// towers.maintenance
Route::middleware(['auth', 'permission:towers.maintenance'])->group(function () {
    Route::get('/Maintenance', [MaintenancesController::class, 'index'])->name('maintenance.index');
    Route::post('/Maintenance', [MaintenancesController::class, 'store'])->name('maintenance.store');
    Route::delete('/Maintenance/{id}', [MaintenancesController::class, 'destroy'])->name('maintenance.destroy');
    Route::put('/Maintenance/{id}', [MaintenancesController::class, 'update'])->name('maintenance.update');
});
