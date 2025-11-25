<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\VacationManager\CollaboratorController;
use App\Http\Controllers\VacationManager\VacationController;
use App\Http\Controllers\VacationManager\VacationCalendarController;
use App\Http\Controllers\VacationManager\CollaboratorCourseController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\Service\ClientController;
use App\Http\Controllers\Service\EquipmentMaintenanceController;
use App\Http\Controllers\Service\MaintenanceController;
use App\Http\Controllers\PostitController;
use App\Http\Controllers\Admin\RecipientController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\RoleController;

use App\Http\Controllers\MkAuthController;



use Illuminate\Support\Facades\Artisan;

Route::aliasMiddleware('permission', PermissionMiddleware::class);
Route::aliasMiddleware('role', RoleMiddleware::class);
Route::aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);

//----------------------------------------- AUTHENTICATION --------------------------

Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/stock.php';

// ---------------------------------------------------------------------------------------------------


//Route::get('/user', [UserController::class, 'index'])->name('user.index');
Route::get('/welcome', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('welcome1');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PostitController::class, 'index'])->name('welcome');
    Route::post('/postits', [PostitController::class, 'store'])->name('postits.store');
    Route::put('/postits/{id}', [PostitController::class, 'update'])->name('postits.update');
    Route::delete('/postits/{id}', [PostitController::class, 'destroy'])->name('postits.destroy');
});


//admin
Route::middleware(['auth', 'permission:administrator.user'])->group(function () {
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/{user}/editar', [UserController::class, 'edit'])->name('admin.usuarios.edit');
    //Route::put('/admin/usuarios/{user}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::put('/admin/usuarios/{user}/permissoes', [UserController::class, 'updatePermissions'])->name('admin.usuarios.update');
    Route::get('/admin/usuarios/criar', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::put('/admin/usuarios/{user}/toggle', [UserController::class, 'toggleActive'])->name('admin.usuarios.toggle');
    Route::delete('/admin/usuarios/{user}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');
    Route::post('/admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::get('/admin/sessions', [UserController::class, 'usersOnline'])->name('admin.users.sessions');
    Route::delete('/admin/sessions/{user}', [UserController::class, 'destroySession'])->name('admin.sessions.destroy');
    Route::get('/admin/admin.systempanel', function () {return view('admin.systempanel'); })->name('admin.systempanel');

    // Rotas de Roles
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/admin/roles/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

});


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





//options
Route::middleware(['auth', 'permission:administrator.options'])->group(function () {
    Route::get('/admin/options/colors', [OptionController::class, 'editColors'])->name('options.colors.edit');
    Route::post('/admin/options/colors', [OptionController::class, 'updateColors'])->name('options.colors.update');
    Route::get('/admin/options/resource', [OptionController::class, 'editResource'])->name('options.resource.edit');
    Route::post('/admin/options/system', [OptionController::class, 'updateSystemResource'])->name('options.systemresource.update');
    Route::post('/admin/options/logo', [OptionController::class, 'updatelogo'])->name('options.logo.update');
    Route::get('/tower/repairsummary', [TowerController::class, 'repairsummary'])->name('tower.repairsummary');
    Route::post('/admin/options/resource', [OptionController::class, 'updateResource'])->name('options.resource.update');
    Route::get('/admin/options/system', [OptionController::class, 'editSystemResource'])->name('options.systemresource.edit');

    Route::post('/admin/database/export', [DatabaseController::class, 'export'])->name('database.export');
    Route::post('/admin/database/import', [DatabaseController::class, 'import'])->name('database.import');
    Route::post('/admin/system/update', [DatabaseController::class, 'updateSystem'])->name('system.update');


    Route::get('admin/activity-logs', [ActivityLogController::class, 'index'])->name('activitylogs.index');
    Route::get('admin/system-logs', [ActivityLogController::class, 'laravelLog'])->name('systemlogs.index');

});

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
});
// towers.maintenance
Route::middleware(['auth', 'permission:towers.maintenance'])->group(function () {
    Route::get('/Maintenance', [MaintenancesController::class, 'index'])->name('maintenance.index');
    Route::post('/Maintenance', [MaintenancesController::class, 'store'])->name('maintenance.store');
    Route::delete('/Maintenance/{id}', [MaintenancesController::class, 'destroy'])->name('maintenance.destroy');
    Route::put('/Maintenance/{id}', [MaintenancesController::class, 'update'])->name('maintenance.update');
});

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

//extras 
//recipients.view
Route::middleware(['auth', 'permission:recipients.view'])->group(function () {
    Route::get('/admin/recipients', [RecipientController::class, 'index'])->name('admin.recipients.index');
    Route::post('/admin/recipients', [RecipientController::class, 'store'])->name('admin.recipients.store');
    Route::put('/admin/recipients/{id}', [RecipientController::class, 'update'])->name('admin.recipients.update');
    Route::delete('/admin/recipients/{id}', [RecipientController::class, 'destroy'])->name('admin.recipients.destroy');
    Route::get('/admin/recipients/logs', [RecipientController::class, 'logs'])->name('admin.recipients.logs');

});
//notification.view
Route::middleware(['auth', 'permission:notification.view'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('notification', NotificationController::class)->only(['index', 'store', 'destroy']);
    // Rotas personalizadas para envio
    Route::post('notification/{notification}/send', [NotificationController::class, 'send'])->name('notification.send');
    Route::post('notification/{notification}/resend', [NotificationController::class, 'resend'])->name('notification.resend');
    Route::post('notification/{notification}/cleanSent', [NotificationController::class, 'cleanSent'])->name('notification.cleanSent');
    Route::put('notification/{notification}', [NotificationController::class, 'update'])->name('notification.update');
    Route::get('notification/logs', [NotificationController::class, 'logs'])->name('notification.logs');
    Route::delete('notification/logs/{id}', [NotificationController::class, 'logsDelete'])->name('notification.logs.delete');
});


Route::get('/deploy/{token}', function ($token) {
    if ($token !== env('DEPLOY_TOKEN')) {
        abort(403, 'Unauthorized.');
    }

    exec('/var/www/gestor/deploy.sh 2>&1', $output);

    return response()->json(['output' => $output]);
})->name('deploy.manual');




Route::get('/api/mk/nfe', [MkAuthController::class, 'buscarNotas'])->name('api.mk.nfe');


