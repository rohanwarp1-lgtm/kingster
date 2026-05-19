<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FbaAuto\Http\Controllers\Admin\FbaAutoController;

Route::prefix('admin/fba-auto')->name('admin.fba-auto.')->group(function () {
    Route::get('/', [FbaAutoController::class, 'index'])->name('index');
    Route::get('/create', [FbaAutoController::class, 'create'])->name('create');
    Route::post('/store', [FbaAutoController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [FbaAutoController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [FbaAutoController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [FbaAutoController::class, 'destroy'])->name('destroy');
    Route::post('/restore/{id}', [FbaAutoController::class, 'restore'])->name('restore');
    Route::post('/change-status/{id}', [FbaAutoController::class, 'changeStatus'])->name('change-status');
    Route::post('/bulk-action', [FbaAutoController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/ajax', [FbaAutoController::class, 'ajax'])->name('ajax');
    Route::get('/stats', [FbaAutoController::class, 'getStats'])->name('stats');
    Route::get('/products/search', [FbaAutoController::class, 'searchProducts'])->name('products.search');
});
