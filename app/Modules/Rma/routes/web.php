<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Rma\Http\Controllers\Admin\RmaController;

Route::prefix('admin/rma')->name('admin.rma.')->group(function () {
    Route::get('/', [RmaController::class, 'index'])->name('index');
    Route::get('/create', [RmaController::class, 'create'])->name('create');
    Route::post('/store', [RmaController::class, 'store'])->name('store');
    Route::get('/show/{id}', [RmaController::class, 'show'])->name('show');
    Route::post('/update-status/{id}', [RmaController::class, 'updateStatus'])->name('update-status');
    Route::post('/assign/{id}', [RmaController::class, 'assign'])->name('assign');
    Route::post('/comment/{id}', [RmaController::class, 'addComment'])->name('comment');
    Route::delete('/delete/{id}', [RmaController::class, 'destroy'])->name('destroy');
    Route::get('/ajax', [RmaController::class, 'ajax'])->name('ajax');
    Route::get('/stats', [RmaController::class, 'getStats'])->name('stats');
});
