<?php

use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockMovementController;

Route::middleware(['auth'])->prefix('stock')->group(function () {

    // Rotas explícitas para Itens
    Route::get('items', [StockItemController::class, 'index'])->name('stock.items.index');
    Route::get('items/create', [StockItemController::class, 'create'])->name('stock.items.create');
    Route::post('items', [StockItemController::class, 'store'])->name('stock.items.store');
    Route::get('items/{id}/edit', [StockItemController::class, 'edit'])->name('stock.items.edit');
    Route::put('items/{id}', [StockItemController::class, 'update'])->name('stock.items.update');
    Route::delete('items/{id}', [StockItemController::class, 'destroy'])->name('stock.items.destroy');
    Route::get('items/{id}', [StockItemController::class, 'show'])->name('stock.items.show');

    Route::get('production', [StockItemController::class, 'showProduction'])->name('stock.items.showProduction');

    //reports
    Route::get('movements/report', [StockMovementController::class, 'reportForm'])->name('stock.movements.reportForm');
    Route::get('movements/report/view', [StockMovementController::class, 'reportView'])->name('stock.movements.reportView');
    
    // Rotas explícitas para Movimentações
    Route::get('movements', [StockMovementController::class, 'index'])->name('stock.movements.index');
    Route::get('movements/create', [StockMovementController::class, 'create'])->name('stock.movements.create');
    Route::post('movements', [StockMovementController::class, 'store'])->name('stock.movements.store');
    Route::get('movements/{id}', [StockMovementController::class, 'show'])->name('stock.movements.show');
    Route::post('movements/{id}/update-prices', [StockMovementController::class, 'updatePrices'])->name('movements.updatePrices');
    


});