<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Modules\FbaAuto\Http\Controllers\Admin\FbaAutoController;
use App\Modules\Warranty\Http\Controllers\Admin\WarrantyController;
use App\Modules\Rma\Http\Controllers\Admin\RmaController;
use App\Modules\ReturnReport\Http\Controllers\Admin\ReturnReportController;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/fba-auto', [FbaAutoController::class, 'index'])->name('admin.fba-auto.index');
    Route::get('/warranty', [WarrantyController::class, 'index'])->name('admin.warranty.index');
    Route::get('/rma', [RmaController::class, 'index'])->name('admin.rma.index');
    Route::get('/return-report', [ReturnReportController::class, 'index'])->name('admin.return-report.index');
});

require __DIR__ . '/../app/Modules/FbaAuto/routes/web.php';
require __DIR__ . '/../app/Modules/Warranty/routes/web.php';
require __DIR__ . '/../app/Modules/Rma/routes/web.php';
require __DIR__ . '/../app/Modules/ReturnReport/routes/web.php';
