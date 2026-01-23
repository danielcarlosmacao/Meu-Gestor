<?php 

use App\Http\Controllers\TowerController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\RoleController;


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