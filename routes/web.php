<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;


use App\Http\Controllers\PostitController;
use App\Http\Controllers\Admin\RecipientController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\TaskController;

use App\Http\Controllers\MkAuthController;

use Illuminate\Support\Facades\Artisan;

Route::aliasMiddleware('permission', PermissionMiddleware::class);
Route::aliasMiddleware('role', RoleMiddleware::class);
Route::aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);

//----------------------------------------- AUTHENTICATION --------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// ---------------------------------------------------------------------------------------------------

require __DIR__ . '/auth.php';
require __DIR__ . '/web/tower.php';
require __DIR__ . '/web/fleet.php';
require __DIR__ . '/web/service.php';
require __DIR__ . '/web/vacation.php';
require __DIR__ . '/web/stock.php';
require __DIR__ . '/web/admin.php';

require __DIR__ . '/web/task.php';

//---------------------------------------
Route::get('/', [TaskController::class, 'index'])->middleware('can:tasks.view')->name('welcome');
Route::get('/welcome', [TaskController::class, 'index'])->middleware('can:tasks.view')->name('welcome');
Route::get('/dashboard', [TaskController::class, 'index'])->middleware('can:tasks.view')->name('dashboard');


/* -----------------------------------------Desativando postit -------------------
Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/welcome', function () {return view('welcome');})->middleware(['auth', 'verified'])->name('welcome1');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PostitController::class, 'index'])->name('welcome');
    Route::post('/postits', [PostitController::class, 'store'])->name('postits.store');
    Route::put('/postits/{id}', [PostitController::class, 'update'])->name('postits.update');
    Route::delete('/postits/{id}', [PostitController::class, 'destroy'])->name('postits.destroy');
});

*/


//extras 
//recipients.view
Route::middleware(['auth', 'permission:recipients.view'])->group(function () {
    Route::get('/admin/recipients', [RecipientController::class, 'index'])->name('admin.recipients.index');
    Route::post('/admin/recipients', [RecipientController::class, 'store'])->name('admin.recipients.store');
    Route::put('/admin/recipients/{id}', [RecipientController::class, 'update'])->name('admin.recipients.update');
    Route::delete('/admin/recipients/{id}', [RecipientController::class, 'destroy'])->name('admin.recipients.destroy');
    Route::get('/admin/recipients/logs', [RecipientController::class, 'logs'])->name('admin.recipients.logs');
    Route::get('/api/mk/nfe', [MkAuthController::class, 'buscarNotas'])->name('api.mk.nfe');

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







