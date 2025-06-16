<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TowerController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BatteryController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\EquipmentProductionController;
use App\Http\Controllers\BatteryProductionController;
use App\Http\Controllers\PlateProductionController;
use App\Http\Controllers\MaintenancesController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\VehicleServiceController;
use App\Http\Controllers\WorkshopController;

//use App\Http\Controllers\UserController;

//Route::get('/user', [UserController::class, 'index'])->name('user.index');
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('/teste', function () {
    return view('teste');
})->name('teste');

//Admin-Opition
Route::get('/admin/options/colors', [OptionController::class, 'editColors'])->name('options.colors.edit');
Route::post('/admin/options/colors', [OptionController::class, 'updateColors'])->name('options.colors.update');
Route::get('/admin/options/towers', [OptionController::class, 'editTowers'])->name('options.towers.edit');
Route::post('/admin/options/towers', [OptionController::class, 'updateTowers'])->name('options.towers.update');

// tower
Route::get('/tower', [TowerController::class, 'index'])->name('tower.index');
Route::get('/tower/repairsummary', [TowerController::class, 'repairsummary'])->name('tower.repairsummary');
Route::post('/tower', [TowerController::class, 'store'])->name('tower.store');
Route::put('/towers/{id}', [TowerController::class, 'update'])->name('tower.update');
Route::delete('/tower/{id}', [TowerController::class, 'destroy'])->name('tower.destroy');
Route::get('/tower/show/{id}', [TowerController::class, 'show'])->name('tower.show');
Route::get('/tower/show2/{id}', [TowerController::class, 'show2'])->name('tower.show2');

//equipment
Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
Route::put('/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
Route::delete('/equipment/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');


//battery
Route::get('/battery', [BatteryController::class, 'index'])->name('battery.index');
Route::post('/battery', [BatteryController::class, 'store'])->name('battery.store');
Route::put('/battery/{id}', [BatteryController::class, 'update'])->name('battery.update');
Route::delete('/battery/{id}', [BatteryController::class, 'destroy'])->name('battery.destroy');

//plate
Route::get('/plate', [PlateController::class, 'index'])->name('plate.index');
Route::post('/plate', [PlateController::class, 'store'])->name('plate.store');
Route::put('/plate/{id}', [PlateController::class, 'update'])->name('plate.update');
Route::delete('/plate/{id}', [PlateController::class, 'destroy'])->name('plate.destroy');

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
Route::get('/batteryproduction/report/{batteryId}', [BatteryProductionController::class, 'report'])->name('batteryproduction.report');


//PlateProduction
Route::post('/towers/{id}/plate', [PlateProductionController::class, 'store']);
Route::delete('/plateproduction/{id}', [PlateProductionController::class, 'destroy'])->name('plateproduction.destroy');

Route::get('/Maintenance', [MaintenancesController::class, 'index'])->name('maintenance.index');
Route::post('/Maintenance', [MaintenancesController::class, 'store'])->name('maintenance.store');
Route::delete('/Maintenance/{id}', [MaintenancesController::class, 'destroy'])->name('maintenance.destroy');
Route::put('/Maintenance/{id}', [MaintenancesController::class, 'update'])->name('maintenance.update');

// CRUD completo para veículos
Route::prefix('fleet')->name('fleet.')->group(function () {
    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicle_maintenances', VehicleMaintenanceController::class)->except(['create', 'edit', 'show']);
    Route::resource('vehicle_services', VehicleServiceController::class);
    Route::get('/veiculos/{vehicle}/manutencoes', [VehicleMaintenanceController::class, 'byVehicle'])->name('vehicle.maintenances');
    Route::resource('vehicle_workshop', WorkshopController::class)->except(['show', 'create', 'edit']);


});






