<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Warranty\Http\Controllers\Admin\WarrantyController;

Route::prefix('admin/warranty')->name('admin.warranty.')->group(function () {
    Route::get('/', [WarrantyController::class, 'index'])->name('index');
    Route::get('/create', [WarrantyController::class, 'create'])->name('create');
    Route::post('/store', [WarrantyController::class, 'store'])->name('store');
    Route::get('/show/{id}', [WarrantyController::class, 'show'])->name('show');
    Route::post('/start-review/{id}', [WarrantyController::class, 'startReview'])->name('start-review');
    Route::post('/approve/{id}', [WarrantyController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [WarrantyController::class, 'reject'])->name('reject');
    Route::delete('/delete/{id}', [WarrantyController::class, 'destroy'])->name('destroy');
    Route::get('/ajax', [WarrantyController::class, 'ajax'])->name('ajax');
    Route::get('/stats', [WarrantyController::class, 'getStats'])->name('stats');
});
