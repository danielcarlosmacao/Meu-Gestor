<?php 

use App\Http\Controllers\Service\ClientController;
use App\Http\Controllers\Service\EquipmentMaintenanceController;
use App\Http\Controllers\Service\MaintenanceController;

//service.view
Route::middleware(['auth', 'permission:service.view'])->group(function () {
    Route::get('/service/clients', [ClientController::class, 'index'])->name('service.clients.index');
    Route::get('/service/equipment_maintenances', [EquipmentMaintenanceController::class, 'index'])->name('service.equipment_maintenances.index');
    Route::get('/service/maintenances', [MaintenanceController::class, 'index'])->name('service.maintenances.index');
});
//service.create
Route::middleware(['auth', 'permission:service.create'])->group(function () {
    Route::post('/service/equipment_maintenances', [EquipmentMaintenanceController::class, 'store'])->name('service.equipment_maintenances.store');
    Route::post('/service/clients', [ClientController::class, 'store'])->name('service.clients.store');
    Route::post('/service/maintenances', [MaintenanceController::class, 'store'])->name('service.maintenances.store');
});
//service.update
Route::middleware(['auth', 'permission:service.edit'])->group(function () {
    Route::put('/service/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('service.maintenances.update');
    Route::put('/service/clients/{client}', [ClientController::class, 'update'])->name('service.clients.update');
    Route::put('/service/equipment_maintenances/{equipment_maintenance}', [EquipmentMaintenanceController::class, 'update'])->name('service.equipment_maintenances.update');
});
//service.delete
Route::middleware(['auth', 'permission:service.delete'])->group(function () {
    Route::delete('/service/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('service.maintenances.destroy');
    Route::delete('/service/equipment_maintenances/{equipment_maintenance}', [EquipmentMaintenanceController::class, 'destroy'])->name('service.equipment_maintenances.destroy');
    Route::delete('/service/clients/{client}', [ClientController::class, 'destroy'])->name('service.clients.destroy');
});