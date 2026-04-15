<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Ftth\PonController;
use App\Http\Controllers\Ftth\FiberBoxController;
use App\Http\Controllers\Ftth\CableFiberBoxController;
use App\Http\Controllers\Ftth\FiberCableController;
use App\Http\Controllers\Ftth\SplinterController;
use App\Http\Controllers\Ftth\FusionController;

Route::prefix('ftth')->group(function () {


    Route::middleware(['auth', 'permission:ftth.view'])->group(function () {
        Route::get('/pons', [PonController::class, 'index'])->name('pon.index');
        Route::get('/ponsmap', [FiberBoxController::class, 'ponsmap'])->name('pon.ponsmap');
        Route::get('/fiber-box', [FiberBoxController::class, 'index'])->name('fiberbox.index');
        Route::get('/fiber-box/{box}', [FiberBoxController::class, 'show'])->name('fiberbox.show');
    });

    Route::middleware(['auth', 'permission:ftth.create'])->group(function () {
        Route::post('/pons', [PonController::class, 'store'])->name('pon.store');
        Route::post('/fiber-box', [FiberBoxController::class, 'store'])->name('fiberbox.store');
        Route::post('/cable', [CableFiberBoxController::class, 'store'])->name('cable.store');
        Route::post('/fiber', [FiberCableController::class, 'store'])->name('fiber.store');
        Route::post('/splinter', [SplinterController::class, 'store'])->name('splinter.store');
        Route::post('/fusion/store', [FusionController::class, 'store'])->name('fusion.store');
    });
    Route::middleware(['auth', 'permission:ftth.delete'])->group(function () {
        Route::delete('/pons/{pon}', [PonController::class, 'destroy'])->name('pon.destroy');
        Route::delete('/fiber-box/{box}', [FiberBoxController::class, 'destroy'])->name('fiberbox.destroy');
        Route::delete('/cable/{cable}', [CableFiberBoxController::class, 'destroy'])->name('cable.destroy');
        Route::delete('/fiber/{fiber}', [FiberCableController::class, 'destroy'])->name('fiber.destroy');
        Route::delete('/splinter/{splinter}', [SplinterController::class, 'destroy'])->name('splinter.destroy');
        Route::delete('/fiber/{id}', [FiberCableController::class, 'destroy'])->name('fiber.destroy');
        Route::delete('/cable/{id}', [CableFiberBoxController::class, 'destroy'])->name('cable.destroy');
        Route::delete('/splinter/{id}', [SplinterController::class, 'destroy'])->name('splinter.destroy');
        Route::delete('/fusion/{id}', [FusionController::class, 'destroy'])->name('fusion.destroy');
    });






});