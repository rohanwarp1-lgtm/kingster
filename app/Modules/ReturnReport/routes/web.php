<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ReturnReport\Http\Controllers\Admin\ReturnReportController;

Route::prefix('admin/return-report')->name('admin.return-report.')->group(function () {
    Route::get('/', [ReturnReportController::class, 'index'])->name('index');
    Route::get('/create', [ReturnReportController::class, 'create'])->name('create');
    Route::post('/store', [ReturnReportController::class, 'store'])->name('store');
    Route::get('/show/{id}', [ReturnReportController::class, 'show'])->name('show');
    Route::delete('/delete/{id}', [ReturnReportController::class, 'destroy'])->name('destroy');
    Route::get('/dashboard', [ReturnReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/export', [ReturnReportController::class, 'export'])->name('export');
    Route::get('/filters', [ReturnReportController::class, 'getFilters'])->name('filters');
    Route::get('/ajax', [ReturnReportController::class, 'ajax'])->name('ajax');
});
