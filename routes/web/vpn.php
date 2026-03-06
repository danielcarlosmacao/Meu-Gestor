<?php


use App\Http\Controllers\WireguardController;


if (config('services.wireguard.url') && config('services.wireguard.password')) {
    Route::middleware(['auth', 'permission:administrator.vpn'])->group(function () {
        Route::get('/vpn', [WireguardController::class, 'index'])->name('api.vpn.index');
        Route::post('/vpn', [WireguardController::class, 'store'])->name('api.vpn.store');
        Route::delete('/vpn/{id}', [WireguardController::class, 'destroy'])->name('api.vpn.destroy');
        Route::get('/vpn/qrcode/{id}', [WireguardController::class, 'qrcode'])->name('api.vpn.qrcode');
        Route::get('/vpn/download/{id}', [WireguardController::class, 'download'])->name('api.vpn.download');

    });
}
;