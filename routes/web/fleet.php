<?php


use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\VehicleServiceController;
use App\Http\Controllers\WorkshopController;


//fleet.view
Route::middleware(['auth', 'permission:fleets.view'])->group(function () {
    Route::get('/fleet/vehicles', [VehicleController::class, 'index'])->name('fleet.vehicles.index');
    Route::get('/fleet/vehicle_maintenances', [VehicleMaintenanceController::class, 'index'])->name('fleet.vehicle_maintenances.index');
    Route::get('/fleet/vehicle_services', [VehicleServiceController::class, 'index'])->name('fleet.vehicle_services.index');
    Route::get('/fleet/vehicle_workshop', [WorkshopController::class, 'index'])->name('fleet.vehicle_workshop.index');
    Route::get('/fleet/veiculos/{vehicle}/manutencoes', [VehicleMaintenanceController::class, 'byVehicle'])->name('fleet.vehicle.maintenances');
    //report
    Route::get('/vehicle-maintenance/report/form', function () {
        return view('fleet.vehicles.vehicle_maintenances_report');
    })->name('vehicle-maintenance.report.form');

    Route::get('/vehicle-maintenance/report/pdf', [VehicleMaintenanceController::class, 'handlePdfReport'])
        ->name('vehicle-maintenance.report.pdf');
});
//fleets.create
Route::middleware(['auth', 'permission:fleets.create'])->group(function () {
    Route::post('/fleet/vehicles', [VehicleController::class, 'store'])->name('fleet.vehicles.store');
    Route::post('/fleet/vehicle_maintenances', [VehicleMaintenanceController::class, 'store'])->name('fleet.vehicle_maintenances.store');
    Route::post('/fleet/vehicle_services', [VehicleServiceController::class, 'store'])->name('fleet.vehicle_services.store');
    Route::post('/fleet/vehicle_workshop', [WorkshopController::class, 'store'])->name('fleet.vehicle_workshop.store');
});
//fleets.edit
Route::middleware(['auth', 'permission:fleets.edit'])->group(function () {
    Route::put('/fleet/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('fleet.vehicles.update');
    Route::put('/fleet/vehicle_maintenances/{vehicle_maintenance}', [VehicleMaintenanceController::class, 'update'])->name('fleet.vehicle_maintenances.update');
    Route::put('/fleet/vehicle_services/{vehicleService}', [VehicleServiceController::class, 'update'])->name('fleet.vehicle_services.update');
    Route::put('/fleet/vehicle_workshop/{service}', [WorkshopController::class, 'update'])->name('fleet.vehicle_workshop.update');
});
//fleets.delete
Route::middleware(['auth', 'permission:fleets.delete'])->group(function () {
    Route::delete('/fleet/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('fleet.vehicles.destroy');
    Route::delete('/fleet/vehicle_maintenances/{id}', [VehicleMaintenanceController::class, 'destroy'])->name('fleet.vehicle_maintenances.destroy');
    Route::delete('/fleet/vehicle_services/{vehicleService}', [VehicleServiceController::class, 'destroy'])->name('fleet.vehicle_services.destroy');
    Route::delete('/fleet/vehicle_workshop/{id}', [WorkshopController::class, 'destroy'])->name('fleet.vehicle_workshop.destroy');
});